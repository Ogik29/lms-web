<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

// Debug route to inspect auth/session state (enabled only when APP_DEBUG=true)
// if (config('app.debug')) {
//     Route::get('/_debug-auth', function () {
//         return response()->json([
//             'auth' => Auth::check(),
//             'user' => Auth::user() ? Auth::user()->only(['id', 'name', 'email', 'role_id']) : null,
//             'session' => session()->all(),
//             'cookies' => request()->cookies->all(),
//         ]);
//     });
// }

Route::middleware('guest')->group(function () {
    // Rute untuk menampilkan form dan memproses registrasi
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Rute untuk menampilkan form dan memproses login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});


// --- Routes untuk Pengguna yang Sudah Login ---
Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // guru
    Route::middleware('role:2')->group(function () {
        Route::get('/teacher/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
        Route::post('/teacher/courses', [TeacherController::class, 'storeCourse'])->name('teacher.courses.store');
        // Course management
        Route::get('/teacher/courses/{course}', [TeacherController::class, 'showCourse'])->name('teacher.courses.show');
        Route::get('/teacher/courses/{course}/edit', [TeacherController::class, 'editCourse'])->name('teacher.courses.edit');
        Route::put('/teacher/courses/{course}', [TeacherController::class, 'updateCourse'])->name('teacher.courses.update');
        Route::delete('/teacher/courses/{course}', [TeacherController::class, 'destroyCourse'])->name('teacher.courses.destroy');

        // Student management inside a course
        Route::delete('/teacher/courses/{course}/students/{student}', [TeacherController::class, 'removeStudent'])->name('teacher.courses.students.remove');
        // Dedicated students list page for a course
        Route::get('/teacher/courses/{course}/students', [TeacherController::class, 'students'])->name('teacher.courses.students.index');

        // Schedule management
        Route::post('/teacher/courses/{course}/schedules', [TeacherController::class, 'storeSchedule'])->name('teacher.courses.schedules.store');
        Route::delete('/teacher/courses/{course}/schedules/{schedule}', [TeacherController::class, 'destroySchedule'])->name('teacher.courses.schedules.destroy');

        // Assignment management
        Route::post('/teacher/courses/{course}/assignments', [TeacherController::class, 'storeAssignment'])->name('teacher.courses.assignments.store');
        Route::delete('/teacher/courses/{course}/assignments/{assignment}', [TeacherController::class, 'destroyAssignment'])->name('teacher.courses.assignments.destroy');
        Route::get('/teacher/assignments/{assignment}/submissions', [TeacherController::class, 'submissions'])->name('teacher.assignments.submissions.index');
        Route::post('/teacher/assignments/{assignment}/submissions/deadline', [TeacherController::class, 'setSubmissionDeadline'])->name('teacher.assignments.submissions.deadline');
        // Assignment edit by teacher
        Route::get('/teacher/courses/{course}/assignments/{assignment}/edit', [TeacherController::class, 'editAssignment'])->name('teacher.courses.assignments.edit');
        Route::put('/teacher/courses/{course}/assignments/{assignment}', [TeacherController::class, 'updateAssignment'])->name('teacher.courses.assignments.update');
        Route::post('/teacher/submissions/{submission}/grade', [TeacherController::class, 'gradeSubmission'])->name('teacher.submissions.grade');
        Route::get('/teacher/submissions/pending', [TeacherController::class, 'pendingSubmissions'])->name('teacher.submissions.pending');

        // Grade management
        Route::get('/teacher/courses/{course}/grades', [TeacherController::class, 'grades'])->name('teacher.courses.grades.index');
        Route::post('/teacher/courses/{course}/grades', [TeacherController::class, 'storeGrade'])->name('teacher.courses.grades.store');
        Route::put('/teacher/grades/{grade}', [TeacherController::class, 'updateGrade'])->name('teacher.grades.update');
        Route::delete('/teacher/grades/{grade}', [TeacherController::class, 'destroyGrade'])->name('teacher.grades.destroy');

        // Quiz management
        Route::get('/teacher/courses/{course}/quiz/create', [TeacherController::class, 'createQuiz'])->name('teacher.quiz.create');
        Route::post('/teacher/courses/{course}/quiz', [TeacherController::class, 'storeQuiz'])->name('teacher.quiz.store');
        Route::get('/teacher/courses/{course}/quiz/{assignment}/edit', [TeacherController::class, 'editQuiz'])->name('teacher.quiz.edit');
        Route::put('/teacher/courses/{course}/quiz/{assignment}', [TeacherController::class, 'updateQuiz'])->name('teacher.quiz.update');
        Route::get('/teacher/quiz/{assignment}/results', [TeacherController::class, 'viewQuizResults'])->name('teacher.quiz.results');
        Route::get('/teacher/quiz/attempt/{attempt}', [TeacherController::class, 'viewStudentQuizAttempt'])->name('teacher.quiz.attempt.view');
        Route::post('/teacher/quiz/answer/{answer}/grade', [TeacherController::class, 'gradeEssayQuestion'])->name('teacher.quiz.answer.grade');
    });

    // murid
    Route::middleware('role:3')->group(function () {
        Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/student/courses', [StudentController::class, 'indexCourses'])->name('student.courses.index');
        Route::post('/student/courses/join', [StudentController::class, 'joinCourse'])->name('student.courses.join');

        // Student course and assignment views
        Route::get('/student/courses/{course}', [StudentController::class, 'showCourse'])->name('student.courses.show');
        Route::get('/student/assignments/{assignment}', [StudentController::class, 'showAssignment'])->name('student.assignments.show');
        Route::post('/student/assignments/{assignment}/submit', [StudentController::class, 'storeSubmission'])->name('student.assignments.submit');

        // Quiz taking
        Route::post('/student/quiz/{assignment}/start', [StudentController::class, 'startQuiz'])->name('student.quiz.start');
        Route::get('/student/quiz/attempt/{attempt}', [StudentController::class, 'takeQuiz'])->name('student.quiz.take');
        Route::post('/student/quiz/attempt/{attempt}/answer', [StudentController::class, 'submitQuizAnswer'])->name('student.quiz.answer.submit');
        Route::post('/student/quiz/attempt/{attempt}/submit', [StudentController::class, 'submitQuiz'])->name('student.quiz.submit');
        Route::get('/student/quiz/attempt/{attempt}/result', [StudentController::class, 'viewQuizResult'])->name('student.quiz.result');
    });
});
