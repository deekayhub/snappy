<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;

class OrganisationCategory extends Model
{
    protected $fillable = [
        'name',
        'type'
    ];   

    
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_organisations',
            'organisation_categories_id',
            'user_id'
        )->withPivot('type')->withTimestamps();
    }


}
