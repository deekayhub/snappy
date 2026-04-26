<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryFieldValue extends Model
{
    protected $fillable = [
        'category_id',
        'field_id',
        'user_id',
        'field_value'
    ];
}
