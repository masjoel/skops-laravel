<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_akademik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnDelete();
            $table->tinyInteger('semester'); // 1 = ganjil, 2 = genap
            $table->boolean('is_aktif')->default(false);
            $table->timestamps();

            // satu tahun ajaran hanya boleh punya satu baris
            // per semester (tidak boleh semester 1 dobel)
            $table->unique(['tahun_ajaran_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_akademik');
    }
};
