@extends('layouts.student')

@section('title', 'Kelas - ' . $course->name)

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>{{ $course->name }}</h4>
                <a href="{{ route('student.courses.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>

            <p class="text-muted">{{ $course->description }}</p>
            <p><strong>Guru:</strong> {{ $course->teacher->name }}</p>

            <h5 class="mt-4">Tugas & Quiz</h5>
            <table class="table table-hover">
                <thead><tr><th>Judul</th><th>Tipe</th><th>Due</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($course->assignments as $a)
                    @php 
                        $sub = $a->submissions->firstWhere('student_id', auth()->id()); 
                        $isQuiz = $a->isQuiz();
                        $userAttempts = $isQuiz ? $a->quiz->attempts()->where('student_id', auth()->id())->get() : collect();
                        $latestAttempt = $userAttempts->sortByDesc('created_at')->first();
                        $ongoingAttempt = $userAttempts->whereNull('submitted_at')->first();
                    @endphp
                    <tr>
                        <td>
                            {{ $a->title }}
                            @if($a->due_date && $a->due_date->isPast() && !$isQuiz && !$sub)
                                <span class="badge bg-danger ms-2">Terlambat</span>
                            @endif
                        </td>
                        <td>
                            @if($isQuiz)
                                <span class="badge bg-info">
                                    <i class="fas fa-clipboard-question"></i> Quiz
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-file-alt"></i> Assignment
                                </span>
                            @endif
                        </td>
                        <td>{{ $a->due_date ? $a->due_date->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            @if($isQuiz)
                                @if($latestAttempt)
                                    @if($latestAttempt->isCompleted())
                                        <span class="badge bg-success">Selesai</span>
                                        @if($latestAttempt->is_graded)
                                            <span class="badge bg-info ms-1">
                                                Nilai: {{ number_format($latestAttempt->getPercentageScore(), 1) }}%
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark ms-1">Pending grading</span>
                                        @endif
                                    @else
                                        <span class="badge bg-warning text-dark">Sedang dikerjakan</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">
                                        Percobaan: {{ $userAttempts->count() }}/{{ $a->quiz->max_attempts }}
                                    </small>
                                @else
                                    <span class="text-muted">Belum dikerjakan</span>
                                @endif
                            @else
                                @if($sub)
                                    <span class="badge bg-success">Terkirim</span>
                                    @if(!is_null($sub->score))
                                        <span class="badge bg-info ms-1">Nilai: {{ $sub->score }}</span>
                                    @endif
                                    @if($sub->file_path)
                                        <a href="{{ asset('storage/' . $sub->file_path) }}" class="ms-2" target="_blank">(File)</a>
                                    @endif
                                @else
                                    <span class="text-muted">Belum dikumpulkan</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($isQuiz)
                                @if($ongoingAttempt)
                                    <a href="{{ route('student.quiz.take', $ongoingAttempt) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-play"></i> Lanjutkan Quiz
                                    </a>
                                @elseif($latestAttempt && $latestAttempt->isCompleted())
                                    <a href="{{ route('student.quiz.result', $latestAttempt) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Lihat Hasil
                                    </a>
                                    @if($userAttempts->count() < $a->quiz->max_attempts)
                                        <form action="{{ route('student.quiz.start', $a) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success ms-1">
                                                <i class="fas fa-redo"></i> Coba Lagi
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <form action="{{ route('student.quiz.start', $a) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-play"></i> Mulai Quiz
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('student.assignments.show', $a) }}" class="btn btn-sm btn-outline-primary">Lihat & Submit</a>
                                @if($sub && $sub->file_path)
                                    <a href="{{ asset('storage/' . $sub->file_path) }}" class="btn btn-sm btn-outline-secondary ms-1" target="_blank">Unduh</a>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Belum ada tugas atau quiz.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
