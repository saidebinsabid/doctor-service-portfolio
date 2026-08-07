@php
    use App\Models\Setting;
    use App\Models\Chamber;

    /* মোবাইল বারে প্রতিটি চেম্বারের নিজস্ব বুকিং বাটন (ক্লায়েন্টের অনুরোধে)। */
    $barChambers = Chamber::forPublic()->get();
@endphp

{{-- মোবাইলে নিচে স্থায়ী অ্যাকশন বার।
     বাংলাদেশে ৮৫%+ দর্শক মোবাইল থেকে আসেন — সিরিয়াল বুকিং সবসময়
     হাতের নাগালে থাকাটাই সবচেয়ে কাজে দেয়।
     একাধিক চেম্বার হলে প্রতিটির আলাদা রঙের বাটন — কোন চেম্বারে দেখাবেন
     সেটি সরাসরি বেছে সিরিয়াল নেওয়া যায়। --}}
<div class="mobile-bar no-print">
    @if($barChambers->count() > 1)
        @foreach($barChambers as $bc)
            <a href="{{ route('booking.create', ['chamber' => $bc->id]) }}"
               class="btn {{ $loop->index % 2 === 0 ? 'btn-ch-1' : 'btn-ch-2' }} !px-2 !py-1.5
                      !flex-col !gap-0 leading-tight text-center whitespace-normal">
                <span class="text-[0.78rem] font-bold">{{ $bc->shortLabel() }}</span>
                <span class="text-[0.6rem] font-normal opacity-90">{{ __('nav.book') }}</span>
            </a>
        @endforeach
    @else
        {{-- একটিমাত্র চেম্বার হলে: কল ও বুক --}}
        <a href="tel:{{ Setting::get('hotline') }}" class="btn btn-primary !px-2 !py-2.5 !text-xs">
            <x-icon name="phone" class="w-4 h-4"/> {{ __('common.call') }}</a>
        <a href="{{ route('booking.create') }}" class="btn btn-primary !px-2 !py-2.5 !text-xs">
            <x-icon name="clock" class="w-4 h-4"/> {{ __('nav.book') }}</a>
    @endif
</div>

{{-- বারটি যেন ফুটারের লেখা ঢেকে না ফেলে --}}
<div class="h-16 md:hidden no-print" aria-hidden="true"></div>
