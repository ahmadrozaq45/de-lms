<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, MorphMany};

class Quiz extends Model
{
    protected $fillable = ['course_id', 'title', 'time_limit'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function grades(): MorphMany
    {
        return $this->morphMany(Grade::class,'gradeable');
    }
}
