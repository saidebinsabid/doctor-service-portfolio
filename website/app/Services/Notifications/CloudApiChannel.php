<?php

namespace App\Services\Notifications;

use App\Models\Appointment;
use App\Models\MessageLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Phase 2 — WhatsApp Cloud API (Meta অফিসিয়াল), পেইড।
 *
 * চালু করার আগে যা লাগবে:
 *   ১. Meta Business Account + বিজনেস ভেরিফিকেশন
 *   ২. একটি নতুন ফোন নম্বর — যা সাধারণ WhatsApp অ্যাপে ব্যবহৃত নয়
 *      ⚠️ 01327804433 অ্যাপে চালু আছে, তাই ওটা ব্যবহার করা যাবে না।
 *         করলে চেম্বারের WhatsApp বন্ধ হয়ে যাবে।
 *   ৩. Meta-অনুমোদিত মেসেজ টেমপ্লেট (প্রতিটি আলাদা রিভিউ হয়)
 *   ৪. ওয়েবসাইটে Privacy Policy ও Terms পেজ
 *
 * তারপর .env-এ:
 *   WHATSAPP_CHANNEL=cloud_api
 *   WHATSAPP_PHONE_ID=…
 *   WHATSAPP_TOKEN=…
 *
 * অ্যাপ্লিকেশনের আর কোথাও কিছু বদলাতে হবে না।
 */
class CloudApiChannel implements WhatsAppChannel
{
    protected const API_VERSION = 'v21.0';

    public function __construct(protected MessageBuilder $messages)
    {
    }

    public function isAutomatic(): bool
    {
        return true;
    }

    public function sendCreated(Appointment $appointment): void
    {
        $this->sendTemplate(
            $appointment,
            config('site.whatsapp.template_confirm'),
            'created',
            [
                $appointment->patient_name,
                $appointment->booking_code,
                fmt_date($appointment->appointment_date),
                fmt_time($appointment->slotHm()),
                (string) $appointment->serial_no,
            ],
        );
    }

    public function sendConfirmed(Appointment $appointment): void
    {
        $this->sendTemplate(
            $appointment,
            config('site.whatsapp.template_confirm'),
            'confirmed',
            [
                $appointment->patient_name,
                $appointment->booking_code,
                fmt_date($appointment->appointment_date),
                fmt_time($appointment->slotHm()),
                (string) $appointment->serial_no,
            ],
        );
    }

    public function sendReminder(Appointment $appointment): void
    {
        $this->sendTemplate(
            $appointment,
            config('site.whatsapp.template_reminder'),
            'reminder',
            [
                $appointment->patient_name,
                fmt_time($appointment->slotHm()),
                (string) $appointment->serial_no,
            ],
        );
    }

    public function sendCancelled(Appointment $appointment): void
    {
        /* বাতিলের বার্তা টেমপ্লেট ছাড়াই পাঠানো যায় শুধু তখনই, যখন
           রোগী শেষ ২৪ ঘণ্টায় বার্তা পাঠিয়েছেন। নিশ্চিত না থাকায়
           এখানে লগ রাখা হচ্ছে; প্রয়োজনে টেমপ্লেট যোগ করা যাবে। */
        $this->log($appointment, 'cancelled',
            $this->messages->cancellationToPatient($appointment), 'queued', null);
    }

    protected function sendTemplate(
        Appointment $appointment,
        string $template,
        string $slug,
        array $params,
    ): void {
        $phoneId = config('site.whatsapp.phone_id');
        $token   = config('site.whatsapp.token');

        if (blank($phoneId) || blank($token)) {
            Log::warning('Cloud API চালু নেই — WHATSAPP_PHONE_ID / WHATSAPP_TOKEN খালি।');
            $this->log($appointment, $slug, '', 'failed', 'credentials missing');

            return;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => intl_bd_phone($appointment->patient_phone),
            'type' => 'template',
            'template' => [
                'name' => $template,
                'language' => ['code' => $appointment->locale === 'en' ? 'en' : 'bn'],
                'components' => [[
                    'type' => 'body',
                    'parameters' => array_map(
                        fn ($p) => ['type' => 'text', 'text' => $p],
                        $params,
                    ),
                ]],
            ],
        ];

        try {
            $response = Http::withToken($token)
                ->timeout(15)
                ->post("https://graph.facebook.com/" . self::API_VERSION . "/{$phoneId}/messages", $payload);

            $this->log(
                $appointment,
                $slug,
                json_encode($params, JSON_UNESCAPED_UNICODE),
                $response->successful() ? 'sent' : 'failed',
                $response->body(),
            );
        } catch (\Throwable $e) {
            /* বার্তা না গেলেও বুকিং যেন নষ্ট না হয় — রোগীর সিরিয়াল ঠিকই থাকবে */
            Log::error('WhatsApp Cloud API ব্যর্থ: ' . $e->getMessage());
            $this->log($appointment, $slug, '', 'failed', $e->getMessage());
        }
    }

    protected function log(
        Appointment $appointment,
        string $template,
        string $body,
        string $status,
        ?string $response,
    ): void {
        MessageLog::create([
            'appointment_id' => $appointment->id,
            'channel'   => 'whatsapp',
            'recipient' => $appointment->patient_phone,
            'template'  => $template,
            'body'      => $body,
            'status'    => $status,
            'provider_response' => $response,
            'sent_at'   => $status === 'sent' ? now() : null,
        ]);
    }
}
