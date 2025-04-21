<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KasMasuk;
use Carbon\Carbon;

class KasMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $studentId = 2; // Target student ID

        // Loop through weeks 1 to 52 (for the whole year)
        for ($week = 1; $week <= 52; $week++) {
            KasMasuk::create([
                'id_siswa' => $studentId,
                'nominal' => 10000, // Random nominal between 10,000
                'catatan' => 'Kas masuk untuk minggu ke-' . $week,
                'created_at' => Carbon::now()->startOfYear()->addWeeks($week - 1)->startOfWeek(), // Set to the start of the specific week
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}