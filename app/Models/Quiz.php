<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, MorphMany};

class Quiz extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'time_limit',
        'passing_score',
        'type',
        'ai_system_prompt',
        'conversation_topic',
        'min_turns',
    ];

    protected $casts = [
        'passing_score' => 'integer',
        'time_limit'    => 'integer',
        'min_turns'     => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function grades(): MorphMany
    {
        return $this->morphMany(Grade::class, 'gradeable');
    }
}