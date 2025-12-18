@extends('layouts.teacher')

@section('title', 'Submisi Menunggu Penilaian')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Submisi Menunggu Penilaian</h4>
            </div>

            <table class="table table-hover">
                <thead><tr><th>Tugas</th><th>Siswa</th><th>Waktu</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($submissions as $sub)
                    <tr>
                        <td>{{ $sub->assignment->title }}</td>
                        <td>{{ $sub->student->name }}</td>
                        <td>{{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : '-' }}</td>
                        <td><a href="{{ route('teacher.assignments.submissions.index', $sub->assignment) }}" class="btn btn-sm btn-outline-primary">Buka & Nilai</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Tidak ada submisi menunggu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
