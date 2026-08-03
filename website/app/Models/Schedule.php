<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * চেম্বারের সাপ্তাহিক সময়সূচি।
 *
 * বর্তমান মান (✅ ভিজিটিং কার্ড অনুযায়ী):
 *   শনি–বৃহস্পতি  ১০:৩০ – ২:০০   (২১০ মিনিট ÷ ২৫ = ৮ মিনিট/রোগী)
 *   শুক্রবার      ৫:০০ – ৮:০০    (১৮০ মিনিট ÷ ২৫ = ৭ মিনিট/রোগী)
 *
 * সবই অ্যাডমিন প্যানেল থেকে বদলানো যায়।
 */
class Schedule extends Model
{
    protected $fillable = [
        'chamber_id', 'day_of_week', 'start_time', 'end_time',
        'slot_minutes', 'max_serials', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week'  => 'integer',
            'slot_minutes' => 'integer',
            'max_serials'  => 'integer',
            'is_active'    => 'boolean',
        ];
    }

    /* Carbon::dayOfWeek ও PHP-র date('w') — দুটোতেই 0=রবিবার */
    public const DAYS_BN = ['রবিবার', 'সোমবার', 'মঙ্গলবার', 'বুধবার', 'বৃহস্পতিবার', 'শুক্রবার', 'শনিবার'];
    public const DAYS_EN = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    public function chamber()
    {
        return $this->belongsTo(Chamber::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function dayName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en'
            ? self::DAYS_EN[$this->day_of_week]
            : self::DAYS_BN[$this->day_of_week];
    }

    /** "10:30:00" → "10:30" */
    public function startHm(): string
    {
        return substr((string) $this->start_time, 0, 5);
    }

    public function endHm(): string
    {
        return substr((string) $this->end_time, 0, 5);
    }

    /** সময়সূচি অনুযায়ী সর্বোচ্চ কতটি স্লট আঁটে (max_serials-এর সীমা মেনে) */
    public function capacity(): int
    {
        $minutes = $this->minutesSpan();

        if ($this->slot_minutes < 1) {
            return 0;
        }

        return min($this->max_serials, intdiv($minutes, $this->slot_minutes));
    }

    public function minutesSpan(): int
    {
        return $this->toMinutes($this->endHm()) - $this->toMinutes($this->startHm());
    }

    protected function toMinutes(string $hm): int
    {
        [$h, $m] = array_map('intval', explode(':', $hm));

        return $h * 60 + $m;
    }
}
