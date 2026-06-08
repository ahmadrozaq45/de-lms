<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'next_material', 'topic', 'course', 'practice'
            $table->string('title');
            $table->text('description')->nullable();
            $table->float('score')->default(0); // relevance score 0-100
            $table->json('basis')->nullable(); // what data drove this recommendation
            $table->string('target_type')->nullable(); // polymorphic: 'course', 'material', 'quiz'
            $table->unsignedBigInteger('target_id')->nullable();
            $table->boolean('is_dismissed')->default(false);
            $table->boolean('is_clicked')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type', 'is_dismissed']);
        });

        Schema::create('recommendation_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommendation_id')->constrained('ai_recommendations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->nullable(); // 1-5
            $table->string('action'); // 'clicked', 'dismissed', 'completed', 'skipped'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_feedbacks');
        Schema::dropIfExists('ai_recommendations');
    }
};
