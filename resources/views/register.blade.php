@extends('layouts.app')

@section('title', 'Buat Akun Baru')

@push('styles')
    <style>
      .row-container { min-height: 100vh; }
      .register-form-container, .info-panel { display: flex; align-items: center; }
      .register-form-container { justify-content: center; padding: 40px 15px; }
      .info-panel { background-color: var(--primary-blue); flex-direction: column; justify-content: center; padding: 3rem; color: white; text-align: center; }
      .info-panel img { max-width: 100%; width: 500px; border-radius: 20px; box-shadow: 0 15px 30px rgba(0,0,0,0.2); margin-bottom: 2rem; }
      .register-form-wrapper { width: 100%; max-width: 450px; }
      .register-icon { width: 70px; height: 70px; background-color: #eaf2ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary-blue); font-size: 2rem; margin: 0 auto 1.5rem auto; }
      .form-control, .input-group-text, .role-selector { border-radius: 0.75rem; }
      .role-selector { border: 1px solid #ced4da; padding: 1rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; }
      .role-selector.active { border-color: var(--primary-blue); background-color: #eaf2ff; font-weight: 500; }
      .role-selector input[type="radio"] { display: none; }
      .role-dot { width: 12px; height: 12px; background-color: #ccc; border-radius: 50%; margin-right: 12px; border: 3px solid #fff; outline: 1px solid #ccc; }
      .role-selector.active .role-dot { background-color: var(--primary-blue); outline-color: var(--primary-blue); }
      @media (max-width: 767.98px) { .info-panel { display: none; } }
    </style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 row-container">
        <!-- Form Register -->
        <div class="col-md-5 register-form-container">
            <div class="register-form-wrapper">
                <div class="text-center">
                    <div class="register-icon"><i class="fa-solid fa-user-plus"></i></div>
                    <h2>Buat Akun Baru</h2>
                    <p class="text-muted">Mari Bergabung bersama kami!</p>
                </div>
                <form method="POST" action="{{ route('register') }}" class="mt-4">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                         @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" required>
                         @error('email')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Masukkan password" required>
                         @error('password')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password" required>
                    </div>

                    {{-- ASUMSI: role_id 2=Guru, 1=Siswa --}}
                    <div class="mb-4">
                        <label class="form-label">Pilih Peran Anda</label>
                        <div id="role-options">
                            <label class="role-selector active w-100 mb-2"><input type="radio" name="role_id" value="3" checked /><span class="role-dot"></span> Siswa / Pelajar</label>
                            <label class="role-selector w-100"><input type="radio" name="role_id" value="2" /><span class="role-dot"></span> Pengajar / Guru</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-3">Daftar</button>
                </form>
                <div class="text-center mt-4">
                    <p class="text-muted">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
                </div>
            </div>
        </div>
        <!-- Panel Info -->
        <div class="col-md-7 info-panel">
            <div class="w-100" style="max-width: 550px;">
                <h2 class="fw-bold mb-4">Mulai Perjalanan Belajar Anda Hari Ini!</h2>
                <img src="{{ asset('img/gambar1.png') }}" alt="Siswa merayakan" class="img-fluid" />
                <h4 class="mt-4 fw-normal">Daftar sekarang dan buka akses ke ratusan materi pembelajaran berkualitas.</h4>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.role-selector').forEach(label => {
        label.addEventListener('click', function () {
            document.querySelectorAll('.role-selector').forEach(item => item.classList.remove('active'));
            this.classList.add('active');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
</script>
@endpush