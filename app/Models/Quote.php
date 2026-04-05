<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    protected $fillable = [
        'customer_job_id',
        'supplier_user_id',
        'delivery_cost',
        'discount_offered',
        'price_for_job',
        'total_price',
        'notes',
        'status',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'delivery_cost' => 'decimal:2',
            'discount_offered' => 'decimal:2',
            'price_for_job' => 'decimal:2',
            'total_price' => 'decimal:2',
            'sent_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(CustomerJob::class, 'customer_job_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_user_id');
    }
}
