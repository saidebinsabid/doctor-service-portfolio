<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| পাবলিক রুট
|--------------------------------------------------------------------------
| ভাষা ঠিকানার প্রথম অংশে:
|     drabusufian.com/         → বাংলা (ডিফল্ট, প্রিফিক্স ছাড়া)
|     drabusufian.com/en       → ইংরেজি
|
| {locale?} ঐচ্ছিক এবং শুধু "en" মিলবে, তাই /booking-এর মতো ঠিকানা
| ভুল করে ভাষা হিসেবে ধরা পড়ে না।
*/
Route::prefix('{locale?}')
    ->where(['locale' => 'en'])
    ->group(function () {

        Route::get('/', [HomeController::class, 'index'])->name('home');

        Route::get('privacy', [HomeController::class, 'privacy'])->name('privacy');
        Route::get('terms', [HomeController::class, 'terms'])->name('terms');

        /* ---------- বুকিং ---------- */
        Route::get('booking', [BookingController::class, 'create'])->name('booking.create');

        Route::post('booking', [BookingController::class, 'store'])
            ->middleware('throttle:booking')
            ->name('booking.store');

        Route::get('booking/success/{code}', [BookingController::class, 'success'])
            ->name('booking.success');

        Route::get('booking/status', [BookingController::class, 'statusForm'])->name('booking.status');

        Route::post('booking/status', [BookingController::class, 'statusLookup'])
            ->middleware('throttle:30,1')
            ->name('booking.status.lookup');

        /* ক্যালেন্ডার ও স্লট — পেজ রিলোড ছাড়াই তারিখ বদলানোর জন্য */
        Route::get('booking/calendar', [BookingController::class, 'calendar'])->name('booking.calendar');
        Route::get('booking/slots', [BookingController::class, 'slots'])->name('booking.slots');

        /* ---------- যোগাযোগ ---------- */
        Route::post('contact', [ContactController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('contact.store');
    });

/*
|--------------------------------------------------------------------------
| একই পাবলিক রুট — প্রিফিক্স ছাড়া (বাংলা ডিফল্ট) ম্যাচিংয়ের জন্য
|--------------------------------------------------------------------------
| Laravel-এর optional prefix {locale?} URL-এর শেষে না থাকায় প্রিফিক্স-ছাড়া
| রূপ (যেমন /booking) কোনো রুটে ম্যাচ করে না — শুধু /en/booking করে। অথচ
| route() ও URL::defaults ঠিকঠাক /booking তৈরি করে, ফলে হোমপেজের বুকিং লিংক
| 404 হতো। সমাধান: একই রুটগুলো প্রিফিক্স ছাড়া আবার রেজিস্টার করা।
|
| এগুলো ইচ্ছাকৃতভাবে নামহীন — URL তৈরি ও locale-awareness আগের নামযুক্ত
| {locale?} group থেকেই আসে; এই group শুধু বাংলা (প্রিফিক্স-ছাড়া) ঠিকানা
| ম্যাচ করিয়ে কন্ট্রোলারে পাঠায়। '/' হোম আগের group-এই ম্যাচ করে, তাই এখানে নেই।
*/
Route::group([], function () {

    Route::get('privacy', [HomeController::class, 'privacy']);
    Route::get('terms', [HomeController::class, 'terms']);

    Route::get('booking', [BookingController::class, 'create']);
    Route::post('booking', [BookingController::class, 'store'])->middleware('throttle:booking');
    Route::get('booking/success/{code}', [BookingController::class, 'success']);
    Route::get('booking/status', [BookingController::class, 'statusForm']);
    Route::post('booking/status', [BookingController::class, 'statusLookup'])->middleware('throttle:30,1');
    Route::get('booking/calendar', [BookingController::class, 'calendar']);
    Route::get('booking/slots', [BookingController::class, 'slots']);

    Route::post('contact', [ContactController::class, 'store'])->middleware('throttle:10,1');
});

/*
|--------------------------------------------------------------------------
| SEO — ভাষা প্রিফিক্স ছাড়া
|--------------------------------------------------------------------------
*/
Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| অ্যাডমিন প্যানেল
|--------------------------------------------------------------------------
| সবসময় বাংলায় — ডাক্তার ও ম্যানেজারের জন্য।
*/
Route::prefix('admin')->name('admin.')->group(function () {

    /* ---------- লগইন ---------- */
    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'login'])
            ->middleware('throttle:login');
    });

    Route::post('logout', [Admin\AuthController::class, 'logout'])
        ->middleware('auth')->name('logout');

    /* ---------- ভেতরের সব পাতা ---------- */
    Route::middleware('auth')->group(function () {

        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        /* ---- অ্যাপয়েন্টমেন্ট ---- */
        Route::controller(Admin\AppointmentController::class)->prefix('appointments')
            ->name('appointments.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('calendar', 'calendar')->name('calendar');
                Route::get('export', 'export')->name('export');
                Route::get('print', 'printList')->name('print');
                Route::get('create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('{appointment}', 'show')->name('show');
                Route::get('{appointment}/slip', 'slip')->name('slip');
                Route::patch('{appointment}/status', 'updateStatus')->name('status');
                Route::patch('{appointment}/reschedule', 'reschedule')->name('reschedule');
                Route::patch('{appointment}/note', 'updateNote')->name('note');
            });

        /* ---- সময়সূচি ও ছুটি ---- */
        Route::resource('chambers', Admin\ChamberController::class)->except('show');

        /* TODO: এই কন্ট্রোলারগুলো এখনো তৈরি হয়নি — schedule/holiday/blocked-slot ব্যবস্থাপনা।
                 তৈরি করার পর নিচের লাইনগুলো আবার চালু করুন।
        Route::resource('schedules', Admin\ScheduleController::class)->except('show', 'create');
        Route::resource('holidays', Admin\HolidayController::class)->except('show');
        Route::controller(Admin\BlockedSlotController::class)->prefix('blocked-slots')
            ->name('blocked-slots.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('{blockedSlot}', 'destroy')->name('destroy');
            });
        Route::post('holiday-mode', [Admin\HolidayController::class, 'toggleMode'])
            ->name('holiday-mode');
        */

        /* ---- কনটেন্ট ---- */
        Route::resource('services', Admin\ServiceController::class)->except('show');
        Route::resource('experiences', Admin\ExperienceController::class)->except('show');
        Route::resource('qualifications', Admin\QualificationController::class)->except('show');
        Route::resource('testimonials', Admin\TestimonialController::class)->except('show');
        Route::resource('gallery', Admin\GalleryController::class)->except('show');
        Route::resource('notices', Admin\NoticeController::class)->except('show');
        Route::resource('faqs', Admin\FaqController::class)->except('show');

        /* ড্র্যাগ করে ক্রম বদলানো — সব কনটেন্ট তালিকায় একই এন্ডপয়েন্ট */
        Route::post('reorder/{type}', [Admin\ContentController::class, 'reorder'])->name('reorder');

        /* ---- বার্তা ---- (TODO: ContactMessageController / MessageLogController এখনো তৈরি হয়নি)
        Route::get('messages', [Admin\ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{contactMessage}', [Admin\ContactMessageController::class, 'show'])->name('messages.show');
        Route::delete('messages/{contactMessage}', [Admin\ContactMessageController::class, 'destroy'])->name('messages.destroy');
        Route::get('message-logs', [Admin\MessageLogController::class, 'index'])->name('message-logs.index');
        */

        /* ---- শুধু অ্যাডমিন ---- */
        Route::middleware('admin')->group(function () {
            Route::get('settings', [Admin\SettingController::class, 'index'])->name('settings.index');
            Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');

            /* TODO: User/ActivityLog/Backup কন্ট্রোলার এখনো তৈরি হয়নি
            Route::resource('users', Admin\UserController::class)->except('show');
            Route::get('activity', [Admin\ActivityLogController::class, 'index'])->name('activity.index');
            Route::post('backup', [Admin\BackupController::class, 'run'])->name('backup.run');
            Route::get('backup', [Admin\BackupController::class, 'index'])->name('backup.index');
            Route::get('backup/{file}', [Admin\BackupController::class, 'download'])->name('backup.download');
            */
        });

        /* ---- নিজের প্রোফাইল ---- */
        Route::get('profile', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
    });
});
