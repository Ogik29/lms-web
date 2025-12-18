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
                <thead><tr><th>Siswa</th><th>Pesan</th><th>Waktu</th><th>Deadline</th><th>Skor</th><th>Aksi</th></tr></thead>
                <tbody>
                    @php $students = $assignment->course->students; @endphp
                    @forelse($students as $student)
                        @php $sub = $assignment->submissions->firstWhere('student_id', $student->id); @endphp
                        <tr>
                            <td>
                                {{ $student->name }}
                                @if($sub && $sub->is_edited)
                                    <span class="badge bg-info ms-2">Edited</span>
                                @endif
                            </td>
                            <td>
                                @if($sub)
                                    {{ \Illuminate\Support\Str::limit($sub->content, 80) }}
                                @else
                                    @if(($sub && $sub->deadline) || ($assignment->due_date && now()->gt($assignment->due_date)))
                                        <span class="text-danger">Belum mengumpulkan (deadline lewat)</span>
                                    @else
                                        <span class="text-muted">Belum mengumpulkan</span>
                                    @endif
                                @endif
                            </td>
                            <td>{{ $sub && $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : '-' }}</td>
                            <td>{{ $sub && $sub->deadline ? $sub->deadline->format('Y-m-d H:i') : ($assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : '-') }}</td>
                            <td>{{ $sub && $sub->score ? $sub->score : '-' }}</td>
                            <td>
                                @if($sub && $sub->file_path)
                                    <a href="{{ asset('storage/' . $sub->file_path) }}" class="btn btn-sm btn-outline-secondary me-2" target="_blank">Download File</a>
                                @endif

                                @if($sub)
                                    <form action="{{ route('teacher.submissions.grade', $sub) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="number" step="0.1" name="score" class="form-control d-inline-block" style="width:100px" placeholder="Skor" required>
                                        <button class="btn btn-sm btn-primary ms-2">Simpan</button>
                                    </form>

                                @else
                                    -
                                @endif

                                <!-- Deadline form -->
                                {{-- <form action="{{ route('teacher.assignments.submissions.deadline', $assignment) }}" method="POST" class="d-inline ms-2">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <input type="datetime-local" name="deadline" class="form-control d-inline-block mt-2" style="width:220px"
                                        value="{{ $sub && $sub->deadline ? $sub->deadline->format('Y-m-d\TH:i') : '' }}">
                                    <button class="btn btn-sm btn-outline-secondary mt-2">Set Deadline</button>
                                </form> --}}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Belum ada siswa di kelas ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
