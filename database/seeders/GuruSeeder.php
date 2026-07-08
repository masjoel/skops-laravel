<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru;
use App\Models\JabatanStruktural;
use Illuminate\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $guruList = Guru::factory()
            ->count(10)
            ->create()
            ->each(function (Guru $guru) {
                User::factory()
                    ->guru()
                    ->create([
                        'personil_id' => $guru->personil_id,
                        'username' => 'guru' . $guru->id,
                    ]);
            });

        // Berikan jabatan struktural ke beberapa guru pertama, supaya ada
        // data siap pakai untuk alur Pemanggilan BK (butuh minimal 1 Guru BK).
        $jabatanList = [
            'Kepala Sekolah',
            'Wakil Kepala Sekolah',
            'Guru BK',
        ];

        foreach ($jabatanList as $i => $namaJabatan) {
            $jabatan = JabatanStruktural::where('nama_jabatan', $namaJabatan)->first();

            if ($jabatan && $guruList->has($i)) {
                $guruList[$i]->update(['jabatan_struktural_id' => $jabatan->id]);
            }
        }
    }
}
