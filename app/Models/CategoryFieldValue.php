<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryFieldValue extends Model
{
    protected $fillable = [
        'job_id',
        'category_id',
        'field_id',
        'user_id',
        'field_value'
    ];

    public function categoryFields()
    {
        return $this->belongsTo(CategoryField::class, 'field_id', 'id');
    }
}
