<?php

namespace App\Services\Notifications;

use App\Models\Appointment;

/**
 * WhatsApp চ্যানেলের চুক্তি।
 *
 * দুটি বাস্তবায়ন আছে:
 *   WaLinkChannel   → Phase 1, ফ্রি। রোগী নিজে Send চাপেন।
 *   CloudApiChannel → Phase 2, পেইড। সার্ভার থেকেই বার্তা যায়।
 *
 * config/site.php-এর whatsapp.channel বদলালেই একটির বদলে অন্যটি চালু হয় —
 * অ্যাপ্লিকেশনের বাকি কোথাও কিছু বদলাতে হয় না।
 */
interface WhatsAppChannel
{
    /** নতুন বুকিং হয়েছে */
    public function sendCreated(Appointment $appointment): void;

    /** অ্যাডমিন নিশ্চিত করেছেন */
    public function sendConfirmed(Appointment $appointment): void;

    /** বুকিং বাতিল হয়েছে */
    public function sendCancelled(Appointment $appointment): void;

    /** আগের দিনের স্মরণ করিয়ে দেওয়া */
    public function sendReminder(Appointment $appointment): void;

    /** এই চ্যানেল সত্যিই স্বয়ংক্রিয়ভাবে পাঠাতে পারে কি না */
    public function isAutomatic(): bool;
}
