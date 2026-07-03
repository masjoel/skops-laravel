<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wali_kelas', function (Blueprint $table) {
            // FK kelas_id menumpang pada index kelas_tahun_ajaran_unique
            // (karena kelas_id kolom pertama di unique key itu), jadi FK
            // harus dilepas dulu sebelum index bisa di-drop.
            $table->dropForeign(['kelas_id']);
            $table->dropUnique('kelas_tahun_ajaran_unique');
            $table->dropColumn('tahun_ajaran');
        });

        Schema::table('wali_kelas', function (Blueprint $table) {
            $table->foreign('kelas_id')
                ->references('id')->on('kelas')
                ->cascadeOnDelete();

            $table->foreignId('tahun_ajaran_id')
                ->after('guru_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnDelete();

            $table->unique(['kelas_id', 'tahun_ajaran_id'], 'kelas_tahun_ajaran_unique');
        });
    }

    public function down(): void
    {
        Schema::table('wali_kelas', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropUnique('kelas_tahun_ajaran_unique');
            $table->dropConstrainedForeignId('tahun_ajaran_id');
        });

        Schema::table('wali_kelas', function (Blueprint $table) {
            $table->foreign('kelas_id')
                ->references('id')->on('kelas')
                ->cascadeOnDelete();

            $table->string('tahun_ajaran', 9)->nullable()->after('guru_id');
            $table->unique(['kelas_id', 'tahun_ajaran'], 'kelas_tahun_ajaran_unique');
        });
    }
};