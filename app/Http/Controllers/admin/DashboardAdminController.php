<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $totalKasMasuk = KasMasuk::sum('nominal'); // Total Kas Masuk
        $totalKasKeluar = KasKeluar::sum('nominal'); // Total Kas Keluar
        $saldoKas = $totalKasMasuk - $totalKasKeluar; // Saldo Kas (Kas Masuk - Kas Keluar)
    
        $totalUsers = User::where('role', 'bendahara')->count(); // Total pengguna dengan role bendahara
        $totalSiswa = Siswa::count(); // Total siswa
    
        // Ambil 5 bendahara dengan kas terbanyak
        $topBendahara = User::where('role', 'bendahara')
            ->get()
            ->map(function ($bendahara) {
                $totalKasMasukUser = KasMasuk::whereHas('siswa', function ($query) use ($bendahara) {
                    $query->where('id_user', $bendahara->id);
                })->sum('nominal');
    
                $totalKasKeluarUser = KasKeluar::where('id_user', $bendahara->id)->sum('nominal');
                $bendahara->total_kas = $totalKasMasukUser - $totalKasKeluarUser;
    
                return $bendahara;
            })
            ->sortByDesc('total_kas')
            ->take(5);
    
        return view('admin.dashboard', compact(
            'totalKasMasuk',
            'totalKasKeluar',
            'saldoKas',
            'totalUsers',
            'totalSiswa',
            'topBendahara'
        ));
    }
}