<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->text('answer')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'graded'])->default('pending');
            $table->integer('score')->nullable();
            $table->text('ai_feedback')->nullable();
            $table->text('teacher_feedback')->nullable();
            $table->unsignedTinyInteger('ai_accuracy')->nullable();
            $table->unsignedTinyInteger('ai_completeness')->nullable();
            $table->unsignedTinyInteger('ai_relevance')->nullable();
            $table->unsignedTinyInteger('ai_confidence')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};