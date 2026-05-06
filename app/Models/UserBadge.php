<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserBadge extends Pivot
{
    // Jika ada field tambahan di tabel pivot, bisa didefinisikan di sini
    protected $table = 'user_badges';
}