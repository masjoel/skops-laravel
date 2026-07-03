<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TahunAjaranFactory extends Factory
{
    protected $model = \App\Models\TahunAjaran::class;

    public function definition(): array
    {
        $tahunMulai = $this->faker->numberBetween(2023, 2026);

        return [
            'nama' => $tahunMulai . '/' . ($tahunMulai + 1),
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
