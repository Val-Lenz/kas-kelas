@extends('layouts.app')

@section('content')
    <div class="row mb-4">
        <!-- Card Belum Bayar Minggu Ini -->
        <div class="col-md-4">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center text-danger">
                    <i class="fas fa-user-times fa-3x mb-3"></i>
                    <h5 class="card-title">Belum Bayar Minggu Ini</h5>
                    <p class="card-text fs-4 text-danger">{{ $belumBayar }}</p>
                </div>
            </div>
        </div>

        <!-- Card Sudah Bayar Minggu Ini -->
        <div class="col-md-4">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center text-success">
                    <i class="fas fa-user-check fa-3x mb-3"></i>
                    <h5 class="card-title">Sudah Bayar Minggu Ini</h5>
                    <p class="card-text fs-4 text-success">{{ $sudahBayar }}</p>
                </div>
            </div>
        </div>

        <!-- Card Kas Masuk Minggu Ini -->
        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center text-primary">
                    <i class="fas fa-wallet fa-3x mb-3"></i>
                    <h5 class="card-title">Kas Masuk Minggu Ini</h5>
                    <p class="card-text fs-4 text-primary">{{ 'Rp ' . number_format($kasMasukMingguIni, 0, ',', '.') }} </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- Pencarian -->
            <div class="input-group w-25">
                <input type="text" id="searchInput" class="form-control shadow-sm" placeholder="Cari nama siswa...">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>

            <!-- Tombol Kas Keluar -->
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#kasKeluarModal">
                <i class="fas fa-money-bill-wave"></i> Kas Keluar
            </button>
        </div>

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

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center" id="siswaTable">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th class="text-start">Nama</th>
                        <th>NIS</th>
                        <th class="text-end">Kas Siswa Rp</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($siswa as $index => $item)
                        @php
                            $kasMasuk = $item->kasMasuk->sum('nominal');
                            $kasSiswa = $kasMasuk - $perStudentDeduction;
                            $belumBayarMingguIni = !in_array($item->id, $siswaYangBayarMingguIni);
                        @endphp
                        <tr class="{{ $belumBayarMingguIni ? 'table-danger' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="nama text-start">{{ $item->nama }}</td>
                            <td>{{ $item->nis }}</td>
                            <td class="text-end">{{ number_format($kasSiswa, 0, ',', '.') }}</td>
                            <td>
                                <!-- Button for Detail -->
                                <button class="btn btn-primary text-light btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#detailKasMasukModal-{{ $item->id }}">
                                    <i class="fas fa-info-circle"></i> Detail
                                </button>

                                <!-- Button for Payment -->
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#bayarKasModal-{{ $item->id }}">
                                    <i class="fas fa-money-check-alt"></i> Bayar Kas
                                </button>
                            </td>
                        </tr>

                        <!-- Detail Modal -->
                        <div class="modal fade" id="detailKasMasukModal-{{ $item->id }}" tabindex="-1"
                            aria-labelledby="detailModalLabel-{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailModalLabel-{{ $item->id }}">
                                            Detail Kas Masuk - {{ $item->nama }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-group">
                                            @foreach ($item->kasMasuk as $kas)
                                                <li class="list-group-item">
                                                    <strong>Nominal:</strong> Rp
                                                    {{ number_format($kas->nominal, 2, ',', '.') }} <br>
                                                    <strong>Catatan:</strong> {{ $kas->catatan ?? '-' }} <br>
                                                    <strong>Tanggal:</strong> {{ $kas->created_at->format('d-m-Y') }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Kas Keluar -->
    <div class="modal fade" id="kasKeluarModal" tabindex="-1" aria-labelledby="kasKeluarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Form untuk Kas Keluar -->
                <form action="{{ route('uangkas.kasKeluar') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="kasKeluarModalLabel">Catat Kas Keluar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Input Nominal -->
                        <div class="mb-3">
                            <label for="nominalKeluar" class="form-label">Nominal</label>
                            <input type="number" name="nominal" id="nominalKeluar" class="form-control"
                                placeholder="Nominal Kas Keluar" required>
                        </div>
                        <!-- Input Catatan -->
                        <div class="mb-3">
                            <label for="catatanKeluar" class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatanKeluar" class="form-control" placeholder="Alasan Mengeluarkan Kas"
                                rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Buat modal untuk setiap siswa melalui loop -->
    @foreach ($siswa as $item)
        <div class="modal fade" id="bayarKasModal-{{ $item->id }}" tabindex="-1"
            aria-labelledby="bayarKasModalLabel-{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('uangkas.kasMasuk', $item->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="bayarKasModalLabel-{{ $item->id }}">
                                Bayar Kas: {{ $item->nama }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <!-- Input Nominal -->
                            <div class="mb-3">
                                <label for="nominalBayar-{{ $item->id }}" class="form-label">Nominal</label>
                                <input type="number" name="nominal" id="nominalBayar-{{ $item->id }}"
                                    class="form-control" placeholder="Nominal Bayar Kas" required>
                            </div>

                            {{-- <!-- Dropdown for Bayar Minggu ke Berapa -->
                            <div class="mb-3">
                                <label for="bayarMingguKe-{{ $item->id }}" class="form-label">Bayar Minggu ke</label>
                                <select id="bayarMingguKe-{{ $item->id }}" name="bayarMingguKe" class="form-select">
                                    <option value="hariini" selected>Bayar Minggu Ini</option>
                                    @for ($i = 2; $i <= 52; $i++)
                                        <option value="{{ $i }}">Minggu ke-{{ $i }}</option>
                                    @endfor
                                </select>
                            </div> --}}
                            <!-- Input Catatan -->
                            <div class="mb-3">
                                <label for="catatanBayar-{{ $item->id }}" class="form-label">Catatan</label>
                                <textarea name="catatan" id="catatanBayar-{{ $item->id }}" class="form-control" placeholder="Catatan Bayar Kas"
                                    rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Bayar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach


    <!-- Skrip JavaScript untuk Fitur Pencarian -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('#siswaTable tbody tr');

            searchInput.addEventListener('keyup', function() {
                const query = searchInput.value.toLowerCase();
                tableRows.forEach(function(row) {
                    const namaCell = row.querySelector('.nama').textContent.toLowerCase();
                    row.style.display = namaCell.includes(query) ? '' : 'none';
                });
            });
        });
    </script>
@endsection
