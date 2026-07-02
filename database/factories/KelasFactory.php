<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class KelasFactory extends Factory
{
    protected $model = \App\Models\Kelas::class;

    public function definition(): array
    {
        $tingkat = $this->faker->randomElement(['7', '8', '9']);
        $rombel = $this->faker->randomElement(['A', 'B', 'C']);

        return [
            'nama_kelas' => $tingkat . $rombel,
            'tingkat' => $tingkat,
        ];
    }
}
