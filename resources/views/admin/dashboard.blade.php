@extends('layouts.app')

@section('content')
    <!-- Dashboard Summary -->

    <!-- Total User dan Total Siswa -->
    <div class="row mb-3">
        <!-- Total User -->
        <div class="col-md-6">
            <div class="card shadow-sm border-warning">
                <div class="card-body d-flex align-items-end justify-content-center text-warning">
                    <i class="fas fa-users fs-1 "></i> <!-- Ikon dengan margin kanan -->
                    <div class="mx-2"></div>
                    <h5 class="card-title"> Total Kelas: {{ $totalUsers }}</h5>
                </div>
            </div>
        </div>

        <!-- Total Siswa -->
        <div class="col-md-6">
            <div class="card shadow-sm border-info">
                <div class="card-body d-flex align-items-end justify-content-center text-info">
                    <i class="fas fa-user-graduate fs-1 "></i>
                    <div class="mx-2"></div>
                    <h5 class="card-title"> Total Siswa: {{ $totalSiswa }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Total Kas Masuk -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3 border-primary">
                <div class="ard-body d-flex align-items-end justify-content-center text-primary py-4">
                    <i class="fas fa-wallet fa-3x mb-3"></i>
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
                <div class="ard-body d-flex align-items-end justify-content-center text-danger py-4">
                    <i class="fas fa-hand-holding-usd fa-3x mb-3"></i>
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
            <div class="card shadow-sm mb-3 border-success">
                <div class="ard-body d-flex align-items-end justify-content-center text-success py-4">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <div class="m-2"></div>
                    <div>
                        <h5 class="card-title">Grand Total Kas</h5>
                        <p class="fs-4 my-0">Rp {{ number_format($saldoKas, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik dan Bendahara -->
    <div class="row mb-3">
        <!-- Grafik Kas Masuk dan Keluar -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Grafik Kas Masuk dan Keluar</h5>
                    <canvas id="kasChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bendahara dengan Kas Terbanyak -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">5 Bendahara dengan Kas Terbanyak</h5>
                    <table class="table table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>Nama</th>
                                <th class="text-end">Total Kas Rp</th>
                                <th>Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topBendahara as $bendahara)
                                <tr>
                                    <td>{{ $bendahara->nama }}</td>
                                    <td class="text-end"> {{ number_format($bendahara->total_kas, 0, ',', '.') }}</td>
                                    <td>{{ $bendahara->angkatan }}, {{ $bendahara->jurusan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Data untuk grafik
        const kasData = {
            labels: ['Kas Masuk', 'Kas Keluar'], // Label data
            datasets: [{
                label: 'Nominal (Rp)',
                data: [{{ $totalKasMasuk }}, {{ $totalKasKeluar }}], // Total kas masuk dan keluar
                backgroundColor: ['rgba(54, 162, 235, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        };

        const kasConfig = {
            type: 'bar', // Tipe grafik (bar)
            data: kasData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Grafik Kas Masuk dan Keluar'
                    }
                }
            },
        };

        // Render grafik
        new Chart(document.getElementById('kasChart'), kasConfig);
    </script>
@endsection
