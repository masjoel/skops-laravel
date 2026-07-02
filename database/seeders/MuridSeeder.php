<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Murid;
use App\Models\MuridKelas;
use App\Models\OrangTua;
use Illuminate\Database\Seeder;

class MuridSeeder extends Seeder
{
    public function run(): void
    {
        $kelasList = Kelas::all();

        Murid::factory()
            ->count(40)
            ->create()
            ->each(function (Murid $murid) use ($kelasList) {
                // User login murid
                User::factory()
                    ->murid()
                    ->create([
                        'personil_id' => $murid->personil_id,
                        'username' => 'murid' . $murid->id,
                    ]);

                // tempatkan murid ke salah satu kelas untuk tahun ajaran berjalan
                MuridKelas::create([
                    'murid_id' => $murid->id,
                    'kelas_id' => $kelasList->random()->id,
                    'tahun_ajaran' => '2025/2026',
                ]);

                // buat 1 atau 2 orang tua/wali untuk murid ini
                $jumlahOrtu = fake()->randomElement([1, 2]);
                $hubunganTersedia = fake()->randomElement([
                    ['Ayah', 'Ibu'],
                    ['Wali'],
                ]);

                foreach (array_slice($hubunganTersedia, 0, $jumlahOrtu) as $hubungan) {
                    $orangTua = OrangTua::factory()->create();

                    User::factory()
                        ->orangTua()
                        ->create([
                            'personil_id' => $orangTua->personil_id,
                            'username' => 'ortu' . $orangTua->id,
                        ]);

                    $murid->orangTua()->attach($orangTua->id, [
                        'hubungan' => $hubungan,
                    ]);
                }
            });
    }
}
