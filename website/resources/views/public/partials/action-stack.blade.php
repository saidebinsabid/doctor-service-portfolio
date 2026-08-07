@php use App\Models\Setting; @endphp

{{-- সব অ্যাকশন বাটন একসাথে, একটার পর একটা (ক্লায়েন্টের অনুরোধে)।
     হিরো ও বুকিং সেকশন — দুই জায়গাতেই এই একই পার্শিয়াল ব্যবহার হয়, তাই
     এক জায়গায় বদলালেই দুটোই আপডেট হয়। দুটোই গাঢ় ব্যাকগ্রাউন্ড। --}}
<div class="flex flex-col gap-3 w-full max-w-sm">

    {{-- ১. অনলাইনে সিরিয়াল বুক।
         একাধিক চেম্বার হলে প্রতিটির আলাদা বাটন (আলাদা রঙ, চেম্বারের নাম লেখা) —
         ক্লিক করলে সরাসরি সেই চেম্বারের ক্যালেন্ডারে যায়। --}}
    @php $bookChambers = ($chambers ?? collect())->filter()->values(); @endphp
    @if($bookChambers->count() > 1)
        <p class="text-xs text-white/75 flex items-start gap-1.5 mb-0.5">
            <x-icon name="clock" class="w-3.5 h-3.5 shrink-0 mt-0.5 text-sky2-200"/>
            {{ __('common.bookGuideMulti') }}
        </p>
        @foreach($bookChambers as $bc)
            <a href="{{ route('booking.create', ['chamber' => $bc->id]) }}"
               class="btn {{ $loop->index % 2 === 0 ? 'btn-ch-1' : 'btn-ch-2' }} w-full !py-3
                      justify-center whitespace-normal text-center leading-snug">
                <x-icon name="clock" class="w-5 h-5 shrink-0"/>
                <span class="flex flex-col leading-tight">
                    <span class="text-[0.68rem] font-semibold opacity-90">{{ __('chm.number', ['n' => bn_number($loop->iteration)]) }}</span>
                    <span class="font-bold">{{ $bc->name }}</span>
                    <span class="text-[0.72rem] font-normal opacity-90">{{ __('common.bookNow') }}</span>
                </span>
            </a>
        @endforeach
    @else
        <a href="{{ route('booking.create') }}" class="btn btn-primary w-full !py-3.5 justify-center">
            <x-icon name="clock" class="w-5 h-5"/> {{ __('common.bookNow') }}
        </a>
        <p class="-mt-1 text-xs text-white/70 flex items-start gap-1.5">
            <x-icon name="clock" class="w-3.5 h-3.5 shrink-0 mt-0.5 text-sky2-200"/>
            {{ __('common.bookGuide') }}
        </p>
    @endif

    {{-- ২. অপারেটরকে কল (হলুদ) --}}
    @if($hotline = Setting::get('hotline'))
        <a href="tel:{{ $hotline }}"
           class="btn btn-primary w-full !py-3.5 justify-center whitespace-normal text-center leading-snug">
            <x-icon name="phone" class="w-5 h-5 shrink-0"/> {{ __('common.callOperator') }}
        </a>
    @endif

    {{-- ৩. জরুরি প্রয়োজনে স্যারকে WhatsApp মেসেজ (গাঢ় নীল) + নোট --}}
    @if($wa = Setting::get('whatsapp'))
        <a href="https://wa.me/{{ intl_bd_phone($wa) }}?text={{ rawurlencode(__('common.msgDoctorPrefill')) }}"
           target="_blank" rel="noopener"
           class="btn btn-deepblue w-full !py-3.5 justify-center whitespace-normal text-center leading-snug">
            <x-icon name="phone" class="w-5 h-5 shrink-0"/> {{ __('common.msgDoctor') }}
        </a>
        <p class="-mt-1 text-xs text-white/70 text-center leading-relaxed">{{ __('common.msgDoctorNote') }}</p>
    @endif

    {{-- ৪. ফি ও বিকাশ তথ্য — সাদা বক্স, লাল লেখা।
         লেখা ও বিকাশ নম্বর দুটোই অ্যাডমিন সেটিংস থেকে আসে (Settings → ভিজিট ফি),
         তাই ক্লায়েন্ট নিজে যেকোনো সময় বদলাতে পারবেন। সেটিং খালি থাকলে বক্সটি দেখাবে না। --}}
    @if($feeNotice = Setting::get('fee_notice'))
        <div class="rounded-xl bg-white border border-red-200 px-4 py-3.5 text-center shadow-sm">
            <p class="text-sm font-semibold text-red-600 leading-relaxed">{{ $feeNotice }}</p>
            @if($bkashNo = Setting::get('bkash_number'))
                {{-- ট্যাপ করলে বিকাশ নম্বরটি কপি হয় (JS হ্যান্ডলার app.js-এ) --}}
                <button type="button" data-copy="{{ $bkashNo }}"
                        class="mt-2 inline-flex items-center gap-2 text-base font-extrabold text-red-700
                               hover:text-red-800 active:scale-[.98] transition">
                    <span>{{ __('common.bkash') }}: {{ $bkashNo }}</span>
                    <span data-copy-label data-copied-text="{{ __('common.copied') }}"
                          class="text-[0.62rem] font-semibold text-red-600 bg-red-50 border border-red-300
                                 rounded-full px-2 py-0.5 whitespace-nowrap">{{ __('common.copy') }}</span>
                </button>
            @endif
        </div>
    @endif

</div>
