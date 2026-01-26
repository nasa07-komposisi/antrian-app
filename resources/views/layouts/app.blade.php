<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Antrian - @yield('title')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-link {
            font-weight: 500;
        }

        .gradient-primary {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            color: white;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="{{ route('public.index') }}">ANT APP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.index') }}">Tampilan Publik</a></li>
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Dashboard Loket</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.login') }}">Admin</a></li>
                    @else
                        @if(auth()->user()->role === 'staff')
                            <li class="nav-item"><a class="nav-link" href="{{ route('counter.index') }}">Dashboard Loket</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-speedometer2 me-1"></i> Admin
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('admin.index') }}">Dashboard</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="{{ route('admin.quotas') }}"><i
                                                class="bi bi-shield-check me-2"></i>Kuota Antrian</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.services') }}">Layanan</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.counters') }}">Loket</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users') }}">User</a></li>
                                </ul>
                            </li>
                        @endif
                    @endguest
                    @auth
                        <li class="nav-item ms-lg-2">
                            <button id="btn-activate-audio" onclick="window.activateAudio && window.activateAudio()"
                                class="btn btn-warning btn-sm nav-link border-0 text-dark fw-bold px-3">
                                <i class="bi bi-volume-up-fill me-1"></i> AKTIFKAN SUARA
                            </button>
                        </li>
                        <li class="nav-item ms-lg-3">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit"
                                    class="btn btn-outline-danger btn-sm nav-link border-0 text-white fw-bold">
                                    <i class="bi bi-box-arrow-right"></i> LOGOUT
                                </button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="{{ Request::is('/') || Request::is('admin*') || Request::is('counter*') ? 'container-fluid px-4' : 'container' }}">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global Auto-Reload on Config Change
        (function () {
            let currentVersion = null;
            function checkVersion() {
                // Determine if we should skip reload to avoid disrupting user input
                const isModifying = document.querySelector('.modal.show') ||
                    (document.activeElement && ['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement.tagName));

                fetch('{{ route("public.config-version") }}')
                    .then(response => response.json())
                    .then(data => {
                        const newVersion = String(data.config_version);
                        if (currentVersion === null) {
                            currentVersion = newVersion;
                            console.log("Initial Config Version:", currentVersion);
                        } else if (currentVersion !== newVersion) {
                            console.log("Config changed! Old:", currentVersion, "New:", newVersion);

                            // Only reload if user is not in the middle of modifying something
                            if (!isModifying) {
                                location.reload();
                            } else {
                                console.log("Config changed but postponing reload check because user is active.");
                                // We don't update currentVersion here, so it will try again in next cycle
                            }
                        }
                    })
                    .catch(err => console.error("Sync error:", err));
            }
            // Check every 5 seconds
            setInterval(checkVersion, 5000);
            setTimeout(checkVersion, 1000); // Initial check after 1s
        })();

        // Auto-hide session alerts/notif after 2 seconds
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach(alert => {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    if (bsAlert) bsAlert.close();
                });
            }, 2000);
        });
    </script>

    @stack('scripts')
</body>

</html>