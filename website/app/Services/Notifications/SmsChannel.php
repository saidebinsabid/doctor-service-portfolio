<?php

namespace App\Services\Notifications;

use App\Models\Appointment;
use App\Models\MessageLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SMS চ্যানেল — বাংলাদেশি প্রোভাইডার sms.net.bd (পেইড)।
 *
 * অ্যাডমিন → সেটিংস → বুকিং সেকশন থেকে API key + (ঐচ্ছিক) Sender ID
 * বসালেই চালু হয়ে যায়; কোনো .env এডিট লাগে না। খালি রাখলে ফিচারটি
 * নীরবে বন্ধ থাকে — বুকিং স্বাভাবিকভাবে চলবে।
 *
 * এন্ডপয়েন্ট: https://api.sms.net.bd/sendsms
 * প্যারামিটার: api_key, msg, to, sender_id (ঐচ্ছিক)
 * সাফল্য প্রতিক্রিয়া: JSON { "error": 0, ... }; ননজিরো = ব্যর্থ।
 */
class SmsChannel
{
    /** sms.net.bd-এর SMS পাঠানোর এন্ডপয়েন্ট */
    protected const ENDPOINT = 'https://api.sms.net.bd/sendsms';

    public function __construct(protected MessageBuilder $messages)
    {
    }

    public function enabled(): bool
    {
        return filled(Setting::get('sms_api_key'));
    }

    /** নতুন বুকিং হওয়ামাত্র রোগীকে সিরিয়াল-স্লিপ পাঠায় (Phase 2 — ক্লায়েন্টের অনুরোধে) */
    public function sendCreated(Appointment $appointment): void
    {
        $this->send($appointment, 'booked', $this->messages->bookedToPatient($appointment));
    }

    public function sendConfirmed(Appointment $appointment): void
    {
        $this->send($appointment, 'confirmed', $this->messages->confirmationToPatient($appointment));
    }

    public function sendReminder(Appointment $appointment): void
    {
        $this->send($appointment, 'reminder', $this->messages->reminderToPatient($appointment));
    }

    public function sendCancelled(Appointment $appointment): void
    {
        $this->send($appointment, 'cancelled', $this->messages->cancellationToPatient($appointment));
    }

    protected function send(Appointment $appointment, string $template, string $body): void
    {
        if (! $this->enabled()) {
            return;
        }

        $to = normalize_bd_phone($appointment->patient_phone);
        $params = array_filter([
            'api_key'   => Setting::get('sms_api_key'),
            'msg'       => $body,
            'to'        => $to,
            'sender_id' => Setting::get('sms_sender_id') ?: null,
        ], fn ($v) => filled($v));

        try {
            $response = Http::timeout(15)->asForm()->post(self::ENDPOINT, $params);
            $json = $response->json();

            /* sms.net.bd: error=0 → সফল, ননজিরো → ব্যর্থ */
            $ok = $response->successful() && (is_array($json) ? ((int) ($json['error'] ?? -1) === 0) : false);

            MessageLog::create([
                'appointment_id' => $appointment->id,
                'channel'   => 'sms',
                'recipient' => $to,
                'template'  => $template,
                'body'      => $body,
                'status'    => $ok ? 'sent' : 'failed',
                'provider_response' => mb_substr($response->body(), 0, 2000),
                'sent_at'   => $ok ? now() : null,
            ]);
        } catch (\Throwable $e) {
            Log::error('SMS পাঠাতে ব্যর্থ (sms.net.bd): ' . $e->getMessage());

            MessageLog::create([
                'appointment_id' => $appointment->id,
                'channel'   => 'sms',
                'recipient' => $to,
                'template'  => $template,
                'body'      => $body,
                'status'    => 'failed',
                'provider_response' => $e->getMessage(),
            ]);
        }
    }
}
