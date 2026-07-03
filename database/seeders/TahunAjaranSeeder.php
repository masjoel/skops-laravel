<?php

namespace Database\Seeders;

use App\Models\PeriodeAkademik;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaran = TahunAjaran::create([
            'nama' => '2025/2026',
            'is_aktif' => true,
        ]);

        $semester1 = PeriodeAkademik::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'semester' => 1,
            'is_aktif' => true,
        ]);

        PeriodeAkademik::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'semester' => 2,
            'is_aktif' => false,
        ]);
    }
}
