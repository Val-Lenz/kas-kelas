@extends('layouts.app')

@section('content')
    <!-- Dashboard Summary -->
    <div class="row mb-3">
        <!-- Total Kas Masuk -->
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body text-center text-primary">
                    <i class="fas fa-wallet fa-3x mb-3"></i>
                    <h5 class="card-title">Total Kas Masuk</h5>
                    <p class="fs-4 my-0">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Total Kas Keluar -->
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body text-center text-danger">
                    <i class="fas fa-hand-holding-usd fa-3x mb-3"></i>
                    <h5 class="card-title">Total Kas Keluar</h5>
                    <p class="fs-4 my-0">Rp {{ number_format($totalKasKeluar, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Saldo Kas -->
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center text-success">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <h5 class="card-title">Saldo Kas</h5>
                    <p class="fs-4 my-0">Rp {{ number_format($saldoKas, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Grafik Kas Masuk dan Keluar</h5>
                    <canvas id="kasChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">5 Transaksi Terakhir - Kas Keluar</h5>
                    <table class="table table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>Catatan</th>
                                <th class="text-end">Nominal Rp</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentKasKeluar as $kas)
                                <tr>
                                    <td>{{ $kas->catatan ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($kas->nominal, 0, ',', '.') }}</td>
                                    <td>{{ $kas->created_at->format('d-m-Y') }}</td>
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
