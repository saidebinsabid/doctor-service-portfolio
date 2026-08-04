<?php

namespace App\Services\Notifications;

use App\Models\Appointment;
use App\Models\Setting;

/**
 * রোগীর কাছে যাওয়া বার্তার লেখা তৈরি করে।
 *
 * এক জায়গায় রাখার কারণ: একই বার্তা WhatsApp লিংক, Cloud API ও SMS —
 * তিন জায়গাতেই লাগে। আলাদা আলাদা লিখলে একটিতে পরিবর্তন করে
 * অন্যটিতে ভুলে যাওয়ার ঝুঁকি থাকত।
 *
 * ⭐ বার্তা সবসময় রোগীর নিজের ভাষায় তৈরি হয় — তিনি যে ভাষায়
 *    বুকিং করেছেন সেটি appointments.locale-এ সংরক্ষিত থাকে।
 */
class MessageBuilder
{
    /** রোগী যে বার্তাটি চেম্বারের নম্বরে পাঠাবেন (Phase 1) */
    public function patientToChamber(Appointment $a): string
    {
        return $this->withLocale($a, function () use ($a) {
            $lines = [];

            $lines[] = Setting::get('whatsapp_greeting')
                ?: __('booking.wa_greeting');
            $lines[] = '';

            foreach ($this->details($a) as $label => $value) {
                $lines[] = '▸ ' . $label . ': ' . $value;
            }

            $lines[] = '';
            $lines[] = __('booking.wa_thanks');

            return implode("\n", $lines);
        });
    }

    /** চেম্বার থেকে রোগীর কাছে যাওয়া নিশ্চিতকরণ (Phase 2 / SMS) */
    public function confirmationToPatient(Appointment $a): string
    {
        return $this->withLocale($a, function () use ($a) {
            $lines = [];

            $lines[] = __('booking.msg_confirmed_head', [
                'name' => $a->patient_name,
            ]);
            $lines[] = '';

            foreach ($this->details($a) as $label => $value) {
                $lines[] = '▸ ' . $label . ': ' . $value;
            }

            $lines[] = '';
            $lines[] = $a->chamber->name;
            $lines[] = $a->chamber->address;
            $lines[] = '';
            $lines[] = __('booking.arrive_early');

            return implode("\n", $lines);
        });
    }

    /** অ্যাপয়েন্টমেন্টের আগের দিনের রিমাইন্ডার (Phase 2) */
    public function reminderToPatient(Appointment $a): string
    {
        return $this->withLocale($a, fn () => implode("\n", [
            __('booking.msg_reminder_head', ['name' => $a->patient_name]),
            '',
            '▸ ' . __('booking.f_date') . ': ' . fmt_date($a->appointment_date) . ' (' . fmt_day($a->appointment_date) . ')',
            '▸ ' . __('booking.f_time') . ': ' . fmt_time($a->slotHm()),
            '▸ ' . __('booking.f_serial') . ': ' . bn_number($a->serial_no),
            '',
            $a->chamber->name,
            '',
            __('booking.arrive_early'),
        ]));
    }

    public function cancellationToPatient(Appointment $a): string
    {
        return $this->withLocale($a, fn () => implode("\n", [
            __('booking.msg_cancelled_head', ['name' => $a->patient_name]),
            '',
            '▸ ' . __('booking.f_serial') . ': ' . bn_number($a->serial_no),
            '▸ ' . __('booking.f_date') . ': ' . fmt_date($a->appointment_date),
            '▸ ' . __('booking.f_time') . ': ' . fmt_time($a->slotHm()),
            '',
            __('booking.msg_cancelled_tail'),
        ]));
    }

    /** অ্যাডমিনের কাছে যাওয়া বার্তা — সবসময় বাংলায় */
    public function newBookingToAdmin(Appointment $a): string
    {
        return $this->withLocale($a, function () use ($a) {
            $lines = ['🔔 নতুন সিরিয়াল বুক হয়েছে', ''];

            foreach ($this->details($a) as $label => $value) {
                $lines[] = '▸ ' . $label . ': ' . $value;
            }

            if ($a->problem) {
                $lines[] = '▸ সমস্যা: ' . $a->problem;
            }

            return implode("\n", $lines);
        }, forceLocale: 'bn');
    }

    /**
     * বার্তার মূল অংশ — সব চ্যানেলে একই ক্রমে একই তথ্য।
     *
     * @return array<string, string>
     */
    protected function details(Appointment $a): array
    {
        $rows = [
            __('booking.f_patient') => $a->patient_name,
        ];

        if ($a->patient_age !== null) {
            $rows[__('booking.f_age')] = $a->ageLabel();
        }

        $rows[__('booking.f_phone')]  = $a->patient_phone;
        $rows[__('booking.f_date')]   = fmt_date($a->appointment_date) . ' (' . fmt_day($a->appointment_date) . ')';
        $rows[__('booking.f_time')]   = fmt_time($a->slotHm());
        $rows[__('booking.f_serial')] = bn_number($a->serial_no);
        $rows[__('booking.f_visit')]  = $a->visitLabel();

        return $rows;
    }

    /**
     * বার্তা তৈরির সময় সাময়িকভাবে রোগীর ভাষায় বদলে নেওয়া।
     *
     * দরকার কারণ: অ্যাডমিন ইংরেজিতে সাইট ব্যবহার করতে করতে বাংলাভাষী
     * রোগীর বুকিং বাতিল করলে বার্তাটি ইংরেজিতে চলে যেত।
     */
    protected function withLocale(Appointment $a, callable $fn, ?string $forceLocale = null): string
    {
        $previous = app()->getLocale();
        $target = $forceLocale ?: ($a->locale ?: 'bn');

        app()->setLocale($target);

        try {
            return $fn();
        } finally {
            app()->setLocale($previous);
        }
    }
}
