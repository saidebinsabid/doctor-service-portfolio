<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasTranslations, Orderable;

    protected array $translatable = ['title', 'description'];

    protected $fillable = [
        'title_bn', 'title_en', 'description_bn', 'description_en',
        'icon', 'tone', 'is_special', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_special' => 'boolean',
            'is_active'  => 'boolean',
        ];
    }

    /** সাধারণ সেবা — প্রচারপত্রের ১৪টি */
    public function scopeGeneral(Builder $q): Builder
    {
        return $q->where('is_special', false);
    }

    /** বিশেষ চিকিৎসা — অ্যাডিনয়েড, টনসিলাইটিস, সাইনুসাইটিস, দীর্ঘমেয়াদি কাশি */
    public function scopeSpecial(Builder $q): Builder
    {
        return $q->where('is_special', true);
    }
}
