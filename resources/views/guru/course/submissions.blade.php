@extends('layouts.teacher')

@section('title', 'Submisi - ' . $assignment->title)

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Submisi untuk: {{ $assignment->title }}</h4>
                <a href="{{ route('teacher.courses.show', $assignment->course) }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>

            <table class="table table-hover">
                <thead><tr><th>Siswa</th><th>Konten</th><th>Waktu</th><th>Skor</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($assignment->submissions as $sub)
                    <tr>
                        <td>{{ $sub->student->name }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($sub->content, 80) }}</td>
                        <td>{{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : '-' }}</td>
                        <td>{{ $sub->score ?? '-' }}</td>
                        <td>
                            @if($sub->file_path)
                                <a href="{{ asset('storage/' . $sub->file_path) }}" class="btn btn-sm btn-outline-secondary me-2" target="_blank">Download File</a>
                            @endif

                            <form action="{{ route('teacher.submissions.grade', $sub) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="number" step="0.1" name="score" class="form-control d-inline-block" style="width:100px" placeholder="Skor" required>
                                <button class="btn btn-sm btn-primary ms-2">Simpan</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Belum ada submisi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
