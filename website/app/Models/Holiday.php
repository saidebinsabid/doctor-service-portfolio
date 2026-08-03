<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * ছুটি ও সময় পরিবর্তন।
 * chamber_id নাল হলে সব চেম্বারে প্রযোজ্য।
 */
class Holiday extends Model
{
    use HasTranslations;

    protected array $translatable = ['reason'];

    protected $fillable = [
        'chamber_id', 'date', 'type',
        'custom_start', 'custom_end', 'custom_max_serials',
        'reason_bn', 'reason_en',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'custom_max_serials' => 'integer',
        ];
    }

    public function chamber()
    {
        return $this->belongsTo(Chamber::class);
    }

    public function isClosed(): bool
    {
        return $this->type === 'closed';
    }

    public function scopeForChamber($query, int $chamberId)
    {
        return $query->where(function ($q) use ($chamberId) {
            $q->whereNull('chamber_id')->orWhere('chamber_id', $chamberId);
        });
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('date', '>=', now()->toDateString());
    }
}
