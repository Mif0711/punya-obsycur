<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    public const KEY_FINE_PER_DAY = 'fine_per_day';

    public const KEY_GRACE_DAYS = 'grace_days';

    public const KEY_LOAN_DAYS = 'loan_days';

    /**
     * Read a setting with a short-lived cache and a sensible fallback.
     */
    public static function value(string $key, string $default = ''): string
    {
        return Cache::remember('setting:'.$key, now()->addMinutes(10), fn (): string => (string) (static::query()
            ->where('key', $key)
            ->value('value') ?? $default));
    }

    public static function integer(string $key, int $default): int
    {
        return (int) static::value($key, (string) $default);
    }

    /**
     * Persist a setting and invalidate its cached value.
     */
    public static function put(string $key, string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('setting:'.$key);
    }
}
