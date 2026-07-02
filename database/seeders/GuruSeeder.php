<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru;
use Illuminate\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        Guru::factory()
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
    }
}
