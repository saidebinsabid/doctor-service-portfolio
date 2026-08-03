<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasTranslations, Orderable;

    protected array $translatable = ['question', 'answer'];

    protected $fillable = [
        'question_bn', 'question_en', 'answer_bn', 'answer_en',
        'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
