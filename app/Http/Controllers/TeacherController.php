<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Schedule;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $user = User::with('role')->find(Auth::id());

        $courses = $user->taughtCourses()->withCount('students')->get();

        // Hitung total siswa dari semua kursus (tanpa duplikasi)
        // menjumlahkan jumlah siswa per kursus
        $totalStudents = $courses->sum('students_count');

        // Kirim data ke view
        return view('guru.dashboard', [
            'user' => $user,
            'courses' => $courses,
            'coursesCount' => $courses->count(),
            'totalStudents' => $totalStudents,
            'tasksToGrade' => Submission::whereNull('score')
                ->whereHas('assignment.course', function ($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                })
                ->count(),
            'chartLabels' => $courses->pluck('name')->toArray(),
            'chartData' => $courses->pluck('students_count')->toArray(),
            'recentSubmissions' => Submission::whereHas('assignment.course', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            })->latest()->take(5)->get(),
        ]);
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        do {
            $code = strtoupper(Str::random(8));
        } while (Course::where('code', $code)->exists());

        $request->user()->taughtCourses()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'code' => $code,
        ]);

        return redirect()->route('teacher.dashboard')
            ->with('success', 'Kursus baru berhasil dibuat!');
    }

    public function showCourse(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $course->load(['students', 'schedules.subject', 'grades.student', 'assignments.submissions']);
        $subjects = Subject::all();

        return view('guru.course.show', compact('course', 'subjects'));
    }

    public function editCourse(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        return view('guru.course.edit', compact('course'));
    }

    public function updateCourse(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course->update($validated);

        return redirect()->route('teacher.courses.show', $course)->with('success', 'Kelas diperbarui.');
    }

    public function destroyCourse(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $course->delete();

        return redirect()->route('teacher.dashboard')->with('success', 'Kelas dihapus.');
    }

    // Assignment management
    public function storeAssignment(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $course->assignments()->create($validated);

        return back()->with('success', 'Tugas berhasil dibuat.');
    }

    public function destroyAssignment(Course $course, Assignment $assignment)
    {
        if ($course->teacher_id !== Auth::id() || $assignment->course_id !== $course->id) {
            abort(403);
        }

        $assignment->delete();

        return back()->with('success', 'Tugas dihapus.');
    }

    public function submissions(Assignment $assignment)
    {
        if ($assignment->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $assignment->load('submissions.student', 'course.students');

        return view('guru.course.submissions', compact('assignment'));
    }

    public function setSubmissionDeadline(Request $request, Assignment $assignment)
    {
        if ($assignment->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'deadline' => 'nullable|date',
        ]);

        // ensure the student belongs to the course
        if (! $assignment->course->students->contains('id', $validated['student_id'])) {
            abort(403);
        }

        $submission = Submission::firstOrNew([
            'assignment_id' => $assignment->id,
            'student_id' => $validated['student_id'],
        ]);

        $submission->deadline = $validated['deadline'] ? now()->createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($validated['deadline']))) : null;
        $submission->save();

        return back()->with('success', 'Deadline submisi diperbarui.');
    }

    public function pendingSubmissions()
    {
        $submissions = Submission::whereNull('score')
            ->whereHas('assignment.course', function ($q) {
                $q->where('teacher_id', Auth::id());
            })
            ->with(['assignment', 'student'])
            ->get();

        return view('guru.submissions.pending', compact('submissions'));
    }

    public function gradeSubmission(Request $request, Submission $submission)
    {
        if ($submission->assignment->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'grader_id' => Auth::id(),
            'graded_at' => now(),
        ]);

        return back()->with('success', 'Nilai tersimpan.');
    }

    // Show edit form for an assignment (teacher edits assignment details)
    public function editAssignment(Course $course, Assignment $assignment)
    {
        if ($course->teacher_id !== Auth::id() || $assignment->course_id !== $course->id) {
            abort(403);
        }

        return view('guru.assignments.edit', compact('course', 'assignment'));
    }

    // Update assignment (teacher)
    public function updateAssignment(Request $request, Course $course, Assignment $assignment)
    {
        if ($course->teacher_id !== Auth::id() || $assignment->course_id !== $course->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $assignment->update($validated);

        return redirect()->route('teacher.courses.show', $course)->with('success', 'Tugas diperbarui.');
    }

    public function removeStudent(Course $course, User $student)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $course->students()->detach($student->id);

        return back()->with('success', 'Siswa dihapus dari kelas.');
    }

    public function students(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $course->load('students');

        return view('guru.course.students', compact('course'));
    }

    public function storeSchedule(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'subject_id' => 'nullable|exists:subjects,id',
            'day_of_week' => 'required|string|max:20',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $course->schedules()->create(array_merge($validated, [
            'teacher_id' => Auth::id(),
        ]));

        return back()->with('success', 'Jadwal ditambahkan.');
    }

    public function destroySchedule(Course $course, Schedule $schedule)
    {
        if ($course->teacher_id !== Auth::id() || $schedule->course_id !== $course->id) {
            abort(403);
        }

        $schedule->delete();

        return back()->with('success', 'Jadwal dihapus.');
    }

    public function grades(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $course->load(['grades.student', 'students']);

        return view('guru.course.grades', compact('course'));
    }

    public function storeGrade(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'score' => 'required|numeric|min:0|max:100',
        ]);

        // pastikan student terdaftar di course
        if (! $course->students()->where('users.id', $validated['student_id'])->exists()) {
            return back()->withErrors(['student_id' => 'Siswa tidak ditemukan di kelas ini.']);
        }

        $course->grades()->create(array_merge($validated, [
            'grader_id' => Auth::id(),
        ]));

        return back()->with('success', 'Nilai disimpan.');
    }

    public function updateGrade(Request $request, Grade $grade)
    {
        if ($grade->grader_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $grade->update($validated);

        return back()->with('success', 'Nilai diperbarui.');
    }

    public function destroyGrade(Grade $grade)
    {
        if ($grade->grader_id !== Auth::id()) {
            abort(403);
        }

        $grade->delete();

        return back()->with('success', 'Nilai dihapus.');
    }

    // Quiz management
    public function createQuiz(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        return view('guru.quiz.create', compact('course'));
    }

    public function storeQuiz(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'show_results_immediately' => 'nullable',
            'shuffle_questions' => 'nullable',
            'shuffle_options' => 'nullable',
            'passing_score' => 'nullable|numeric|min:0|max:100',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:multiple_choice,essay',
            'questions.*.points' => 'required|numeric|min:0',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.option_text' => 'nullable|string',
            'questions.*.correct_option' => 'nullable|integer',
        ]);

        // Additional validation for multiple choice questions
        if (isset($validated['questions'])) {
            foreach ($validated['questions'] as $index => $question) {
                if ($question['question_type'] === 'multiple_choice') {
                    if (empty($question['options']) || count($question['options']) < 2) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['questions.' . $index . '.options' => 'Soal pilihan ganda harus memiliki minimal 2 pilihan.']);
                    }
                    if (!isset($question['correct_option'])) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['questions.' . $index . '.correct_option' => 'Pilih jawaban yang benar untuk soal pilihan ganda.']);
                    }
                }
            }
        }

        // Create assignment
        $assignment = $course->assignments()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'] ?? null,
            'type' => 'quiz',
        ]);

        // Create quiz
        $quiz = $assignment->quiz()->create([
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'max_attempts' => $validated['max_attempts'],
            'show_results_immediately' => $validated['show_results_immediately'] ?? false,
            'shuffle_questions' => $validated['shuffle_questions'] ?? false,
            'shuffle_options' => $validated['shuffle_options'] ?? false,
            'passing_score' => $validated['passing_score'] ?? null,
        ]);

        // Create questions and options
        foreach ($validated['questions'] as $index => $questionData) {
            $question = $quiz->questions()->create([
                'question_text' => $questionData['question_text'],
                'question_type' => $questionData['question_type'],
                'points' => $questionData['points'],
                'order' => $index + 1,
                'explanation' => $questionData['explanation'] ?? null,
            ]);

            if ($questionData['question_type'] === 'multiple_choice' && isset($questionData['options'])) {
                foreach ($questionData['options'] as $optIndex => $option) {
                    $question->options()->create([
                        'option_text' => $option['option_text'],
                        'is_correct' => isset($questionData['correct_option']) && $questionData['correct_option'] == $optIndex,
                        'order' => $optIndex + 1,
                    ]);
                }
            }
        }

        return redirect()->route('teacher.courses.show', $course)->with('success', 'Quiz berhasil dibuat.');
    }

    public function editQuiz(Course $course, Assignment $assignment)
    {
        if ($course->teacher_id !== Auth::id() || $assignment->course_id !== $course->id || !$assignment->isQuiz()) {
            abort(403);
        }

        $assignment->load('quiz.questions.options');

        return view('guru.quiz.edit', compact('course', 'assignment'));
    }

    public function updateQuiz(Request $request, Course $course, Assignment $assignment)
    {
        if ($course->teacher_id !== Auth::id() || $assignment->course_id !== $course->id || !$assignment->isQuiz()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'show_results_immediately' => 'nullable|boolean',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'passing_score' => 'nullable|numeric|min:0|max:100',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:multiple_choice,essay',
            'questions.*.points' => 'required|numeric|min:0',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.options' => 'required_if:questions.*.question_type,multiple_choice|array|min:2',
            'questions.*.options.*.option_text' => 'required_if:questions.*.question_type,multiple_choice|string',
            'questions.*.correct_option' => 'required_if:questions.*.question_type,multiple_choice|integer',
        ]);

        // Update assignment
        $assignment->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'] ?? null,
        ]);

        // Update quiz
        $assignment->quiz->update([
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'max_attempts' => $validated['max_attempts'],
            'show_results_immediately' => $validated['show_results_immediately'] ?? false,
            'shuffle_questions' => $validated['shuffle_questions'] ?? false,
            'shuffle_options' => $validated['shuffle_options'] ?? false,
            'passing_score' => $validated['passing_score'] ?? null,
        ]);

        // Delete existing questions and recreate
        $assignment->quiz->questions()->delete();

        foreach ($validated['questions'] as $index => $questionData) {
            $question = $assignment->quiz->questions()->create([
                'question_text' => $questionData['question_text'],
                'question_type' => $questionData['question_type'],
                'points' => $questionData['points'],
                'order' => $index + 1,
                'explanation' => $questionData['explanation'] ?? null,
            ]);

            if ($questionData['question_type'] === 'multiple_choice' && isset($questionData['options'])) {
                foreach ($questionData['options'] as $optIndex => $option) {
                    $question->options()->create([
                        'option_text' => $option['option_text'],
                        'is_correct' => isset($questionData['correct_option']) && $questionData['correct_option'] == $optIndex,
                        'order' => $optIndex + 1,
                    ]);
                }
            }
        }

        return redirect()->route('teacher.courses.show', $course)->with('success', 'Quiz berhasil diupdate.');
    }

    public function viewQuizResults(Assignment $assignment)
    {
        if ($assignment->course->teacher_id !== Auth::id() || !$assignment->isQuiz()) {
            abort(403);
        }

        $assignment->load([
            'quiz.attempts' => function ($q) {
                $q->with('student')->latest();
            }
        ]);

        return view('guru.quiz.results', compact('assignment'));
    }

    public function viewStudentQuizAttempt(\App\Models\StudentQuizAttempt $attempt)
    {
        if ($attempt->quiz->assignment->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $attempt->load([
            'student',
            'answers.question.options',
            'answers.selectedOption'
        ]);

        return view('guru.quiz.attempt_detail', compact('attempt'));
    }

    public function gradeEssayQuestion(Request $request, \App\Models\StudentQuizAnswer $answer)
    {
        $attempt = $answer->attempt;
        if ($attempt->quiz->assignment->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'points_earned' => 'required|numeric|min:0|max:' . $answer->question->points,
        ]);

        $answer->update([
            'points_earned' => $validated['points_earned'],
        ]);

        // Recalculate total score
        $totalScore = $attempt->answers()->sum('points_earned');
        $allEssayGraded = $attempt->answers()
            ->whereHas('question', function ($q) {
                $q->where('question_type', 'essay');
            })
            ->whereNull('points_earned')
            ->count() === 0;

        $attempt->update([
            'total_score' => $totalScore,
            'is_graded' => $allEssayGraded,
            'graded_by' => Auth::id(),
            'graded_at' => now(),
        ]);

        return back()->with('success', 'Essay berhasil dinilai.');
    }
}
