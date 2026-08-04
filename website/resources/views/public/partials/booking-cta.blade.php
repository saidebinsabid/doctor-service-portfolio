@php use App\Models\Setting; @endphp

<div class="container-x">
    <div class="relative overflow-hidden rounded-3xl bg-brand-900 px-6 py-10 md:px-12 md:py-14">
        <div class="absolute inset-0 opacity-80
                    bg-[radial-gradient(36rem_20rem_at_85%_20%,#2e8bc0_0%,transparent_60%)]"></div>

        <div class="relative grid lg:grid-cols-[1.2fr_.8fr] gap-8 items-center">
            <div class="text-white">
                <p class="eyebrow !bg-white/15 !text-sky2-100">
                    <x-icon name="clock" class="w-3.5 h-3.5"/> {{ __('nav.book') }}
                </p>
                <h2 class="text-2xl md:text-3xl !text-white font-extrabold">{{ __('booking.title') }}</h2>
                <p class="mt-3 text-white/80 text-[0.97rem] leading-relaxed max-w-lg">
                    {{ __('booking.sub') }}
                </p>

                @if(! Setting::bool('booking_enabled', true) || Setting::bool('holiday_mode'))
                    {{-- অ্যাডমিন বুকিং বন্ধ রাখলে ক্যালেন্ডারের বদলে ফোনের অনুরোধ --}}
                    <p class="mt-5 inline-flex items-center gap-2 rounded-xl bg-amber-400/15
                              border border-amber-300/40 px-4 py-3 text-sm text-amber-100">
                        <x-icon name="clock" class="w-4 h-4 shrink-0"/>
                        {{ Setting::bool('holiday_mode') ? __('booking.holiday_mode') : __('booking.disabled') }}
                    </p>
                @endif
            </div>

            <div class="flex flex-col gap-3">
                <a href="{{ route('booking.create') }}" class="btn btn-primary !py-3.5 justify-center">
                    <x-icon name="clock" class="w-5 h-5"/> {{ __('common.bookNow') }}
                </a>
                <p class="-mt-1 text-xs text-white/70 flex items-start gap-1.5">
                    <x-icon name="clock" class="w-3.5 h-3.5 shrink-0 mt-0.5 text-sky2-200"/>
                    {{ __('common.bookGuide') }}
                </p>
                @if($hotline = Setting::get('hotline'))
                    <a href="tel:{{ $hotline }}" class="btn btn-yellow !py-3.5 justify-center whitespace-normal text-center leading-snug">
                        <x-icon name="phone" class="w-5 h-5 shrink-0"/> {{ __('common.callOperator') }}
                    </a>
                @endif
                <a href="{{ route('booking.status') }}"
                   class="text-center text-sm text-white/70 hover:text-white underline underline-offset-4">
                    {{ __('nav.status') }}
                </a>
            </div>
        </div>
    </div>

    {{-- ক্লায়েন্টের অনুরোধে যোগ করা দুটি বাটন — বুকিং কার্ডের নিচে, হালকা
         ব্যাকগ্রাউন্ডে (গাঢ় নীল বাটন ও লাল-লেখা সাদা বক্স গাঢ় কার্ডে মিশে যেত)। --}}
    <div class="mt-5 grid gap-3 max-w-2xl mx-auto">

        {{-- জরুরি প্রয়োজনে স্যারকে সরাসরি WhatsApp মেসেজ (গাঢ় নীল) --}}
        @if($wa = Setting::get('whatsapp'))
            <div>
                <a href="https://wa.me/{{ intl_bd_phone($wa) }}?text={{ rawurlencode(__('common.msgDoctorPrefill')) }}"
                   target="_blank" rel="noopener"
                   class="btn btn-deepblue w-full !py-3.5 whitespace-normal text-center leading-snug">
                    <x-icon name="phone" class="w-5 h-5 shrink-0"/> {{ __('common.msgDoctor') }}
                </a>
                <p class="mt-1.5 text-xs text-slate-500 text-center leading-relaxed">
                    {{ __('common.msgDoctorNote') }}
                </p>
            </div>
        @endif

        {{-- ফি ও বিকাশ তথ্য — সাদা বক্স, লাল লেখা --}}
        <div class="rounded-xl bg-white border border-red-200 px-4 py-3.5 text-center shadow-sm">
            <p class="text-sm font-semibold text-red-600 leading-relaxed">{{ __('common.feeInfo') }}</p>
            <p class="mt-2 text-base font-extrabold text-red-700 tracking-wide">
                {{ __('common.bkash') }}: 01327084433
            </p>
        </div>

    </div>
</div>
