@php use App\Models\Setting; @endphp

{{-- মোবাইলে নিচে স্থায়ী অ্যাকশন বার।
     বাংলাদেশে ৮৫%+ দর্শক মোবাইল থেকে আসেন — কল ও সিরিয়াল
     সবসময় হাতের নাগালে থাকাটাই সবচেয়ে কাজে দেয়। --}}
<div class="mobile-bar no-print">
    <a href="tel:{{ Setting::get('hotline') }}" class="btn btn-yellow !px-2 !py-2.5 !text-xs">
        <x-icon name="phone" class="w-4 h-4"/> {{ __('common.call') }}</a>

    <a href="https://wa.me/{{ intl_bd_phone(Setting::get('whatsapp')) }}"
       target="_blank" rel="noopener" class="btn btn-wa !px-2 !py-2.5 !text-xs">
        <x-icon name="phone" class="w-4 h-4"/> {{ __('common.whatsapp') }}</a>

    <a href="{{ route('booking.create') }}" class="btn btn-primary !px-2 !py-2.5 !text-xs">
        <x-icon name="clock" class="w-4 h-4"/> {{ __('nav.book') }}</a>
</div>

{{-- বারটি যেন ফুটারের লেখা ঢেকে না ফেলে --}}
<div class="h-16 md:hidden no-print" aria-hidden="true"></div>
