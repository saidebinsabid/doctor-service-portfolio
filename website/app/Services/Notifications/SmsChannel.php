<?php

namespace App\Services\Notifications;

use App\Models\Appointment;
use App\Models\MessageLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SMS — ঐচ্ছিক ও পেইড।
 *
 * ⚠️ SMS ও WhatsApp সম্পূর্ণ আলাদা দুটি মাধ্যম। SMS পাঠালে সেটি
 *    রোগীর WhatsApp-এ যায় না, WhatsApp বার্তাও SMS-এ যায় না।
 *
 * চালু করতে হলে:
 *   ১. বাংলাদেশি গেটওয়ে অ্যাকাউন্ট (BulkSMSBD, Alpha Net, MIM SMS…)
 *      — প্রতিষ্ঠানের নামে খুলতে হয়
 *   ২. Sender ID (মাস্কিং) অনুমোদন — ৩–৭ কর্মদিবস
 *   ৩. .env-এ SMS_ENABLED=true এবং API তথ্য
 *
 * বেশিরভাগ বাংলাদেশি গেটওয়ে একই ধাঁচের GET/POST API দেয়, তাই
 * নিচের গঠনটি সামান্য বদলেই যেকোনো প্রোভাইডারে চলবে।
 */
class SmsChannel
{
    public function __construct(protected MessageBuilder $messages)
    {
    }

    public function enabled(): bool
    {
        return (bool) config('site.sms.enabled')
            && filled(config('site.sms.api_url'))
            && filled(config('site.sms.api_key'));
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
            return;                       // চুপচাপ বাদ — SMS চালু না থাকলে কিছু হবে না
        }

        try {
            $response = Http::timeout(15)->asForm()->post(config('site.sms.api_url'), [
                'api_key'   => config('site.sms.api_key'),
                'senderid'  => config('site.sms.sender_id'),
                'number'    => normalize_bd_phone($appointment->patient_phone),
                'message'   => $body,
            ]);

            MessageLog::create([
                'appointment_id' => $appointment->id,
                'channel'   => 'sms',
                'recipient' => $appointment->patient_phone,
                'template'  => $template,
                'body'      => $body,
                'status'    => $response->successful() ? 'sent' : 'failed',
                'provider_response' => $response->body(),
                'sent_at'   => $response->successful() ? now() : null,
            ]);
        } catch (\Throwable $e) {
            Log::error('SMS পাঠাতে ব্যর্থ: ' . $e->getMessage());

            MessageLog::create([
                'appointment_id' => $appointment->id,
                'channel'   => 'sms',
                'recipient' => $appointment->patient_phone,
                'template'  => $template,
                'body'      => $body,
                'status'    => 'failed',
                'provider_response' => $e->getMessage(),
            ]);
        }
    }
}
