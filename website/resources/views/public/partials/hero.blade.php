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

                {{-- সব অ্যাকশন বাটন (শেয়ার্ড পার্শিয়াল)।
                     heroGrid=true → ডেস্কটপে ২ কলাম গ্রিডে বসে (বাঁ পাশ যেন লম্বা
                     লিস্ট না হয়)। মোবাইলে single-column অপরিবর্তিত। --}}
                <div class="mt-7">
                    @include('public.partials.action-stack', ['heroGrid' => true])
                </div>
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

                    {{-- ছবির উপরে ভাসমান চেম্বার-ব্যাজ সরানো হয়েছে (ক্লায়েন্টের অনুরোধে) —
                         ছবির উপরে কোনো টেক্সট বা শেপ থাকবে না। চেম্বারের নাম/ঠিকানা
                         ছবির নিচে দেখানো হয় (পরের ব্লকে)। --}}
                </div>

                {{-- ছবির নিচে চেম্বারের নাম ও ঠিকানা — chamber রেকর্ড থেকে ডাইনামিক।
                     অ্যাডমিন → চেম্বার থেকে নাম/ঠিকানা যেকোনো সময় বদলানো যায়। --}}
                @if($chamber ?? null)
                    <div class="mt-8 sm:mt-7 text-center text-sky2-100">
                        <p class="text-sm font-semibold flex items-center justify-center gap-1.5">
                            <x-icon name="pin" class="w-4 h-4 shrink-0"/> {{ $chamber->name }}
                        </p>
                        @if($chamber->address)
                            <p class="mt-1 text-xs text-sky2-100/85 leading-snug">{{ $chamber->address }}</p>
                        @endif
                    </div>
                @endif
            </div>

        </div>

        {{-- ট্রাস্ট আইকন — হিরোর সবশেষে, বাটন ও ছবির নিচে (ক্লায়েন্টের অনুরোধে)।
             লেখা আগে একটু ফ্যাকাশে ছিল — স্পষ্ট করা হলো। --}}
        <dl class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-3xl mx-auto">
            @foreach($trust as [$icon, $label])
                <div class="flex items-center gap-2.5 rounded-xl bg-white/15 backdrop-blur
                            px-4 py-3 border border-white/20">
                    <span class="text-sky2-100 shrink-0"><x-icon :name="$icon" class="w-5 h-5"/></span>
                    <dt class="text-[0.85rem] font-medium text-white leading-snug">{{ $label }}</dt>
                </div>
            @endforeach
        </dl>
    </div>
</section>
