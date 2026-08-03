<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * প্রতিটি রেসপন্সে নিরাপত্তা হেডার।
 *
 * রোগীর স্বাস্থ্যতথ্য নিয়ে কাজ, তাই ক্লিকজ্যাকিং ও MIME-স্নিফিং
 * আক্রমণের সহজ পথগুলো বন্ধ রাখা হয়েছে।
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            /* অন্য সাইট iframe-এ ঢুকিয়ে ক্লিকজ্যাকিং করতে পারবে না */
            'X-Frame-Options' => 'SAMEORIGIN',

            /* ব্রাউজার ফাইলের ধরন নিজে অনুমান করবে না */
            'X-Content-Type-Options' => 'nosniff',

            /* অন্য সাইটে যাওয়ার সময় পুরো ঠিকানা ফাঁস হবে না */
            'Referrer-Policy' => 'strict-origin-when-cross-origin',

            /* ক্যামেরা, মাইক্রোফোন, লোকেশন — কিছুই লাগে না */
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=()',

            /* ⚠️ রোগীর তথ্যসহ পাতা যেন সার্চ ইঞ্জিন ক্যাশে না রাখে */
            'X-Robots-Tag' => $this->robotsFor($request),
        ];

        foreach ($headers as $key => $value) {
            if ($value !== null && ! $response->headers->has($key)) {
                $response->headers->set($key, $value);
            }
        }

        /* HTTPS-এ থাকলে ব্রাউজারকে বলা: এই সাইটে আর কখনো http নয় */
        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains',
            );
        }

        return $response;
    }

    /**
     * অ্যাডমিন প্যানেল ও বুকিং-স্ট্যাটাসের পাতা গুগলে আসা উচিত নয় —
     * ওখানে রোগীর নাম ও ফোন নম্বর থাকে।
     */
    protected function robotsFor(Request $request): ?string
    {
        $private = $request->is('admin', 'admin/*')
            || $request->is('*/booking/status*', 'booking/status*')
            || $request->is('*/booking/success*', 'booking/success*');

        return $private ? 'noindex, nofollow' : null;
    }
}
