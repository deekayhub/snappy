<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::rememberForever('setting.' . $key, function () use ($key) {
            return static::query()->where('key', $key)->value('value');
        });

        return $value ?? $default;
    }

    public static function set(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
            Cache::forget('setting.' . $key);
        }

        Cache::forget('settings.all');
    }

    public static function allCached(): array
    {
        $rows = Cache::rememberForever('settings.all', function () {
            return static::query()->pluck('value', 'key')->toArray();
        });

        return $rows;
    }
}
