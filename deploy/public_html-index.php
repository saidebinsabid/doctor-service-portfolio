<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| প্রোডাকশন docroot বুটস্ট্র্যাপ (~/public_html/index.php)
|--------------------------------------------------------------------------
| deploy.sh এই ফাইলটি ~/public_html/index.php হিসেবে বসায়।
|
| Laravel অ্যাপ থাকে ~/dr-abu-sufian/website-এ — ওয়েব থেকে সম্পূর্ণ অগম্য।
| এই ফাইল অ্যাপকে বাইরে থেকে বুট করে এবং public path হিসেবে ~/public_html
| ব্যবহার করায়। LiteSpeed-এ symlink করা docroot সার্ভ হয় না, তাই deploy.sh
| অ্যাপের public/ এই ফোল্ডারে কপি করে রাখে।
*/

$appBase = dirname(__DIR__) . '/doctor-service-portfolio/website';

// রক্ষণাবেক্ষণ মোড
if (file_exists($maintenance = $appBase . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer অটোলোডার (অ্যাপ ফোল্ডার থেকে)
require $appBase . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $appBase . '/bootstrap/app.php';

// public path = এই docroot (~/public_html) — অ্যাসেট ও storage এখান থেকে সার্ভ হয়
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
