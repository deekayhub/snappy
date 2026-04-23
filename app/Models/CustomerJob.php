<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'organisation_name',
        'location',
        'budget',
        'delivery_in_uk',
        'personalisation_required',
        'personalisation_mode',
        'supplier_target_type',
        'supplier_target_count',
        'needed_by',
        'description',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'delivery_in_uk' => 'boolean',
            'personalisation_required' => 'boolean',
            'supplier_target_count' => 'integer',
            'needed_by' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'customer_job_id');
    }

    public function jobItems(): HasMany
    {
        return $this->hasMany(JobItem::class, 'customer_job_id');
    }
}
