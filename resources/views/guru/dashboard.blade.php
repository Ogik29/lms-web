@extends('layouts.teacher')

@section('title', 'Dashboard Guru')



@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Selamat Datang, Guru {{ $user->name }}!</h1>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
        <i class="fas fa-plus me-2"></i> Buat Kelas Baru
      </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Statistik Cards -->
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Total Kelas Aktif</h5>
                            <p class="fs-1 fw-bold text-primary">{{ $coursesCount }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Jumlah Siswa</h5>
                            <p class="fs-1 fw-bold text-info">{{ $totalStudents }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <a href="{{ route('teacher.submissions.pending') }}" class="text-decoration-none text-dark">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Tugas Perlu Dinilai</h5>
                            <p class="fs-1 fw-bold text-danger">{{ $tasksToGrade }}</p>
                            <p class="text-muted small">Klik untuk lihat submisi yang menunggu</p>
                        </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Siswa per Kelas</h5>
                            <div style="height:240px">
                              <canvas id="studentsChart"></canvas>
                            </div>
                            <div class="mt-3">
                              <h6 class="mb-2">Submisi Terbaru</h6>
                              <ul class="list-group list-group-flush">
                                @if(!empty($recentSubmissions) && $recentSubmissions->count()) 
                                  @foreach($recentSubmissions as $sub)
                                  <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div><strong>{{ $sub->student->name ?? '-' }}</strong><br><small class="text-muted">{{ $sub->assignment->title }}</small></div>
                                    <div class="text-end"><small>{{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : '-' }}</small></div>
                                  </li>
                                  @endforeach
                                @else
                                  <li class="list-group-item text-center text-muted">Belum ada submisi.</li>
                                @endif
                              </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Kelas -->
            <h2 class="h4 my-4">Kelas Anda</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Kelas</th>
                                <th>Jumlah Siswa</th>
                                <th>Kode Kelas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($courses as $course)
                            <tr>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->students_count }} Siswa</td>
                                <td><span class="badge bg-secondary">{{ $course->code }}</span></td>
                                <td><a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-sm btn-outline-primary">Kelola</a></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Anda belum membuat kelas apapun.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
{{-- Jika terjadi error validasi, modal akan otomatis terbuka kembali --}}
@if($errors->any())
<script>
    var myModal = new bootstrap.Modal(document.getElementById('createCourseModal'), {
        keyboard: false
    });
    myModal.show();
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('studentsChart')) {
            var ctx = document.getElementById('studentsChart').getContext('2d');
            var studentsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels ?? []) !!},
                    datasets: [{
                        label: 'Jumlah Siswa',
                        backgroundColor: '#4e73df',
                        data: {!! json_encode($chartData ?? []) !!}
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    });
</script>
@endpush