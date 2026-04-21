<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class QuizAttempt extends Model
{
    protected $fillable = ['quiz_id', 'user_id', 'score'];

    public function answers(): HasMany {
        return $this->hasMany(QuizAnswer::class);
    }
}
