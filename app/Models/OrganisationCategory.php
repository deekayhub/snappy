<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrganisationCategory extends Model
{
    protected $fillable = [
        'name',
        'type'
    ];   

    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function categorySetting()
    {
        return $this->hasOne(OrganisationCategorySetting::class, 'organisation_category_id');
    }


}
