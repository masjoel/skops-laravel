<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_kelas' => '7A', 'tingkat' => '7'],
            ['nama_kelas' => '7B', 'tingkat' => '7'],
            ['nama_kelas' => '8A', 'tingkat' => '8'],
            ['nama_kelas' => '8B', 'tingkat' => '8'],
            ['nama_kelas' => '9A', 'tingkat' => '9'],
            ['nama_kelas' => '9B', 'tingkat' => '9'],
        ];

        foreach ($data as $kelas) {
            Kelas::create($kelas);
        }
    }
}
