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
}
