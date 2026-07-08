<?php

namespace Database\Seeders;

use App\Models\JenisPoin;
use Illuminate\Database\Seeder;

class JenisPoinSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // reward
            ['kode' => 'R-01', 'deskripsi' => 'Juara lomba akademik', 'tindakan' => 'Pemberian apresiasi', 'keterangan' => 'Meraih prestasi di lomba tingkat sekolah/kota/nasional', 'skor' => 20, 'jenis' => 'reward'],
            ['kode' => 'R-02', 'deskripsi' => 'Kehadiran sempurna satu bulan', 'tindakan' => 'Pemberian apresiasi', 'keterangan' => 'Tidak pernah absen tanpa keterangan selama satu bulan', 'skor' => 15, 'jenis' => 'reward'],
            ['kode' => 'R-03', 'deskripsi' => 'Aktif dalam kegiatan OSIS', 'tindakan' => 'Pemberian apresiasi', 'keterangan' => 'Berperan aktif dalam kegiatan organisasi sekolah', 'skor' => 10, 'jenis' => 'reward'],
            ['kode' => 'R-04', 'deskripsi' => 'Membantu teman kesulitan', 'tindakan' => 'Pemberian apresiasi', 'keterangan' => 'Menunjukkan sikap peduli terhadap sesama', 'skor' => 5, 'jenis' => 'reward'],

            // pelanggaran
            ['kode' => 'PR-01', 'deskripsi' => 'Terlambat masuk sekolah', 'tindakan' => 'Teguran lisan', 'keterangan' => 'Datang terlambat tanpa alasan yang jelas', 'skor' => -5, 'jenis' => 'pelanggaran'],
            ['kode' => 'PS-02', 'deskripsi' => 'Tidak mengerjakan tugas', 'tindakan' => 'Teguran lisan', 'keterangan' => 'Tidak mengumpulkan tugas sesuai tenggat waktu', 'skor' => -10, 'jenis' => 'pelanggaran'],
            ['kode' => 'PS-03', 'deskripsi' => 'Bolos pelajaran', 'tindakan' => 'Pembinaan oleh BK', 'keterangan' => 'Tidak mengikuti pelajaran tanpa izin', 'skor' => -15, 'jenis' => 'pelanggaran'],
            ['kode' => 'PB-04', 'deskripsi' => 'Berkelahi di sekolah', 'tindakan' => 'Pembinaan oleh BK dan orang tua', 'keterangan' => 'Terlibat perkelahian dengan siswa lain', 'skor' => -25, 'jenis' => 'pelanggaran'],

            // pemutihan
            // per kasus ditentukan guru BK dan disimpan di kartu_kontrol.skor
            ['kode' => 'PMT', 'deskripsi' => 'Pemutihan Poin', 'tindakan' => 'Persetujuan BK setelah tugas selesai', 'keterangan' => 'Penutupan poin pelanggaran setelah proses pemanggilan BK selesai', 'skor' => 0, 'jenis' => 'pemutihan'],
        ];

        foreach ($data as $i => $item) {
            JenisPoin::create(array_merge($item, ['urut' => $i + 1]));
        }
    }
}
