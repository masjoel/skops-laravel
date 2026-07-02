<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('murid_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')
                ->constrained('murid')
                ->cascadeOnDelete();
            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();
            $table->string('tahun_ajaran', 9); // contoh: "2025/2026"
            $table->timestamps();

            // satu murid hanya boleh terdaftar di satu kelas
            // per tahun ajaran
            $table->unique(['murid_id', 'tahun_ajaran'], 'murid_tahun_ajaran_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('murid_kelas');
    }
};
