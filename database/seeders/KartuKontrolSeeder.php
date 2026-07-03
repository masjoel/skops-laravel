<?php

namespace Database\Seeders;

use App\Models\JenisPoin;
use App\Models\KartuKontrol;
use App\Models\MuridKelas;
use App\Models\PeriodeAkademik;
use App\Models\WaliKelas;
use Illuminate\Database\Seeder;

class KartuKontrolSeeder extends Seeder
{
    public function run(): void
    {
        $jenisPoinList = JenisPoin::all();
        $periodeAkademikList = PeriodeAkademik::all();

        MuridKelas::with('kelas')->get()->each(function (MuridKelas $muridKelas) use ($jenisPoinList, $periodeAkademikList) {
            // setiap murid dapat 0-4 catatan poin secara acak,
            // supaya datanya bervariasi (ada yang bersih, ada yang tidak)
            $jumlahCatatan = fake()->numberBetween(0, 4);

            if ($jumlahCatatan === 0) {
                return;
            }

            $waliKelas = WaliKelas::where('kelas_id', $muridKelas->kelas_id)
                ->where('tahun_ajaran_id', $muridKelas->tahun_ajaran_id)
                ->first();

            // ambil periode akademik yang cocok dengan tahun ajaran
            // riwayat kelas ini (bukan selalu periode aktif), supaya
            // data tahun lalu tercatat di semester tahun lalu juga
            $periodeCocok = $periodeAkademikList
                ->where('tahun_ajaran_id', $muridKelas->tahun_ajaran_id)
                ->random();

            for ($i = 0; $i < $jumlahCatatan; $i++) {
                $jenisPoin = $jenisPoinList->random();

                KartuKontrol::create([
                    'user_id' => null,
                    'guru_id' => $waliKelas?->guru_id,
                    'murid_kelas_id' => $muridKelas->id,
                    'jenis_poin_id' => $jenisPoin->id,
                    'periode_akademik_id' => $periodeCocok->id,
                    'tgl' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                    'skor' => $jenisPoin->skor,
                    'tindakan' => $jenisPoin->tindakan,
                    'is_reset' => false,
                ]);
            }
        });
    }
}
