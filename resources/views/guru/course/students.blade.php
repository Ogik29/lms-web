@extends('layouts.teacher')

@section('title', 'Daftar Siswa - ' . $course->name)

@section('content')
<div class="container-fluid">
  <div class="row">
    <main class="col-12 py-4">
      <div class="mb-3">
        <nav class="nav nav-pills align-items-center justify-content-between flex-column flex-sm-row gap-2">
          <div class="d-flex align-items-center gap-2">
            <a class="nav-link" href="{{ route('teacher.courses.show', $course) }}"><i class="fas fa-book-open me-1"></i> Overview</a>
            <a class="nav-link active" href="{{ route('teacher.courses.students.index', $course) }}"><i class="fas fa-users me-1"></i> Siswa <span class="badge bg-light text-dark ms-1">{{ $course->students->count() }}</span></a>
            <a class="nav-link" href="{{ route('teacher.courses.show', $course) }}#schedules"><i class="fas fa-calendar-alt me-1"></i> Jadwal</a>
            <a class="nav-link" href="{{ route('teacher.courses.show', $course) }}#assignments"><i class="fas fa-tasks me-1"></i> Tugas</a>
            <a class="nav-link" href="{{ route('teacher.courses.grades.index', $course) }}"><i class="fas fa-clipboard-list me-1"></i> Nilai</a>
          </div>

          <div class="d-flex align-items-center">
            <div class="me-3 text-muted small">Kode: <span class="badge badge-vibrant-primary">{{ $course->code }}</span></div>
            <a href="{{ route('teacher.courses.edit', $course) }}" class="btn btn-outline-secondary me-2"><i class="fas fa-edit me-1"></i> Edit</a>
            <form action="{{ route('teacher.courses.destroy', $course) }}" method="POST" class="d-inline" data-confirm="Yakin ingin menghapus kelas ini?" data-confirm-title="Hapus Kelas">
              @csrf
              @method('DELETE')
              <button class="btn btn-danger"><i class="fas fa-trash me-1"></i> Hapus Kelas</button>
            </form>
          </div>
        </nav>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h2 class="h4 mb-1 gradient-heading">Daftar Siswa — {{ $course->name }}</h2>
          <small class="text-muted">Total terdaftar: <strong>{{ $course->students->count() }}</strong></small>
        </div>
        <div>
          <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
        </div>
      </div>

      <div class="card card-modern shadow-modern animate-fade-in">
        <div class="card-body">
          <div class="mb-3"><input type="text" id="studentListSearch" class="form-control form-control-modern" placeholder="Cari siswa (nama / email)..."></div>

          <div class="table-responsive">
            <table id="studentListTable" class="table table-modern align-middle">
              <thead class="table-light"><tr><th>Nama</th><th>Email</th><th class="text-end">Aksi</th></tr></thead>
              <tbody>
                @forelse($course->students as $student)
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="avatar bg-secondary text-white rounded-circle d-inline-flex justify-content-center align-items-center" style="width:36px;height:36px;font-weight:600">{{ strtoupper(substr($student->name,0,1)) }}</div>
                      <div>
                        <div class="fw-bold">{{ $student->name }}</div>
                        <div class="text-muted small">{{ $student->email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="text-truncate" style="max-width:220px">{{ $student->email }}</td>
                  <td class="text-end">
                    <form action="{{ route('teacher.courses.students.remove', [$course, $student]) }}" method="POST" class="d-inline" data-confirm="Hapus siswa dari kelas?" data-confirm-title="Hapus Siswa">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger"><i class="fas fa-user-times me-1"></i> Hapus</button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center">Belum ada siswa di kelas ini.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    tableSearch('studentListSearch','studentListTable');
  });
</script>
@endpush
