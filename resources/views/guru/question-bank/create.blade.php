@extends('layouts.teacher')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-modern shadow-modern animate-fade-in">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas {{ isset($questionBank) ? 'fa-edit' : 'fa-plus' }} me-2"></i>
                        {{ isset($questionBank) ? 'Edit' : 'Tambah' }} Soal ke Bank
                    </h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-modern alert-modern-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ isset($questionBank) ? route('teacher.question-bank.update', $questionBank) : route('teacher.question-bank.store') }}" 
                        method="POST" id="questionForm">
                        @csrf
                        @if(isset($questionBank)) @method('PUT') @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kategori</label>
                                <select name="category_id" class="form-select">
                                    <option value="">Tanpa Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" 
                                            {{ (isset($questionBank) && $questionBank->category_id == $cat->id) || old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Tipe Soal <span class="text-danger">*</span></label>
                                <select name="question_type" required class="form-select" id="questionType" onchange="toggleOptions()">
                                    <option value="multiple_choice" {{ (isset($questionBank) && $questionBank->question_type == 'multiple_choice') || old('question_type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                    <option value="essay" {{ (isset($questionBank) && $questionBank->question_type == 'essay') || old('question_type') == 'essay' ? 'selected' : '' }}>Essay</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Bobot Nilai <span class="text-danger">*</span></label>
                                <input type="number" name="points" min="0" step="0.01" required class="form-control" 
                                    value="{{ old('points', $questionBank->points ?? 1) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Teks Soal <span class="text-danger">*</span></label>
                            <textarea name="question_text" required rows="3" class="form-control" 
                                placeholder="Tulis pertanyaan di sini...">{{ old('question_text', $questionBank->question_text ?? '') }}</textarea>
                        </div>

                        <div id="optionsContainer" style="display: {{ (isset($questionBank) && $questionBank->question_type == 'essay') || old('question_type') == 'essay' ? 'none' : 'block' }}">
                            <label class="form-label fw-bold">Pilihan Jawaban <span class="text-danger">*</span></label>
                            <small class="text-muted d-block mb-2">Pilih jawaban yang benar dengan klik radio button</small>
                            <div id="optionsList">
                                @if(isset($questionBank) && $questionBank->options->count() > 0)
                                    @foreach($questionBank->options as $index => $option)
                                        <div class="input-group mb-2">
                                            <div class="input-group-text">
                                                <input type="radio" name="correct_option" value="{{ $index }}" 
                                                    {{ $option->is_correct ? 'checked' : '' }} required>
                                            </div>
                                            <input type="text" name="options[{{ $index }}][option_text]" required 
                                                placeholder="Pilihan {{ chr(65 + $index) }}" 
                                                value="{{ $option->option_text }}" 
                                                class="form-control">
                                            @if($index > 1)
                                                <button type="button" onclick="this.parentElement.remove()" class="btn btn-outline-danger">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <div class="input-group-text">
                                            <input type="radio" name="correct_option" value="0" required checked>
                                        </div>
                                        <input type="text" name="options[0][option_text]" required placeholder="Pilihan A" class="form-control">
                                    </div>
                                    <div class="input-group mb-2">
                                        <div class="input-group-text">
                                            <input type="radio" name="correct_option" value="1" required>
                                        </div>
                                        <input type="text" name="options[1][option_text]" required placeholder="Pilihan B" class="form-control">
                                    </div>
                                @endif
                            </div>
                            <button type="button" onclick="addOption()" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-plus me-1"></i>Tambah Pilihan
                            </button>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-bold">Penjelasan (opsional)</label>
                            <textarea name="explanation" rows="2" class="form-control" 
                                placeholder="Penjelasan untuk jawaban yang benar...">{{ old('explanation', $questionBank->explanation ?? '') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('teacher.question-bank.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-modern-success">
                                <i class="fas fa-save me-1"></i>{{ isset($questionBank) ? 'Update' : 'Simpan' }} Soal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let optionCount = {{ isset($questionBank) && $questionBank->options->count() > 0 ? $questionBank->options->count() : 2 }};

function toggleOptions() {
    const type = document.getElementById('questionType').value;
    const container = document.getElementById('optionsContainer');
    const inputs = container.querySelectorAll('input');
    
    if (type === 'essay') {
        container.style.display = 'none';
        inputs.forEach(input => input.removeAttribute('required'));
    } else {
        container.style.display = 'block';
        const textInputs = container.querySelectorAll('input[type="text"]');
        const radioInputs = container.querySelectorAll('input[type="radio"]');
        textInputs.forEach(input => input.setAttribute('required', 'required'));
        if (radioInputs.length > 0) radioInputs[0].setAttribute('required', 'required');
    }
}

function addOption() {
    const list = document.getElementById('optionsList');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <div class="input-group-text">
            <input type="radio" name="correct_option" value="${optionCount}" required>
        </div>
        <input type="text" name="options[${optionCount}][option_text]" required 
            placeholder="Pilihan ${String.fromCharCode(65 + optionCount)}" class="form-control">
        <button type="button" onclick="this.parentElement.remove()" class="btn btn-outline-danger">
            <i class="fas fa-times"></i>
        </button>
    `;
    list.appendChild(div);
    optionCount++;
}
</script>
@endsection
