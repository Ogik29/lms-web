@extends('layouts.app')

@section('title', 'Login Akun')

@push('styles')
    <style>
      .main-container { min-height: 100vh; }
      .row-container { height: 100%; }
      .info-panel {
        background-color: var(--primary-blue);
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 5rem;
        color: white;
      }
      .info-panel img { max-width: 100%; border-radius: 20px; box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2); }
      .login-form-wrapper { width: 100%; max-width: 400px; padding: 20px; }
      .login-icon { width: 70px; height: 70px; background-color: #eaf2ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary-blue); font-size: 2rem; margin: 0 auto 1.5rem auto; }
      .form-control { height: 50px; border-radius: 0.5rem; }
      .btn-primary { background-color: var(--primary-blue); border-color: var(--primary-blue); padding: 12px; font-weight: 600; }
      @media (max-width: 768px) { .info-panel { display: none; } }
    </style>
@endpush

@section('content')
<div class="container-fluid main-container p-0">
    <div class="row g-0 vh-100">
        <!-- Kolom Kiri: Form Login -->
        <div class="col-md-5 d-flex justify-content-center align-items-center">
            <div class="login-form-wrapper">
                <div class="text-center">
                    <div class="login-icon"><i class="fa-solid fa-book-open"></i></div>
                    <h2>Selamat Datang Kembali</h2>
                    <p class="text-muted">Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="mt-4">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="Masukkan email">
                        </div>
                        @error('email')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Masukkan password">
                        </div>
                        @error('password')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- <div class="text-end mb-4">
                        <a href="#">Lupa password?</a>
                    </div> --}}

                    <button type="submit" class="btn btn-primary w-100">Masuk</button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted">Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
                </div>
            </div>
        </div>
        
        <!-- Kolom Kanan: Panel Info -->
        <div class="col-md-7 info-panel">
            <h1 class="display-5 fw-bold">Platform E-Learning Modern</h1>
            <p class="lead my-3">Aplikasi untuk mengelola kursus, materi, dan aktivitas pembelajaran secara digital.</p>
            <img src="{{ asset('img/gambar2.png') }}" alt="Siswa belajar bersama" class="mt-4 img-fluid" width="400" />
        </div>
    </div>
</div>
@endsection