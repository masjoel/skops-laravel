<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kartu_kontrol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            $table->foreignId('murid_kelas_id')->nullable()->constrained('murid_kelas')->cascadeOnDelete();
            $table->foreignId('jenis_poin_id')->nullable()->constrained('jenis_poin')->nullOnDelete();
            $table->date('tgl')->nullable();
            $table->integer('skor')->nullable();
            $table->tinyText('tindakan')->nullable();
            $table->tinyInteger('semester')->default(1);
            $table->boolean('is_reset')->default(false);
            $table->foreignId('pemanggilan_bk_id')
                ->nullable() // hanya diisi kalau baris ini adalah baris pemutihan
                ->constrained('pemanggilan_bk')
                ->nullOnDelete();
            $table->timestamps();
            $table->index(['murid_kelas_id', 'semester']);
            $table->index('tgl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_kontrol');
    }
};
