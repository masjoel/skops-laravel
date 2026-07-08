<?php

namespace Database\Seeders;

use App\Models\JabatanStruktural;
use Illuminate\Database\Seeder;

class JabatanStrukturalSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_jabatan' => 'Kepala Sekolah', 'kategori' => 'Struktural'],
            ['nama_jabatan' => 'Wakil Kepala Sekolah', 'kategori' => 'Struktural'],
            ['nama_jabatan' => 'Kepala Tata Usaha', 'kategori' => 'Struktural'],
            ['nama_jabatan' => 'Guru BK', 'kategori' => 'Fungsional'],
            ['nama_jabatan' => 'Guru Kelas', 'kategori' => 'Fungsional'],
            ['nama_jabatan' => 'Guru Mata Pelajaran', 'kategori' => 'Fungsional'],
            ['nama_jabatan' => 'Wali Kelas', 'kategori' => 'Tugas Tambahan'],
            ['nama_jabatan' => 'Ketua Program Keahlian', 'kategori' => 'Struktural'],
            ['nama_jabatan' => 'Operator Sekolah', 'kategori' => 'Administrasi'],
            ['nama_jabatan' => 'Bendahara', 'kategori' => 'Administrasi'],
            ['nama_jabatan' => 'Pembina OSIS', 'kategori' => 'Tugas Tambahan'],
            ['nama_jabatan' => 'Pembina Ekstrakurikuler', 'kategori' => 'Tugas Tambahan'],
            ['nama_jabatan' => 'Ketua Laboratorium', 'kategori' => 'Tugas Tambahan'],
            ['nama_jabatan' => 'Kepala Perpustakaan', 'kategori' => 'Tugas Tambahan'],
            ['nama_jabatan' => 'Ketua Program Keahlian (SMK)', 'kategori' => 'Tugas Tambahan'],
        ];

        foreach ($data as $item) {
            JabatanStruktural::create($item);
        }
    }
}
