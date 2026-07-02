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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('personil_id')
                ->nullable()
                ->constrained('personil')
                ->cascadeOnDelete()->after('id');
            $table->string('username', 20)->unique()->after('name');
            $table->integer('opr_id')->nullable()->after('password');
            $table->enum('role', ['admin', 'guru', 'murid', 'orang_tua'])->default('murid')->after('opr_id');
            $table->boolean('status')->default(false)->after('role');
            $table->string('photo')->nullable()->after('status');
            $table->string('telpon', 50)->nullable()->after('photo');
            $table->enum('q1', ['Siapa Nama lengkap Anda?', 'Siapa Nama lengkap Ayah Anda?', 'Siapa Nama lengkap Ibu Anda?', 'Siapa Nama sahabat Anda?', 'Dimana Anda dilahirkan?'])->nullable()->after('telpon');
            $table->enum('q2', ['Buah favorit Anda?', 'Makanan favorit Anda?', 'Binatang kesayangan Anda?'])->nullable()->after('q1');
            $table->string('a1', 250)->nullable()->after('q2');
            $table->string('a2', 250)->nullable()->after('a1');
            $table->string('kodeact')->nullable()->after('a2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the columns added in the up() method
            $table->dropColumn([
                'personil_id',
                'username',
                'opr_id',
                'role',
                'status',
                'photo',
                'telpon',
                'q1',
                'q2',
                'a1',
                'a2',
                'kodeact',
            ]);
        });
    }
};
