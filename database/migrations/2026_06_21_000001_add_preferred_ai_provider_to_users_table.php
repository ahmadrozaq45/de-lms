<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Preferensi provider AI milik guru sendiri (opsional).
            // Jika null, sistem pakai provider aktif global (yang diatur admin).
            $table->string('preferred_ai_provider')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('preferred_ai_provider');
        });
    }
};