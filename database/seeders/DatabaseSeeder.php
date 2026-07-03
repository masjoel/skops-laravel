<?php

namespace Database\Seeders;

use App\Models\Perusahaan;
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
        Perusahaan::create([
            'nama_client' => 'SKOPS',
            'nama_app' => 'Reward Point',
            'versi_app' => '1.0',
            'desc_app' => 'Aplikasi Reward Point',
            'alamat_client' => 'Jl. Raya No.1',
            'signature' => 'Kepala Sekolah',
            'email' => 'sekolah@sekolah.com',
            'logo' => 'images/skops-logo.webp',
            'image_icon' => 'images/skops-logo.webp',
            'mcad' => null,
            'init' => null,
            'bank' => null,
            'footnot' => null,
            'jdigit' => 0,
            'jdelay' => 0,
        ]);

        User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@skops.web.id',
            'password' => bcrypt('password'),
            'name' => 'Administrator',
            'photo' => 'images/skops-logo.webp',
            'role' => 'admin',
        ]);
        $this->call([
            TahunAjaranSeeder::class,
            KelasSeeder::class,
            GuruSeeder::class,
            WaliKelasSeeder::class,
            MuridSeeder::class,
            JenisPoinSeeder::class,
            KartuKontrolSeeder::class,
        ]);
    }
}
