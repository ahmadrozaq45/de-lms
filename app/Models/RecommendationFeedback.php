<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendationFeedback extends Model
{
    protected $fillable = ['recommendation_id', 'user_id', 'rating', 'action'];

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(AiRecommendation::class, 'recommendation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
