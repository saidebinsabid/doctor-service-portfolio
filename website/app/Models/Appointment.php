<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code', 'chamber_id', 'appointment_date', 'slot_time', 'serial_no',
        'slot_hold', 'serial_hold',
        'patient_name', 'patient_phone', 'patient_age', 'patient_age_unit',
        'gender', 'guardian_name', 'address',
        'visit_type', 'problem', 'status', 'admin_note',
        'source', 'locale', 'ip_address', 'user_agent',
        'confirmed_at', 'cancelled_at', 'handled_by',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'serial_no'        => 'integer',
            'patient_age'      => 'integer',
            'confirmed_at'     => 'datetime',
            'cancelled_at'     => 'datetime',
        ];
    }

    /** যেসব অবস্থায় স্লটটি আর দখলে থাকে না — অন্য রোগী নিতে পারেন */
    public const RELEASED_STATUSES = ['cancelled', 'no_show'];

    public const STATUS_LABELS = [
        'pending'   => ['bn' => 'অপেক্ষমাণ',  'en' => 'Pending',   'tone' => 'amber'],
        'confirmed' => ['bn' => 'নিশ্চিত',     'en' => 'Confirmed', 'tone' => 'sky'],
        'completed' => ['bn' => 'সম্পন্ন',     'en' => 'Completed', 'tone' => 'green'],
        'cancelled' => ['bn' => 'বাতিল',      'en' => 'Cancelled', 'tone' => 'red'],
        'no_show'   => ['bn' => 'অনুপস্থিত',   'en' => 'No show',   'tone' => 'slate'],
    ];

    public const VISIT_LABELS = [
        'new'      => ['bn' => 'নতুন রোগী',      'en' => 'New patient'],
        'followup' => ['bn' => 'ফলো-আপ',         'en' => 'Follow-up'],
        'report'   => ['bn' => 'রিপোর্ট দেখানো', 'en' => 'Report review'],
    ];

    public const AGE_UNITS = [
        'day'   => ['bn' => 'দিন',  'en' => 'days'],
        'month' => ['bn' => 'মাস',  'en' => 'months'],
        'year'  => ['bn' => 'বছর',  'en' => 'years'],
    ];

    /*
    |--------------------------------------------------------------------------
    | স্লট দখল
    |--------------------------------------------------------------------------
    | slot_hold / serial_hold কলামের উপর unique index আছে।
    | বুকিং সক্রিয় থাকলে সেখানে "তারিখ|সময়" বসে, বাতিল হলে NULL।
    |
    | ফলে বাতিল করা সিরিয়াল সাথে সাথেই আবার খালি হয়ে যায়,
    | অথচ পুরনো বুকিংয়ের রেকর্ড ও ইতিহাস মুছে যায় না।
    */
    public function syncHolds(): void
    {
        if (in_array($this->status, self::RELEASED_STATUSES, true)) {
            $this->slot_hold = null;
            $this->serial_hold = null;

            return;
        }

        $date = $this->dateString();

        $this->slot_hold   = $date . '|' . $this->slotHm();
        $this->serial_hold = $date . '|' . $this->serial_no;
    }

    protected static function booted(): void
    {
        /* স্ট্যাটাস যেভাবেই বদলাক — অ্যাডমিন প্যানেল, কমান্ড, বা কোড —
           দখলের হিসাব সবসময় নিজে থেকেই ঠিক থাকে */
        static::saving(fn (self $a) => $a->syncHolds());
    }

    /*
    |--------------------------------------------------------------------------
    | সম্পর্ক
    |--------------------------------------------------------------------------
    */
    public function chamber()
    {
        return $this->belongsTo(Chamber::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class);
    }

    /*
    |--------------------------------------------------------------------------
    | স্কোপ
    |--------------------------------------------------------------------------
    */

    /** স্লট দখল করে আছে এমন বুকিং (বাতিল ও অনুপস্থিত বাদ) */
    public function scopeHolding(Builder $q): Builder
    {
        return $q->whereNotIn('status', self::RELEASED_STATUSES);
    }

    public function scopeForDate(Builder $q, string $date): Builder
    {
        return $q->whereDate('appointment_date', $date);
    }

    public function scopeToday(Builder $q): Builder
    {
        return $q->whereDate('appointment_date', now()->toDateString());
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->whereDate('appointment_date', '>=', now()->toDateString());
    }

    /*
    |--------------------------------------------------------------------------
    | সহায়ক
    |--------------------------------------------------------------------------
    */
    public function dateString(): string
    {
        return $this->appointment_date instanceof \DateTimeInterface
            ? $this->appointment_date->format('Y-m-d')
            : (string) $this->appointment_date;
    }

    /** "10:54:00" → "10:54" */
    public function slotHm(): string
    {
        return substr((string) $this->slot_time, 0, 5);
    }

    public function statusLabel(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return self::STATUS_LABELS[$this->status][$locale]
            ?? self::STATUS_LABELS[$this->status]['bn']
            ?? $this->status;
    }

    public function statusTone(): string
    {
        return self::STATUS_LABELS[$this->status]['tone'] ?? 'slate';
    }

    public function visitLabel(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return self::VISIT_LABELS[$this->visit_type][$locale]
            ?? self::VISIT_LABELS[$this->visit_type]['bn']
            ?? $this->visit_type;
    }

    /** "৩ বছর" / "18 months" */
    public function ageLabel(?string $locale = null): string
    {
        if ($this->patient_age === null) {
            return '';
        }

        $locale ??= app()->getLocale();
        $unit = self::AGE_UNITS[$this->patient_age_unit][$locale]
            ?? self::AGE_UNITS[$this->patient_age_unit]['bn'];

        $number = $locale === 'bn'
            ? bn_number($this->patient_age)
            : (string) $this->patient_age;

        return $number . ' ' . $unit;
    }

    public function isReleased(): bool
    {
        return in_array($this->status, self::RELEASED_STATUSES, true);
    }

    /** অতীতের তারিখ কি না — অ্যাডমিনে সম্পাদনা সীমিত করতে */
    public function isPast(): bool
    {
        return $this->appointment_date->lt(now()->startOfDay());
    }
}
