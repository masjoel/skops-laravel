<?php

namespace Database\Factories;

use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodeAkademikFactory extends Factory
{
    protected $model = \App\Models\PeriodeAkademik::class;

    public function definition(): array
    {
        return [
            'tahun_ajaran_id' => TahunAjaran::factory(),
            'semester' => $this->faker->randomElement([1, 2]),
            'is_aktif' => false,
        ];
    }

    public function aktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_aktif' => true,
        ]);
    }
}
