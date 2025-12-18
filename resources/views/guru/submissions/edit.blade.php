{{-- This view has been removed: teacher-submission-edit feature is disabled. --}}
@extends('layouts.teacher')

@section('title', 'Edit Submisi - Dinonaktifkan')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">Fitur edit submisi oleh guru telah dinonaktifkan. Gunakan fitur sunting tugas untuk mengubah detail tugas yang diassign.</div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>
    </div>
</div>
@endsection