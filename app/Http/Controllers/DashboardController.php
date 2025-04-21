<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\KasMasuk;
use App\Models\KasKeluar;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKasMasuk = KasMasuk::whereHas('siswa', function ($query) {
            $query->where('id_user', Auth::id());
        })->sum('nominal');
    
        $totalKasKeluar = KasKeluar::where('id_user', Auth::id())->sum('nominal');
    
        $saldoKas = $totalKasMasuk - $totalKasKeluar;
    
        // $totalSiswa = Siswa::where('id_user', Auth::id())->count();
    
        // $recentKasMasuk = KasMasuk::whereHas('siswa', function ($query) {
        //     $query->where('id_user', Auth::id());
        // })->orderBy('created_at', 'desc')->take(5)->get();
    
        $recentKasKeluar = KasKeluar::where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')->take(5)->get();
    
        return view('bendahara.dashboard', compact('totalKasMasuk', 'totalKasKeluar', 'saldoKas', 'recentKasKeluar'));
    }
}