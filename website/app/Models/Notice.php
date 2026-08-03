<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * হোমপেজের উপরে ব্যানার নোটিশ।
 * "১৫ আগস্ট চেম্বার বন্ধ থাকবে" জাতীয় ঘোষণা।
 *
 * starts_at / ends_at দিয়ে নির্দিষ্ট সময়ের জন্য চালু রাখা যায় —
 * অ্যাডমিনকে পরে মনে করে বন্ধ করতে হয় না।
 */
class Notice extends Model
{
    use HasTranslations, Orderable;

    protected array $translatable = ['title', 'body'];

    protected $fillable = [
        'title_bn', 'title_en', 'body_bn', 'body_en',
        'severity', 'starts_at', 'ends_at', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at'   => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public const TONES = [
        'info'    => 'sky',
        'warning' => 'amber',
        'urgent'  => 'red',
    ];

    /** এখন দেখানোর মতো নোটিশ */
    public function scopeCurrent(Builder $q): Builder
    {
        $now = now();

        return $q->where('is_active', true)
            ->where(fn ($x) => $x->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($x) => $x->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }

    public function tone(): string
    {
        return self::TONES[$this->severity] ?? 'sky';
    }
}
