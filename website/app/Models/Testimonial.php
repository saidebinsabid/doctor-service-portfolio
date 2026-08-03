<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * রোগীদের মতামত।
 *
 * is_approved ডিফল্ট false — অ্যাডমিন না দেখা পর্যন্ত ওয়েবসাইটে আসে না।
 * একটিও অনুমোদিত মতামত না থাকলে সেকশনটি নিজে থেকেই লুকিয়ে যায়।
 */
class Testimonial extends Model
{
    use HasTranslations;

    protected array $translatable = ['comment'];

    protected $fillable = [
        'patient_name', 'location', 'rating',
        'comment_bn', 'comment_en', 'is_approved', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'rating'      => 'integer',
        ];
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('is_approved', true);
    }

    public function scopeForPublic(Builder $q): Builder
    {
        return $q->approved()->orderBy('sort_order')->orderByDesc('id');
    }
}
