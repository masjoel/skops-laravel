<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use App\Models\User;
use Database\Seeders\TahunAjaranSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Sekolah::create([
            'nama_client' => 'SKOPS',
            'nama_app' => 'Sistem Kelola Operasional Sekolah',
            'versi_app' => '1.0',
            'desc_app' => 'Aplikasi Sistem Kelola Operasional Sekolah',
            'alamat_client' => 'Jl. Raya No.1',
            'signature' => 'Kepala Sekolah',
            'email' => 'sekolah@sekolah.com',
            'logo' => 'images/skops-logo.webp',
            'image_icon' => 'images/skops-logo.webp',
            'npsn' => 000000000,
            'telpon' => 1234567890,
            'mcad' => null,
            'init' => null,
            'bank' => null,
            'footnot' => null,
            'jdigit' => 100,
            'jdelay' => 0,
        ]);

        User::factory()->create([
            'username' => 'demo',
            'email' => 'admin@skops.web.id',
            'password' => bcrypt('demo'),
            'name' => 'Administrator',
            'photo' => 'images/skops-logo.webp',
            'role' => 'administrator',
            'status' => 1,
        ]);
        $this->call([
            TahunAjaranSeeder::class,
            KelasSeeder::class,
            JabatanStrukturalSeeder::class,
            GuruSeeder::class,
            WaliKelasSeeder::class,
            MuridSeeder::class,
            JenisPoinSeeder::class,
            KartuKontrolSeeder::class,
        ]);
    }
}
