<?php

namespace App\Services\Notifications;

use App\Models\Appointment;
use App\Models\MessageLog;

/**
 * Phase 1 — সম্পূর্ণ ফ্রি, লঞ্চের দিন থেকেই কাজ করে।
 *
 * সার্ভার থেকে কোনো বার্তা পাঠানো হয় না। বদলে রোগীকে একটি
 * wa.me লিংক দেওয়া হয় যাতে বার্তাটি আগে থেকেই লেখা থাকে —
 * রোগী শুধু Send চাপেন।
 *
 * ⚠️ কেন এভাবে:
 *    WhatsApp-এ সার্ভার থেকে স্বয়ংক্রিয় বার্তা পাঠাতে হলে Meta-র
 *    Cloud API লাগে, যাতে বিজনেস ভেরিফিকেশন, টেমপ্লেট অনুমোদন ও
 *    প্রতি বার্তায় খরচ আছে। অনানুষ্ঠানিক লাইব্রেরি দিয়েও করা যায়,
 *    কিন্তু তাতে WhatsApp-এর শর্ত ভঙ্গ হয় ও নম্বর ব্যান হওয়ার
 *    ঝুঁকি থাকে — ডাক্তারের চেম্বারের নম্বরে সেই ঝুঁকি নেওয়া হয়নি।
 *
 *    রোগীর দিক থেকে ফল প্রায় একই, শুধু একবার Send চাপতে হয়।
 *    আর চেম্বারের WhatsApp Business অ্যাপের Greeting Message
 *    স্বয়ংক্রিয় উত্তরটি দিয়ে দেয় — সেটিও ফ্রি।
 */
class WaLinkChannel implements WhatsAppChannel
{
    public function __construct(protected MessageBuilder $messages)
    {
    }

    public function isAutomatic(): bool
    {
        return false;
    }

    /**
     * রোগীকে দেখানোর জন্য লিংক তৈরি করে।
     * সাকসেস পেজের সবুজ বাটনটি এই লিংকেই যায়।
     */
    public function linkFor(Appointment $appointment): string
    {
        return 'https://wa.me/' . config('site.whatsapp.number')
            . '?text=' . rawurlencode($this->messages->patientToChamber($appointment));
    }

    public function sendCreated(Appointment $appointment): void
    {
        /* পাঠানো হয় না — শুধু লগে রাখা হয় যে বার্তাটি রোগীকে দেখানো হয়েছে।
           অ্যাডমিন প্যানেলে দেখা যাবে কোন বুকিংয়ে কী দেখানো হয়েছিল। */
        $this->log($appointment, 'created', $this->messages->patientToChamber($appointment));
    }

    public function sendConfirmed(Appointment $appointment): void
    {
        /* Phase 1-এ নিশ্চিতকরণ বার্তা অ্যাডমিন নিজে WhatsApp থেকে পাঠান।
           অ্যাডমিন প্যানেলে "কপি করুন" বাটনসহ লেখাটি প্রস্তুত থাকে। */
        $this->log($appointment, 'confirmed', $this->messages->confirmationToPatient($appointment));
    }

    public function sendCancelled(Appointment $appointment): void
    {
        $this->log($appointment, 'cancelled', $this->messages->cancellationToPatient($appointment));
    }

    public function sendReminder(Appointment $appointment): void
    {
        $this->log($appointment, 'reminder', $this->messages->reminderToPatient($appointment));
    }

    /** অ্যাডমিন প্যানেল থেকে রোগীকে সরাসরি বার্তা পাঠানোর লিংক */
    public function adminReplyLink(Appointment $appointment, string $body): string
    {
        return 'https://wa.me/' . intl_bd_phone($appointment->patient_phone)
            . '?text=' . rawurlencode($body);
    }

    protected function log(Appointment $appointment, string $template, string $body): void
    {
        MessageLog::create([
            'appointment_id' => $appointment->id,
            'channel'   => 'whatsapp',
            'recipient' => $appointment->patient_phone,
            'template'  => $template,
            'body'      => $body,
            /* manual = বার্তাটি প্রস্তুত, কিন্তু মানুষ পাঠাবে */
            'status'    => 'manual',
        ]);
    }
}
