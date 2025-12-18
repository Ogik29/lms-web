@extends('layouts.teacher')

@section('title', 'Edit Tugas - ' . $assignment->title)

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Edit Tugas: {{ $assignment->title }}</h4>
                <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>

            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

            <form action="{{ route('teacher.courses.assignments.update', [$course, $assignment]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3"><label class="form-label">Judul</label><input name="title" class="form-control" required value="{{ old('title', $assignment->title) }}"></div>
                <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="4">{{ old('description', $assignment->description) }}</textarea></div>
                <div class="mb-3 row">
                    <div class="col-md-6"><label class="form-label">Tanggal Jatuh Tempo (opsional)</label><input type="datetime-local" name="due_date" class="form-control" value="{{ $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '' }}"></div>
                </div>

                <div class="modal-footer p-0 mt-3"><a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-secondary">Batal</a> <button class="btn btn-primary">Simpan Tugas</button></div>
            </form>

        </div>
    </div>
</div>

@endsection