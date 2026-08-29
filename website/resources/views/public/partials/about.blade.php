@php use App\Models\Setting; @endphp

<div class="container-x">
    <div class="grid lg:grid-cols-[.9fr_1.1fr] gap-10 items-start">

        <div>
            <p class="eyebrow"><x-icon name="users" class="w-3.5 h-3.5"/> {{ __('about.eyebrow') }}</p>
            <h2 class="section-title">{{ __('about.title') }}</h2>

            @if($intro = Setting::get('intro'))
                <p class="mt-4 text-slate-600 leading-relaxed">{{ $intro }}</p>
            @endif

            {{-- পরিচিতি ভিডিও — লিংক আসে সেটিং থেকে (অ্যাডমিন → সেটিংস →
                 ডাক্তারের পরিচয়), তাই ক্লায়েন্ট যেকোনো সময় বদলাতে বা খালি করে
                 লুকিয়ে ফেলতে পারেন। লিংকটি ইউটিউবের না হলে youtube_id() null
                 দেয় আর ব্লকটি দেখানোই হয় না — ভাঙা iframe বসে না।

                 youtube-nocookie: ভিডিও না চালানো পর্যন্ত ইউটিউব দর্শকের উপর
                 ট্র্যাকিং কুকি বসায় না। loading="lazy" রাখা হয়েছে কারণ এটি
                 পর্দার প্রথম অংশে নয় — স্ক্রল করে কাছে এলে তবেই লোড হবে,
                 মোবাইল ইন্টারনেটে হোমপেজ ভারী হবে না। --}}
            @if($aboutVideo = youtube_id(Setting::get('about_video')))
                <figure class="mt-6">
                    <figcaption class="flex items-center gap-2 text-sm font-bold text-brand-900 mb-2">
                        <span class="text-sky2-600"><x-icon name="play" class="w-4 h-4"/></span>
                        {{ __('about.video') }}
                    </figcaption>
                    <div class="aspect-video rounded-2xl overflow-hidden border border-brand-100
                                shadow-sm bg-brand-900">
                        <iframe class="w-full h-full" loading="lazy"
                                src="https://www.youtube-nocookie.com/embed/{{ $aboutVideo }}"
                                title="{{ Setting::get('doctor_name') }} — {{ __('about.video') }}"
                                allowfullscreen
                                referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    </div>
                </figure>
            @endif
            {{-- বিবরণের নিচের ফাঁকা জায়গায় পরিচিতি/ফ্লায়ার ছবি —
                 অ্যাডমিন → সেটিংস → ডাক্তারের পরিচয় থেকে বদলানো যায়।
                 আসল আকৃতি (৭০৯×৯০৮) width/height-এ দিয়ে লেআউট-শিফট এড়ানো হয়েছে। --}}
            @if($aboutPhoto = Setting::get('about_photo'))
                <img src="{{ Storage::url($aboutPhoto) }}"
                     alt="{{ Setting::get('doctor_name') }} — {{ __('about.title') }}"
                     width="709" height="908" loading="lazy"
                     class="mt-6 w-full max-w-sm h-auto rounded-2xl border border-brand-100 shadow-sm mx-auto lg:mx-0">
            @endif

            {{-- ⚠️ ফি ডিফল্টভাবে দেখানো হয় না।
                 প্রকৃত ফি না জানা পর্যন্ত ভুল সংখ্যা প্রকাশ্যে গেলে রোগী
                 সেই টাকা নিয়ে চেম্বারে এসে বিব্রত হতেন। অ্যাডমিন প্যানেল
                 থেকে প্রকৃত ফি বসিয়ে টগলটি চালু করতে হবে। --}}
            @if(Setting::bool('show_fees'))
                <div class="mt-6 card p-5">
                    <p class="text-sm font-bold text-brand-900 mb-3">{{ __('about.fees') }}</p>
                    <ul class="space-y-2 text-sm">
                        @foreach([
                            ['about.fee_new', 'fee_new'],
                            ['about.fee_followup', 'fee_followup'],
                            ['about.fee_report', 'fee_report'],
                        ] as [$label, $key])
                            @if($amount = Setting::get($key))
                                <li class="flex items-baseline justify-between gap-3 border-b
                                           border-brand-50 pb-2 last:border-0">
                                    <span class="text-slate-600">{{ __($label) }}</span>
                                    <strong class="text-brand-900 whitespace-nowrap">
                                        {{ bn_number($amount) }} {{ __('about.taka') }}</strong>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div>
            @if($experiences->isNotEmpty())
                <div class="timeline space-y-4">
                    @foreach($experiences as $exp)
                        <div class="relative flex gap-4">
                            <span class="icon-bubble bg-white border-2 border-sky2-200 text-sky2-600
                                         z-10 shadow-sm">
                                <x-icon :name="$exp->icon" class="w-5 h-5"/>
                            </span>
                            <div class="flex-1 pb-1 pt-1.5">
                                <p class="font-bold text-brand-900 text-[0.95rem] leading-snug">
                                    {{ $exp->position }}
                                    @if($exp->is_current)
                                        <span class="ms-1.5 align-middle text-[0.65rem] font-semibold
                                                     text-wa-700 bg-wa-500/10 rounded px-1.5 py-0.5">
                                            {{ app()->getLocale() === 'en' ? 'Current' : 'বর্তমান' }}</span>
                                    @endif
                                </p>
                                <p class="text-sm text-slate-500 mt-0.5 leading-snug">
                                    {{ $exp->organization }}
                                    @if($exp->period)
                                        <span class="text-slate-400"> · {{ $exp->period }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($qualifications->isNotEmpty())
                <div class="mt-7 card p-5">
                    <p class="flex items-center gap-2 text-sm font-bold text-brand-900 mb-3">
                        <span class="text-sky2-600"><x-icon name="cap" class="w-4 h-4"/></span>
                        {{ __('exp.qualTitle') }}
                    </p>
                    <ul class="grid sm:grid-cols-3 gap-3">
                        @foreach($qualifications as $q)
                            <li class="rounded-lg bg-brand-50 px-3 py-2.5">
                                <p class="font-semibold text-brand-900 text-sm">{{ $q->degree }}</p>
                                {{-- প্রতিষ্ঠান বা সাল অজানা থাকলে ফাঁকা লাইন দেখাবে না --}}
                                @if($q->institution)
                                    <p class="text-[0.72rem] text-slate-500 mt-0.5 leading-snug">
                                        {{ $q->institution }}</p>
                                @endif
                                @if($q->year)
                                    <p class="text-[0.7rem] text-slate-400 mt-0.5">{{ bn_number($q->year) }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

    </div>
</div>
