@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Hasil Quiz: {{ $assignment->title }}</h4>
                    <a href="{{ route('teacher.courses.show', $assignment->course) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    @if($assignment->quiz->attempts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="fas fa-user me-1"></i>Nama Murid</th>
                                        <th class="text-center"><i class="fas fa-redo me-1"></i>Percobaan</th>
                                        <th class="text-center"><i class="fas fa-trophy me-1"></i>Nilai</th>
                                        <th class="text-center"><i class="fas fa-award me-1"></i>Status Kelulusan</th>
                                        <th class="text-center"><i class="fas fa-tasks me-1"></i>Status Penilaian</th>
                                        <th class="text-center"><i class="fas fa-clock me-1"></i>Waktu</th>
                                        <th class="text-center"><i class="fas fa-eye me-1"></i>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignment->quiz->attempts as $attempt)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar bg-primary text-white rounded-circle me-2" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                        {{ strtoupper(substr($attempt->student->name, 0, 1)) }}
                                                    </div>
                                                    <strong>{{ $attempt->student->name }}</strong>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">
                                                    {{ $assignment->quiz->attempts->where('student_id', $attempt->student_id)->search($attempt) + 1 }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($attempt->isCompleted())
                                                    <div>
                                                        <strong class="fs-5">{{ number_format($attempt->total_score, 1) }}</strong>
                                                        <span class="text-muted">/ {{ $attempt->max_score }}</span>
                                                    </div>
                                                    <small class="text-primary fw-bold">
                                                        {{ number_format($attempt->getPercentageScore(), 1) }}%
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($attempt->isCompleted() && $attempt->quiz->passing_score)
                                                    @if($attempt->isPassed())
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>LULUS
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">≥ {{ $attempt->quiz->passing_score }}%</small>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times-circle me-1"></i>TIDAK LULUS
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">< {{ $attempt->quiz->passing_score }}%</small>
                                                    @endif
                                                @elseif($attempt->isCompleted())
                                                    <span class="text-muted">Tidak ada passing score</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(!$attempt->isCompleted())
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-hourglass-half me-1"></i>Sedang dikerjakan
                                                    </span>
                                                @elseif($attempt->is_graded)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Sudah dinilai
                                                    </span>
                                                @else
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-pen me-1"></i>Perlu dinilai
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($attempt->isCompleted())
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fas fa-stopwatch me-1"></i>{{ gmdate('i:s', $attempt->time_taken_seconds) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($attempt->isCompleted())
                                                    <a href="{{ route('teacher.quiz.attempt.view', $attempt) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>Detail
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Stats -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">{{ $assignment->quiz->attempts->count() }}</h3>
                                        <small>Total Percobaan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">{{ $assignment->quiz->attempts->where('is_graded', true)->count() }}</h3>
                                        <small>Sudah Ternilai</small>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                <div class="card bg-warning text-dark">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0">{{ $assignment->quiz->attempts->whereNotNull('submitted_at')->where('is_graded', false)->count() }}</h3>
                                        <small>Perlu Dinilai</small>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada murid yang mengerjakan quiz ini</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
