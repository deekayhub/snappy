<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = [
        'section_type',
        'data',
        'status',
    ];

    protected $casts = [
        'data' => 'array'
    ];
}
