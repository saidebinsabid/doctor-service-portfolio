<?php

namespace App\Exceptions;

use Exception;

/**
 * একই মোবাইল নম্বর বা একই IP থেকে অনুমোদিত সীমার বেশি বুকিং।
 *
 * ভুয়া বুকিং দিয়ে ২৫টি সিরিয়াল দখল করে রাখা ঠেকাতে।
 */
class BookingLimitException extends Exception
{
}
