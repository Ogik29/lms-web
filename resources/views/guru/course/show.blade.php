@extends('layouts.teacher')

@section('title', 'Kelola Kelas - ' . $course->name)

@section('content')
<div class="container-fluid">
  <div class="row">
    <main class="col-12 py-4">
      <div class="mb-3">
        <nav class="nav nav-pills align-items-center justify-content-between flex-column flex-sm-row gap-2">
          <div class="d-flex align-items-center gap-2">
            <a class="nav-link active" href="{{ route('teacher.courses.show', $course) }}"><i class="fas fa-book-open me-1"></i> Overview</a>
            {{-- <a class="nav-link" href="{{ route('teacher.courses.students.index', $course) }}"><i class="fas fa-users me-1"></i> Siswa <span class="badge bg-light text-dark ms-1">{{ $course->students->count() }}</span></a> --}}
            {{-- <a class="nav-link" href="{{ route('teacher.courses.show', $course) }}#schedules"><i class="fas fa-calendar-alt me-1"></i> Jadwal</a> --}}
            {{-- <a class="nav-link" href="{{ route('teacher.courses.show', $course) }}#assignments"><i class="fas fa-tasks me-1"></i> Tugas</a> --}}
            {{-- <a class="nav-link" href="{{ route('teacher.courses.grades.index', $course) }}"><i class="fas fa-clipboard-list me-1"></i> Nilai</a> --}}
          </div>

          <div class="d-flex align-items-center">
            <div class="me-3 text-muted small">Kode: <span class="badge bg-secondary">{{ $course->code }}</span></div>
            <a href="{{ route('teacher.courses.edit', $course) }}" class="btn btn-outline-secondary me-2"><i class="fas fa-edit me-1"></i> Edit</a>
            <form action="{{ route('teacher.courses.destroy', $course) }}" method="POST" class="d-inline" data-confirm="Yakin ingin menghapus kelas ini?" data-confirm-title="Hapus Kelas">
              @csrf
              @method('DELETE')
              <button class="btn btn-danger"><i class="fas fa-trash me-1"></i> Hapus Kelas</button>
            </form>
          </div>
        </nav>
      </div>

      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <h2 class="h4 mb-1">{{ $course->name }}</h2>
          <small class="text-muted">Kode: <span class="badge bg-secondary">{{ $course->code }}</span></small>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title mb-1">Deskripsi</h5>
          <p class="text-muted mb-0">{{ $course->description ?? 'Tidak ada deskripsi.' }}</p>
        </div>
      </div>

      <ul class="nav nav-tabs mb-3" id="courseTabs" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">Overview</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button">Siswa <span class="badge bg-light text-dark ms-1">{{ $course->students->count() }}</span></button></li>
        {{-- <li class="nav-item" role="presentation"><button class="nav-link" id="schedules-tab" data-bs-toggle="tab" data-bs-target="#schedules" type="button">Jadwal</button></li> --}}
        <li class="nav-item" role="presentation"><button class="nav-link" id="assignments-tab" data-bs-toggle="tab" data-bs-target="#assignments" type="button">Tugas</button></li>
        {{-- <li class="nav-item" role="presentation"><button class="nav-link" id="grades-tab" data-bs-toggle="tab" data-bs-target="#grades" type="button">Nilai</button></li> --}}
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade show active" id="overview">
          <div class="row">
            <div class="col-md-8">
              <div class="card mb-3">
                <div class="card-body">
                  <h5 class="card-title">Ringkasan</h5>
                  <p class="mb-1"><strong>Guru:</strong> {{ $course->teacher->name ?? '-' }}</p>
                  <p class="mb-0 text-muted">Siswa terdaftar: <strong>{{ $course->students->count() }}</strong></p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card mb-3">
                <div class="card-body text-center">
                  <h6 class="mb-0">Statistik</h6>
                  <p class="mb-0 text-muted">Tugas: <strong>{{ $course->assignments->count() }}</strong></p> <br>
                  {{-- <p class="mb-0 text-muted">Jadwal: <strong>{{ $course->schedules->count() }}</strong></p> --}}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="students">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Daftar Siswa</h5>
                <small class="text-muted">Total: {{ $course->students->count() }}</small>
              </div>

              <div class="mb-3"><input type="text" id="studentSearch" class="form-control" placeholder="Cari siswa (nama / email)..."></div>

              <div class="table-responsive">
                <table id="studentsTable" class="table table-hover align-middle">
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
        </div>

        <div class="tab-pane fade" id="schedules">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Jadwal</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal"><i class="fas fa-plus me-1"></i> Tambah Jadwal</button>
              </div>

              @if($course->schedules->isEmpty())
                <div class="text-muted">Belum ada jadwal.</div>
              @else
                <ul class="list-group">
                  @foreach($course->schedules as $s)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <div class="fw-bold">{{ $s->subject->name ?? '-' }}</div>
                        <small class="text-muted">{{ $s->day_of_week }} • {{ $s->start_time }} - {{ $s->end_time }}</small>
                      </div>
                      <form action="{{ route('teacher.courses.schedules.destroy', [$course, $s]) }}" method="POST" data-confirm="Hapus jadwal ini?" data-confirm-title="Hapus Jadwal">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash me-1"></i> Hapus</button>
                      </form>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="assignments">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Tugas & Quiz</h5>
                <div>
                  <a href="{{ route('teacher.quiz.create', $course) }}" class="btn btn-sm btn-success me-2">
                    <i class="fas fa-clipboard-question me-1"></i> Buat Quiz
                  </a>
                  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
                    <i class="fas fa-plus me-1"></i> Tambah Tugas
                  </button>
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light"><tr><th>Judul</th><th>Tipe</th><th>Due</th><th>Submisi/Attempts</th><th class="text-end">Aksi</th></tr></thead>
                  <tbody>
                    @forelse($course->assignments as $a)
                    <tr>
                      <td>
                        <div class="fw-bold">{{ $a->title }}</div>
                        <div class="text-muted small">{{ \Illuminate\Support\Str::limit($a->description, 100) }}</div>
                      </td>
                      <td>
                        @if($a->isQuiz())
                          <span class="badge bg-info">
                            <i class="fas fa-clipboard-question"></i> Quiz
                          </span>
                        @else
                          <span class="badge bg-secondary">
                            <i class="fas fa-file-alt"></i> Assignment
                          </span>
                        @endif
                      </td>
                      <td>{{ $a->due_date ? $a->due_date->format('Y-m-d H:i') : '-' }}</td>
                      <td>
                        @if($a->isQuiz())
                          {{ $a->quiz->attempts()->count() }} attempts
                        @else
                          {{ $a->submissions->count() }} submisi
                        @endif
                      </td>
                      <td class="text-end">
                        @if($a->isQuiz())
                          <a href="{{ route('teacher.quiz.results', $a) }}" class="btn btn-sm btn-outline-info me-2">
                            <i class="fas fa-chart-bar me-1"></i> Hasil
                          </a>
                          <a href="{{ route('teacher.quiz.edit', [$course, $a]) }}" class="btn btn-sm btn-outline-success me-2">
                            <i class="fas fa-pen me-1"></i> Edit
                          </a>
                        @else
                          <a href="{{ route('teacher.courses.assignments.edit', [$course, $a]) }}" class="btn btn-sm btn-outline-success me-2">
                            <i class="fas fa-pen me-1"></i> Edit
                          </a>
                          <a href="{{ route('teacher.assignments.submissions.index', $a) }}" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-eye me-1"></i> Submisi
                          </a>
                        @endif
                        <form action="{{ route('teacher.courses.assignments.destroy', [$course, $a]) }}" method="POST" class="d-inline" data-confirm="Hapus {{ $a->isQuiz() ? 'quiz' : 'tugas' }} ini?" data-confirm-title="Hapus">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash me-1"></i> Hapus</button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Belum ada tugas atau quiz.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="grades">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Daftar Nilai</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addGradeModal"><i class="fas fa-plus me-1"></i> Tambah Nilai</button>
              </div>

              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light"><tr><th>Judul</th><th>Siswa</th><th>Skor</th><th>Deskripsi</th><th class="text-end">Aksi</th></tr></thead>
                  <tbody>
                    @forelse($course->grades as $g)
                    <tr>
                      <td>{{ $g->title }}</td>
                      <td>{{ $g->student->name ?? '-' }}</td>
                      <td>
                        @php
                          $cls = 'bg-danger';
                          if($g->score >= 85) $cls = 'bg-success';
                          elseif($g->score >= 70) $cls = 'bg-warning text-dark';
                        @endphp
                        <span class="badge {{ $cls }}">{{ $g->score }}</span>
                      </td>
                      <td>{{ \Illuminate\Support\Str::limit($g->description, 80) }}</td>
                      <td class="text-end">
                        <form action="{{ route('teacher.grades.destroy', $g) }}" method="POST" class="d-inline" data-confirm="Hapus nilai?" data-confirm-title="Hapus Nilai">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash me-1"></i> Hapus</button>
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
        </div>
      </div>

      <!-- Modals -->

    </main>
  </div>
