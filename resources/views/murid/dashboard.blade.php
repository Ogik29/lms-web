@extends('layouts.student')

@section('title', 'Dashboard Siswa')



@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Selamat Datang, {{ $user->name }}!</h1>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Kelas Terdaftar</h5>
                    <p class="fs-1 fw-bold text-primary">{{ $enrolledCoursesCount }}</p>
                    <p class="card-text text-muted">Kelas yang sedang Anda ikuti.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Tugas Mendatang</h5>
                     <!-- Data dinamis -->
                    <p class="fs-1 fw-bold text-warning">{{ $tasksCount }}</p>
                    <p class="card-text text-muted">Tugas yang perlu diselesaikan.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Total Nilai</h5>
                     <!-- Data dinamis -->
                    <p class="fs-1 fw-bold text-success">{{ $gradesCount }}</p>
                    <p class="card-text text-muted">Total nilai yang sudah masuk.</p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="h4 my-4">Lanjutkan Belajar</h2>
    <!-- Cek jika murid punya Kelas -->
    @if($latestCourse)
    <div class="card shadow-sm card-hover">
        <div class="card-body d-flex align-items-center">
            <i class="fas fa-laptop-code fa-3x text-primary me-4"></i>
            <div>
                <h5 class="card-title fw-bold mb-1">{{ $latestCourse->name }}</h5>
            </div>
            <a href="#" class="btn btn-primary ms-auto">Lanjutkan</a>
        </div>
    </div>
    @else
    <p>Anda belum terdaftar di Kelas manapun.</p>
    @endif

</div>
@endsection