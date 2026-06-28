<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'profile_picture',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function customerProfile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function supplierProfile(): HasOne
    {
        return $this->hasOne(SupplierProfile::class);
    }

    public function organisationCategories(): BelongsToMany
    {
        return $this->belongsToMany(OrganisationCategory::class)->withTimestamps();
    }

    public function customerJobs(): HasMany
    {
        return $this->hasMany(CustomerJob::class);
    }

    public function supplierQuotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'supplier_user_id');
    }

    public function ratedSupplierQuotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'supplier_user_id')->whereNotNull('customer_rating');
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(UserNotificationPreference::class);
    }

    public function onTrialOrSubscribed(): bool
    {
        return $this->onTrial('default') || $this->subscribed('default');
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscribed('default');
    }

    public function currentPlan(): ?Plan
    {
        $subscription = $this->subscription('default');
        if (!$subscription || !$subscription->stripe_price) {
            return Plan::where('is_free', true)->first();
        }
        return Plan::where('stripe_price_id', $subscription->stripe_price)->first();
    }

    public function hasFeature(string $slug): bool
    {
        $plan = $this->currentPlan();
        return $plan && $plan->hasFeature($slug);
    }

    public function winsCount(): int
    {
        return $this->supplierQuotes()
            ->where('status', 'completed')
            ->count();
    }

    public function isRecommended(): bool
    {
        return $this->winsCount() >= 10;
    }

    public function subscriptionUsage(): array
    {
        $plan = $this->currentPlan();

        if (!$plan || $plan->is_free) {
            $quotesThisMonth = $this->supplierQuotes()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $quotesThisYear = $this->supplierQuotes()
                ->whereYear('created_at', now()->year)
                ->count();

            return [
                'can_submit_quote' => $quotesThisMonth < 1 && $quotesThisYear < 6,
                'quotes_remaining_this_month' => max(0, 1 - $quotesThisMonth),
                'quotes_remaining_this_year' => max(0, 6 - $quotesThisYear),
            ];
        }

        return [
            'can_submit_quote' => true,
            'quotes_remaining_this_month' => -1,
            'quotes_remaining_this_year' => -1,
        ];
    }
}
