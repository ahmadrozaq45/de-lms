<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class QuizAttempt extends Model
{
    protected $fillable = ['quiz_id', 'user_id', 'score','is_passed','completed_at'];
    
    protected $casts = [
        'is_passed' => 'boolean',
        'completed_at' => 'datetime',
        'score' => 'integer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany 
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(AiConversation::class);
    }
    
}
