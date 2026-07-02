<?php

namespace Database\Factories;

use App\Models\Personil;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrangTuaFactory extends Factory
{
    protected $model = \App\Models\OrangTua::class;

    public function definition(): array
    {
        return [
            'personil_id' => Personil::factory(),
        ];
    }
}
