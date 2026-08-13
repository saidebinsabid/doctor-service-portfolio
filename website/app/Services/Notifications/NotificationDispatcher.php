<?php

namespace App\Services\Notifications;

use App\Mail\AppointmentCreatedMail;
use App\Models\Appointment;
use App\Models\MessageLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * কোন ঘটনায় কোন চ্যানেলে বার্তা যাবে — সেই সিদ্ধান্ত এখানে।
 *
 * কন্ট্রোলার শুধু "নতুন বুকিং হয়েছে" জানায়; কোন চ্যানেল চালু আছে,
 * SMS পেইড কি না, Cloud API কনফিগার করা কি না — এসব নিয়ে
 * কন্ট্রোলারকে ভাবতে হয় না।
 */
class NotificationDispatcher
{
    public function __construct(
        protected WhatsAppChannel $whatsapp,
        protected SmsChannel $sms,
        protected MessageBuilder $messages,
    ) {
    }

    public function appointmentCreated(Appointment $appointment): void
    {
        /* WhatsApp link ছাড়া অন্যগুলো external HTTP call — সাথে সাথে
           চালালে রোগীর বুকিং ফর্ম "সাবমিটিং…" অবস্থায় ২–৪ সেকেন্ড
           আটকে থাকে। সেগুলো response পাঠানোর পরে চালানো হয় (ব্যবহারকারী
           তাৎক্ষণিক success পেজ দেখেন, notify গুলো background-এ যায়)। */
        $this->whatsapp->sendCreated($appointment);   // শুধু wa.me লিংক বানানো — instant

        app()->terminating(function () use ($appointment) {
            $this->mailAdmin($appointment);
            $this->sms->sendCreated($appointment);        // sms.net.bd
            $this->whatsappAdmin($appointment);           // CallMeBot
        });
    }

    public function appointmentConfirmed(Appointment $appointment): void
    {
        $this->whatsapp->sendConfirmed($appointment);
        $this->sms->sendConfirmed($appointment);
    }

    public function appointmentCancelled(Appointment $appointment): void
    {
        $this->whatsapp->sendCancelled($appointment);
        $this->sms->sendCancelled($appointment);
    }

    public function appointmentRescheduled(Appointment $appointment): void
    {
        /* সময় বদলালে নতুন সময়ের নিশ্চিতকরণই পাঠানো হয় */
        $this->whatsapp->sendConfirmed($appointment);
        $this->sms->sendConfirmed($appointment);
    }

    public function appointmentReminder(Appointment $appointment): void
    {
        $this->whatsapp->sendReminder($appointment);
        $this->sms->sendReminder($appointment);
    }

    /** নতুন বুকিং হলে চেম্বারের ইমেইলে জানানো — ফ্রি ও নির্ভরযোগ্য */
    protected function mailAdmin(Appointment $appointment): void
    {
        $to = config('site.notify.admin_email');

        if (blank($to)) {
            return;
        }

        try {
            Mail::to($to)->queue(new AppointmentCreatedMail($appointment));

            MessageLog::create([
                'appointment_id' => $appointment->id,
                'channel'   => 'email',
                'recipient' => $to,
                'template'  => 'admin_new_booking',
                'status'    => 'queued',
            ]);
        } catch (\Throwable $e) {
            /* ইমেইল না গেলেও রোগীর বুকিং নষ্ট হবে না */
            Log::error('অ্যাডমিন ইমেইল ব্যর্থ: ' . $e->getMessage());
        }
    }

    /**
     * নতুন বুকিং হলে ডাক্তারের নম্বরে স্বয়ংক্রিয় WhatsApp — CallMeBot (ফ্রি)।
     *
     * সেটআপ (একবার): অ্যাডমিন → সেটিংস-এ ডাক্তারের নম্বর ও CallMeBot API key
     * বসালেই চালু। দুটোর যেকোনোটি খালি থাকলে কিছুই হয় না (বুকিং অটুট)।
     * পেইড গেটওয়ে লাগে না; শুধু ডাক্তারের নিজের নম্বরে যায়।
     */
    protected function whatsappAdmin(Appointment $appointment): void
    {
        $number = trim((string) Setting::get('notify_whatsapp'));
        $apikey = trim((string) Setting::get('callmebot_apikey'));

        if (blank($number) || blank($apikey)) {
            return;
        }

        try {
            Http::timeout(8)->get('https://api.callmebot.com/whatsapp.php', [
                'phone'  => intl_bd_phone($number),
                'text'   => $this->messages->newBookingToAdmin($appointment),
                'apikey' => $apikey,
            ]);

            MessageLog::create([
                'appointment_id' => $appointment->id,
                'channel'   => 'whatsapp',
                'recipient' => $number,
                'template'  => 'admin_new_booking',
                'status'    => 'sent',
            ]);
        } catch (\Throwable $e) {
            /* না গেলেও রোগীর বুকিং নষ্ট হবে না */
            Log::error('অ্যাডমিন WhatsApp (CallMeBot) ব্যর্থ: ' . $e->getMessage());
        }
    }

    /** সাকসেস পেজের WhatsApp বাটনের লিংক (Phase 1) */
    public function whatsappLink(Appointment $appointment): ?string
    {
        return $this->whatsapp instanceof WaLinkChannel
            ? $this->whatsapp->linkFor($appointment)
            : null;
    }

    public function isAutomatic(): bool
    {
        return $this->whatsapp->isAutomatic();
    }
}
