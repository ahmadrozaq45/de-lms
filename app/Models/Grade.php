<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

class Grade extends Model
{
    protected $fillable = ['user_id', 'gradeable_id', 'gradeable_type', 'score'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Memungkinkan relasi ke berbagai model (Quiz/Assignment)
    public function gradeable(): MorphTo
    {
        return $this->morphTo();
    }
}
