@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-3 border-primary">
                <div class="ard-body d-flex align-items-end justify-content-center text-primary py-4">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <div class="m-2"></div>
                    <div>
                        <h5 class="card-title">Total Bendahara</h5>
                        <p class="fs-4 text-center my-0">{{ $users->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm mb-3 border-success">
                <div class="ard-body d-flex align-items-end justify-content-center text-success py-4">
                    <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                    <div class="m-2"></div>
                    <div>
                        <h5 class="card-title">Angkatan Terbaru</h5>
                        <p class="fs-4 text-center my-0">@if ($users->isNotEmpty())
                            {{ $users->max('angkatan') }}
                        @else
                            -
                        @endif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- Pencarian di Kiri -->
            <div class="input-group w-25">
                <input type="text" id="searchInput" class="form-control shadow-sm" placeholder="Cari nama bendahara...">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>

            <!-- Tombol Tambah di Kanan -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Bendahara
            </button>
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

        <!-- Table -->
        <table class="table table-bordered table-hover text-center" id="bendaharaTable">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th class="text-start">Nama</th>
                    <th>Angkatan</th>
                    <th>Jurusan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="nama text-start">{{ $user->nama }}</td>
                        <td>{{ $user->angkatan }}</td>
                        <td>{{ $user->jurusan }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editModal-{{ $user->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.bendahara.destroy', $user->id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('{{ __('Apakah Anda yakin ingin menghapus data ini?') }}')">
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
                <form action="{{ route('admin.bendahara.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Tambah Bendahara</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="angkatan" class="form-label">Angkatan</label>
                            <input type="number" name="angkatan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="jurusan" class="form-label">Jurusan</label>
                            <input type="text" name="jurusan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit: Satu modal per bendahara -->
    @foreach ($users as $user)
        <div class="modal fade" id="editModal-{{ $user->id }}" tabindex="-1"
            aria-labelledby="editModalLabel-{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.bendahara.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel-{{ $user->id }}">Edit Bendahara</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" name="nama" class="form-control" value="{{ $user->nama }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="angkatan" class="form-label">Angkatan</label>
                                <input type="number" name="angkatan" class="form-control"
                                    value="{{ $user->angkatan }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="jurusan" class="form-label">Jurusan</label>
                                <input type="text" name="jurusan" class="form-control" value="{{ $user->jurusan }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru (Opsional)</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="Kosongkan jika tidak ingin mengubah">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Ambil elemen input dan tabel
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('#bendaharaTable tbody tr');

            // Event listener untuk input
            searchInput.addEventListener('keyup', function() {
                const query = searchInput.value.toLowerCase();

                tableRows.forEach(row => {
                    const nama = row.querySelector('.nama').textContent.toLowerCase();

                    // Cek apakah teks pencarian cocok
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
