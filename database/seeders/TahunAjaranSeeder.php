<?php

namespace Database\Seeders;

use App\Models\PeriodeAkademik;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        // Tahun ajaran lalu (sudah selesai, kedua semesternya non-aktif)
        $tahunLalu = TahunAjaran::create([
            'nama' => '2024/2025',
            'is_aktif' => false,
        ]);

        PeriodeAkademik::create([
            'tahun_ajaran_id' => $tahunLalu->id,
            'semester' => 1,
            'is_aktif' => false,
        ]);

        PeriodeAkademik::create([
            'tahun_ajaran_id' => $tahunLalu->id,
            'semester' => 2,
            'is_aktif' => false,
        ]);

        // Tahun ajaran berjalan (aktif, semester 1 aktif)
        $tahunAktif = TahunAjaran::create([
            'nama' => '2025/2026',
            'is_aktif' => true,
        ]);

        PeriodeAkademik::create([
            'tahun_ajaran_id' => $tahunAktif->id,
            'semester' => 1,
            'is_aktif' => false,
        ]);

        PeriodeAkademik::create([
            'tahun_ajaran_id' => $tahunAktif->id,
            'semester' => 2,
            'is_aktif' => true,
        ]);
    }
}
