<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Murid;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class MuridKelasFactory extends Factory
{
    protected $model = \App\Models\MuridKelas::class;

    public function definition(): array
    {
        return [
            'murid_id' => Murid::factory(),
            'kelas_id' => Kelas::factory(),
            'tahun_ajaran_id' => TahunAjaran::factory(),
        ];
    }
}
