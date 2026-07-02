<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('murid_orang_tua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')
                ->constrained('murid')
                ->cascadeOnDelete();
            $table->foreignId('orang_tua_id')
                ->constrained('orang_tua')
                ->cascadeOnDelete();
            $table->enum('hubungan', ['Ayah', 'Ibu', 'Wali']);
            $table->timestamps();

            // satu orang tua tidak boleh terdaftar dua kali
            // dengan hubungan yang sama untuk murid yang sama
            $table->unique(['murid_id', 'orang_tua_id', 'hubungan'], 'murid_ortu_hubungan_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('murid_orang_tua');
    }
};
