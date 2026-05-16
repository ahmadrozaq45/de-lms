<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserBadge extends Pivot
{
    protected $fillable = ['badge_id', 'earned_at'];
}
