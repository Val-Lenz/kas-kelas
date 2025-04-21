<?php
use App\Http\Controllers\admin\DashboardAdminController;
use App\Http\Controllers\admin\LaporanAdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UangKasController;
use App\Http\Controllers\admin\UserController;
use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return redirect()->route('login');
});


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard untuk admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardAdminController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/bendahara', [UserController::class, 'index'])->name('admin.bendahara');
    Route::post('/admin/bendahara', [UserController::class, 'store'])->name('admin.bendahara.store');
    Route::put('/admin/bendahara/{bendahara}', [UserController::class, 'update'])->name('admin.bendahara.update');
    Route::delete('/admin/bendahara/{bendahara}', [UserController::class, 'destroy'])->name('admin.bendahara.destroy');

    Route::get('/admin/laporan', [LaporanAdminController::class, 'index'])->name('admin.laporan');

    //Halaman Laporan Kas Masuk
    Route::get('admin/laporan/kasmasuk/{id}', action: [LaporanAdminController::class, 'laporanKasMasuk'])
        ->name('admin.laporan.kasmasuk');

    //Halaman Laporan Kas Masuk
    Route::get('admin/laporan/kaskeluar/{id}', action: [LaporanAdminController::class, 'laporanKaskeluar'])
        ->name('admin.laporan.kaskeluar');
});

// Dashboard untuk bendahara
Route::middleware(['auth', 'role:bendahara'])->group(function () {
    // Halaman Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Halaman Data Siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa');
    Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/{siswa}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');

    //Halaman Uang Kas
    Route::get('/uang-kas', action: [UangKasController::class, 'index'])->name('uangkas');
    Route::post('/uang-kas/kasKeluar', [UangKasController::class, 'kasKeluar'])->name('uangkas.kasKeluar');
    Route::post('/uang-kas/{id}/kasMasuk', [UangKasController::class, 'kasMasuk'])->name('uangkas.kasMasuk');

    Route::put('/kasMasuk/update/{id}', [UangKasController::class, 'updateKasMasuk'])->name('kasMasuk.update');
    Route::delete('/kasMasuk/delete/{id}', [UangKasController::class, 'deleteKasMasuk'])->name('kasMasuk.delete');

    //Halaman Laporan Kas Masuk
    Route::get('laporan/kasmasuk', action: [LaporanController::class, 'laporanKasMasuk'])
        ->name('laporan.kasmasuk');

    //Halaman Laporan Kas Masuk
    Route::get('laporan/kaskeluar', action: [LaporanController::class, 'laporanKaskeluar'])
        ->name('laporan.kaskeluar');
    Route::put('/kaskeluar/update/{id}', [LaporanController::class, 'updateKasKeluar'])->name('kaskeluar.update');
    Route::delete('/kaskeluar/delete/{id}', [LaporanController::class, 'deleteKasKeluar'])->name('kaskeluar.delete');

});