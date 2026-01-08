@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-clipboard-question me-2"></i>Buat Quiz Baru</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Terdapat Kesalahan</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('teacher.quiz.store', $course) }}" method="POST" id="quizForm">
                        @csrf

                        <!-- Basic Info -->
                        <div class="border-bottom pb-4 mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Dasar</h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Judul Quiz <span class="text-danger">*</span></label>
                                <input type="text" name="title" required class="form-control" value="{{ old('title') }}" placeholder="Contoh: Quiz Laravel Basics">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi</label>
                                <textarea name="description" rows="3" class="form-control" placeholder="Jelaskan tentang quiz ini...">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Deadline</label>
                                <input type="datetime-local" name="due_date" class="form-control" value="{{ old('due_date') }}">
                                <small class="text-muted">Kosongkan jika tidak ada deadline</small>
                            </div>
                        </div>

                        <!-- Quiz Settings -->
                        <div class="border-bottom pb-4 mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-cog me-2"></i>Pengaturan Quiz</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Durasi (menit)</label>
                                    <input type="number" name="duration_minutes" min="1" class="form-control" value="{{ old('duration_minutes') }}" placeholder="Contoh: 60">
                                    <small class="text-muted">Kosongkan untuk tanpa batas waktu</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Maksimal Percobaan <span class="text-danger">*</span></label>
                                    <input type="number" name="max_attempts" min="1" required class="form-control" value="{{ old('max_attempts', 1) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nilai Minimum Lulus (%)</label>
                                <input type="number" name="passing_score" min="0" max="100" step="0.01" class="form-control" value="{{ old('passing_score') }}" placeholder="Contoh: 70">
                                <small class="text-muted">Kosongkan jika tidak ada passing score</small>
                            </div>

                            <div class="border p-3 rounded bg-light">
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="show_results_immediately" value="1" class="form-check-input" id="showResults" {{ old('show_results_immediately') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showResults">Tampilkan hasil langsung setelah submit</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input type="checkbox" name="shuffle_questions" value="1" class="form-check-input" id="shuffleQ" {{ old('shuffle_questions') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="shuffleQ">Acak urutan soal</label>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" name="shuffle_options" value="1" class="form-check-input" id="shuffleO" {{ old('shuffle_options') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="shuffleO">Acak urutan pilihan jawaban</label>
                                </div>
                            </div>
                        </div>

                        <!-- Questions -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-primary mb-0"><i class="fas fa-list-ol me-2"></i>Soal-soal</h5>
                                <button type="button" onclick="addQuestion()" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus me-1"></i> Tambah Soal
                                </button>
                            </div>
                            
                            <div id="questionsContainer"></div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Buat Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let questionCount = 0;

function addQuestion() {
    const container = document.getElementById('questionsContainer');
    const questionDiv = document.createElement('div');
    questionDiv.className = 'card mb-3 border-primary';
    questionDiv.id = `question-${questionCount}`;
    
    questionDiv.innerHTML = `
        <div class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary">Soal #${questionCount + 1}</h6>
            <button type="button" onclick="removeQuestion(${questionCount})" class="btn btn-sm btn-danger">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-bold">Teks Soal <span class="text-danger">*</span></label>
                <textarea name="questions[${questionCount}][question_text]" required rows="2" class="form-control" placeholder="Tulis pertanyaan di sini..."></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tipe Soal <span class="text-danger">*</span></label>
                    <select name="questions[${questionCount}][question_type]" required onchange="toggleOptions(${questionCount}, this.value)" class="form-select">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="essay">Essay</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Bobot Nilai <span class="text-danger">*</span></label>
                    <input type="number" name="questions[${questionCount}][points]" min="0" step="0.01" required value="1" class="form-control">
                </div>
            </div>

            <div id="options-container-${questionCount}">
                <label class="form-label fw-bold">Pilihan Jawaban <span class="text-danger">*</span></label>
                <small class="text-muted d-block mb-2">Pilih jawaban yang benar dengan klik radio button</small>
                <div id="options-list-${questionCount}" class="mb-2"></div>
                <button type="button" onclick="addOption(${questionCount})" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-plus me-1"></i> Tambah Pilihan
                </button>
            </div>

            <div class="mt-3">
                <label class="form-label fw-bold">Penjelasan (opsional)</label>
                <textarea name="questions[${questionCount}][explanation]" rows="2" class="form-control" placeholder="Penjelasan untuk jawaban yang benar..."></textarea>
            </div>
        </div>
    `;
    
    container.appendChild(questionDiv);
    
    // Add default options for multiple choice
    addOptionWithData(questionCount, 0, '', false);
    addOptionWithData(questionCount, 1, '', false);
    
    questionCount++;
}

function addOptionWithData(questionIndex, optionIndex, text, isCorrect) {
    const optionsList = document.getElementById(`options-list-${questionIndex}`);
    const optionDiv = document.createElement('div');
    optionDiv.className = 'input-group mb-2';
    optionDiv.innerHTML = `
        <div class="input-group-text">
            <input type="radio" name="questions[${questionIndex}][correct_option]" value="${optionIndex}" ${isCorrect ? 'checked' : ''} required>
        </div>
        <input type="text" name="questions[${questionIndex}][options][${optionIndex}][option_text]" required placeholder="Pilihan ${String.fromCharCode(65 + optionIndex)}" value="${text}" class="form-control">
        ${optionIndex > 1 ? `<button type="button" onclick="this.parentElement.remove()" class="btn btn-outline-danger"><i class="fas fa-times"></i></button>` : ''}
    `;
    optionsList.appendChild(optionDiv);
}

function removeQuestion(index) {
    const questionDiv = document.getElementById(`question-${index}`);
    if (questionDiv && confirm('Hapus soal ini?')) {
        questionDiv.remove();
    }
}

function toggleOptions(questionIndex, type) {
    const optionsContainer = document.getElementById(`options-container-${questionIndex}`);
    if (type === 'essay') {
        optionsContainer.style.display = 'none';
        const inputs = optionsContainer.querySelectorAll('input[type="text"], input[type="radio"]');
        inputs.forEach(input => input.removeAttribute('required'));
    } else {
        optionsContainer.style.display = 'block';
        const textInputs = optionsContainer.querySelectorAll('input[type="text"]');
        const radioInputs = optionsContainer.querySelectorAll('input[type="radio"]');
        textInputs.forEach(input => input.setAttribute('required', 'required'));
        if (radioInputs.length > 0) radioInputs[0].setAttribute('required', 'required');
    }
}

function addOption(questionIndex) {
    const optionsList = document.getElementById(`options-list-${questionIndex}`);
    const optionCount = optionsList.children.length;
    addOptionWithData(questionIndex, optionCount, '', false);
}

// Add first question by default
document.addEventListener('DOMContentLoaded', function() {
    addQuestion();
});
</script>
@endsection
