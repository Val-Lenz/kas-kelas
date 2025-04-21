<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    // Tampilkan daftar siswa
    public function index()
    {
        // Ambil data siswa yang hanya dibuat oleh user yang sedang login beserta relasi user dan kasMasuk
        $siswa = Siswa::with('user', 'kasMasuk')
            ->where('id_user', Auth::id())
            ->orderBy('nama', 'asc')
            ->get();

        // Hitung total kas keluar dari tabel kas_keluar
        $totalKeluar = DB::table('kas_keluar')->sum('nominal');
        $siswaCount = $siswa->count();
        $perStudentDeduction = $siswaCount > 0 ? $totalKeluar / $siswaCount : 0;

        // Tambahkan atribut 'kasSiswa' ke masing-masing objek siswa
        $siswaWithCash = $siswa->map(function ($item) use ($perStudentDeduction) {
            $kasMasuk = $item->kasMasuk->sum('nominal');
            $item->kasSiswa = $kasMasuk - $perStudentDeduction;
            return $item;
        });

        // Cari nilai kas maksimum dan kelompokkan siswa dengan nilai tersebut
        $maxKas = $siswaWithCash->max('kasSiswa');
        $topStudents = $siswaWithCash->filter(function ($item) use ($maxKas) {
            return $item->kasSiswa == $maxKas;
        });
        $topNames = $topStudents->count() <= 3
            ? implode(', ', $topStudents->pluck('nama')->toArray())
            : 'Lebih Dari 3';

        // Cari nilai kas minimum dan kelompokkan siswa dengan nilai tersebut
        $minKas = $siswaWithCash->min('kasSiswa');
        $bottomStudents = $siswaWithCash->filter(function ($item) use ($minKas) {
            return $item->kasSiswa == $minKas;
        });
        $bottomNames = $bottomStudents->count() <= 3
            ? implode(', ', $bottomStudents->pluck('nama')->toArray())
            : 'Lebih Dari 3';

        return view('bendahara.siswa', compact('siswa', 'perStudentDeduction', 'topNames', 'bottomNames', 'maxKas', 'minKas'));
    }


    // Simpan siswa baru
    public function store(Request $request)
    {
        // Validasi data input, tidak perlu menerima id_user karena diisi otomatis
        $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|unique:siswa,nis|max:20',
        ]);

        // Buat data siswa baru dengan id_user diisi dengan ID pengguna yang sedang login
        Siswa::create([
            'nama' => $request->nama,
            'nis' => $request->nis,
            'id_user' => Auth::id(),
        ]);

        return redirect()->route('siswa')->with('success', 'Siswa berhasil ditambahkan!');
    }

    // Update data siswa
    public function update(Request $request, Siswa $siswa)
    {
        // Validasi data input, kecuali id_user yang tidak diubah
        $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:siswa,nis,' . $siswa->id,
        ]);

        $siswa->update([
            'nama' => $request->nama,
            'nis' => $request->nis,
        ]);

        return redirect()->route('siswa')->with('success', 'Data siswa berhasil diperbarui!');
    }

    // Hapus data siswa
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('siswa')->with('success', 'Siswa berhasil dihapus!');
    }
}
