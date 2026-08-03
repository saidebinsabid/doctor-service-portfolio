<?php

namespace App\Exceptions;

use Exception;

/**
 * চাওয়া স্লটটি ইতিমধ্যে অন্য কেউ নিয়ে ফেলেছেন, ব্লক করা আছে,
 * বা সময় পেরিয়ে গেছে।
 */
class SlotUnavailableException extends Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?: __('booking.slot_taken'));
    }
}
