<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiRecommendation extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'description',
        'score', 'basis', 'target_type', 'target_id',
        'is_dismissed', 'is_clicked', 'expires_at',
    ];

    protected $casts = [
        'basis'        => 'array',
        'is_dismissed' => 'boolean',
        'is_clicked'   => 'boolean',
        'expires_at'   => 'datetime',
        'score'        => 'float',
    ];

    // ── Type constants ──────────────────────────────────────────────────────
    const TYPE_NEXT_MATERIAL = 'next_material';
    const TYPE_TOPIC         = 'topic';
    const TYPE_COURSE        = 'course';
    const TYPE_PRACTICE      = 'practice';

    // ── Relationships ────────────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(RecommendationFeedback::class, 'recommendation_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_dismissed', false)
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_NEXT_MATERIAL => 'Materi Berikutnya',
            self::TYPE_TOPIC         => 'Topik Belajar',
            self::TYPE_COURSE        => 'Course Relevan',
            self::TYPE_PRACTICE      => 'Latihan Soal',
            default                  => ucfirst($this->type),
        };
    }

    public function getTypeIcon(): string
    {
        return match ($this->type) {
            self::TYPE_NEXT_MATERIAL => 'book-open',
            self::TYPE_TOPIC         => 'bulb',
            self::TYPE_COURSE        => 'school',
            self::TYPE_PRACTICE      => 'pencil',
            default                  => 'star',
        };
    }

    public function getTypeColor(): string
    {
        return match ($this->type) {
            self::TYPE_NEXT_MATERIAL => 'blue',
            self::TYPE_TOPIC         => 'amber',
            self::TYPE_COURSE        => 'teal',
            self::TYPE_PRACTICE      => 'purple',
            default                  => 'gray',
        };
    }
}
