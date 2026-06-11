<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganisationCategorySetting extends Model
{
    protected $fillable = [
        'organisation_category_id',
        'image',
        'status',
    ];

    public function organisationCategory()
    {
        return $this->belongsTo(OrganisationCategory::class);
    }
}
