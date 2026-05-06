<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Untuk quiz tipe conversation: instruksi AI assessor
            $table->text('ai_system_prompt')->nullable()->after('type');
            // Topik yang harus dibahas dalam conversation quiz
            $table->text('conversation_topic')->nullable()->after('ai_system_prompt');
            // Berapa minimum pesan/turn sebelum AI menilai
            $table->integer('min_turns')->default(3)->after('conversation_topic');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['ai_system_prompt', 'conversation_topic', 'min_turns']);
        });
    }
};
