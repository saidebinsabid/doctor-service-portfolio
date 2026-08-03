<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * ভাষা নির্ধারণ।
 *
 * বাংলা ডিফল্ট, ঠিকানায় প্রিফিক্স ছাড়াই:   drabusufian.com/
 * ইংরেজি /en প্রিফিক্সে:                   drabusufian.com/en
 *
 * এভাবে করার কারণ: গুগল দুটি ভাষাকে আলাদা পেজ হিসেবে ইনডেক্স করতে পারে।
 * শুধু কুকি বা সেশনে রাখলে গুগল একটিই ভাষা দেখত।
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('site.locales', ['bn', 'en']);
        $default   = config('site.default_locale', 'bn');

        /* ঠিকানার প্রথম অংশ ভাষা কি না */
        $segment = $request->segment(1);

        $locale = in_array($segment, $supported, true) ? $segment : $default;

        App::setLocale($locale);

        /*
        | route('home') লিখলেই যেন চলমান ভাষার ঠিকানা তৈরি হয়।
        | বাংলায় null দেওয়ায় সেগমেন্টটি বাদ পড়ে — /  আর  /en
        */
        URL::defaults(['locale' => $locale === 'en' ? 'en' : null]);

        /* ভিউতে ব্যবহারের জন্য */
        view()->share('locale', $locale);
        view()->share('isBn', $locale === 'bn');

        return $next($request);
    }
}
