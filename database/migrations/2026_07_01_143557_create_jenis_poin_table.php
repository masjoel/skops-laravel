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
        Schema::create('jenis_poin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('urut')->nullable();
            $table->string('kode')->unique();
            $table->string('deskripsi');
            $table->string('tindakan')->nullable();
            $table->string('keterangan')->nullable();
            $table->integer('skor')->default(0);
            $table->enum('jenis', ['reward', 'pelanggaran']);
            $table->timestamps();
            $table->index('jenis');
            $table->index('kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_poin');
    }
};
