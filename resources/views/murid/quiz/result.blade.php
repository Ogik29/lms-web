@extends('layouts.student')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-poll-h me-2"></i>Hasil Quiz: {{ $attempt->quiz->assignment->title }}</h4>
                </div>

                <div class="card-body">
                    <!-- Score Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100 {{ $attempt->quiz->passing_score && $attempt->isPassed() ? 'border-success' : ($attempt->quiz->passing_score ? 'border-danger' : 'border-primary') }}">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2"><i class="fas fa-chart-line me-1"></i>Nilai Anda</h6>
                                    <h1 class="display-3 fw-bold text-primary mb-0">{{ number_format($attempt->total_score, 1) }}</h1>
                                    <p class="text-muted mb-0">dari {{ $attempt->max_score }} poin</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 {{ $attempt->quiz->passing_score && $attempt->isPassed() ? 'border-success' : ($attempt->quiz->passing_score ? 'border-danger' : 'border-info') }}">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2"><i class="fas fa-percentage me-1"></i>Persentase</h6>
                                    <h1 class="display-3 fw-bold {{ $attempt->quiz->passing_score && $attempt->isPassed() ? 'text-success' : ($attempt->quiz->passing_score ? 'text-danger' : 'text-info') }} mb-0">
                                        {{ number_format($attempt->getPercentageScore(), 1) }}%
                                    </h1>
                                    @if($attempt->quiz->passing_score)
                                        <p class="text-muted mb-0">Passing: {{ $attempt->quiz->passing_score }}%</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pass/Fail Status -->
                    @if($attempt->quiz->passing_score)
                        <div class="alert {{ $attempt->isPassed() ? 'alert-success' : 'alert-danger' }} d-flex align-items-center" role="alert">
                            @if($attempt->isPassed())
                                <i class="fas fa-check-circle fa-2x me-3"></i>
                                <div>
                                    <h5 class="alert-heading mb-1"><i class="fas fa-trophy me-1"></i>SELAMAT! ANDA LULUS!</h5>
                                    <p class="mb-0">Nilai Anda melebihi passing score {{ $attempt->quiz->passing_score }}%</p>
                                </div>
                            @else
                                <i class="fas fa-times-circle fa-2x me-3"></i>
                                <div>
                                    <h5 class="alert-heading mb-1">Belum Lulus</h5>
                                    <p class="mb-0">Nilai Anda belum mencapai passing score {{ $attempt->quiz->passing_score }}%. Silakan coba lagi.</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Grading Status -->
                    @if(!$attempt->is_graded)
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fas fa-hourglass-half fa-2x me-3"></i>
                            <div>
                                <h6 class="alert-heading mb-1"><i class="fas fa-info-circle me-1"></i>Status Penilaian</h6>
                                <p class="mb-0">Beberapa jawaban essay Anda masih dalam proses penilaian oleh guru. Nilai final mungkin berubah.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Detail Answers -->
                    @if($attempt->quiz->show_results_immediately || $attempt->is_graded)
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="fas fa-list-alt me-2"></i>Detail Jawaban</h5>
                        
                        @foreach($attempt->answers as $answer)
                            <div class="card mb-3 {{ $answer->is_correct === true ? 'border-success' : ($answer->is_correct === false ? 'border-danger' : 'border-secondary') }}">
                                <div class="card-header {{ $answer->is_correct === true ? 'bg-success bg-opacity-10' : ($answer->is_correct === false ? 'bg-danger bg-opacity-10' : 'bg-light') }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-question-circle me-2"></i>Soal #{{ $loop->iteration }}
                                        </h6>
                                        <span class="badge {{ $answer->is_correct === true ? 'bg-success' : ($answer->is_correct === false ? 'bg-danger' : 'bg-secondary') }}">
                                            <i class="fas {{ $answer->is_correct === true ? 'fa-check' : ($answer->is_correct === false ? 'fa-times' : 'fa-minus') }} me-1"></i>
                                            {{ $answer->points_earned ?? 0 }} / {{ $answer->question->points }} poin
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="fw-bold mb-3">{{ $answer->question->question_text }}</p>

                                    @if($answer->question->isMultipleChoice())
                                        <div class="list-group">
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
                                                                <i class="fas fa-times me-1"></i>Jawaban Anda
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if($answer->question->explanation)
                                            <div class="alert alert-info mt-3 mb-0">
                                                <h6 class="alert-heading"><i class="fas fa-lightbulb me-1"></i>Penjelasan:</h6>
                                                <p class="mb-0">{{ $answer->question->explanation }}</p>
                                            </div>
                                        @endif
                                    @else
                                        <div class="mb-3">
                                            <h6 class="text-muted mb-2"><i class="fas fa-pen me-1"></i>Jawaban Anda:</h6>
                                            <div class="p-3 bg-light rounded border">
                                                {{ $answer->essay_answer ?? 'Tidak dijawab' }}
                                            </div>
                                        </div>
                                        @if($answer->feedback)
                                            <div class="alert alert-primary mb-0">
                                                <h6 class="alert-heading"><i class="fas fa-comment-dots me-1"></i>Feedback Guru:</h6>
                                                <p class="mb-0">{{ $answer->feedback }}</p>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-eye-slash me-2"></i>Detail jawaban akan ditampilkan setelah guru menilai semua jawaban.
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('student.courses.show', $attempt->quiz->assignment->course) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali ke Kelas
                        </a>

                        @php
                            $attemptsCount = $attempt->quiz->attempts()->where('student_id', auth()->id())->count();
                        @endphp
                        @if($attemptsCount < $attempt->quiz->max_attempts)
                            <form action="{{ route('student.quiz.start', $attempt->quiz->assignment) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-redo me-1"></i>Coba Lagi 
                                    <span class="badge bg-light text-dark ms-1">{{ $attemptsCount }}/{{ $attempt->quiz->max_attempts }}</span>
                                </button>
                            </form>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-ban me-1"></i>Batas percobaan tercapai ({{ $attempt->quiz->max_attempts }}x)
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
