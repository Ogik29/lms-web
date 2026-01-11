<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Dashboard Murid - E-Learning')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    
    <!-- Custom Animations -->
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}" />
    
    <!-- Enhanced Visual Styling -->
    <link rel="stylesheet" href="{{ asset('css/enhanced-style.css') }}" />

    <style>
      body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
      :root { --primary-blue: #275be0; }
      a { text-decoration: none; color: var(--primary-blue); font-weight: 500; }
      .sidebar { background-color: #ffffff; min-height: 100vh; }
      .sidebar .nav-link { color: #333; padding: .6rem .75rem; border-radius: .4rem; }
      .sidebar .nav-link.active, .sidebar .nav-link:hover { background-color: rgba(39,91,224,0.06); color: var(--primary-blue); }
      .sidebar .avatar { width:36px;height:36px;font-weight:600;border-radius:50%;display:inline-flex;align-items:center;justify-content:center }
      @media (max-width: 767px) { .sidebar { display: none; } }
    </style>

    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">E-Learning</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
          <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">{{ Auth::user()->name }}</a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                <li><a class="dropdown-item" href="{{ route('student.dashboard') }}">Dashboard Siswa</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="{{ route('logout') }}">Logout</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <aside class="col-md-3 col-lg-2 d-none d-md-block bg-white border-end sidebar p-3">
          <div class="mb-3 text-center">
            <h6 class="mb-0 fw-bold">E-Learning</h6>
            <small class="text-muted">Murid</small>
          </div>
          <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
            <a class="nav-link {{ request()->routeIs('student.courses.*') ? 'active' : '' }}" href="{{ route('student.courses.index') }}"><i class="fas fa-book me-2"></i> Kelas Saya</a>
            {{-- <a class="nav-link" href="{{ route('home') }}"><i class="fas fa-search me-2"></i> Jelajah Kelas</a> --}}

            <div class="mt-3 border-top pt-3">
              {{-- <a class="nav-link" href="#"><i class="fas fa-user me-2"></i> Profil</a> --}}
              <a class="nav-link text-danger" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>
          </nav>
        </aside>

        <main class="col-md-9 col-lg-10 py-4">
          @yield('content')
        </main>
      </div>
    </div>

    <!-- Global confirmation modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title" id="confirmModalTitle">Konfirmasi</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
          <div class="modal-body" id="confirmModalBody">Apakah Anda yakin?</div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-danger" id="confirmModalYes">Ya, Lanjutkan</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    // Simple table search utility: provide input ID and table ID
    function tableSearch(inputId, tableId) {
        var input = document.getElementById(inputId);
        if (!input) return;
        input.addEventListener('input', function () {
            var filter = input.value.toLowerCase();
            var rows = document.querySelectorAll('#' + tableId + ' tbody tr');
            rows.forEach(function(row) {
                var text = row.innerText.toLowerCase();
                row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
            });
        });
    }

    // File preview helper (images only)
    function previewFile(input, previewSelector) {
        var file = input.files && input.files[0];
        if (!file) return;
        var preview = document.querySelector(previewSelector);
        if (!preview) return;
        if (file.type.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) { preview.src = e.target.result; preview.classList.remove('d-none'); };
            reader.readAsDataURL(file);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Delegate click for any form that has data-confirm attribute
        document.body.addEventListener('click', function (e) {
            var el = e.target.closest('form[data-confirm], button[data-confirm]');
            if (!el) return;
            e.preventDefault();
            var form = el.tagName.toLowerCase() === 'form' ? el : el.closest('form');
            var message = form.getAttribute('data-confirm') || form.getAttribute('data-confirm-message') || 'Apakah Anda yakin?';
            var title = form.getAttribute('data-confirm-title') || 'Konfirmasi';

            var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            document.getElementById('confirmModalTitle').innerText = title;
            document.getElementById('confirmModalBody').innerText = message;

            var yesBtn = document.getElementById('confirmModalYes');
            var handler = function () {
                modal.hide();
                yesBtn.removeEventListener('click', handler);
                form.submit();
            };
            yesBtn.addEventListener('click', handler);
            modal.show();
        });
    });
    </script>

    @stack('scripts')
</body>
</html>