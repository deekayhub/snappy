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
        'needed_by',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'needed_by' => 'date',
        ];
    }

    public function categoryId()
    {
        return $this->belongsTo(OrganisationCategory::class, 'category');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'customer_job_id');
    }

    public function dynamicFieldValues()
    {
        return $this->hasMany(CategoryFieldValue::class, 'job_id');
    }
}
