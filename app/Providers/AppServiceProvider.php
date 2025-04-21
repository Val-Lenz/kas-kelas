<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot()
    {
        // Ini akan diterapkan ke semua view (gunakan '*' atau tentukan view yang spesifik)
        View::composer('*', function ($view) {
            $totalKas = 0;

            // Pastikan data user telah tersedia
            if (Auth::check()) {
                $userId = Auth::id();

                $totalKasMasuk = DB::table('kas_masuk')
                    ->join('siswa', 'kas_masuk.id_siswa', '=', 'siswa.id')
                    ->where('siswa.id_user', $userId)
                    ->sum('kas_masuk.nominal');

                $totalKasKeluar = DB::table('kas_keluar')
                    ->where('id_user', $userId)
                    ->sum('nominal');

                $totalKas = $totalKasMasuk - $totalKasKeluar;
            }

            $view->with('totalKas', $totalKas);
        });
    }
}
