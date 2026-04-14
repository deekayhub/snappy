<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierProfile extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_logo',
        'address',
        'company_description',
        'website',
        'review_link',
        'social_link',
        'social_links',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
