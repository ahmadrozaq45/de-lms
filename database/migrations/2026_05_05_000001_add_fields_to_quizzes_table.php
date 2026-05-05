<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->integer('passing_score')->default(60)->after('time_limit'); // skor minimum lulus
            $table->enum('type', ['multiple_choice', 'conversation'])->default('multiple_choice')->after('passing_score');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['description', 'passing_score', 'type']);
        });
    }
};
