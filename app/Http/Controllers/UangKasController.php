<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\KasMasuk;   // Model untuk tabel kas_masuk
use App\Models\KasKeluar;  // Model untuk tabel kas_keluar
use Illuminate\Support\Facades\Auth;

class UangKasController extends Controller
{
    public function index()
    {
        $currentWeekStart = now()->startOfWeek()->toDateString();
        $currentWeekEnd = now()->endOfWeek()->toDateString();

        // Get student data for the currently logged-in user and eager load kasMasuk relation
        $siswa = Siswa::with('kasMasuk')
            ->where('id_user', Auth::id())
            ->orderBy('nama')
            ->get();

        // Calculate total kas keluar
        $totalKeluar = KasKeluar::where('id_user', Auth::id())->sum('nominal');

        // Primary variables
        $siswaIds = $siswa->pluck('id');
        $siswaCount = $siswa->count();
        $perStudentDeduction = $siswaCount ? $totalKeluar / $siswaCount : 0;

        // Weekly data for kas masuk
        // Fetch IDs of students who have paid kas this week
        $siswaYangBayarMingguIni = KasMasuk::whereIn('id_siswa', $siswaIds)
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->distinct('id_siswa')
            ->pluck('id_siswa');

        // Total kas masuk this week for all students
        $kasMasukMingguIni = KasMasuk::whereIn('id_siswa', $siswaIds)
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->sum('nominal');

        return view('bendahara.uangkas', [
            'siswa' => $siswa,
            'perStudentDeduction' => $perStudentDeduction,
            'belumBayar' => $siswaCount - $siswaYangBayarMingguIni->count(),
            'sudahBayar' => $siswaYangBayarMingguIni->count(),
            'kasMasukMingguIni' => $kasMasukMingguIni,
            'siswaYangBayarMingguIni' => $siswaYangBayarMingguIni->toArray(),
        ]);
    }

    public function kasKeluar(Request $request)
    {
        // Validasi input
        $request->validate([
            'nominal' => 'required|numeric|min:0|max:100000000',
            'catatan' => 'required|string',
        ]);

        // Membuat entri baru menggunakan model KasKeluar
        $kasKeluar = new KasKeluar();
        $kasKeluar->nominal = $request->nominal;
        $kasKeluar->catatan = $request->catatan;
        $kasKeluar->id_user = Auth::id(); // Menggunakan ID pengguna yang sedang login
        $kasKeluar->save();

        return redirect()->back()->with('success', 'Kas keluar berhasil dicatat!');
    }

    public function kasMasuk(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nominal' => 'required|numeric|min:0|max:100000000',
            'catatan' => 'nullable|string',
        ]);

        // Buat entri baru untuk kas masuk menggunakan model KasMasuk
        $kasMasuk = new KasMasuk();
        $kasMasuk->nominal = $request->nominal;
        $kasMasuk->catatan = $request->catatan;
        $kasMasuk->id_siswa = $id;
        $kasMasuk->save();

        return redirect()->back()->with('success', 'Kas masuk berhasil dicatat!');
    }
}