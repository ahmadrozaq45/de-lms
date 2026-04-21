<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Module extends Model
{
    protected $fillable = ['course_id', 'title', 'order'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }
}
