<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Murid;
use App\Models\MuridKelas;
use App\Models\OrangTua;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;

class MuridSeeder extends Seeder
{
    public function run(): void
    {
        $kelasList = Kelas::all();
        $tahunAktif = TahunAjaran::aktif();
        $tahunLalu = TahunAjaran::where('id', '!=', $tahunAktif->id)->first();

        Murid::factory()
            ->count(5)
            ->create()
            ->each(function (Murid $murid) use ($kelasList, $tahunAktif, $tahunLalu) {
                // akun login murid
                User::factory()
                    ->murid()
                    ->create([
                        'personil_id' => $murid->personil_id,
                        'username' => 'murid' . $murid->id,
                    ]);

                // tempatkan murid ke salah satu kelas untuk tahun ajaran berjalan
                $kelasSekarang = $kelasList->random();

                MuridKelas::create([
                    'murid_id' => $murid->id,
                    'kelas_id' => $kelasSekarang->id,
                    'tahun_ajaran_id' => $tahunAktif->id,
                ]);

                // KHUSUS DEMO KENAIKAN KELAS:
                // kalau murid sekarang ada di tingkat 8 atau 9, berarti dulu
                // (tahun ajaran sebelumnya) dia ada di satu tingkat di bawahnya
                // dengan rombel yang sama. Contoh: sekarang 8A -> dulu 7A.
                if (in_array($kelasSekarang->tingkat, ['8', '9'])) {
                    $rombel = substr($kelasSekarang->nama_kelas, -1); // ambil huruf terakhir, misal "A"
                    $tingkatSebelumnya = (string) ((int) $kelasSekarang->tingkat - 1);
                    $namaKelasSebelumnya = $tingkatSebelumnya . $rombel; // misal "7A"

                    $kelasSebelumnya = $kelasList->firstWhere('nama_kelas', $namaKelasSebelumnya);

                    if ($kelasSebelumnya) {
                        MuridKelas::create([
                            'murid_id' => $murid->id,
                            'kelas_id' => $kelasSebelumnya->id,
                            'tahun_ajaran_id' => $tahunLalu->id,
                        ]);
                    }
                }

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
