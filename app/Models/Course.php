<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};
use App\Models\Quiz;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $fillable = ['title', 'description', 'teacher_id','course_code', 'category_id'];
    protected static function booted(): void
    {
        // Generate course_code otomatis saat membuat kursus baru
        static::creating(function ($course) {
            do {
                $generatedCode = strtoupper(Str::random(7));
            } while (self::where('course_code', $generatedCode)->exists());

            $course->course_code = $generatedCode;
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function categories(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }
}
