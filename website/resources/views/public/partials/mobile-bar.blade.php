@php
    use App\Models\Setting;
    use App\Models\Chamber;

    /* মোবাইল বারে প্রতিটি চেম্বারের নিজস্ব বুকিং বাটন (ক্লায়েন্টের অনুরোধে)। */
    $barChambers = Chamber::forPublic()->get();

    /* গ্লোবাল hotline সেটিংটি সব চেম্বারের জন্য একটিই — ক্লায়েন্ট অ্যাডমিন থেকে
       কোনো চেম্বার লুকিয়ে দিলেও ওই সেটিংয়ে পুরোনো হাসপাতালের নম্বর থেকে যায়।
       তাই একটিমাত্র চেম্বার চালু থাকলে সেই চেম্বারের নিজের নম্বরই অগ্রাধিকার পায়,
       নইলে রোগী ভুল হাসপাতালে ফোন করতেন। চেম্বার একটিও না থাকলে first() null —
       ?-> ক্র্যাশ ঠেকায় ও গ্লোবাল নম্বরে ফিরিয়ে আনে। */
    $soleChamber = $barChambers->first();
    $barHotline  = $soleChamber?->hotline ?: Setting::get('hotline');
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
        {{-- একটিমাত্র চেম্বার হলে: কল ও বুক।
             বারটি সরু বলে হাসপাতালের নাম দৃশ্যমান লেখায় বসানো হয়নি (লেআউট ভাঙত);
             তবু কোথায় ফোন যাচ্ছে সেটি aria-label-এ থাকে, যাতে স্ক্রিন-রিডারে
             গন্তব্য স্পষ্ট হয়। দৃশ্যমান লেখাটিই ("কল করুন") aria-label-এর শুরুতে
             রাখা হয়েছে — ভয়েস-কন্ট্রোল ব্যবহারকারী যা চোখে দেখেন সেটিই উচ্চারণ
             করে বাটন চাপেন, নাম দিয়ে সেটি ঢেকে দিলে বাটনটি আর ডাকে সাড়া দিত না। --}}
        <a href="tel:{{ $barHotline }}" class="btn btn-primary !px-2 !py-2.5 !text-xs"
           aria-label="{{ __('common.call') }}{{ $soleChamber?->hotline ? ' — ' . $soleChamber->shortLabel() : '' }}">
            <x-icon name="phone" class="w-4 h-4"/> {{ __('common.call') }}</a>
        <a href="{{ route('booking.create') }}" class="btn btn-primary !px-2 !py-2.5 !text-xs">
            <x-icon name="clock" class="w-4 h-4"/> {{ __('nav.book') }}</a>
    @endif
</div>

{{-- বারটি যেন ফুটারের লেখা ঢেকে না ফেলে --}}
<div class="h-16 md:hidden no-print" aria-hidden="true"></div>
