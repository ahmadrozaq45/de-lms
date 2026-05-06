<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // submissions sudah punya ai_feedback, teacher_feedback, score, status
        // Tambahkan course_id untuk memudahkan query laporan
        Schema::table('submissions', function (Blueprint $table) {
            // Pastikan kolom belum ada
            if (!Schema::hasColumn('submissions', 'course_id')) {
                $table->foreignId('course_id')
                      ->nullable()
                      ->after('student_id')
                      ->constrained()
                      ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'course_id')) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            }
        });
    }
};
