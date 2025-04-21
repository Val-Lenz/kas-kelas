@extends('layouts.app')

@section('content')
    <style>
        .card {
            max-width: 100%;
        }

        .table-responsive {
            max-width: 100%;
            overflow-x: auto;
        }

        .sticky-col {
            position: sticky;
            background-color: #fff;
        }

        .sticky-col.first {
            left: 0;
            z-index: 2;
        }

        .sticky-col.second {
            left: 60px;
            z-index: 2;
        }

        th,
        td {
            text-align: center;
            vertical-align: middle;
        }

        .bg-primary-light {
            background-color: #e8f4f8;
        }

        /* Warna lembut */
        .text-primary-light {
            color: #007bff;
        }

        /* Warna teks */
    </style>

    <!-- Card Section -->
    <div class="row mb-4">
        <!-- Total Kas Masuk -->
        <div class="col-md-6">
            <div class="card shadow-sm bg-primary-light">
                <div class="card-body text-center text-primary-light">
                    <i class="fas fa-wallet fa-3x mb-3"></i> <!-- Ikon dompet -->
                    <h5 class="card-title">Total Nominal Kas Masuk</h5>
                    <p class="card-text fs-5">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Tahun Kas Masuk -->
        <div class="col-md-6">
            <div class="card shadow-sm bg-primary-light">
                <div class="card-body text-center text-primary-light">
                    <i class="fas fa-calendar-alt fa-3x mb-3"></i> <!-- Ikon kalender -->
                    <h5 class="card-title">Tahun Kas Masuk</h5>
                    <p class="card-text fs-5">{{ $year }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm p-4">
        @if (Auth::user()->role == 'admin')
            <form method="GET" action="{{ route('admin.laporan.kasmasuk', $id) }}" class="mb-4">
            @else
                <form method="GET" action="{{ route('laporan.kasmasuk') }}" class="mb-4">
        @endif
        <div class="row">
            <!-- Input Pencarian -->
            <div class="col-md-4 mb-2">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama Siswa"
                    value="{{ $search ?? '' }}">
            </div>

            <!-- Dropdown Tahun -->
            <div class="col-md-4 mb-2">
                <select name="year" class="form-control">
                    @foreach ($availableYears as $availableYear)
                        <option value="{{ $availableYear }}" {{ $availableYear == $year ? 'selected' : '' }}>
                            {{ $availableYear }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol -->
            <div class="col-md-4 text-right">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center">
                <thead class="table-primary">
                    <tr>
                        <th class="sticky-col first" rowspan="2" style="min-width: 60px;">No</th>
                        <th class="sticky-col second" rowspan="2" style="min-width: 120px;">Nama</th>
                        @php
                            $currentMonth = null;
                            $monthColspan = 0;
                        @endphp
                        @foreach ($weeks as $week)
                            @if ($currentMonth != $week['month'])
                                @if ($currentMonth !== null)
                                    <th colspan="{{ $monthColspan }}">
                                        {{ DateTime::createFromFormat('!m', $currentMonth)->format('F') }}</th>
                                @endif
                                @php
                                    $currentMonth = $week['month'];
                                    $monthColspan = 0;
                                @endphp
                            @endif
                            @php $monthColspan++; @endphp
                        @endforeach
                        <th colspan="{{ $monthColspan }}">
                            {{ DateTime::createFromFormat('!m', $currentMonth)->format('F') }}</th>
                    </tr>
                    <tr>
                        @foreach ($weeks as $week)
                            <th style="min-width: 60px;">M{{ $week['week'] }}</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach ($siswa as $index => $data)
                        <tr>
                            <td class="sticky-col first bg-white">{{ $index + 1 }}</td>
                            <td class="sticky-col second bg-white">{{ $data->nama }}</td>
                            @foreach ($weeks as $week)
                                @php
                                    $kasMinggu = $data->kasMasuk->where('minggu_ke', $week['week'])->first();
                                @endphp
                                <td>{{ $kasMinggu ? number_format($kasMinggu['nominal'], 0, ',', '.') : '-' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
