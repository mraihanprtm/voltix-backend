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
            // 1. Buat kolom firebase_uid agar boleh kosong (nullable)
            $table->string('firebase_uid')->nullable()->change();

            // 2. Tambahkan kolom password, yang juga boleh kosong
            $table->string('password')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('firebase_uid')->nullable(false)->change(); // Kembalikan seperti semula
            $table->dropColumn('password');
        });
    }
};
