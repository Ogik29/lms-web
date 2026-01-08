@extends('layouts.student')

@section('title', 'Dashboard Siswa')



@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Selamat Datang, {{ $user->name }}!</h1>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Kelas Terdaftar</h5>
                    <p class="fs-1 fw-bold text-primary">{{ $enrolledCoursesCount }}</p>
                    <p class="card-text text-muted">Kelas yang sedang Anda ikuti.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Tugas Mendatang</h5>
                     <!-- Data dinamis -->
                    <p class="fs-1 fw-bold text-warning">{{ $tasksCount }}</p>
                    <p class="card-text text-muted">Tugas yang perlu diselesaikan.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Total Nilai</h5>
                     <!-- Data dinamis -->
                    <p class="fs-1 fw-bold text-success">{{ $gradesCount }}</p>
                    <p class="card-text text-muted">Total nilai yang sudah masuk.</p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="h4 my-4">Tugas & Quiz yang Perlu Diselesaikan</h2>
    
    @if(isset($pendingTasks) && $pendingTasks->count() > 0)
        <div class="row">
            @foreach($pendingTasks as $task)
                @php
                    $isQuiz = $task->isQuiz();
                    $userAttempts = collect();
                    $ongoingAttempt = null;
                    
                    if ($isQuiz && $task->quiz) {
                        $userAttempts = $task->quiz->attempts ?? collect();
                        $ongoingAttempt = $userAttempts->whereNull('submitted_at')->first();
                    }
                @endphp
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $task->title }}</h5>
                                @if($isQuiz)
                                    <span class="badge bg-info">
                                        <i class="fas fa-clipboard-question"></i> Quiz
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-file-alt"></i> Tugas
                                    </span>
                                @endif
                            </div>
                            
                            @if($task->course)
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-book me-1"></i> {{ $task->course->name }}
                                </p>
                            @endif
                            
                            @if($task->due_date)
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-clock me-1"></i> 
                                    Deadline: {{ $task->due_date->format('d M Y, H:i') }}
                                    @if($task->due_date->isPast())
                                        <span class="badge bg-danger ms-1">Terlambat</span>
                                    @elseif($task->due_date->diffInHours(now()) < 24)
                                        <span class="badge bg-warning text-dark ms-1">Segera!</span>
                                    @endif
                                </p>
                            @endif

                            @if($isQuiz && $task->quiz)
                                <div class="mb-3">
                                    <small class="text-muted">
                                        Percobaan: {{ $userAttempts->where('submitted_at', '!=', null)->count() }}/{{ $task->quiz->max_attempts }}
                                    </small>
                                </div>
                            @endif

                            <div class="d-flex gap-2">
                                @if($isQuiz)
                                    @if($ongoingAttempt)
                                        <a href="{{ route('student.quiz.take', ['attempt' => $ongoingAttempt->id]) }}" class="btn btn-sm btn-warning flex-grow-1">
                                            <i class="fas fa-play"></i> Lanjutkan Quiz
                                        </a>
                                    @else
                                        <form action="{{ route('student.quiz.start', ['assignment' => $task->id]) }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                                <i class="fas fa-play"></i> Mulai Quiz
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('student.assignments.show', ['assignment' => $task->id]) }}" class="btn btn-sm btn-primary flex-grow-1">
                                        <i class="fas fa-pencil-alt"></i> Kerjakan
                                    </a>
                                @endif
                                @if($task->course)
                                    <a href="{{ route('student.courses.show', ['course' => $task->course->id]) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{-- @if($latestCourse)
            <div class="text-center mt-4">
                <a href="{{ route('student.courses.show', ['course' => $latestCourse->id]) }}" class="btn btn-outline-primary">
                    <i class="fas fa-list me-1"></i> Lihat Semua Tugas & Quiz
                </a>
            </div>
        @endif --}}
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5 class="mb-2">Semua Tugas Selesai!</h5>
                <p class="text-muted mb-3">Anda tidak memiliki tugas atau quiz yang perlu diselesaikan saat ini.</p>
                @if(isset($latestCourse) && $latestCourse)
                    <a href="{{ route('student.courses.show', ['course' => $latestCourse->id]) }}" class="btn btn-primary">
                        <i class="fas fa-book me-1"></i> Lihat Kelas Saya
                    </a>
                @else
                    <a href="{{ route('student.courses.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Gabung Kelas Sekarang
                    </a>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection
