<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = ['quiz_id', 'question_text', 'options', 'correct_answer'];

    protected $casts = [
        'options' => 'array', // Mengonversi JSON otomatis ke Array
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