</div>

{{-- Modal Tambah Jadwal --}}
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Tambah Jadwal</h5><button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <form action="{{ route('teacher.courses.schedules.store', $course) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Mata Pelajaran (opsional)</label>
            <select name="subject_id" class="form-select">
                <option value="">-- Pilih --</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Hari</label><input name="day_of_week" class="form-control" required></div>
          <div class="row">
            <div class="col"><label class="form-label">Mulai</label><input type="time" name="start_time" class="form-control" required></div>
            <div class="col"><label class="form-label">Selesai</label><input type="time" name="end_time" class="form-control" required></div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Tambah Tugas --}}
<div class="modal fade" id="addAssignmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Tambah Tugas</h5><button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <form action="{{ route('teacher.courses.assignments.store', $course) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Judul</label><input name="title" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="4"></textarea></div>
          <div class="mb-3 row">
            <div class="col-md-6"><label class="form-label">Tanggal Jatuh Tempo (opsional)</label><input type="datetime-local" name="due_date" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Tipe</label><select name="type" class="form-select"><option value="">Biasa</option></select></div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan Tugas</button></div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Tambah Nilai --}}
<div class="modal fade" id="addGradeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Tambah Nilai</h5><button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <form action="{{ route('teacher.courses.grades.store', $course) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Siswa</label>
            <select name="student_id" class="form-select" required>
                <option value="">-- Pilih Siswa --</option>
                @foreach($course->students as $s)
                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>
                @endforeach
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Judul</label><input name="title" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Skor (0-100)</label><input type="number" step="0.1" name="score" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Deskripsi (opsional)</label><textarea name="description" class="form-control" rows="3"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan Nilai</button></div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tableSearch('studentSearch','studentsTable');
        // init bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
    });
</script>
@endpush

