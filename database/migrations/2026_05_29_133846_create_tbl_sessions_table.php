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
        Schema::create('tbl_sessions', function (Blueprint $table) {
            $table->string('id', 128)->primary();
            $table->string('ip_address', 45);
            $table->timestamp('timestamp')->useCurrent()->index('ci_sessions_timestamp');
            $table->binary('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_sessions');
    }
};
