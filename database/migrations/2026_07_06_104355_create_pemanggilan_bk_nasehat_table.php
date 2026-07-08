<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemanggilan_bk_nasehat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemanggilan_bk_id')
                ->constrained('pemanggilan_bk')
                ->cascadeOnDelete();
            $table->foreignId('guru_id') // pengurus yang diminta nasehat & ttd, dipilih bebas oleh BK
                ->constrained('guru')
                ->cascadeOnDelete();
            $table->integer('urutan')->default(1); // urutan tampil di form, bukan hierarki wajib
            $table->text('nasehat')->nullable(); // diisi saat pengurus memberi nasehat
            $table->boolean('sudah_ttd')->default(false);
            $table->date('tgl_ttd')->nullable();
            $table->timestamps();

            // satu pengurus tidak boleh diminta dobel di form pemanggilan yang sama
            $table->unique(['pemanggilan_bk_id', 'guru_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemanggilan_bk_nasehat');
    }
};