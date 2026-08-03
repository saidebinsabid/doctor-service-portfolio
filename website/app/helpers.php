<?php

/*
|--------------------------------------------------------------------------
| বাংলা সংখ্যা, তারিখ ও সময়
|--------------------------------------------------------------------------
| ওয়েবসাইটের সব জায়গায় এই ফাংশনগুলোই ব্যবহার হয়, যাতে ফরম্যাট
| এক রকম থাকে — একজায়গায় "১০:৩০" আর আরেক জায়গায় "10:30" না হয়।
|
| চলমান ভাষা ইংরেজি হলে ইংরেজি অঙ্কই ফেরত যায়।
*/

if (! function_exists('bn_number')) {
    /** 25 → "২৫" (বাংলা ভাষায়), "25" (ইংরেজিতে) */
    function bn_number(int|string|float|null $value, ?string $locale = null): string
    {
        if ($value === null) {
            return '';
        }

        $locale ??= app()->getLocale();

        if ($locale !== 'bn') {
            return (string) $value;
        }

        return strtr((string) $value, [
            '0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪',
            '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯',
        ]);
    }
}

if (! function_exists('bn_months')) {
    function bn_months(): array
    {
        return ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন',
                'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
    }
}

if (! function_exists('bn_days')) {
    /** 0 = রবিবার (PHP-র date('w') ও Carbon::dayOfWeek এর সাথে মিল) */
    function bn_days(bool $short = false): array
    {
        return $short
            ? ['রবি', 'সোম', 'মঙ্গল', 'বুধ', 'বৃহঃ', 'শুক্র', 'শনি']
            : ['রবিবার', 'সোমবার', 'মঙ্গলবার', 'বুধবার', 'বৃহস্পতিবার', 'শুক্রবার', 'শনিবার'];
    }
}

if (! function_exists('fmt_date')) {
    /** "৫ আগস্ট ২০২৬" / "5 August 2026" */
    function fmt_date(mixed $date, ?string $locale = null): string
    {
        if (blank($date)) {
            return '';
        }

        $d = $date instanceof \DateTimeInterface ? $date : \Carbon\Carbon::parse($date);
        $locale ??= app()->getLocale();

        if ($locale !== 'bn') {
            return $d->format('j F Y');
        }

        return bn_number($d->format('j')) . ' '
            . bn_months()[(int) $d->format('n') - 1] . ' '
            . bn_number($d->format('Y'));
    }
}

if (! function_exists('fmt_day')) {
    /** "বুধবার" / "Wednesday" */
    function fmt_day(mixed $date, bool $short = false, ?string $locale = null): string
    {
        $d = $date instanceof \DateTimeInterface ? $date : \Carbon\Carbon::parse($date);
        $locale ??= app()->getLocale();

        if ($locale !== 'bn') {
            return $short ? $d->format('D') : $d->format('l');
        }

        return bn_days($short)[(int) $d->format('w')];
    }
}

if (! function_exists('fmt_time')) {
    /**
     * "14:30" → "দুপুর ২:৩০" / "2:30 PM"
     *
     * বাংলায় দিনের ভাগ যোগ করা হয় — বাংলাদেশে "২টা" বললে
     * দুপুর না রাত সেটা না বললে বোঝা যায় না।
     */
    function fmt_time(?string $hm, ?string $locale = null): string
    {
        if (blank($hm)) {
            return '';
        }

        $parts = explode(':', $hm);
        $H = (int) ($parts[0] ?? 0);
        $M = (int) ($parts[1] ?? 0);

        $h12 = $H % 12 === 0 ? 12 : $H % 12;
        $mm  = str_pad((string) $M, 2, '0', STR_PAD_LEFT);

        $locale ??= app()->getLocale();

        if ($locale !== 'bn') {
            return $h12 . ':' . $mm . ' ' . ($H < 12 ? 'AM' : 'PM');
        }

        $part = match (true) {
            $H < 4  => 'রাত',
            $H < 6  => 'ভোর',
            $H < 12 => 'সকাল',
            $H < 15 => 'দুপুর',
            $H < 18 => 'বিকাল',
            $H < 20 => 'সন্ধ্যা',
            default => 'রাত',
        };

        return $part . ' ' . bn_number($h12) . ':' . bn_number($mm);
    }
}

if (! function_exists('to_minutes')) {
    /** "10:30" → 630 */
    function to_minutes(string $hm): int
    {
        $parts = explode(':', $hm);

        return ((int) ($parts[0] ?? 0)) * 60 + ((int) ($parts[1] ?? 0));
    }
}

