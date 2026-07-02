<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PersonilFactory extends Factory
{
    protected $model = \App\Models\Personil::class;

    public function definition(): array
    {
        $jenisKelamin = $this->faker->randomElement(['L', 'P']);

        return [
            'nama' => $jenisKelamin === 'L'
                ? $this->faker->name('male')
                : $this->faker->name('female'),
            'jenis_kelamin' => $jenisKelamin,
            'alamat' => $this->faker->address(),
            'no_hp' => $this->faker->unique()->numerify('08##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'foto' => null,
            'status' => 'aktif',
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'nonaktif',
        ]);
    }
}
