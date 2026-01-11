@extends('layouts.teacher')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card card-modern shadow-modern animate-fade-in">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-bank me-2"></i>Bank Soal</h4>
                    <div>
                        <a href="{{ route('teacher.question-bank.categories') }}" class="btn btn-light btn-sm me-2">
                            <i class="fas fa-folder me-1"></i>Kelola Kategori
                        </a>
                        <a href="{{ route('teacher.question-bank.create') }}" class="btn btn-modern-info btn-sm">
                            <i class="fas fa-plus me-1"></i>Tambah Soal
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Cari soal..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="category_id" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="type" class="form-select">
                                    <option value="">Semua Tipe</option>
                                    <option value="multiple_choice" {{ request('type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                    <option value="essay" {{ request('type') == 'essay' ? 'selected' : '' }}>Essay</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($questions->count() > 0)
                        <div class="row">
                            @foreach($questions as $question)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 {{ $question->question_type == 'multiple_choice' ? 'border-primary' : 'border-info' }}">
                                        <div class="card-header {{ $question->question_type == 'multiple_choice' ? 'bg-primary bg-opacity-10' : 'bg-info bg-opacity-10' }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <span class="badge badge-vibrant-{{ $question->question_type == 'multiple_choice' ? 'primary' : 'info' }} me-2">
                                                        {{ $question->question_type == 'multiple_choice' ? 'MC' : 'Essay' }}
                                                    </span>
                                                    @if($question->category)
                                                        <span class="badge badge-vibrant-primary">{{ $question->category->name }}</span>
                                                    @endif
                                                    <span class="badge badge-vibrant-warning ms-1">{{ $question->points }} poin</span>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('teacher.question-bank.edit', $question) }}" class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('teacher.question-bank.destroy', $question) }}" method="POST" class="d-inline"
                                                        onsubmit="return confirm('Yakin hapus soal ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="fw-bold mb-2">{{ Str::limit($question->question_text, 150) }}</p>
                                            
                                            @if($question->isMultipleChoice() && $question->options->count() > 0)
                                                <small class="text-muted d-block mb-2"><i class="fas fa-list-ul me-1"></i>{{ $question->options->count() }} pilihan</small>
                                                <div class="small">
                                                    @foreach($question->options->take(2) as $option)
                                                        <div class="d-flex align-items-center mb-1">
                                                            <input type="radio" disabled class="form-check-input me-2">
                                                            <span class="{{ $option->is_correct ? 'text-success fw-bold' : '' }}">
                                                                {{ Str::limit($option->option_text, 50) }}
                                                                @if($option->is_correct) <i class="fas fa-check text-success"></i> @endif
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                    @if($question->options->count() > 2)
                                                        <small class="text-muted">... dan {{ $question->options->count() - 2 }} pilihan lainnya</small>
                                                    @endif
                                                </div>
                                            @endif

                                            @if($question->explanation)
                                                <div class="mt-2 p-2 bg-light rounded small">
                                                    <strong>Penjelasan:</strong> {{ Str::limit($question->explanation, 80) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $question->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            {{ $questions->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada soal di bank soal</h5>
                            <a href="{{ route('teacher.question-bank.create') }}" class="btn btn-modern-success mt-2">
                                <i class="fas fa-plus me-1"></i>Tambah Soal Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
