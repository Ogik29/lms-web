@extends('layouts.student')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg">
                <div class="card-header bg-gradient bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0"><i class="fas fa-clipboard-question me-2"></i>{{ $quiz->assignment->title }}</h4>
                            <small>{{ $quiz->assignment->course->name }}</small>
                        </div>
                        @if($quiz->duration_minutes)
                            <div class="col-auto">
                                <div id="timer" class="fs-2 fw-bold" style="font-family: 'Courier New', monospace;"></div>
                                <small class="d-block text-center">Waktu Tersisa</small>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted"><i class="fas fa-tasks me-1"></i>Progress</span>
                            <span id="progress" class="fw-bold">0 / {{ $questions->count() }} soal</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>

                    <form id="quizForm" action="{{ route('student.quiz.submit', $attempt) }}" method="POST">
                        @csrf

                        @foreach($questions as $index => $question)
                            <div class="card mb-4 border-start border-primary border-4">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><i class="fas fa-question-circle text-primary me-2"></i>Soal #{{ $index + 1 }}</h5>
                                        <span class="badge bg-primary">{{ $question->points }} poin</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="fw-bold mb-4">{{ $question->question_text }}</p>

                                    @if($question->isMultipleChoice())
                                        <div class="list-group">
                                            @foreach($question->options as $optIndex => $option)
                                                <label class="list-group-item list-group-item-action cursor-pointer">
                                                    <div class="d-flex align-items-center">
                                                        <input type="radio" 
                                                            name="question_{{ $question->id }}" 
                                                            value="{{ $option->id }}"
                                                            data-question-id="{{ $question->id }}"
                                                            class="quiz-answer form-check-input me-3"
                                                            {{ isset($existingAnswers[$question->id]) && $existingAnswers[$question->id]->selected_option_id == $option->id ? 'checked' : '' }}>
                                                        <span class="badge bg-secondary me-2">{{ chr(65 + $optIndex) }}</span>
                                                        <span>{{ $option->option_text }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="form-floating">
                                            <textarea 
                                                name="question_{{ $question->id }}" 
                                                data-question-id="{{ $question->id }}"
                                                class="quiz-answer form-control" 
                                                rows="6"
                                                style="height: 150px"
                                                placeholder="Tulis jawaban Anda di sini...">{{ $existingAnswers[$question->id]->essay_answer ?? '' }}</textarea>
                                            <label><i class="fas fa-pen me-2"></i>Tulis jawaban Anda di sini...</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <p class="text-muted mb-3"><i class="fas fa-info-circle me-1"></i>Pastikan semua jawaban sudah terisi sebelum submit</p>
                                <button type="submit" 
                                    class="btn btn-success btn-lg px-5"
                                    onclick="return confirm('Apakah Anda yakin ingin submit quiz ini? Jawaban tidak dapat diubah setelah submit.')">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Quiz
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const attemptId = {{ $attempt->id }};
const startTime = new Date('{{ $attempt->started_at->toIso8601String() }}');
const durationMinutes = {{ $quiz->duration_minutes ?? 'null' }};

// Timer
@if($quiz->duration_minutes)
let endTime = new Date(startTime.getTime() + (durationMinutes * 60 * 1000));

function updateTimer() {
    const now = new Date();
    const remaining = Math.max(0, endTime - now);
    
    // Safety check: only auto-submit if remaining is actually 0 and at least 5 seconds have passed since start
    const timeSinceStart = now - startTime;
    if (remaining === 0 && timeSinceStart > 5000) {
        document.getElementById('quizForm').submit();
        return;
    }
    
    const minutes = Math.floor(remaining / 60000);
    const seconds = Math.floor((remaining % 60000) / 1000);
    
    const timerEl = document.getElementById('timer');
    timerEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    // Change color when less than 5 minutes
    if (remaining < 300000) {
        timerEl.classList.add('text-danger');
    }
}

updateTimer();
setInterval(updateTimer, 1000);
@endif

// Auto-save answers and update progress
const answers = document.querySelectorAll('.quiz-answer');
const totalQuestions = {{ $questions->count() }};

function updateProgress() {
    let answeredCount = 0;
    const questionIds = new Set();
    
    answers.forEach(answer => {
        const questionId = answer.dataset.questionId;
        if (answer.type === 'radio' && answer.checked) {
            questionIds.add(questionId);
        } else if (answer.tagName === 'TEXTAREA' && answer.value.trim() !== '') {
            questionIds.add(questionId);
        }
    });
    
    answeredCount = questionIds.size;
    const percentage = (answeredCount / totalQuestions) * 100;
    
    document.getElementById('progress').textContent = `${answeredCount} / ${totalQuestions} soal`;
    document.getElementById('progressBar').style.width = percentage + '%';
    document.getElementById('progressBar').setAttribute('aria-valuenow', percentage);
}

answers.forEach(answer => {
    answer.addEventListener('change', function() {
        saveAnswer(this);
        updateProgress();
    });
    
    if (answer.tagName === 'TEXTAREA') {
        // Debounce for textarea
        let timeout;
        answer.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                saveAnswer(this);
                updateProgress();
            }, 1000);
        });
    }
});

function saveAnswer(element) {
    const questionId = element.dataset.questionId;
    let data = {
        question_id: questionId,
        _token: '{{ csrf_token() }}'
    };
    
    if (element.type === 'radio') {
        data.selected_option_id = element.value;
    } else {
        data.essay_answer = element.value;
    }
    
    fetch('{{ route("student.quiz.answer.submit", $attempt) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    });
}

// Initial progress update
updateProgress();

// Removed beforeunload warning to allow smooth navigation
// Users can navigate freely without dialog
</script>

<style>
.cursor-pointer {
    cursor: pointer;
}
.list-group-item:hover {
    background-color: #f8f9fa;
}
.list-group-item:has(input:checked) {
    background-color: #e7f3ff;
    border-color: #0d6efd;
}
</style>
@endsection
