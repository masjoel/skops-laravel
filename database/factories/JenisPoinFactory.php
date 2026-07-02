<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JenisPoinFactory extends Factory
{
    protected $model = \App\Models\JenisPoin::class;

    public function definition(): array
    {
        $tipe = $this->faker->randomElement(['reward', 'pelanggaran']);

        $contohReward = [
            ['jenis' => 'Juara lomba akademik', 'skor' => 20],
            ['jenis' => 'Membantu teman kesulitan', 'skor' => 5],
            ['jenis' => 'Aktif dalam kegiatan OSIS', 'skor' => 10],
            ['jenis' => 'Kehadiran sempurna satu bulan', 'skor' => 15],
        ];

        $contohPelanggaran = [
            ['jenis' => 'Terlambat masuk sekolah', 'skor' => -5],
            ['jenis' => 'Tidak mengerjakan tugas', 'skor' => -10],
            ['jenis' => 'Bolos pelajaran', 'skor' => -15],
            ['jenis' => 'Berkelahi di sekolah', 'skor' => -25],
        ];

        $contoh = $tipe === 'reward'
            ? $this->faker->randomElement($contohReward)
            : $this->faker->randomElement($contohPelanggaran);

        return [
            'user_id' => null,
            'urut' => $this->faker->numberBetween(1, 100),
            'kode' => strtoupper($this->faker->unique()->bothify('??-###')),
            'jenis' => $contoh['jenis'],
            'tindakan' => $tipe === 'reward' ? 'Pemberian apresiasi' : 'Pembinaan oleh BK',
            'deskripsi' => $this->faker->sentence(),
            'skor' => $contoh['skor'],
            'tipe' => $tipe,
        ];
    }

    public function reward(): static
    {
        return $this->state(fn (array $attributes) => ['tipe' => 'reward']);
    }

    public function pelanggaran(): static
    {
        return $this->state(fn (array $attributes) => ['tipe' => 'pelanggaran']);
    }
}
