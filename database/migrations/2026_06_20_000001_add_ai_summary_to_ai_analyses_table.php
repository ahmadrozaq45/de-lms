<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            // Ringkasan naratif: apa saja yang sudah dikerjakan siswa
            // (materi, quiz, assignment) dalam bentuk paragraf, bukan status singkat.
            // status_prediction TETAP DIPERTAHANKAN agar teacher/dashboard.blade.php
            // (yang masih query berdasarkan status_prediction = 'at_risk' / 'needs_improvement')
            // tidak rusak.
            $table->text('ai_summary')->nullable()->after('recommendation');
        });
    }

    public function down(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            $table->dropColumn('ai_summary');
        });
    }
};