<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * অ্যাডমিন ও ম্যানেজার। রোগীদের কোনো অ্যাকাউন্ট নেই — গেস্ট বুকিং।
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    /** শুধু অ্যাডমিন সেটিংস ও ব্যবহারকারী ব্যবস্থাপনা করতে পারেন */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function roleLabel(): string
    {
        return $this->isAdmin() ? 'অ্যাডমিন' : 'ম্যানেজার';
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
