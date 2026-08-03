<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * পুরো দিন বন্ধ না করে নির্দিষ্ট কয়েকটি স্লট আটকে রাখা।
 */
class BlockedSlot extends Model
{
    protected $fillable = [
        'chamber_id', 'date', 'slot_time', 'reason', 'blocked_by',
    ];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function chamber()
    {
        return $this->belongsTo(Chamber::class);
    }

    public function blockedBy()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public function slotHm(): string
    {
        return substr((string) $this->slot_time, 0, 5);
    }
}
