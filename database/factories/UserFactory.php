<?php

namespace Database\Factories;

use App\Models\Personil;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'personil_id' => Personil::factory(),
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('password'), // default: "password"
            // 'role' => $this->faker->randomElement(['guru', 'murid', 'orang_tua']),
            'role' => $this->faker->randomElement(['guru']),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'status' => 1,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'administrator',
        ]);
    }

    public function guru(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'guru',
        ]);
    }

    public function murid(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'murid',
        ]);
    }

    public function orangTua(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'orang_tua',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
