@extends('layouts.app')

@section('content')
    <div class="row mb-4">
        <!-- Card Total Siswa -->
        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center text-primary">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h5 class="card-title">Total Siswa</h5>
                    <b class="card-text text-primary fs-5">{{ $siswa->count() }}</b>
                </div>
            </div>
        </div>

        <!-- Card Siswa dengan Kas Terbanyak -->
        <div class="col-md-4">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center text-success">
                    <i class="fas fa-money-check-alt fa-3x mb-3"></i>
                    <h5 class="card-title">Siswa Kas Terbanyak</h5>
                    <p class="card-text text-success fs-5">{{ $topNames }}</p>
                </div>
            </div>
        </div>

        <!-- Card Siswa dengan Kas Tersedikit -->
        <div class="col-md-4">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center text-danger">
                    <i class="fas fa-money-check-alt fa-3x mb-3"></i>
                    <h5 class="card-title">Siswa Kas Tersedikit</h5>
                    <p class="card-text text-danger fs-5">{{ $bottomNames }}</p>
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

            <!-- Tambah Siswa -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Tambah Siswa
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

        <!-- Table -->
        <table class="table table-bordered table-hover text-center" id="siswaTable">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th class="text-start">Nama</th>
                    <th>NIS</th>
                    <th class="text-end" >Kas Siswa Rp</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <tbody>
                @foreach ($siswa as $item)
                    @php
                        // Hitung kas masuk untuk masing-masing siswa
                        $kasMasuk = $item->kasMasuk->sum('nominal');
                        // Kas Siswa = kas masuk - potongan rata kas keluar
                        $kasSiswa = $kasMasuk - $perStudentDeduction;
                        // Tentukan kelas baris: hijau untuk kas terbanyak, merah untuk kas tersedikit
                        $rowClass = '';
                        if ($kasSiswa == $maxKas) {
                            $rowClass = 'table-success';
                        } elseif ($kasSiswa == $minKas) {
                            $rowClass = 'table-danger';
                        }
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $loop->iteration }}</td>
                        <td class="nama text-start">{{ $item->nama }}</td>
                        <td>{{ $item->nis }}</td>
                        <td class="text-end">{{ number_format($kasSiswa, 0, ',', '.') }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editModal-{{ $item->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('siswa.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('siswa.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Tambah Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nis" class="form-label">NIS</label>
                            <input type="number" name="nis" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    @foreach ($siswa as $item)
        <div class="modal fade" id="editModal-{{ $item->id }}" tabindex="-1"
            aria-labelledby="editModalLabel-{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('siswa.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel-{{ $item->id }}">Edit Siswa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" name="nama" class="form-control" value="{{ $item->nama }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="nis" class="form-label">NIS</label>
                                <input type="number" name="nis" class="form-control" value="{{ $item->nis }}"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('#siswaTable tbody tr');

            searchInput.addEventListener('keyup', function() {
                const query = searchInput.value.toLowerCase();

                tableRows.forEach(row => {
                    const nama = row.querySelector('.nama').textContent.toLowerCase();
                    row.style.display = nama.includes(query) ? '' : 'none';
                });
            });
        });
    </script>
@endsection
