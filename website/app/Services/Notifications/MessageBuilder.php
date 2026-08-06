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
    /**
     * সিরিয়াল স্লিপের কম্প্যাক্ট রূপ (ক্লায়েন্টের অনুরোধে — ইবনে সিনার SMS-এর মতো):
     *   ডা. আবু সুফিয়ান — সিরিয়াল: ১৪
     *   [চেম্বারের নাম]
     *   [ঠিকানা / রুম]
     *   [তারিখ (বার), সময়]
     *   হটলাইন: [নম্বর]
     *
     * @return array<int, string>
     */
    protected function compactSlip(Appointment $a): array
    {
        $hotline = $a->chamber->hotline ?: Setting::get('hotline');

        return array_values(array_filter([
            Setting::get('doctor_short') . ' — ' . __('booking.f_serial') . ': ' . bn_number($a->serial_no),
            $a->chamber->name,
            $a->chamber->address,
            fmt_date($a->appointment_date) . ' (' . fmt_day($a->appointment_date) . '), ' . fmt_time($a->slotHm()),
            $hotline ? __('chm.hotline') . ': ' . $hotline : null,
        ]));
    }

    /** রোগী যে বার্তাটি চেম্বারের নম্বরে পাঠাবেন (Phase 1) */
    public function patientToChamber(Appointment $a): string
    {
        return $this->withLocale($a, function () use ($a) {
            return implode("\n", array_merge(
                [
                    Setting::get('whatsapp_greeting') ?: __('booking.wa_greeting'),
                    '',
                    __('booking.f_patient') . ': ' . $a->patient_name . ' — ' . $a->patient_phone,
                ],
                $this->compactSlip($a),
                ['', __('booking.wa_thanks')],
            ));
        });
    }

    /** চেম্বার থেকে রোগীর কাছে যাওয়া নিশ্চিতকরণ (Phase 2 / SMS) */
    public function confirmationToPatient(Appointment $a): string
    {
        return $this->withLocale($a, function () use ($a) {
            return implode("\n", array_merge(
                [__('booking.msg_confirmed_head', ['name' => $a->patient_name]), ''],
                $this->compactSlip($a),
                ['', __('booking.arrive_early')],
            ));
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
