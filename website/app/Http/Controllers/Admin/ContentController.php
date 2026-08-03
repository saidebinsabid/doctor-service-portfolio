<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Notice;
use App\Models\Qualification;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * তালিকার ক্রম বদলানো (ড্র্যাগ-অ্যান্ড-ড্রপ)।
 *
 * সাতটি তালিকার জন্য সাতটি আলাদা এন্ডপয়েন্ট না বানিয়ে একটিই —
 * শুধু কোন তালিকা সেটি ঠিকানায় বলা থাকে।
 */
class ContentController extends Controller
{
    /** ঠিকানার নাম → মডেল। এই তালিকার বাইরে কিছু গ্রহণ করা হয় না। */
    protected const TYPES = [
        'services'       => Service::class,
        'experiences'    => Experience::class,
        'qualifications' => Qualification::class,
        'testimonials'   => Testimonial::class,
        'gallery'        => GalleryItem::class,
        'notices'        => Notice::class,
        'faqs'           => Faq::class,
    ];

    public function reorder(Request $request, string $type): JsonResponse
    {
        $model = self::TYPES[$type] ?? null;

        abort_if($model === null, 404);

        $data = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['required', 'integer'],
        ]);

        /* ১০ ধাপ অন্তর নম্বর দেওয়া হয়, যাতে পরে মাঝখানে একটি
           আইটেম ঢোকাতে হলে পুরো তালিকা নতুন করে সাজাতে না হয় */
        foreach ($data['ids'] as $index => $id) {
            $model::where('id', $id)->update(['sort_order' => ($index + 1) * 10]);
        }

        return response()->json(['ok' => true]);
    }
}
