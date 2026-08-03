<?php

namespace App\Http\Controllers;

use App\Models\Chamber;
use App\Models\Experience;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Notice;
use App\Models\Qualification;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        /*
        | পুরো ল্যান্ডিং পেজের কনটেন্ট একবারে।
        |
        | ⭐ কোনো তালিকা খালি থাকলে সেই সেকশনটি ভিউতে দেখানোই হয় না —
        |    ফলে চেম্বারের ছবি বা রোগীর মতামত না থাকা পর্যন্ত ফাঁকা
        |    সেকশন বা "কিছু নেই" লেখা কেউ দেখবে না।
        */
        return view('public.home', [
            'notices'        => Notice::current()->get(),
            'chamber'        => Chamber::forPublic()->with('schedules')->first(),
            'chambers'       => Chamber::forPublic()->with('schedules')->get(),
            'services'       => Service::forPublic()->general()->get(),
            'specials'       => Service::forPublic()->special()->get(),
            'experiences'    => Experience::forPublic()->get(),
            'qualifications' => Qualification::forPublic()->get(),
            'gallery'        => GalleryItem::forPublic()->get(),
            'testimonials'   => Testimonial::forPublic()->get(),
            'faqs'           => Faq::forPublic()->get(),
        ]);
    }

    public function privacy(): View
    {
        return view('public.privacy');
    }

    public function terms(): View
    {
        return view('public.terms');
    }
}
