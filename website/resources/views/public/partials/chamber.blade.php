@php use App\Models\Setting; @endphp

<div class="container-x">
    <div class="section-head">
        <p class="eyebrow"><x-icon name="pin" class="w-3.5 h-3.5"/> {{ __('chm.eyebrow') }}</p>
        <h2 class="section-title">{{ __('chm.title') }}</h2>
        <p class="section-sub">{{ __('chm.sub') }}</p>
    </div>

    {{-- একাধিক চেম্বার থাকলে একটার নিচে আরেকটা দেখায় (ক্লায়েন্টের অনুরোধে)।
         নতুন চেম্বার যোগ/সম্পাদনা: অ্যাডমিন → Chambers। --}}
    <div class="space-y-8">
        @foreach(($chambers ?? collect([$chamber ?? null]))->filter() as $chamber)
            @php
                $groups = group_schedules($chamber->schedules);
                $maxSerials = collect($groups)->max('max') ?? 25;
                /* চেম্বার ১ = নীল, চেম্বার ২ = সবুজ (বুকিং বাটন/সিলেক্টরের রঙের সাথে মিল) */
                $chamberBadge = [
                    'bg-sky2-100 text-sky2-800 border-sky2-300',
                    'bg-emerald-100 text-emerald-800 border-emerald-300',
                    'bg-amber-100 text-amber-800 border-amber-300',
                ];
            @endphp

            <div class="grid lg:grid-cols-2 gap-6">

                <div class="card p-5 sm:p-6">
                    <span class="inline-block mb-2 text-[0.72rem] font-bold px-2.5 py-0.5 rounded-full border
                                 {{ $chamberBadge[$loop->index % 3] }}">
                        {{ __('chm.number', ['n' => bn_number($loop->iteration)]) }}
                    </span>
                    <h3 class="text-lg leading-snug">{{ $chamber->name }}</h3>

                    <p class="mt-3 flex items-start gap-2.5 text-sm text-slate-600">
                        <span class="text-sky2-600 shrink-0 mt-0.5"><x-icon name="pin" class="w-4.5 h-4.5"/></span>
                        <span>{{ $chamber->address }}</span>
                    </p>

                    <div class="mt-5 rounded-xl border border-brand-100 overflow-hidden">
                        <p class="bg-brand-50 px-4 py-2.5 text-sm font-bold text-brand-900 flex items-center gap-2">
                            <x-icon name="clock" class="w-4 h-4"/> {{ __('chm.schedule') }}
                        </p>

                        {{-- টেবিলের বদলে flex-wrap তালিকা — ৩২০px চওড়া ফোনেও
                             সময়টা নিচের লাইনে নামে, কিন্তু লেআউট ভাঙে না --}}
                        <ul class="text-sm">
                            @foreach($groups as $g)
                                <li class="border-t border-brand-100 px-4 py-3 flex flex-wrap
                                           items-baseline justify-between gap-x-3 gap-y-1">
                                    <span class="text-slate-600">{{ $g['label'] }}</span>
                                    <span class="font-semibold text-brand-900 whitespace-nowrap">
                                        {{ fmt_time($g['start']) }} – {{ fmt_time($g['end']) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <p class="mt-3 flex items-center gap-2 text-[0.8rem] text-amber-700
                              bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                        <x-icon name="users" class="w-4 h-4 shrink-0"/>
                        {{ __('chm.serialLimit', ['count' => bn_number($maxSerials)]) }}
                    </p>

                    <div class="mt-5 pt-5 border-t border-brand-100">
                        <p class="text-xs text-slate-500 mb-2">{{ __('chm.hotline') }}</p>
                        <div class="flex flex-wrap gap-2.5">
                            @if($hotline = ($chamber->hotline ?: Setting::get('hotline')))
                                <a href="tel:{{ $hotline }}" class="btn btn-primary !py-2.5 !text-sm whitespace-normal text-center leading-snug">
                                    <x-icon name="phone" class="w-4 h-4 shrink-0"/>
                                    {{ __('chm.callOperator', ['name' => $chamber->shortLabel()]) }}
                                </a>
                            @endif
                            @if($wa = Setting::get('whatsapp'))
                                <a href="https://wa.me/{{ intl_bd_phone($wa) }}" target="_blank" rel="noopener"
                                   class="btn btn-wa !py-2.5 !text-sm whitespace-normal text-center leading-snug">
                                    <x-icon name="phone" class="w-4 h-4 shrink-0"/> {{ __('common.whatsapp') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Google Maps এমবেড — সাধারণ iframe, কোনো API কী বা বিলিং লাগে না --}}
                <div class="card overflow-hidden flex flex-col">
                    <iframe src="{{ $chamber->mapEmbedUrl() }}" class="w-full flex-1 min-h-[19rem]"
                            style="border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                            title="{{ $chamber->name }}"></iframe>
                    <div class="p-3 border-t border-brand-100">
                        <a href="{{ $chamber->mapDirectionsUrl() }}" target="_blank" rel="noopener"
                           class="btn btn-outline w-full !py-2.5 !text-sm">
                            <x-icon name="pin" class="w-4 h-4"/> {{ __('common.getDirection') }}
                        </a>
                        <p class="mt-1.5 text-[0.7rem] text-slate-500 text-center">{{ __('common.getDirectionHint') }}</p>
                    </div>
                </div>

            </div>
        @endforeach
    </div>
</div>
