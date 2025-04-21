<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <!-- Bootstrap Offline CSS -->
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
    <div class="card shadow-lg" style="width: 100%; max-width: 500px;">
        <div class="card-body">
            <h3 class="text-center text-primary fs-1 mb-4"><b>LogIn</b></h3>
            <p class="text-center">Selamat datang di Kas Kelas SMKN 17.</p>
            
            <form action="{{ route('login') }}" method="POST" class="px-4 pb-4">
                @csrf
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama" required />
                    <label for="nama">Nama</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required />
                    <label for="password">Password</label>
                </div>
                <button type="submit" class="btn btn-primary w-100"><b>Login</b></button>
            </form>
            <div class="px-4">
                <!-- Menampilkan alert -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Sukses!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
        
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Offline JS -->
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
</body>
</html>