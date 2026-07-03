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
        $tahunAjaranId = TahunAjaran::aktif()->id;

        Kelas::all()->each(function (Kelas $kelas) use (&$guruIds, $tahunAjaranId) {
            // ambil satu guru unik untuk tiap kelas, jangan sampai
            // habis kalau guru lebih sedikit dari kelas
            if ($guruIds->isEmpty()) {
                $guruIds = Guru::pluck('id')->shuffle();
            }

            WaliKelas::create([
                'kelas_id' => $kelas->id,
                'guru_id' => $guruIds->pop(),
                'tahun_ajaran_id' => $tahunAjaranId,
            ]);
        });
    }
}
