@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><i class="fas fa-file-alt me-2"></i>{{ $attempt->quiz->assignment->title }}</h4>
                            <small><i class="fas fa-user me-1"></i>Murid: <strong>{{ $attempt->student->name }}</strong></small>
                        </div>
                        <a href="{{ route('teacher.quiz.results', $attempt->quiz->assignment) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Summary Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-subtitle mb-2"><i class="fas fa-star me-1"></i>Nilai</h6>
                                    <h3 class="card-title mb-0">{{ $attempt->total_score }} / {{ $attempt->max_score }}</h3>
                                    <small>{{ number_format($attempt->getPercentageScore(), 1) }}%</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ $attempt->quiz->passing_score && $attempt->isPassed() ? 'bg-success' : ($attempt->quiz->passing_score ? 'bg-danger' : 'bg-info') }} text-white h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-subtitle mb-2"><i class="fas fa-award me-1"></i>Status</h6>
                                    <h3 class="card-title mb-0">
                                        @if($attempt->quiz->passing_score)
                                            {{ $attempt->isPassed() ? 'LULUS' : 'TIDAK LULUS' }}
                                        @else
                                            SELESAI
                                        @endif
                                    </h3>
                                    @if($attempt->quiz->passing_score)
                                        <small>Passing: {{ $attempt->quiz->passing_score }}%</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-secondary text-white h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-subtitle mb-2"><i class="fas fa-stopwatch me-1"></i>Waktu Pengerjaan</h6>
                                    <h3 class="card-title mb-0">{{ gmdate('i:s', $attempt->time_taken_seconds) }}</h3>
                                    <small>menit:detik</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3"><i class="fas fa-list-ol me-2"></i>Detail Jawaban</h5>

                    @foreach($attempt->answers as $answer)
                        <div class="card mb-3 {{ $answer->is_correct === true ? 'border-success' : ($answer->is_correct === false ? 'border-danger' : 'border-warning') }}">
                            <div class="card-header {{ $answer->is_correct === true ? 'bg-success bg-opacity-10' : ($answer->is_correct === false ? 'bg-danger bg-opacity-10' : 'bg-warning bg-opacity-10') }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-question-circle me-2"></i>Soal #{{ $loop->iteration }}
                                        <span class="text-muted small">({{ $answer->question->points }} poin)</span>
                                    </h6>
                                    @if($answer->question->isMultipleChoice())
                                        <span class="badge {{ $answer->is_correct ? 'bg-success' : 'bg-danger' }}">
                                            <i class="fas {{ $answer->is_correct ? 'fa-check' : 'fa-times' }} me-1"></i>
                                            {{ $answer->is_correct ? 'BENAR' : 'SALAH' }} - {{ $answer->points_earned }} / {{ $answer->question->points }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-pen me-1"></i>Essay - {{ $answer->points_earned ?? 0 }} / {{ $answer->question->points }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="fw-bold mb-3">{{ $answer->question->question_text }}</p>

                                @if($answer->question->isMultipleChoice())
                                    <div class="list-group mb-3">
                                        @foreach($answer->question->options as $option)
                                            <div class="list-group-item {{ $option->is_correct ? 'list-group-item-success' : '' }} {{ $answer->selected_option_id == $option->id && !$option->is_correct ? 'list-group-item-danger' : '' }}">
                                                <div class="d-flex align-items-center">
                                                    <input type="radio" disabled class="form-check-input me-2" {{ $answer->selected_option_id == $option->id ? 'checked' : '' }}>
                                                    <span class="flex-grow-1">{{ $option->option_text }}</span>
                                                    @if($option->is_correct)
                                                        <span class="badge bg-success ms-2">
                                                            <i class="fas fa-check me-1"></i>Jawaban Benar
                                                        </span>
                                                    @endif
                                                    @if($answer->selected_option_id == $option->id && !$option->is_correct)
                                                        <span class="badge bg-danger ms-2">
                                                            <i class="fas fa-times me-1"></i>Jawaban Murid
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($answer->question->explanation)
                                        <div class="alert alert-info mb-0">
                                            <h6 class="alert-heading"><i class="fas fa-lightbulb me-1"></i>Penjelasan:</h6>
                                            <p class="mb-0">{{ $answer->question->explanation }}</p>
                                        </div>
                                    @endif
                                @else
                                    <!-- Essay Answer -->
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-2"><i class="fas fa-pen me-1"></i>Jawaban Essay:</h6>
                                        <div class="p-3 bg-light rounded border">
                                            {{ $answer->essay_answer ?? 'Tidak dijawab' }}
                                        </div>
                                    </div>

                                    <!-- Grading Form -->
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <form action="{{ route('teacher.quiz.answer.grade', $answer) }}" method="POST">
                                                @csrf
                                                <div class="row align-items-end">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-bold"><i class="fas fa-award me-1"></i>Nilai:</label>
                                                        <div class="input-group">
                                                            <input type="number" 
                                                                name="points_earned" 
                                                                min="0" 
                                                                max="{{ $answer->question->points }}" 
                                                                step="0.01" 
                                                                value="{{ $answer->points_earned ?? '' }}" 
                                                                required
                                                                class="form-control"
                                                                placeholder="0">
                                                            <span class="input-group-text">/ {{ $answer->question->points }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-save me-1"></i>Simpan Nilai
                                                        </button>
                                                        @if($answer->points_earned !== null)
                                                            <span class="text-success ms-2">
                                                                <i class="fas fa-check-circle me-1"></i>Sudah dinilai
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Summary Footer -->
                    <div class="card bg-light mt-4">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Total Soal</small>
                                    <strong class="fs-5">{{ $attempt->answers->count() }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Soal Benar (MC)</small>
                                    <strong class="fs-5 text-success">{{ $attempt->answers->where('is_correct', true)->count() }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Nilai Total</small>
                                    <strong class="fs-5 text-primary">{{ $attempt->total_score }} / {{ $attempt->max_score }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
