<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'stripe_price_id',
        'description',
        'features',
        'price',
        'duration_months',
        'is_active',
        'is_free',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'integer',
        'is_active' => 'boolean',
        'is_free' => 'boolean',
        'sort_order' => 'integer',
        'duration_months' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Plan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getPriceFormattedAttribute(): string
    {
        return '£' . number_format($this->price / 100, 2);
    }

    public function getDurationLabelAttribute(): string
    {
        if ($this->duration_months >= 12) {
            $years = $this->duration_months / 12;
            return $years == 1 ? '1 Year' : "{$years} Years";
        }
        return $this->duration_months == 1 ? '1 Month' : "{$this->duration_months} Months";
    }

    public function getYearlyPriceAttribute(): ?int
    {
        if ($this->duration_months <= 0) return null;
        return ($this->price / $this->duration_months) * 12;
    }

    public function getYearlyPriceFormattedAttribute(): ?string
    {
        $yearly = $this->yearly_price;
        return $yearly ? '£' . number_format($yearly / 100, 2) : null;
    }
}
