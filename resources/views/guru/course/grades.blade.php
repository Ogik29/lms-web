@extends('layouts.teacher')

@section('title', 'Nilai - ' . $course->name)

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Nilai - {{ $course->name }}</h4>
                <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>

            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

            <table class="table table-hover">
                <thead>
                    <tr><th>Judul</th><th>Siswa</th><th>Skor</th><th>Deskripsi</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($course->grades as $g)
                    <tr>
                        <td>{{ $g->title }}</td>
                        <td>{{ $g->student->name ?? '-' }}</td>
                        <td>{{ $g->score }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($g->description, 80) }}</td>
                        <td>
                            <form action="{{ route('teacher.grades.destroy', $g) }}" method="POST" class="d-inline" data-confirm="Hapus nilai?" data-confirm-title="Hapus Nilai">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Belum ada nilai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
