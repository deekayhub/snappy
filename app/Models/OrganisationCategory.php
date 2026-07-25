<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganisationCategory extends Model
{
    protected $fillable = [
        'name',
        'type'
    ];   

    protected static function booted(): void
    {
        static::created(function (OrganisationCategory $category) {
            if ($category->type !== 'supplier') return;

            $source = static::where('name', 'trophies & awards')
                ->where('type', 'supplier')
                ->first();

            if (!$source) return;

            $sourceFields = CategoryField::where('category_id', $source->id)->get();

            foreach ($sourceFields as $field) {
                CategoryField::create([
                    'category_id' => $category->id,
                    'field_label' => $field->field_label,
                    'field_name' => $field->field_name,
                    'field_type' => $field->field_type,
                    'field_options' => $field->field_options,
                    'placeholder' => $field->placeholder,
                    'help_text' => $field->help_text,
                    'is_required' => $field->is_required,
                    'sort_order' => $field->sort_order,
                    'status' => $field->status,
                ]);
            }
        });
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CategoryField::class, 'category_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function categorySetting()
    {
        return $this->hasOne(OrganisationCategorySetting::class, 'organisation_category_id');
    }

}
