<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * sort_order ও is_active — প্রায় সব কনটেন্ট টেবিলে যে দুটি কলাম আছে।
 */
trait Orderable
{
    /** শুধু সক্রিয় সারি */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** অ্যাডমিন প্যানেলে নির্ধারিত ক্রম অনুযায়ী */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** ওয়েবসাইটে দেখানোর জন্য — সক্রিয় ও ক্রমানুসারে */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query->active()->ordered();
    }

    /** নতুন সারি যোগ করলে তালিকার শেষে বসবে */
    public static function nextSortOrder(): int
    {
        return (int) static::max('sort_order') + 10;
    }
}
