<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Model;

class Chamber extends Model
{
    use HasTranslations, Orderable;

    protected array $translatable = ['name', 'address'];

    protected $fillable = [
        'name_bn', 'name_en', 'address_bn', 'address_en',
        'hotline', 'map_query', 'lat', 'lng', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'lat' => 'float',
            'lng' => 'float',
        ];
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }

    public function blockedSlots()
    {
        return $this->hasMany(BlockedSlot::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /** Google Maps এমবেড — API কী বা বিলিং অ্যাকাউন্ট লাগে না */
    public function mapEmbedUrl(): string
    {
        $q = $this->map_query ?: $this->address_bn;

        return 'https://www.google.com/maps?q=' . urlencode($q) . '&output=embed';
    }

    public function mapDirectionsUrl(): string
    {
        $q = $this->map_query ?: $this->address_bn;

        return 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($q);
    }
}
