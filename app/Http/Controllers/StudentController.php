<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = User::with('role')->find(Auth::id());

        $enrolledCourses = $user->enrolledCourses()->get();
        $grades = $user->gradesReceived()->get();

        $latestCourse = $user->enrolledCourses()->with('teacher')->latest()->first();

        // Get pending assignments (not submitted yet)
        $pendingAssignments = Assignment::whereIn('course_id', $enrolledCourses->pluck('id'))
            ->where('type', 'regular')
            ->whereDoesntHave('submissions', function ($q) use ($user) {
                $q->where('student_id', $user->id);
            })
            ->with('course')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        // Get pending quizzes (not started or not completed)
        $pendingQuizzes = Assignment::whereIn('course_id', $enrolledCourses->pluck('id'))
            ->where('type', 'quiz')
            ->with(['course', 'quiz.attempts' => function ($q) use ($user) {
                $q->where('student_id', $user->id);
            }])
            ->get()
            ->filter(function ($assignment) use ($user) {
                // Check if quiz relationship exists
                if (!$assignment->quiz) {
                    return false;
                }

                $attempts = $assignment->quiz->attempts ?? collect();
                $completedAttempts = $attempts->where('submitted_at', '!=', null)->count();
                $ongoingAttempt = $attempts->whereNull('submitted_at')->first();

                // Show if: has ongoing attempt, or completed attempts < max attempts
                return $ongoingAttempt || $completedAttempts < $assignment->quiz->max_attempts;
            })
            ->take(5);

        // Merge and sort by due date
        $pendingTasks = $pendingAssignments->concat($pendingQuizzes)
            ->sortBy('due_date')
            ->take(5);

        return view('murid.dashboard', [
            'user' => $user,
            'enrolledCoursesCount' => $enrolledCourses->count(),
            'gradesCount' => $grades->count(),
            'tasksCount' => $pendingAssignments->count() + $pendingQuizzes->count(),
            'latestCourse' => $latestCourse,
            'pendingTasks' => $pendingTasks,
        ]);
    }

    public function indexCourses()
    {
        $user = User::with('role')->find(Auth::id());

        // Ambil semua kelas yang diikuti murid, dan ambil juga data gurunya (eager loading)
        $courses = $user->enrolledCourses()->with('teacher')->latest()->get();

        return view('murid.kelas', [
            'courses' => $courses
        ]);
    }

    public function joinCourse(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'size:8',
                Rule::exists('courses', 'code'), // Pastikan kode ada di tabel courses
            ],
        ], [
            'code.exists' => 'Kode kelas tidak ditemukan atau tidak valid.',
            'code.size' => 'Kode kelas harus terdiri dari 8 karakter.',
        ]);

        $course = Course::where('code', $validated['code'])->first();
        $user = User::with('role')->find(Auth::id());

        // Cek apakah user sudah terdaftar di kursus ini
        if ($user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('student.courses.index')->with('error', 'Anda sudah terdaftar di kelas ini.');
        }

        // Daftarkan murid ke kelas menggunakan relasi
        $user->enrolledCourses()->attach($course->id);

        return redirect()->route('student.courses.index')->with('success', 'Selamat! Anda berhasil bergabung dengan kelas: ' . $course->name);
    }

    public function showCourse(Course $course)
    {
        $user = User::find(Auth::id());

        // pastikan murid terdaftar di kelas
        if (! $user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            abort(403);
        }

        $course->load(['teacher', 'schedules.subject', 'assignments.submissions']);

        return view('murid.course.show', compact('course'));
    }

    public function showAssignment(Assignment $assignment)
    {
        $user = User::find(Auth::id());

        $course = $assignment->course;
        if (! $user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            abort(403);
        }

        $submission = $assignment->submissions()->where('student_id', $user->id)->first();

        return view('murid.assignment.show', compact('assignment', 'submission'));
    }

    public function storeSubmission(Request $request, Assignment $assignment)
    {
        $user = User::find(Auth::id());

        $course = $assignment->course;
        if (! $user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip,jpg,jpeg,png|max:20480', // max 20MB
        ]);

        $existing = $assignment->submissions()->where('student_id', $user->id)->first();

        $data = ['content' => $validated['content'] ?? null, 'submitted_at' => now()];

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('submissions', 'public');
            $data['file_path'] = $path;

            if ($existing && $existing->file_path) {
                Storage::disk('public')->delete($existing->file_path);
            }
        }

        if ($existing) {
            // mark as edited when updating an existing submission
            $data['is_edited'] = true;
            $data['edited_at'] = now();
            $existing->update($data);
        } else {
            // set per-submission deadline to assignment due_date by default
            if ($assignment->due_date) {
                $data['deadline'] = $assignment->due_date;
            }
            $assignment->submissions()->create(array_merge(['student_id' => $user->id], $data));
        }

        return back()->with('success', 'Tugas dikirimkan.');
    }

    // Quiz functions
    public function startQuiz(Assignment $assignment)
    {
        $user = User::find(Auth::id());
        $course = $assignment->course;

        if (!$user->enrolledCourses()->where('course_id', $course->id)->exists() || !$assignment->isQuiz()) {
            abort(403);
        }

        $quiz = $assignment->quiz()->with('questions')->first();

        // Check attempts limit
        $attemptCount = $quiz->attempts()->where('student_id', $user->id)->count();
        if ($attemptCount >= $quiz->max_attempts) {
            return back()->with('error', 'Anda sudah mencapai batas maksimal percobaan.');
        }

        // Check if there's an ongoing attempt
        $ongoingAttempt = $quiz->attempts()
            ->where('student_id', $user->id)
            ->whereNull('submitted_at')
            ->first();

        if ($ongoingAttempt) {
            return redirect()->route('student.quiz.take', $ongoingAttempt);
        }

        // Calculate max score
        $maxScore = $quiz->questions->sum('points');

        // Create new attempt
        $attempt = $quiz->attempts()->create([
            'student_id' => $user->id,
            'started_at' => now(),
            'max_score' => $maxScore,
        ]);

        return redirect()->route('student.quiz.take', $attempt);
    }

    public function takeQuiz(\App\Models\StudentQuizAttempt $attempt)
    {
        $user = User::find(Auth::id());

        if ($attempt->student_id !== $user->id) {
            abort(403);
        }

        if ($attempt->isCompleted()) {
            return redirect()->route('student.quiz.result', $attempt);
        }

        $course = $attempt->quiz->assignment->course;
        if (!$user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            abort(403);
        }

        $quiz = $attempt->quiz;
        $questions = $quiz->questions()->with('options')->get();

        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        if ($quiz->shuffle_options) {
            $questions->each(function ($question) {
                if ($question->options) {
                    $question->setRelation('options', $question->options->shuffle());
                }
            });
        }

        // Load existing answers
        $existingAnswers = $attempt->answers()->get()->keyBy('question_id');

        return view('murid.quiz.take', compact('attempt', 'quiz', 'questions', 'existingAnswers'));
    }

    public function submitQuizAnswer(Request $request, \App\Models\StudentQuizAttempt $attempt)
    {
        $user = User::find(Auth::id());

        if ($attempt->student_id !== $user->id || $attempt->isCompleted()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:quiz_questions,id',
            'selected_option_id' => 'nullable|exists:quiz_question_options,id',
            'essay_answer' => 'nullable|string',
        ]);

        $question = \App\Models\QuizQuestion::find($validated['question_id']);

        if ($question->quiz_id !== $attempt->quiz_id) {
            return response()->json(['error' => 'Invalid question'], 400);
        }

        $answerData = [
            'attempt_id' => $attempt->id,
            'question_id' => $validated['question_id'],
        ];

        if ($question->isMultipleChoice()) {
            $answerData['selected_option_id'] = $validated['selected_option_id'] ?? null;

            // Auto-grade multiple choice
            if ($validated['selected_option_id']) {
                $option = \App\Models\QuizQuestionOption::find($validated['selected_option_id']);
                if ($option && $option->is_correct) {
                    $answerData['points_earned'] = $question->points;
                    $answerData['is_correct'] = true;
                } else {
                    $answerData['points_earned'] = 0;
                    $answerData['is_correct'] = false;
                }
            }
        } else {
            $answerData['essay_answer'] = $validated['essay_answer'] ?? null;
            // Essay will be graded by teacher later
        }

        \App\Models\StudentQuizAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $validated['question_id'],
            ],
            $answerData
        );

        return response()->json(['success' => true]);
    }

    public function submitQuiz(\App\Models\StudentQuizAttempt $attempt)
    {
        $user = User::find(Auth::id());

        if ($attempt->student_id !== $user->id || $attempt->isCompleted()) {
            abort(403);
        }

        $timeTaken = now()->diffInSeconds($attempt->started_at);

        // Calculate total score (only from graded questions)
        $totalScore = $attempt->answers()->sum('points_earned');

        // Check if all questions are graded (no pending essay questions)
        $hasEssayQuestions = $attempt->quiz->questions()->where('question_type', 'essay')->exists();
        $isFullyGraded = !$hasEssayQuestions || $attempt->answers()
            ->whereHas('question', function ($q) {
                $q->where('question_type', 'essay');
            })
            ->whereNotNull('points_earned')
            ->count() === $attempt->quiz->questions()->where('question_type', 'essay')->count();

        $attempt->update([
            'submitted_at' => now(),
            'time_taken_seconds' => $timeTaken,
            'total_score' => $totalScore,
            'is_graded' => $isFullyGraded,
        ]);

        return redirect()->route('student.quiz.result', $attempt)->with('success', 'Quiz berhasil diselesaikan.');
    }

    public function viewQuizResult(\App\Models\StudentQuizAttempt $attempt)
    {
        $user = User::find(Auth::id());

        if ($attempt->student_id !== $user->id) {
            abort(403);
        }

        if (!$attempt->isCompleted()) {
            return redirect()->route('student.quiz.take', $attempt);
        }

        $attempt->load([
            'quiz.questions.options',
            'answers.question.options',
            'answers.selectedOption'
        ]);

        return view('murid.quiz.result', compact('attempt'));
    }
}
