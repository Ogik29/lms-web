@extends('layouts.app')

@section('title', 'Selamat Datang di Platform E-Learning')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(rgba(39, 91, 224, 0.8), rgba(39, 91, 224, 0.8)), url("{{ asset('images/hero-bg.jpg') }}");
        background-size: cover;
        background-position: center;
        height: 70vh;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .features-section {
        padding: 60px 0;
    }
    .feature-icon {
        font-size: 3rem;
        color: var(--primary-blue);
    }
</style>
@endpush

@section('content')
{{-- Navbar Sederhana --}}
{{-- <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('home') }}">
      <i class="fa-solid fa-book-open text-primary"></i> E-Learning
    </a>
    <div class="ms-auto">
      <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Masuk</a>
      <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
    </div>
  </div> --}}
</nav>

{{-- Hero Section --}}
<div class="hero-section">
    <div class="container">
        <h1 class="display-4 fw-bold">YAYASAN PENDIDIKAN AL-FASYAH</h1>
        <p class="lead my-4">Selamat datang di platform e-learning kami</p>
        @auth
            @if(Auth::user()->role_id == 2)
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-light btn-lg fw-bold">Monggo</a>
            @elseif(Auth::user()->role_id == 3)
                <a href="{{ route('student.dashboard') }}" class="btn btn-light btn-lg fw-bold">Monggo</a>
            @else
                <a href="{{ route('home') }}" class="btn btn-light btn-lg fw-bold">Monggo</a>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn btn-light btn-lg fw-bold">Monggo</a>
        @endauth

        
    </div>
</div>

{{-- Features Section --}}
<div class="container features-section">
    <div class="row text-center">
        <div class="col-md-6">
            <div class="feature-icon mb-3"><i class="fas fa-laptop-code"></i></div>
            <h4 class="fw-bold">Kursus Digital</h4>
            <p class="text-muted">Sistem Penilaian LMS</p>
        </div>
        <div class="col-md-6">
            <div class="feature-icon mb-3"><i class="fas fa-chalkboard-teacher"></i></div>
            <h4 class="fw-bold">Pengajar Profesional</h4>
            <p class="text-muted">Belajar langsung dari guru dan profesional berpengalaman.</p>
        </div>
    </div>
</div>

<footer class="text-center py-4 bg-light">
    <p class="mb-0">&copy; {{ date('Y') }} Platform E-Learning. All Rights Reserved.</p>
</footer>
@endsection