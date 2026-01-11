@extends('layouts.teacher')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-modern shadow-modern animate-fade-in">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-folder me-2"></i>Kategori Soal</h4>
                    <a href="{{ route('teacher.question-bank.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke Bank Soal
                    </a>
                </div>
                <div class="card-body">
                    <!-- Add Category Form -->
                    <form action="{{ route('teacher.question-bank.categories.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="name" required class="form-control" placeholder="Nama Kategori">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="description" class="form-control" placeholder="Deskripsi (opsional)">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-modern-success w-100">
                                    <i class="fas fa-plus me-1"></i>Tambah
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Categories List -->
                    @if($categories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Deskripsi</th>
                                        <th class="text-center">Jumlah Soal</th>
                                        <th>Dibuat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $cat)
                                        <tr>
                                            <td><strong>{{ $cat->name }}</strong></td>
                                            <td>{{ $cat->description ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-vibrant-primary">{{ $cat->questions_count }}</span>
                                            </td>
                                            <td>{{ $cat->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">Belum ada kategori. Tambahkan kategori untuk mengorganisir soal Anda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
