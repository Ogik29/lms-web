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

        $latestCourse = $user->enrolledCourses()->latest()->first();

        return view('murid.dashboard', [
            'user' => $user,
            'enrolledCoursesCount' => $enrolledCourses->count(),
            'gradesCount' => $grades->count(), // Misal: menggantikan 'sertifikat'
            'tasksCount' => Assignment::whereIn('course_id', $enrolledCourses->pluck('id'))
                ->whereDoesntHave('submissions', function ($q) use ($user) {
                    $q->where('student_id', $user->id);
                })
                ->count(), // jumlah tugas yang belum disubmit oleh murid
            'latestCourse' => $latestCourse,
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
            $existing->update($data);
        } else {
            $assignment->submissions()->create(array_merge(['student_id' => $user->id], $data));
        }

        return back()->with('success', 'Tugas dikirimkan.');
    }
}
