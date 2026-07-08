<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemanggilan_bk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_kelas_id')
                ->constrained('murid_kelas')
                ->cascadeOnDelete();
            $table->foreignId('guru_bk_id') // guru BK yang memanggil murid
                ->constrained('guru')
                ->cascadeOnDelete();
            $table->date('tgl_pemanggilan');

            // snapshot poin pelanggaran murid SAAT dipanggil -- bukan poin
            // yang dihitung ulang tiap saat, supaya histori tidak berubah
            // walau ada catatan poin baru sesudahnya.
            $table->decimal('total_poin_pelanggaran', 8, 2);

            $table->enum('status', ['menunggu_ttd', 'lengkap', 'selesai', 'dibatalkan'])
                ->default('menunggu_ttd');

            // diisi guru BK setelah SEMUA tanda tangan nasehat lengkap
            $table->text('tugas')->nullable();
            $table->decimal('poin_pemutihan', 8, 2)->nullable();
            $table->date('tgl_tugas_diberikan')->nullable();
            $table->date('tgl_tugas_selesai')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemanggilan_bk');
    }
};