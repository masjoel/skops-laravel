<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\WaliKelas;
use Illuminate\Database\Seeder;

class WaliKelasSeeder extends Seeder
{
    public function run(): void
    {
        $guruIds = Guru::pluck('id')->shuffle();

        // wali kelas dibuat untuk SEMUA tahun ajaran yang ada,
        // supaya data historis (tahun lalu) juga punya wali kelas
        TahunAjaran::all()->each(function (TahunAjaran $tahunAjaran) use (&$guruIds) {
            Kelas::all()->each(function (Kelas $kelas) use (&$guruIds, $tahunAjaran) {
                if ($guruIds->isEmpty()) {
                    $guruIds = Guru::pluck('id')->shuffle();
                }

                WaliKelas::create([
                    'kelas_id' => $kelas->id,
                    'guru_id' => $guruIds->pop(),
                    'tahun_ajaran_id' => $tahunAjaran->id,
                ]);
            });
        });
    }
}
