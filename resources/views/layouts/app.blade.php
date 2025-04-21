<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>UANG KAS Dashboard</title>
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('fontawesome/css/all.min.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #f0f6f8;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
            color: #4b5563;
        }

        .sidebar {
            background-color: #2f3a40;
            min-height: 100vh;
            width: 14rem;
            color: #d1d5db;
            display: flex;
            flex-direction: column;
            user-select: none;
        }

        .sidebar .logo {
            border-bottom: 1px solid #4b5563;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #d1d5db;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .sidebar a {
            color: #d1d5db;
            text-decoration: none;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            border-bottom: 1px solid #4b5563;
            transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
            position: relative;
        }

        .sidebar a:hover {
            background-color: #d1d5db;
            color: #4b5563;
        }

        .sidebar a.user-info:hover {
            background-color: #4b5563;
        }

        .sidebar a.active {
            background-color: #274772ab;
            color: white;
        }

        .sidebar .total-kas {
            background-color: #16a34a;
            color: white;
            font-weight: 600;
            border-radius: 4px;
            margin: 6px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar .total-kas:hover {
            background-color: white;
            color: #16a34a;
            outline: 2px solid #16a34a;
        }

        .sidebar a.user-info {
            flex-direction: column;
            align-items: flex-start;
            padding-left: 3.5rem;
            border-bottom: 1px solid #4b5563;
        }

        .sidebar a.user-info .fa-user {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #d1d5db;
            font-size: 1rem;
        }

        .sidebar a.user-info .name {
            font-weight: 500;
            font-size: 0.875rem;
            color: #d1d5db;
            line-height: 1.1;
        }

        .sidebar a.user-info .subtext {
            font-size: 0.625rem;
            color: #9ca3af;
            margin-top: 0.125rem;
            line-height: 1.1;
        }

        .sidebar hr.divider {
            border-color: #4b5563;
            margin: 1rem 0;
        }

        .topbar {
            background-color: white;
            border-bottom: 1px solid #d1d5db;
            color: #4b5563;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .topbar .menu-btn {
            background: none;
            border: none;
            color: #4b5563;
            font-size: 1.25rem;
        }

        .topbar .title {
            font-size: 1.250rem;
            user-select: none;
        }

        .topbar .btn-outline-danger {
            margin-left: auto;
        }

        main {
            padding: 1.5rem;
            flex-grow: 1;
        }

        .card-custom {
            box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
            border-radius: 0.25rem;
            background-color: white;
            padding: 1rem;
            user-select: none;
        }

        .card-header {
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }

        .card-header .fa-arrow-up,
        .card-header .fa-dollar-sign {
            color: #16a34a;
        }

        .card-header .fa-arrow-down,
        .card-header .fa-dollar-sign.red {
            color: #dc2626;
        }

        .card-text {
            font-size: 0.75rem;
            color: #6b7280;
            margin-bottom: 0.75rem;
        }

        .card-button {
            background-color: #0f766e;
            border: none;
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-button:hover {
            background-color: #115e59;
            color: white;
        }

        @media (max-width: 575.98px) {
            .sidebar {
                width: 100%;
                min-height: auto;
                flex-direction: row;
                overflow-x: auto;
            }

            .sidebar a,
            .sidebar a.total-kas,
            .sidebar a.user-info {
                border-bottom: none;
                border-right: 1px solid #4b5563;
                padding: 0.5rem 1rem;
                white-space: nowrap;
            }

            .sidebar a.user-info {
                padding-left: 1rem;
                flex-direction: row;
                align-items: center;
            }

            .sidebar a.user-info .fa-user {
                position: static;
                transform: none;
                margin-right: 0.5rem;
            }

            main {
                padding: 1rem 0.5rem;
            }

            .card-custom {
                padding: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div class=""> <!-- Sidebar -->
        <nav class="sidebar position-fixed ">
            <div class="logo"> <img src="{{ asset(path: 'logo/logo.jpg') }}" width="28" height="28"
                    class="rounded-circle" /> KAS KELAS SMKN 17 </div>
            <a href="#" class="user-info position-relative"> <i class="fas fa-user"></i>
                <div>
                    <div class="name">{{ Auth::user()->nama }}</div>
                    @if (Auth::user()->role == 'admin')
                        <div class="subtext">{{ Auth::user()->role }}</div>
                    @else
                        <div class="subtext">{{ Auth::user()->angkatan }}, {{ Auth::user()->jurusan }}</div>
                    @endif
                </div>
            </a>

            @if (Auth::user()->role == 'admin')
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}">
                    <i class="fas fa-info-circle"></i> Dashboard
                </a>
                <a href="{{ route('admin.bendahara') }}"
                    class="{{ Route::currentRouteName() == 'admin.bendahara' ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Data Bendahara
                </a>
                <a href="{{ route('admin.laporan') }}"
                class="{{ Request::is('admin/laporan*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Laporan
            </a>
            @else
                <div class="total-kas px-3 py-2 ">
                    <i class="fas fa-money-bill-wave"></i>Rp {{  number_format($totalKas, 0, ',', '.') }}
                </div>
                <a href="{{ route('dashboard') }}"
                    class="{{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}">
                    <i class="fas fa-info-circle"></i> Dashboard
                </a>
                <a href="{{ route('uangkas') }}" class="{{ Route::currentRouteName() == 'uangkas' ? 'active' : '' }}">
                    <i class="fas fa-dollar-sign"></i> Uang Kas
                </a>
                <a href="{{ route('siswa') }}" class="{{ Route::currentRouteName() == 'siswa' ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i> Data Siswa
                </a>
                <a href="{{ route('laporan.kasmasuk') }}"
                    class="{{ Route::currentRouteName() == 'laporan.kasmasuk' ? 'active' : '' }}">
                    <i class="fas fa-arrow-circle-down"></i> Laporan Kas Masuk
                </a>
                <a href="{{ route('laporan.kaskeluar') }}"
                    class="{{ Route::currentRouteName() == 'laporan.kaskeluar' ? 'active' : '' }}">
                    <i class="fas fa-arrow-circle-up"></i> Laporan Kas Keluar
                </a>
            @endif
        </nav>

        <!-- Main content -->
        <div style="min-height: 100vh; margin-left: 14rem;">
            <!-- Topbar -->
            <header class="topbar">
                <button class="menu-btn" aria-label="Menu"><i class="fas fa-bars"></i></button>
                <span class="title">
                    <b>
                        @if (Route::currentRouteName() == 'admin.dashboard')
                            Dashboard Admin
                        @elseif (Route::currentRouteName() == 'admin.bendahara')
                            Data Bendahara
                        @elseif (Route::currentRouteName() == 'admin.laporan')
                            Laporan
                        @elseif (Route::currentRouteName() == 'admin.laporan.kasmasuk')
                            @php
                                // Ambil id dari segmen URL (misalnya untuk URL http://127.0.0.1:8000/admin/laporan/kasmasuk/2,
                                // user id ada di segmen ke-4)
                                $userId = request()->segment(4);
                                // Cari user berdasarkan id; pastikan model User sudah di-import (contoh: \App\Models\User)
                                $user = \App\Models\User::find($userId);
                            @endphp
                            Laporan Kas Masuk - {{ $user ? $user->nama : 'User tidak ditemukan' }}
                        @elseif (Route::currentRouteName() == 'admin.laporan.kaskeluar')
                            @php
                                // Ambil id dari segmen URL (misalnya untuk URL http://127.0.0.1:8000/admin/laporan/kaskeluar/2,
                                // user id ada di segmen ke-4)
                                $userId = request()->segment(4);
                                // Cari user berdasarkan id; pastikan model User sudah di-import (contoh: \App\Models\User)
                                $user = \App\Models\User::find($userId);
                            @endphp
                            Laporan Kas Masuk - {{ $user ? $user->nama : 'User tidak ditemukan' }}
                        @elseif (Route::currentRouteName() == 'dashboard')
                            Dashboard
                        @elseif (Route::currentRouteName() == 'uangkas')
                            Uang Kas
                        @elseif (Route::currentRouteName() == 'siswa')
                            Data Siswa
                        @elseif (Route::currentRouteName() == 'laporan.kasmasuk')
                            Laporan Kas Masuk
                        @elseif (Route::currentRouteName() == 'laporan.kaskeluar')
                            Laporan Kas Keluar
                        @else
                            Halaman Tidak Diketahui
                        @endif
                    </b>
                </span>
                <a href="{{ route('logout') }}" class="btn btn-outline-danger profile-btn">LogOut</a>
            </header>

            <!-- Page content -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>
</body>
<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            // Ambil semua elemen dengan kelas .alert
            const alerts = document.querySelectorAll('.alert');
            // Hapus setiap elemen alert dari DOM
            alerts.forEach((alert) => {
                alert.remove();
            });
        }, 5000); // 5000 ms = 5 detik
    });
</script>

</html>
