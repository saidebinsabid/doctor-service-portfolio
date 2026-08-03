<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasTranslations, Orderable;

    protected array $translatable = ['degree', 'institution'];

    protected $fillable = [
        'degree_bn', 'degree_en', 'institution_bn', 'institution_en',
        'year', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
