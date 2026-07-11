<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personil_id')
                ->constrained('personil')
                ->cascadeOnDelete();
            $table->string('nik', 30)->unique()->nullable();
            $table->string('nip', 30)->unique()->nullable();
            $table->string('nuptk', 30)->unique()->nullable();
            $table->foreignId('jabatan_struktural_id')
                ->nullable()
                ->constrained('jabatan_struktural')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};
