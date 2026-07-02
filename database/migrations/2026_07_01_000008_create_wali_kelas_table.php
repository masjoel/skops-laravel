<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wali_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();
            $table->foreignId('guru_id')
                ->constrained('guru')
                ->cascadeOnDelete();
            $table->string('tahun_ajaran', 9); // contoh: "2025/2026"
            $table->timestamps();

            // satu kelas hanya boleh punya satu wali kelas
            // per tahun ajaran
            $table->unique(['kelas_id', 'tahun_ajaran'], 'kelas_tahun_ajaran_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wali_kelas');
    }
};
