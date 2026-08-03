<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// রক্ষণাবেক্ষণ মোড চালু আছে কি না
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer অটোলোডার
require __DIR__ . '/../vendor/autoload.php';

// Laravel চালু করে রিকোয়েস্ট হ্যান্ডল করা
/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
