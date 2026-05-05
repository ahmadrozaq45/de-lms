<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Definisi semua badge yang tersedia
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('icon')->nullable(); // nama icon atau path gambar
            $table->enum('type', [
                'first_login',
                'course_complete',
                'quiz_perfect',
                'quiz_passed',
                'material_complete',
                'assignment_submitted',
                'streak_3',
                'streak_7',
            ]);
            $table->timestamps();
        });

        // Badge yang sudah diraih oleh user
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('earned_at');
            $table->timestamps();

            $table->unique(['user_id', 'badge_id']); // tiap badge hanya bisa diraih 1x
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
    }
};
