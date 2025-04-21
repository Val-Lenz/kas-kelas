@extends('layouts.app')

@section('content')


<div class="row">
    <!-- Total Kas Masuk -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-3 border-primary">
            <div class="card-body d-flex align-items-end justify-content-center text-primary py-4">
                <i class="fas fa-arrow-down fa-3x mb-3"></i> <!-- Ikon panah ke bawah -->
                <div class="m-2"></div>
                <div>
                    <h5 class="card-title">Grand Total Kas Masuk</h5>
                    <p class="fs-4 my-0">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Kas Keluar -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-3 border-danger">
            <div class="card-body d-flex align-items-end justify-content-center text-danger py-4">
                <i class="fas fa-arrow-up fa-3x mb-3"></i> <!-- Ikon panah ke atas -->
                <div class="m-2"></div>
                <div>
                    <h5 class="card-title">Grand Total Kas Keluar</h5>
                    <p class="fs-4 my-0">Rp {{ number_format($totalKasKeluar, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Saldo Kas -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-3 border-warning">
            <div class="card-body d-flex align-items-end justify-content-center text-warning py-4">
                <i class="fas fa-wallet fa-3x mb-3"></i> <!-- Ikon dompet -->
                <div class="m-2"></div>
                <div>
                    <h5 class="card-title">Grand Total Kas</h5>
                    <p class="fs-4 my-0">Rp {{ number_format($totalSaldoKas, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Card Data Bendahara -->
    <div class="card shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- Pencarian di Kiri -->
            <div class="input-group w-25">
                <input type="text" id="searchInput" class="form-control shadow-sm" placeholder="Cari nama bendahara...">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
        </div>

        <!-- Alert Success -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Alert Error -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

  <!-- Tabel Data Bendahara -->
<table class="table table-bordered table-hover text-center" id="bendaharaTable">
    <thead class="table-primary">
        <tr>
            <th>No</th>
            <th class="text-start">Nama</th>
            <th>Angkatan</th>
            <th>Jurusan</th>
            <th>Total Siswa</th> <!-- Kolom baru -->
            <th class="text-end">Saldo Kas Rp</th>
            <th>Laporan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="nama text-start">{{ $user->nama }}</td>
                <td>{{ $user->angkatan }}</td>
                <td>{{ $user->jurusan }}</td>
                <td>{{ $user->total_siswa }}</td> <!-- Menampilkan total siswa -->
                <td class="text-end">{{  number_format($user->saldo_kas, 0, ',', '.') }}</td>
                <td>
                    <!-- Tombol untuk laporan kas masuk -->
                    <a href="{{ route('admin.laporan.kasmasuk', $user->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-down"></i> Kas Masuk
                    </a>
                    <!-- Tombol untuk laporan kas keluar -->
                    <a href="{{ route('admin.laporan.kaskeluar', $user->id) }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-arrow-up"></i> Kas Keluar
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
    </div>

    <!-- Bootstrap JS -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('#bendaharaTable tbody tr');

            // Filter baris tabel berdasarkan pencarian nama
            searchInput.addEventListener('keyup', function() {
                const query = searchInput.value.toLowerCase();

                tableRows.forEach(row => {
                    const nama = row.querySelector('.nama').textContent.toLowerCase();

                    if (nama.includes(query)) {
                        row.style.display = ''; // Tampilkan baris
                    } else {
                        row.style.display = 'none'; // Sembunyikan baris
                    }
                });
            });
        });
    </script>
@endsection