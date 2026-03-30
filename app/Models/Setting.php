<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['key', 'value'];

    /**
     * الحصول على قيمة إعداد بالمفتاح
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = static::query()->where('key', $key)->first();
            return $setting?->value ?? $default;
        });
    }

    /**
     * تعيين قيمة إعداد
     */
    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrInsert(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    /**
     * تعيين مجموعة إعدادات
     */
    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::set($key, $value);
        }
    }
}

