<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, MorphMany};

class Assignment extends Model
{
    protected $fillable = ['course_id', 'title', 'instructions', 'due_date'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function grades(): MorphMany
    {
        return $this->morphMany(Grade::class,'gradeable');
    }
}
