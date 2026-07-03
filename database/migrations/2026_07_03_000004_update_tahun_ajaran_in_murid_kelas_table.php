<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('murid_kelas', function (Blueprint $table) {
            // FK murid_id menumpang pada index murid_tahun_ajaran_unique
            // (karena murid_id kolom pertama di unique key itu), jadi FK
            // harus dilepas dulu sebelum index bisa di-drop.
            $table->dropForeign(['murid_id']);
            $table->dropUnique('murid_tahun_ajaran_unique');
            $table->dropColumn('tahun_ajaran');
        });

        Schema::table('murid_kelas', function (Blueprint $table) {
            $table->foreign('murid_id')
                ->references('id')->on('murid')
                ->cascadeOnDelete();

            $table->foreignId('tahun_ajaran_id')
                ->after('kelas_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnDelete();

            $table->unique(['murid_id', 'tahun_ajaran_id'], 'murid_tahun_ajaran_unique');
        });
    }

    public function down(): void
    {
        Schema::table('murid_kelas', function (Blueprint $table) {
            $table->dropForeign(['murid_id']);
            $table->dropUnique('murid_tahun_ajaran_unique');
            $table->dropConstrainedForeignId('tahun_ajaran_id');
        });

        Schema::table('murid_kelas', function (Blueprint $table) {
            $table->foreign('murid_id')
                ->references('id')->on('murid')
                ->cascadeOnDelete();

            $table->string('tahun_ajaran', 9)->nullable()->after('kelas_id');
            $table->unique(['murid_id', 'tahun_ajaran'], 'murid_tahun_ajaran_unique');
        });
    }
};