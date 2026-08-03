<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * গুগলের জন্য sitemap.xml ও robots.txt।
 *
 * দুই ভাষার প্রতিটি পাতা আলাদা করে দেওয়া হয়, সাথে hreflang —
 * যাতে গুগল বাংলাভাষী ও ইংরেজিভাষী দর্শককে সঠিক ভার্সন দেখায়।
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        $routes = [
            ['name' => 'home',           'priority' => '1.0', 'freq' => 'weekly'],
            ['name' => 'booking.create', 'priority' => '0.9', 'freq' => 'weekly'],
            ['name' => 'privacy',        'priority' => '0.3', 'freq' => 'yearly'],
            ['name' => 'terms',          'priority' => '0.3', 'freq' => 'yearly'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '
            . 'xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($routes as $route) {
            foreach (['bn', 'en'] as $locale) {
                $url = route($route['name'], ['locale' => $locale === 'en' ? 'en' : null]);

                $xml .= "  <url>\n";
                $xml .= '    <loc>' . e($url) . "</loc>\n";
                $xml .= '    <changefreq>' . $route['freq'] . "</changefreq>\n";
                $xml .= '    <priority>' . $route['priority'] . "</priority>\n";

                /* একই পাতার অন্য ভাষার ঠিকানা */
                foreach (['bn', 'en'] as $alt) {
                    $altUrl = route($route['name'], ['locale' => $alt === 'en' ? 'en' : null]);
                    $xml .= '    <xhtml:link rel="alternate" hreflang="' . $alt
                        . '" href="' . e($altUrl) . "\"/>\n";
                }

                $xml .= "  </url>\n";
            }
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        /* ⚠️ অ্যাডমিন প্যানেল ও রোগীর তথ্যসহ পাতাগুলো গুগলে আসা উচিত নয় */
        $lines = [
            'User-agent: *',
            'Allow: /',
            '',
            'Disallow: /admin',
            'Disallow: /booking/success',
            'Disallow: /booking/status',
            'Disallow: /en/booking/success',
            'Disallow: /en/booking/status',
            '',
            'Sitemap: ' . route('sitemap'),
        ];

        return response(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }
}
