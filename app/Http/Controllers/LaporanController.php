<?php

namespace App\Http\Controllers;

use App\Models\KasKeluar;
use App\Models\Siswa;
use App\Models\KasMasuk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function laporanKasMasuk(Request $request)
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
        $siswa = Siswa::with(['kasMasuk' => function ($query) use ($firstDay, $lastDay) {
            $query->whereBetween('created_at', [$firstDay, $lastDay]);
        }])
        ->where('id_user', Auth::id())
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
            ->whereHas('siswa', function ($query) {
                $query->where('id_user', Auth::id());
            })
            ->sum('nominal');
    
        // Retrieve unique years based on kasMasuk records
        $availableYears = KasMasuk::selectRaw('YEAR(DATE_SUB(created_at, INTERVAL (WEEKDAY(created_at) - 1) DAY)) as iso_year')
            ->distinct()
            ->orderBy('iso_year', 'desc')
            ->pluck('iso_year');
    
        return view('bendahara.laporan.kasmasuk', compact('siswa', 'availableYears', 'year', 'weeks', 'totalKasMasuk', 'search'));
    }
    
    // Method untuk halaman laporan kas keluar
    public function laporanKasKeluar(Request $request)
    {
        $year = $request->input('year', now()->year);
        $search = $request->input('search'); // Ambil input pencarian
    
        // Tentukan rentang tanggal untuk tahun yang dipilih
        $firstDay = now()->create($year, 1, 1)->startOfYear();
        $lastDay = now()->create($year, 12, 31)->endOfYear();
    
        // Ambil data Kas Keluar berdasarkan filter nama dan tahun
        $kasKeluar = KasKeluar::whereBetween('created_at', [$firstDay, $lastDay])
            ->where('id_user', Auth::id())
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
    
        return view('bendahara.laporan.kaskeluar', compact('kasKeluar', 'totalKasKeluar', 'jumlahKasKeluar', 'availableYears', 'year', 'search'));
    }

    // Method untuk update data kas keluar
    public function updateKasKeluar(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric',
            'catatan' => 'required|string|max:255',
        ]);
    
        // Exclude _token from request data
        $data = $request->except('_token');
    
        // Update the record
        $kasKeluar = KasKeluar::findOrFail($id);
        $kasKeluar->update($data);
    
        return redirect()->route('laporan.kaskeluar')->with('success', 'Data kas keluar berhasil diubah!');
    }

    // Method untuk delete data kas keluar
    public function deleteKasKeluar($id)
    {
        $kasKeluar = KasKeluar::findOrFail($id);
        $kasKeluar->delete();

        return redirect()->route('laporan.kaskeluar')->with('success', 'Data kas keluar berhasil dihapus!');
    }


}