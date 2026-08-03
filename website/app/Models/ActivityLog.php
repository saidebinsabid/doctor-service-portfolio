<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * কে কখন কী বদলাল। একাধিক ম্যানেজার থাকলে জবাবদিহি নিশ্চিত হয়।
 */
class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'description', 'changes', 'ip_address',
    ];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** যেকোনো জায়গা থেকে এক লাইনে লগ রাখা */
    public static function record(
        string $action,
        ?Model $model = null,
        ?string $description = null,
        ?array $changes = null,
    ): void {
        static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'model_type'  => $model ? class_basename($model) : null,
            'model_id'    => $model?->getKey(),
            'description' => $description,
            'changes'     => $changes,
            'ip_address'  => request()->ip(),
        ]);
    }
}
