<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatan_struktural', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan', 100)->unique(); // contoh: "Kepala Sekolah", "Guru BK"
            $table->enum('kategori', ['Struktural', 'Fungsional', 'Tugas Tambahan', 'Administrasi']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan_struktural');
    }
};