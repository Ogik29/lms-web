@extends('layouts.student')

@section('title', 'Tugas - ' . $assignment->title)

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>{{ $assignment->title }}</h4>
                <a href="{{ route('student.courses.show', $assignment->course) }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>

            <p class="text-muted">{{ $assignment->description }}</p>
            <p><strong>Due:</strong> {{ $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : '-' }}</p>

            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

            <h5 class="mt-4">Pengiriman Anda</h5>
            @if($submission)
                <div class="card mb-3"><div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p>{{ $submission->content }}</p>
                                            <p class="text-muted">Dikirim: {{ $submission->submitted_at ? $submission->submitted_at->format('Y-m-d H:i') : '-' }}</p>
                            <p class="text-muted">Deadline: {{ ($submission->deadline ?? $assignment->due_date) ? ($submission->deadline ?? $assignment->due_date)->format('Y-m-d H:i') : '-' }}</p>
                            <p><strong>Skor:</strong> {{ $submission->score ?? 'Belum dinilai' }}</p>
                        </div>
                        <div class="text-end">
                            @if($submission->is_edited)
                                <p><span class="badge bg-info">Edited</span></p>
                            @endif
                            @php $effectiveDeadline = $submission->deadline ?? $assignment->due_date; @endphp
                            @if($effectiveDeadline && $submission->submitted_at && $submission->submitted_at->gt($effectiveDeadline))
                                <p><span class="badge bg-warning">Dikirim terlambat</span></p>
                            @endif
                        </div>
                    </div>
                    @if($submission->file_path)
                        <p class="mt-2">File: <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank">Download</a></p>
                        @php $ext = strtolower(pathinfo($submission->file_path, PATHINFO_EXTENSION)); @endphp
                        @if(in_array($ext, ['jpg','jpeg','png','gif']))
                            <div class="mt-2"><img src="{{ asset('storage/' . $submission->file_path) }}" alt="file" class="img-fluid" style="max-height:240px"></div>
                        @endif
                    @endif
                </div></div>
            @else

                @if($effectiveDeadline && now()->gt($effectiveDeadline))
                    <div class="alert alert-danger">Tenggat waktu telah lewat dan Anda belum mengumpulkan tugas ini.</div>
                @else
                    <div class="alert alert-info">Anda belum mengirim tugas ini.</div>
                @endif
            @endif

            <form action="{{ route('student.assignments.submit', $assignment) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Pesan (teks)</label>
                    <textarea name="content" class="form-control" rows="6">{{ old('content', $submission->content ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload File (opsional, pdf/doc/img/zip, max 20MB)</label>
                    <input type="file" name="file" class="form-control">
                    @error('file')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                    @if($submission && $submission->file_path)
                        <p class="mt-2">File sebelumnya: <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank">Download</a></p>
                    @endif
                </div>

                <button class="btn btn-primary">Kirim / Update Pengiriman</button>
            </form>
        </div>
    </div>
</div>

@endsection
