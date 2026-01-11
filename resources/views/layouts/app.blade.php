<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Platform E-Learning')</title>

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
      body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
      }
      :root {
        --primary-blue: #275be0;
      }
      a {
        text-decoration: none;
        color: var(--primary-blue);
        font-weight: 500;
      }

      /* Sidebar shared styles */
      .sidebar { background-color: #ffffff; min-height: 100vh; }
      .sidebar .nav-link { color: #333; padding: .6rem .75rem; border-radius: .4rem; }
      .sidebar .nav-link.active, .sidebar .nav-link:hover { background-color: rgba(39,91,224,0.06); color: var(--primary-blue); }
      .sidebar .avatar { width:36px;height:36px;font-weight:600;border-radius:50%;display:inline-flex;align-items:center;justify-content:center }
      @media (max-width: 767px) {
        .sidebar { display: none; }
      }
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
            @auth
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">{{ Auth::user()->name }}</a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                  @if(Auth::user()->role_id == 2)
                    <li><a class="dropdown-item" href="{{ route('teacher.dashboard') }}">Dashboard Guru</a></li>
                  @elseif(Auth::user()->role_id == 3)
                    <li><a class="dropdown-item" href="{{ route('student.dashboard') }}">Dashboard Murid</a></li>
                  @endif
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                    {{-- <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form> --}}
                  </li>
                </ul>
              </li>
            @else
              <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Masuk</a></li>
              <li class="nav-item"><a class="btn btn-primary ms-2" href="{{ route('register') }}">Daftar</a></li>
            @endauth
          </ul>
        </div>
      </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>

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
    </script>



@stack('scripts')
</body>
</html>