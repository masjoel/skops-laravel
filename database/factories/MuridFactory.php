<?php

namespace Database\Factories;

use App\Models\Personil;
use Illuminate\Database\Eloquent\Factories\Factory;

class MuridFactory extends Factory
{
    protected $model = \App\Models\Murid::class;

    public function definition(): array
    {
        return [
            'personil_id' => Personil::factory(),
            'nis' => $this->faker->unique()->numerify('##########'),
            'nisn' => $this->faker->unique()->numerify('##########'),
        ];
    }
}
