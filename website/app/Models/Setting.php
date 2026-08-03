<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * সাইটের একক তথ্য — ডাক্তারের নাম, ফোন, ফি, সোশ্যাল লিংক।
 *
 * ব্যবহার:
 *     Setting::get('doctor_name')          চলমান ভাষায়
 *     Setting::get('hotline')              ভাষাহীন মান
 *     Setting::bool('show_fees')           সত্য/মিথ্যা
 *     Setting::put('hotline', '09610009614')
 *
 * প্রতিটি পেজে ২০-৩০টি সেটিং লাগে, তাই পুরো টেবিল একবারে ক্যাশে রাখা হয় —
 * প্রতি রিকোয়েস্টে একটির বেশি কোয়েরি হয় না।
 */
class Setting extends Model
{
    protected $fillable = [
        'key', 'value_bn', 'value_en',
        'group', 'type', 'label_bn', 'label_en', 'hint_bn', 'sort_order',
    ];

    protected const CACHE_KEY = 'settings.all';

    /** পুরো টেবিল একবার পড়ে key => [bn, en] আকারে ক্যাশে রাখে */
    public static function all_cached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()
                ->get(['key', 'value_bn', 'value_en'])
                ->keyBy('key')
                ->map(fn ($s) => ['bn' => $s->value_bn, 'en' => $s->value_en])
                ->all();
        });
    }

    /** চলমান ভাষা অনুযায়ী মান; ইংরেজি খালি থাকলে বাংলা */
    public static function get(string $key, ?string $default = null): ?string
    {
        $row = static::all_cached()[$key] ?? null;

        if ($row === null) {
            return $default;
        }

        $locale = app()->getLocale();
        $value = $row[$locale] ?? null;

        return filled($value) ? $value : ($row['bn'] ?? $default);
    }

    /** নির্দিষ্ট ভাষার মান, ফলব্যাক ছাড়া (অ্যাডমিন ফর্মের জন্য) */
    public static function raw(string $key, string $locale = 'bn'): ?string
    {
        return static::all_cached()[$key][$locale] ?? null;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = static::get($key);

        return $value === null
            ? $default
            : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function int(string $key, int $default = 0): int
    {
        $value = static::get($key);

        return $value === null ? $default : (int) $value;
    }

    public static function put(string $key, ?string $bn, ?string $en = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value_bn' => $bn, 'value_en' => $en],
        );

        static::flush();
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        /* অ্যাডমিন সেটিং বদলালে ক্যাশ সাথে সাথে মুছে যায়,
           নইলে ওয়েবসাইটে পুরনো তথ্য দেখাতে থাকত */
        static::saved(fn () => static::flush());
        static::deleted(fn () => static::flush());
    }
}
