<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOrganisation extends Model
{
    protected $table = 'organisation_category_user';

    protected $fillable = [
        'user_id',
        'organisation_category_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organisationCategory()
    {
        return $this->belongsTo(
            OrganisationCategory::class,
            'organisation_category_id'
        );
    }
}
