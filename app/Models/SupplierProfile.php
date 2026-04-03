<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierProfile extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'address',
        'website',
        'review_link',
        'social_link',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
