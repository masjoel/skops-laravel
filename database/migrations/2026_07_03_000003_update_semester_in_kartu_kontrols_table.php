<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kartu_kontrol', function (Blueprint $table) {
            $table->dropColumn('semester');

            $table->foreignId('periode_akademik_id')
                ->nullable()
                ->after('jenis_poin_id')
                ->constrained('periode_akademik')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kartu_kontrol', function (Blueprint $table) {
            $table->dropConstrainedForeignId('periode_akademik_id');

            $table->integer('semester')->default(1);
        });
    }
};
