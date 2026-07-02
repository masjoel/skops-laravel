<?php

namespace Database\Factories;

use App\Models\Personil;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuruFactory extends Factory
{
    protected $model = \App\Models\Guru::class;

    public function definition(): array
    {
        return [
            'personil_id' => Personil::factory(),
            'nip' => $this->faker->unique()->numerify('19########0#1###'),
        ];
    }
}
