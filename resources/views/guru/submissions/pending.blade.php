@extends('layouts.teacher')

@section('title', 'Submisi Menunggu Penilaian')

@section('content')
<div class="container mt-4">
    <div class="card card-modern shadow-modern animate-fade-in">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="gradient-heading"><i class="fas fa-clock me-2"></i>Submisi Menunggu Penilaian</h4>
            </div>

            <table class="table table-modern">
                <thead><tr><th>Tugas</th><th>Siswa</th><th>Waktu</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($submissions as $sub)
                    <tr>
                        <td>{{ $sub->assignment->title }}</td>
                        <td>
                            {{ $sub->student->name }}
                            @if($sub->submitted_at)
                                @php 
                                    $deadline = $sub->deadline ?? $sub->assignment->due_date;
                                    $isLate = $deadline && $sub->submitted_at->gt($deadline);
                                @endphp
                                @if($isLate)
                                    <span class="badge badge-vibrant-warning ms-2">
                                        <i class="fas fa-clock"></i> Terlambat
                                    </span>
                                @endif
                            @endif
                        </td>
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
