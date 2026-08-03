<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * সাইট সেটিংস — ডাক্তারের পরিচয়, যোগাযোগ, সোশ্যাল, ফি, SEO, বুকিং।
 * হোমপেজের প্রায় সব টেক্সট এখান থেকে বদলানো যায়।
 */
class SettingController extends Controller
{
    /** গ্রুপের প্রদর্শন-নাম ও ক্রম */
    private const GROUPS = [
        'doctor'  => 'ডাক্তারের পরিচয়',
        'contact' => 'যোগাযোগ',
        'social'  => 'সোশ্যাল মিডিয়া',
        'fees'    => 'ভিজিট ফি',
        'booking' => 'বুকিং সেটিংস',
        'seo'     => 'SEO / সার্চ ইঞ্জিন',
    ];

    public function index(): View
    {
        $groups = Setting::orderBy('sort_order')->get()->groupBy('group');

        return view('admin.settings.index', [
            'groups'      => $groups,
            'groupLabels' => self::GROUPS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $input = $request->input('settings', []);

        foreach (Setting::all() as $setting) {
            $row = $input[$setting->key] ?? [];

            if ($setting->type === 'boolean') {
                $setting->value_bn = ! empty($row['bn']) ? '1' : '0';
            } elseif ($setting->type === 'image') {
                if ($request->hasFile("settings.{$setting->key}.file")) {
                    if ($setting->value_bn) {
                        Storage::disk('public')->delete($setting->value_bn);
                    }
                    $setting->value_bn = $request->file("settings.{$setting->key}.file")
                        ->store('site', 'public');
                }
            } else {
                $setting->value_bn = $row['bn'] ?? null;
                $setting->value_en = $row['en'] ?? null;
            }

            $setting->save();
        }

        Setting::flush();

        ActivityLog::record('updated', $request->user(), 'সাইট সেটিংস হালনাগাদ করেছেন');

        return redirect()->route('admin.settings.index')->with('success', 'সেটিংস সংরক্ষিত হয়েছে।');
    }
}
