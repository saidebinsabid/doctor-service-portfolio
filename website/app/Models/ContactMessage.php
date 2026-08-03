<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'subject', 'message', 'is_read', 'ip_address',
    ];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }

    public function scopeUnread(Builder $q): Builder
    {
        return $q->where('is_read', false);
    }
}