if (! function_exists('from_minutes')) {
    /** 630 → "10:30" */
    function from_minutes(int $minutes): string
    {
        return str_pad((string) intdiv($minutes, 60), 2, '0', STR_PAD_LEFT)
            . ':' . str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('group_schedules')) {
    /**
     * একই সময়ের পরপর দিনগুলো এক সারিতে জুড়ে দেয়।
     *
     * ৭টি আলাদা সারির বদলে ভিজিটিং কার্ডের মতো দুটি সারি দেখায়:
     *     শনিবার – বৃহস্পতিবার   ১০:৩০ – ২:০০
     *     শুক্রবার                ৫:০০ – ৮:০০
     *
     * সপ্তাহ শনিবার থেকে শুরু ধরা হয় — বাংলাদেশে কর্মসপ্তাহ সেভাবেই চলে।
     *
     * @return array<int, array{label: string, start: string, end: string, max: int}>
     */
    function group_schedules(iterable $schedules): array
    {
        /* শনি → শুক্র */
        $order = [6, 0, 1, 2, 3, 4, 5];

        $byDay = [];
        foreach ($schedules as $s) {
            if ($s->is_active) {
                $byDay[$s->day_of_week] = $s;
            }
        }

        $groups = [];
        $current = null;

        foreach ($order as $dow) {
            $s = $byDay[$dow] ?? null;

            if (! $s) {
                $current = null;          // ফাঁক পড়লে দল ভাঙে
                continue;
            }

            $signature = $s->startHm() . '-' . $s->endHm();

            if ($current && $current['signature'] === $signature) {
                $current['days'][] = $dow;
                $groups[array_key_last($groups)] = $current;

                continue;
            }

            $current = [
                'signature' => $signature,
                'days'      => [$dow],
                'start'     => $s->startHm(),
                'end'       => $s->endHm(),
                'max'       => $s->max_serials,
            ];

            $groups[] = $current;
        }

        $days = app()->getLocale() === 'en'
            ? ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
            : bn_days();

        return array_map(function (array $g) use ($days) {
            $first = $days[$g['days'][0]];
            $last  = $days[end($g['days'])];

            return [
                'label' => count($g['days']) > 1 ? "{$first} – {$last}" : $first,
                'start' => $g['start'],
                'end'   => $g['end'],
                'max'   => $g['max'],
            ];
        }, $groups);
    }
}

if (! function_exists('tone_classes')) {
    /**
     * আইকন বাবলের রঙ।
     *
     * পূর্ণ ক্লাস নাম লেখা জরুরি — Tailwind সোর্স ফাইল স্ক্যান করে
     * ক্লাস খুঁজে নেয়, তাই 'bg-' . $color এর মতো জোড়া লাগানো নাম
     * সে খুঁজে পায় না এবং CSS-এ থাকে না।
     */
    function tone_classes(?string $tone): string
    {
        return match ($tone) {
            'sky'     => 'bg-sky-50 text-sky-600',
            'rose'    => 'bg-rose-50 text-rose-600',
            'cyan'    => 'bg-cyan-50 text-cyan-600',
            'amber'   => 'bg-amber-50 text-amber-600',
            'green'   => 'bg-green-50 text-green-600',
            'lime'    => 'bg-lime-50 text-lime-600',
            'violet'  => 'bg-violet-50 text-violet-600',
            'indigo'  => 'bg-indigo-50 text-indigo-600',
            'teal'    => 'bg-teal-50 text-teal-600',
            'blue'    => 'bg-blue-50 text-blue-600',
            'orange'  => 'bg-orange-50 text-orange-600',
            'red'     => 'bg-red-50 text-red-600',
            'emerald' => 'bg-emerald-50 text-emerald-600',
            'slate'   => 'bg-slate-100 text-slate-600',
            default   => 'bg-brand-50 text-brand-700',
        };
    }
}

if (! function_exists('badge_classes')) {
    /** স্ট্যাটাস ব্যাজের রঙ (অ্যাডমিন প্যানেলে) */
    function badge_classes(?string $tone): string
    {
        return match ($tone) {
            'amber' => 'bg-amber-100 text-amber-800 ring-amber-200',
            'sky'   => 'bg-sky-100 text-sky-800 ring-sky-200',
            'green' => 'bg-green-100 text-green-800 ring-green-200',
            'red'   => 'bg-red-100 text-red-800 ring-red-200',
            default => 'bg-slate-100 text-slate-700 ring-slate-200',
        };
    }
}

if (! function_exists('normalize_bd_phone')) {
    /**
     * বাংলাদেশি মোবাইল নম্বর একই রূপে আনা।
     * "+8801712345678", "8801712345678", "01712-345678" → "01712345678"
     *
     * রোগীরা নানা ফরম্যাটে লেখেন; একই নম্বর দুই রকমে সেভ হলে
     * "দিনে সর্বোচ্চ ২টি বুকিং" নিয়মটি ফাঁকি দেওয়া যেত।
     */
    function normalize_bd_phone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';

        if (str_starts_with($digits, '880')) {
            $digits = '0' . substr($digits, 3);
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            $digits = '0' . $digits;
        }

        return $digits;
    }
}

if (! function_exists('intl_bd_phone')) {
    /** "01712345678" → "8801712345678" (wa.me লিংকের জন্য) */
    function intl_bd_phone(?string $phone): string
    {
        $local = normalize_bd_phone($phone);

        return $local ? '88' . $local : '';
    }
}
