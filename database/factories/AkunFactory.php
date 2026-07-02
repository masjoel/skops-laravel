<?php

namespace Database\Factories;

use App\Models\Personil;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Str;

class AkunFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'personil_id' => Personil::factory(),
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('password'), // default: "password"
            'role' => $this->faker->randomElement(['guru', 'murid', 'orang_tua']),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    public function guru(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'guru',
        ]);
    }

    public function murid(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'murid',
        ]);
    }

    public function orangTua(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'orang_tua',
        ]);
    }
}
