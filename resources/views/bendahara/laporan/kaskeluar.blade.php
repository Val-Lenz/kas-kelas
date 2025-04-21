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

        th,
        td {
            text-align: center;
            vertical-align: middle;
        }

        .bg-danger-light {
            background-color: #fff8f8;
        }
    </style>

    <!-- Card Section -->
    <div class="row mb-4">
        <!-- Nominal Kas Keluar -->
        <div class="col-md-4">
            <div class="card shadow-sm bg-danger-light">
                <div class="card-body text-center text-danger">
                    <i class="fas fa-wallet fa-3x mb-3"></i>
                    <h5 class="card-title">Total Nominal Kas Keluar</h5>
                    <p class="card-text fs-5">Rp {{ number_format($totalKasKeluar, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Jumlah Kas Keluar -->
        <div class="col-md-4">
            <div class="card shadow-sm bg-danger-light">
                <div class="card-body text-center text-danger">
                    <i class="fas fa-list fa-3x mb-3"></i>
                    <h5 class="card-title">Jumlah Kas Keluar</h5>
                    <p class="card-text fs-5">{{ $jumlahKasKeluar }} Transaksi</p>
                </div>
            </div>
        </div>

        <!-- Kas Keluar Tahun -->
        <div class="col-md-4">
            <div class="card shadow-sm bg-danger-light">
                <div class="card-body text-center text-danger">
                    <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                    <h5 class="card-title">Kas Keluar Tahun</h5>
                    <p class="card-text fs-5">{{ $year }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm p-4">
        <!-- Filters -->
        @if (Auth::user()->role == 'admin')
            <form method="GET" action="{{ route('admin.laporan.kaskeluar',$id) }}" class="mb-4">
            @else
                <form method="GET" action="{{ route('laporan.kaskeluar') }}" class="mb-4">
        @endif
        <div class="row">
            <!-- Pencarian -->
            <div class="col-md-4 mb-2">
                <input type="text" name="search" class="form-control" placeholder="Cari Catatan"
                    value="{{ $search ?? '' }}">
            </div>

            <!-- Tahun -->
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
        <!-- Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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
        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center">
                <thead class="table-primary">
                    <tr>
                        <th class="sticky-col first" style="min-width: 60px;">No</th>
                        <th style="min-width: 120px;">Tanggal</th>
                        <th class="text-end" style="min-width: 120px;">Nominal Rp</th>
                        <th style="min-width: 240px;">Catatan</th>
                        @if (Auth::user()->role == 'bendahara')
                        <th style="min-width: 180px;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kasKeluar as $index => $data)
                        <tr class="table-danger">
                            <td class="sticky-col first">{{ $index + 1 }}</td>
                            <td>{{ $data->created_at->format('d-m-Y') }}</td>
                            <td class="text-end">{{ number_format($data->nominal, 0, ',', '.') }}</td>
                            <td>{{ $data->catatan }}</td>
                            @if (Auth::user()->role == 'bendahara')
                            <td>
                                <!-- Tombol Edit -->
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editModal-{{ $data->id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <!-- Tombol Delete -->
                                <form action="{{ route('kaskeluar.delete', $data->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editModal-{{ $data->id }}" tabindex="-1"
                            aria-labelledby="editModalLabel-{{ $data->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel-{{ $data->id }}">Edit Kas Keluar
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="{{ route('kaskeluar.update', $data->id) }}">
                                        <div class="modal-body">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label for="editNominal-{{ $data->id }}"
                                                    class="form-label">Nominal</label>
                                                <input type="number" class="form-control"
                                                    id="editNominal-{{ $data->id }}" name="nominal"
                                                    value="{{ $data->nominal }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="editCatatan-{{ $data->id }}"
                                                    class="form-label">Catatan</label>
                                                <textarea class="form-control" id="editCatatan-{{ $data->id }}" name="catatan" rows="3" required>{{ $data->catatan }}</textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
