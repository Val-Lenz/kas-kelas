<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanAdminController extends Controller
{
    public function index()
    {
        // Mengambil data pengguna dengan role bendahara
        $users = User::where('role', 'bendahara')->get()->map(function ($user) {
            // Menghitung total kas masuk berdasarkan id_user dari tabel siswa
            $kasMasuk = KasMasuk::whereHas('siswa', function ($query) use ($user) {
                $query->where('id_user', $user->id);
            })->sum('nominal');
    
            // Menghitung total kas keluar berdasarkan id_user
            $kasKeluar = KasKeluar::where('id_user', $user->id)->sum('nominal');
    
            // Menghitung total siswa berdasarkan id_user
            $totalSiswa = Siswa::where('id_user', $user->id)->count();
    
            // Menambahkan properti kas dan total siswa ke objek pengguna
            $user->kas_masuk = $kasMasuk;
            $user->kas_keluar = $kasKeluar;
            $user->saldo_kas = $kasMasuk - $kasKeluar;
            $user->total_siswa = $totalSiswa; // Menambahkan total siswa
    
            return $user;
        });
    
        // Mengurutkan data berdasarkan angkatan dari terkecil ke terbesar
        $users = $users->sortBy('angkatan');
    
        // Menghitung total keseluruhan kas masuk, kas keluar, dan saldo kas
        $totalKasMasuk = $users->sum('kas_masuk');
        $totalKasKeluar = $users->sum('kas_keluar');
        $totalSaldoKas = $users->sum('saldo_kas');
    
        // Mengembalikan view dengan data pengguna, saldo kas, total keseluruhan, dan total siswa
        return view('admin.laporan', compact('users', 'totalKasMasuk', 'totalKasKeluar', 'totalSaldoKas'));
    }
    public function laporanKasMasuk(Request $request, $id)
    {
        // Ambil tahun dan pencarian dari input
        $year = $request->input('year', now()->year);
        $search = $request->input('search'); // Parameter pencarian nama

        // Generate weeks data
        $weeks = [];
        $currentWeek = Carbon::createFromDate($year, 1, 1)->startOfWeek(Carbon::MONDAY);
        $lastWeek = Carbon::createFromDate($year, 12, 31)->endOfWeek(Carbon::SUNDAY);

        while ($currentWeek->lte($lastWeek)) {
            $weeks[] = [
                'week' => $currentWeek->isoWeek(),
                'month' => $currentWeek->month,
                'year' => $currentWeek->year,
            ];
            $currentWeek->addWeek();
        }

        // Calculate date range for ISO year
        $firstDay = Carbon::createFromDate($year, 1, 1)->startOfWeek(Carbon::MONDAY);
        $lastDay = Carbon::createFromDate($year, 12, 31)->endOfWeek(Carbon::SUNDAY);

        // Retrieve student data and kasMasuk records based on filters
        $siswa = Siswa::with([
            'kasMasuk' => function ($query) use ($firstDay, $lastDay) {
                $query->whereBetween('created_at', [$firstDay, $lastDay]);
            }
        ])
            ->where('id_user', $id) // Mengambil data berdasarkan parameter $id
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', '%' . $search . '%'); // Filter berdasarkan nama siswa
            })
            ->orderBy('nama')
            ->get();

        // Group data per ISO week
        foreach ($siswa as $student) {
            $groupedKasMasuk = $student->kasMasuk->groupBy(function ($kas) {
                return $kas->created_at->isoWeek(); // Mengelompokkan berdasarkan minggu ISO
            });

            $student->kasMasuk = $groupedKasMasuk->map(function ($entries) {
                return [
                    'minggu_ke' => $entries->first()->created_at->isoWeek(),
                    'nominal' => $entries->sum('nominal'),
                ];
            })->values();
        }

        // Calculate total kasMasuk for the selected year
        $totalKasMasuk = KasMasuk::whereBetween('created_at', [$firstDay, $lastDay])
            ->whereHas('siswa', function ($query) use ($id) {
                $query->where('id_user', $id); // Ambil berdasarkan id pengguna
            })
            ->sum('nominal');

        // Retrieve unique years based on kasMasuk records
        $availableYears = KasMasuk::selectRaw('YEAR(DATE_SUB(created_at, INTERVAL (WEEKDAY(created_at) - 1) DAY)) as iso_year')
            ->distinct()
            ->orderBy('iso_year', 'desc')
            ->pluck('iso_year');

        return view('bendahara.laporan.kasmasuk', compact('id','siswa', 'availableYears', 'year', 'weeks', 'totalKasMasuk', 'search'));
    }
    public function laporanKasKeluar(Request $request, $id)
    {
        $year = $request->input('year', now()->year);
        $search = $request->input('search'); // Ambil input pencarian

        // Tentukan rentang tanggal untuk tahun yang dipilih
        $firstDay = now()->create($year, 1, 1)->startOfYear();
        $lastDay = now()->create($year, 12, 31)->endOfYear();

        // Ambil data Kas Keluar berdasarkan filter nama dan tahun
        $kasKeluar = KasKeluar::whereBetween('created_at', [$firstDay, $lastDay])
            ->where('id_user', $id) // Menggunakan $id sebagai parameter
            ->when($search, function ($query, $search) { // Jika ada pencarian
                $query->where('catatan', 'like', '%' . $search . '%'); // Sesuaikan kolom jika berbeda
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Hitung total nominal kas keluar
        $totalKasKeluar = $kasKeluar->sum('nominal');

        // Hitung jumlah kas keluar
        $jumlahKasKeluar = $kasKeluar->count();

        // Ambil tahun unik untuk filter
        $availableYears = KasKeluar::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('bendahara.laporan.kaskeluar', compact('id','kasKeluar', 'totalKasKeluar', 'jumlahKasKeluar', 'availableYears', 'year', 'search'));
    }
}
