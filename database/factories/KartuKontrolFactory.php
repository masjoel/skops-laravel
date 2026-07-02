<?php

namespace Database\Factories;

use App\Models\Guru;
use App\Models\JenisPoin;
use App\Models\MuridKelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class KartuKontrolFactory extends Factory
{
    protected $model = \App\Models\KartuKontrol::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'guru_id' => Guru::factory(),
            'murid_kelas_id' => MuridKelas::factory(),
            'jenis_poin_id' => JenisPoin::factory(),
            'tgl' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'skor' => $this->faker->numberBetween(-25, 20),
            'tindakan' => $this->faker->sentence(),
            'semester' => $this->faker->randomElement([1, 2]),
            'is_reset' => false,
        ];
    }
}
