@extends('layouts.student')

@section('title', 'Kelas - ' . $course->name)

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>{{ $course->name }}</h4>
                <a href="{{ route('student.courses.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>

            <p class="text-muted">{{ $course->description }}</p>
            <p><strong>Guru:</strong> {{ $course->teacher->name }}</p>

            <h5 class="mt-4">Tugas</h5>
            <table class="table table-hover">
                <thead><tr><th>Judul</th><th>Due</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($course->assignments as $a)
                    @php $sub = $a->submissions->firstWhere('student_id', auth()->id()); @endphp
                    <tr>
                        <td>
                            {{ $a->title }}
                            @if($a->due_date && $a->due_date->isPast() && !$sub)
                                <span class="badge bg-danger ms-2">Terlambat</span>
                            @endif
                        </td>
                        <td>{{ $a->due_date ? $a->due_date->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            @if($sub)
                                <span class="badge bg-success">Terkirim</span>
                                @if(!is_null($sub->score))
                                    <span class="badge bg-info ms-1">Nilai: {{ $sub->score }}</span>
                                @endif
                                @if($sub->file_path)
                                    <a href="{{ asset('storage/' . $sub->file_path) }}" class="ms-2" target="_blank">(File)</a>
                                @endif
                            @else
                                <span class="text-muted">Belum dikumpulkan</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('student.assignments.show', $a) }}" class="btn btn-sm btn-outline-primary">Lihat & Submit</a>
                            @if($sub && $sub->file_path)
                                <a href="{{ asset('storage/' . $sub->file_path) }}" class="btn btn-sm btn-outline-secondary ms-1" target="_blank">Unduh</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Belum ada tugas.</td></tr>
                    @endforelse
                </tbody>
            </table>
                        {{-- <td>{{ $a->title }}</td>
                        <td>{{ $a->due_date ? $a->due_date->format('Y-m-d H:i') : '-' }}</td>
                        <td><a href="{{ route('student.assignments.show', $a) }}" class="btn btn-sm btn-outline-primary">Lihat & Submit</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center">Belum ada tugas.</td></tr>
                    @endforelse
                </tbody>
            </table> --}}
        </div>
    </div>
</div>

@endsection
