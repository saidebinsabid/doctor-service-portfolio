@php use App\Models\Setting; $heroGrid = $heroGrid ?? false; @endphp

{{-- সব অ্যাকশন বাটন একসাথে (ক্লায়েন্টের অনুরোধে)।
     হিরো ও বুকিং সেকশন — দুই জায়গাতেই এই একই পার্শিয়াল ব্যবহার হয়।

     heroGrid=true (শুধু হিরো থেকে পাঠানো) → ডেস্কটপে ২ কলাম গ্রিড, যাতে বাঁ পাশ
     লম্বা লিস্ট না হয়। মোবাইলে ও বুকিং সেকশনে single-column অপরিবর্তিত (flex-col)।
     full-width আইটেম (হিন্ট, WhatsApp, নোট, ফি) দুই কলাম জুড়ে বসে ($span)। --}}
@php $span = $heroGrid ? 'sm:col-span-2' : ''; @endphp
<div class="flex flex-col gap-3 w-full max-w-sm
            {{ $heroGrid ? 'sm:grid sm:grid-cols-2 sm:content-start sm:max-w-2xl' : '' }}">

    {{-- ১. অনলাইনে সিরিয়াল বুক।
         একাধিক চেম্বার হলে প্রতিটির আলাদা বাটন (আলাদা রঙ, চেম্বারের নাম লেখা) —
         ক্লিক করলে সরাসরি সেই চেম্বারের ক্যালেন্ডারে যায়। --}}
    @php $bookChambers = ($chambers ?? collect())->filter()->values(); @endphp
    @if($bookChambers->count() > 1)
        {{-- হিন্ট লাইনটি ছোট ও ম্লান ছিল বলে চোখে পড়ত না — ক্লায়েন্টের অনুরোধে
             একটু বড় ফন্ট + হালকা সাদা পিলে গাঢ় নীল অক্ষরে বসানো হলো, যাতে
             গাঢ় হিরো ব্যাকগ্রাউন্ডেই স্পষ্ট দেখা যায়। --}}
        <p class="text-sm font-semibold text-brand-900 bg-white rounded-lg
                  px-3 py-2 flex items-start gap-1.5 mb-1 shadow-sm {{ $span }}">
            <x-icon name="clock" class="w-4 h-4 shrink-0 mt-0.5 text-brand-900"/>
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
        <a href="{{ route('booking.create') }}" class="btn btn-primary w-full !py-3.5 justify-center {{ $span }}">
            <x-icon name="clock" class="w-5 h-5"/> {{ __('common.bookNow') }}
        </a>
        <p class="-mt-1 text-xs text-white/70 flex items-start gap-1.5 {{ $span }}">
            <x-icon name="clock" class="w-3.5 h-3.5 shrink-0 mt-0.5 text-sky2-200"/>
            {{ __('common.bookGuide') }}
        </p>
    @endif

    {{-- ২. চেম্বার-ভিত্তিক অপারেটর কল (লাল)।
         একাধিক চেম্বার হলে প্রতিটির আলাদা লাল বাটন — ক্লিক করলে সেই চেম্বারের
         অপারেটরের নম্বর সরাসরি ডায়াল-লিস্টে আসে। --}}
    @if($bookChambers->count() > 1)
        @foreach($bookChambers as $bc)
            @if($bc->hotline)
                <a href="tel:{{ $bc->hotline }}"
                   class="btn btn-primary w-full !py-3 justify-center whitespace-normal text-center leading-snug">
                    <x-icon name="phone" class="w-5 h-5 shrink-0"/>
                    <span class="flex flex-col leading-tight">
                        <span class="font-bold">{{ $bc->shortLabel() }}</span>
                        <span class="text-[0.72rem] font-normal opacity-90">{{ __('common.callOperatorMulti') }}</span>
                    </span>
                </a>
            @endif
        @endforeach
    @else
        {{-- গ্লোবাল hotline সেটিংটি সব চেম্বারের জন্য একটিই। ক্লায়েন্ট অ্যাডমিন থেকে
             কোনো চেম্বার নিষ্ক্রিয় করলে ওই সেটিংয়ে পুরোনো হাসপাতালের নম্বরই থেকে
             যায় — ফলে পাতায় এক হাসপাতাল দেখিয়ে ফোন যেত অন্য হাসপাতালে। তাই
             দৃশ্যমান চেম্বারের নিজের নম্বর সবসময় আগে; সেটি ফাঁকা হলে তবেই গ্লোবাল।
             একটিও সক্রিয় চেম্বার না থাকলে first() null দেয়, ?-> ক্র্যাশ ঠেকিয়ে
             গ্লোবাল নম্বরেই নামিয়ে আনে। --}}
        @php $soleChamber = $bookChambers->first(); @endphp
        @if($hotline = ($soleChamber?->hotline ?: Setting::get('hotline')))
            <a href="tel:{{ $hotline }}"
               class="btn btn-primary w-full !py-3 justify-center whitespace-normal text-center leading-snug {{ $span }}">
                <x-icon name="phone" class="w-5 h-5 shrink-0"/>
                <span class="flex flex-col leading-tight">
                    {{-- নম্বরটি একটি নির্দিষ্ট হাসপাতালের, তাই বাটনের লেখাও নির্দিষ্ট
                         হওয়া দরকার — না হলে রোগী জানেন না কোথায় ফোন যাচ্ছে।
                         তবে নামটি তখনই দেখাই যখন নম্বরটি সত্যিই এই চেম্বারের;
                         হটলাইন ফাঁকা থাকলে উপরের ?: গ্লোবাল নম্বরে নেমে যায়, আর
                         তার উপর হাসপাতালের নাম বসালে সেটিই ভুল তথ্য হতো। --}}
                    @if($soleChamber?->hotline)
                        <span class="font-bold">{{ $soleChamber->shortLabel() }}</span>
                    @endif
                    <span class="text-[0.72rem] font-normal opacity-90">{{ __('common.callOperatorMulti') }}</span>
                </span>
            </a>
        @endif
    @endif

    {{-- ৩. জরুরি প্রয়োজনে স্যারকে WhatsApp মেসেজ (গাঢ় নীল) + নোট --}}
    @if($wa = Setting::get('whatsapp'))
        <a href="https://wa.me/{{ intl_bd_phone($wa) }}?text={{ rawurlencode(__('common.msgDoctorPrefill')) }}"
           target="_blank" rel="noopener"
           class="btn btn-deepblue w-full !py-3.5 justify-center whitespace-normal text-center leading-snug {{ $span }}">
            <x-icon name="phone" class="w-5 h-5 shrink-0"/> {{ __('common.msgDoctor') }}
        </a>
        <p class="-mt-1 text-xs text-white/70 text-center leading-relaxed {{ $span }}">{{ __('common.msgDoctorNote') }}</p>
    @endif

    {{-- ৪. ফি ও বিকাশ তথ্য — সাদা বক্স, লাল লেখা।
         লেখা ও বিকাশ নম্বর দুটোই অ্যাডমিন সেটিংস থেকে আসে (Settings → ভিজিট ফি),
         তাই ক্লায়েন্ট নিজে যেকোনো সময় বদলাতে পারবেন। সেটিং খালি থাকলে বক্সটি দেখাবে না। --}}
    @if($feeNotice = Setting::get('fee_notice'))
        <div class="rounded-xl bg-white border border-red-200 px-4 py-3.5 text-center shadow-sm {{ $span }}">
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
