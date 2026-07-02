<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('murid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personil_id')
                ->constrained('personil')
                ->cascadeOnDelete();
            $table->string('nis', 30)->unique();
            $table->timestamps();
            // kelas_id sengaja tidak diletakkan di sini,
            // digantikan oleh tabel riwayat murid_kelas
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('murid');
    }
};
