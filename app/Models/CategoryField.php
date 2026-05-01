<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryField extends Model
{
    protected $fillable = [
        'category_id',
        'field_label',
        'field_name',
        'field_type',
        'field_options',
        'placeholder',
        'help_text',
        'is_required',
        'sort_order',
        'status'
    ];

    public function categoryId()
    {
        return $this->belongsTo(OrganisationCategory::class, 'category_id');
    }
}
