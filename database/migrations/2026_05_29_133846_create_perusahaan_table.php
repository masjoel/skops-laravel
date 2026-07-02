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
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->id('id');
            $table->integer('user_id')->index('user_id')->nullable();
            $table->string('nama_client');
            $table->string('nama_app')->nullable();
            $table->string('versi_app')->nullable();
            $table->string('desc_app')->nullable();
            $table->string('alamat_client')->nullable();
            $table->string('signature')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->string('image_icon')->nullable();
            $table->string('website')->nullable();
            $table->string('mcad')->nullable();
            $table->string('init')->nullable();
            $table->string('bank')->nullable();
            $table->string('footnot')->nullable();
            $table->tinyInteger('jdigit')->default(0);
            $table->tinyInteger('jdelay')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perusahaan');
    }
};
