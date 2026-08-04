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
                <a href="{{ route('booking.create') }}" class="btn btn-wa !py-3.5 justify-center">
                    <x-icon name="clock" class="w-5 h-5"/> {{ __('common.bookNow') }}
                </a>
                @if($hotline = Setting::get('hotline'))
                    <a href="tel:{{ $hotline }}" class="btn btn-ghost !py-3.5 justify-center">
                        <x-icon name="phone" class="w-5 h-5"/>
                        {{ app()->getLocale() === 'en' ? 'Call to book a serial' : 'সিরিয়াল নিয়ে আপনি কল করুন' }}
                        · {{ bn_number($hotline) }}
                    </a>
                @endif
                <a href="{{ route('booking.status') }}"
                   class="text-center text-sm text-white/70 hover:text-white underline underline-offset-4">
                    {{ __('nav.status') }}
                </a>
            </div>
        </div>
    </div>
</div>
