<?php

namespace Database\Factories;

use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class WaliKelasFactory extends Factory
{
    protected $model = \App\Models\WaliKelas::class;

    public function definition(): array
    {
        return [
            'kelas_id' => Kelas::factory(),
            'guru_id' => Guru::factory(),
            'tahun_ajaran' => '2025/2026',
        ];
    }
}
