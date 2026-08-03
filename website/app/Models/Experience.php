<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasTranslations, Orderable;

    protected array $translatable = ['position', 'organization'];

    protected $fillable = [
        'position_bn', 'position_en', 'organization_bn', 'organization_en',
        'period', 'icon', 'is_current', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'is_active'  => 'boolean',
        ];
    }
}
