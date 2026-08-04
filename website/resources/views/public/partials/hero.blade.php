@php
    use App\Models\Setting;

    $photo = Setting::get('doctor_photo');

    $trust = [
        ['award', __('hero.trust1')],
        ['flask', __('hero.trust2')],
        ['heart', __('hero.trust3')],
    ];
@endphp

<section class="relative overflow-hidden bg-brand-900">
    <div class="absolute inset-0 opacity-90
                bg-[radial-gradient(48rem_28rem_at_78%_18%,#2e8bc0_0%,transparent_62%),radial-gradient(40rem_24rem_at_8%_92%,#12264a_0%,transparent_58%)]"></div>

    <div class="container-x relative py-12 md:py-20">
        <div class="grid lg:grid-cols-[1.15fr_.85fr] gap-10 lg:gap-14 items-center">

            <div class="text-white">
                <p class="eyebrow !bg-white/15 !text-sky2-100">
                    <x-icon name="stetho" class="w-3.5 h-3.5"/> {{ Setting::get('specialty') }}
                </p>

                <h1 class="text-3xl md:text-5xl !text-white font-extrabold tracking-tight text-balance-x">
                    {{ Setting::get('doctor_name') }}
                </h1>

                <p class="mt-3 text-sky2-100 text-base md:text-lg font-medium">
                    {{ Setting::get('degrees') }}
                </p>

                <p class="mt-1.5 text-white/70 text-sm md:text-base">
                    {{ Setting::get('designation') }}
                    @if($bmdc = Setting::get('bmdc'))
                        <span class="mx-2 opacity-40">•</span>{{ __('hero.bmdc') }} {{ $bmdc }}
                    @endif
                </p>

                @if($tagline = Setting::get('tagline'))
                    <p class="mt-5 text-white/85 leading-relaxed max-w-xl text-[0.97rem]">{{ $tagline }}</p>
                @endif

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('booking.create') }}" class="btn btn-primary">
                        <x-icon name="clock" class="w-4.5 h-4.5"/> {{ __('common.bookNow') }}
                    </a>
                    @if($hotline = Setting::get('hotline'))
                        <a href="tel:{{ $hotline }}" class="btn btn-yellow whitespace-normal text-center leading-snug">
                            <x-icon name="phone" class="w-4.5 h-4.5 shrink-0"/>
                            {{ __('common.callOperator') }}
                        </a>
                    @endif
                </div>

                {{-- বুকিং বাটনের নিচে ছোট নির্দেশনা (ক্লায়েন্টের অনুরোধে) --}}
                <p class="mt-3 text-sm text-white/75 flex items-start gap-1.5 max-w-md">
                    <x-icon name="clock" class="w-4 h-4 shrink-0 mt-0.5 text-sky2-200"/>
                    {{ __('common.bookGuide') }}
                </p>

                <dl class="mt-9 grid sm:grid-cols-3 gap-3 max-w-2xl">
                    @foreach($trust as [$icon, $label])
                        <div class="flex items-center gap-2.5 rounded-xl bg-white/10 backdrop-blur
                                    px-3.5 py-3 border border-white/15">
                            <span class="text-sky2-200 shrink-0"><x-icon :name="$icon" class="w-5 h-5"/></span>
                            <dt class="text-[0.8rem] text-white/90 leading-snug">{{ $label }}</dt>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="relative">
                <div class="relative mx-auto max-w-sm">
                    <div class="absolute -inset-4 bg-sky2-400/20 rounded-[2rem] blur-2xl"></div>

                    <div class="relative aspect-square rounded-[1.75rem] overflow-hidden
                                bg-gradient-to-br from-white/95 to-sky2-50 border-4 border-white/25
                                shadow-2xl grid place-items-center">
                        @if($photo)
                            <img src="{{ Storage::url($photo) }}"
                                 alt="{{ Setting::get('doctor_name') }}"
                                 width="640" height="640"
                                 class="w-full h-full object-cover" fetchpriority="high">
                        @else
                            {{-- ⚠️ ডাক্তারের আসল ছবি এখনো পাওয়া যায়নি।
                                 অ্যাডমিন প্যানেল → সেটিংস → ছবি আপলোড করলেই বসে যাবে। --}}
                            <div class="flex flex-col items-center justify-center gap-3 text-brand-300 p-6 text-center">
                                <x-icon name="users" class="w-16 h-16"/>
                                <p class="text-xs font-medium text-brand-400">
                                    {{ app()->getLocale() === 'en' ? "Doctor's photo" : 'ডাক্তারের ছবি' }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if($chamber ?? null)
                        <div class="absolute -bottom-4 -left-2 sm:left-2 bg-white rounded-xl shadow-lg
                                    px-4 py-2.5 border border-brand-100 flex items-center gap-2.5">
                            <span class="grid place-items-center w-8 h-8 rounded-lg bg-wa-500/10 text-wa-700">
                                <x-icon name="pin" class="w-4 h-4"/></span>
                            <div class="leading-tight">
                                <p class="text-[0.7rem] text-slate-500">{{ __('chm.eyebrow') }}</p>
                                <p class="text-[0.8rem] font-bold text-brand-900">
                                    {{ Str::limit($chamber->name, 22) }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ছবির নিচে চেম্বারের নাম ও ঠিকানা (ক্লায়েন্টের অনুরোধে) --}}
                <p class="mt-8 sm:mt-7 text-center text-sky2-100 text-sm font-medium
                          flex items-center justify-center gap-1.5">
                    <x-icon name="pin" class="w-4 h-4 shrink-0"/>
                    {{ app()->getLocale() === 'en'
                        ? 'Ibn Sina Diagnostic, Badda, Dhaka'
                        : 'ইবনে সিনা ডায়াগনস্টিক, বাড্ডা, ঢাকা' }}
                </p>
            </div>

        </div>
    </div>
</section>
