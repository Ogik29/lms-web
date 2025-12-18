@extends('layouts.student')

@section('title', 'Kelas Saya')



@section('content')
<div class="container-fluid">

    <!-- Form Gabung Kelas -->
    {{-- <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title fw-bold">Gabung Kelas Baru</h5>
            <p class="card-text text-muted">Masukkan 8 digit kode kelas yang diberikan oleh guru Anda untuk bergabung.</p>
            <form action="{{ route('student.courses.join') }}" method="POST" class="row g-2 align-items-center">
                @csrf
                <div class="col-sm-8">
                    <label for="code" class="visually-hidden">Kode Kelas</label>
                    <input type="text" class="form-control" name="code" id="code"
                           placeholder="CONTOH: A1B2C3D4" maxlength="8" style="text-transform:uppercase" required>
                </div>
                <div class="col-sm-4">
                    <button type="submit" class="btn btn-primary w-100">Gabung</button>
                </div>
            </form>
            <!-- Tampilan Pesan Error/Sukses -->
            @if (session('success'))
                <div class="alert alert-success mt-3 mb-0">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger mt-3 mb-0">{{ session('error') }}</div>
            @endif
            @error('code')
                <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>
            @enderror
        </div>
    </div> --}}

    <!-- Daftar Kelas -->
    <h1 class="h3 mb-4">Daftar Kelas Anda</h1>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Gabung Kelas Baru</h5>
                    <p class="card-text text-muted">Masukkan 8 digit kode kelas yang diberikan oleh guru Anda untuk bergabung.</p>
                    <form action="{{ route('student.courses.join') }}" method="POST" class="row g-2 align-items-center">
                        @csrf
                        <div class="col-sm-8">
                            <label for="code" class="visually-hidden">Kode Kelas</label>
                            <input type="text" class="form-control" name="code" id="code"
                                   placeholder="CONTOH: A1B2C3D4" maxlength="8" style="text-transform:uppercase" required>
                        </div>
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-primary w-100">Gabung</button>
                        </div>
                    </form>
                    <!-- Tampilan Pesan Error/Sukses -->
                    @if (session('success'))
                        <div class="alert alert-success mt-3 mb-0">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mt-3 mb-0">{{ session('error') }}</div>
                    @endif
                    @error('code')
                        <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Daftar Kelas -->
            <h1 class="h3 mb-4">Daftar Kelas Anda</h1>
            <div class="row">
                @forelse ($courses as $course)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm course-card">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold">{{ $course->name }}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">Guru: {{ $course->teacher->name }}</h6>
                                <p class="card-text flex-grow-1">{{ Str::limit($course->description, 100) }}</p>
                                <a href="{{ route('student.courses.show', $course) }}" class="btn btn-outline-primary mt-auto">Lihat Detail Kelas</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">Anda belum terdaftar di kelas manapun. Silakan gunakan form di atas untuk bergabung.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection