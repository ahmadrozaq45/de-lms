<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialView extends Model
{
    protected $fillable = ['user_id', 'material_id', 'seconds_spent'];
}
