<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'stripe_price_id',
        'stripe_product_id',
        'description',
        'features',
        'price',
        'duration',
        'duration_months',
        'is_active',
        'is_free',
        'is_popular',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'integer',
        'is_active' => 'boolean',
        'is_free' => 'boolean',
        'is_popular' => 'boolean',
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

        static::saved(function (Plan $plan) {
            if (!$plan->is_free) {
                Artisan::call('stripe:sync-plans');
            }
        });

        static::deleted(function () {
            Artisan::call('stripe:sync-plans');
        });
    }

    public function setDurationAttribute($value)
    {
        $this->attributes['duration'] = $value;

        $map = [
            'monthly' => 1,
            '3_months' => 3,
            '6_months' => 6,
            'yearly' => 12,
            'lifetime' => 0,
        ];

        $this->attributes['duration_months'] = $map[$value] ?? 1;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function featureModels(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'feature_plan', 'plan_id', 'feature_id');
    }

    public function getPriceFormattedAttribute(): string
    {
        return '£' . number_format($this->price / 100, 2);
    }

    public function getDurationLabelAttribute(): string
    {
        if ($this->duration === 'lifetime') {
            return 'Lifetime';
        }
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

    public function getDisplayFeaturesAttribute(): array
    {
        $featureNames = $this->featureModels->pluck('name')->toArray();
        $legacyFeatures = $this->features ?? [];

        return array_values(array_unique(array_merge($featureNames, $legacyFeatures)));
    }

    public function hasFeature(string $slug): bool
    {
        return $this->featureModels()->where('slug', $slug)->exists();
    }
}
