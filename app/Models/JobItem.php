<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_job_id',
        'item_name',
        'quantity',
        'sku_codes',
        'item_link',
        'image_paths',
        'allow_similar_quote',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'sku_codes' => 'array',
            'image_paths' => 'array',
            'allow_similar_quote' => 'boolean',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(CustomerJob::class, 'customer_job_id');
    }
}

