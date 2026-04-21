<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    protected $fillable = ['module_id', 'title', 'type', 'content', 'file_path'];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
