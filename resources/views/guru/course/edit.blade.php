@extends('layouts.teacher')

@section('title', 'Edit Kelas - ' . $course->name)

@section('content')
<div class="container mt-4">
    <div class="card card-modern shadow-modern mx-auto animate-fade-in" style="max-width:800px">
        <div class="card-body">
            <h3 class="card-title gradient-heading"><i class="fas fa-edit me-2"></i>Edit Kelas</h3>
            <form action="{{ route('teacher.courses.update', $course) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Nama Kelas</label>
                    <input name="name" class="form-control form-control-modern @error('name') is-invalid @enderror" value="{{ old('name', $course->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control form-control-modern">{{ old('description', $course->description) }}</textarea>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-secondary">Batal</a>
                    <button class="btn btn-modern-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
