{{-- ⚠️ এই সেকশনটি তখনই দেখা যায় যখন অ্যাডমিন প্যানেলে অন্তত একটি
     মতামত অনুমোদিত থাকে। প্রকৃত রোগীর মতামত না পাওয়া পর্যন্ত
     কিছু বানিয়ে লেখা হয়নি — চিকিৎসা পেশায় তা গ্রহণযোগ্য নয়। --}}
<div class="container-x">
    <div class="section-head">
        <p class="eyebrow">{{ __('tst.eyebrow') }}</p>
        <h2 class="section-title">{{ __('tst.title') }}</h2>
    </div>

    <div class="grid-auto-lg">
        @foreach($testimonials as $t)
            <blockquote class="card p-5">
                @if($t->rating)
                    <div class="flex gap-0.5 mb-2.5" aria-label="{{ bn_number($t->rating) }}/৫">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $t->rating ? 'text-amber-400' : 'text-slate-200' }}"
                                 viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="m12 2 3 6.5 7 1-5 4.9 1.2 7L12 18l-6.2 3.4L7 14.4 2 9.5l7-1z"/>
                            </svg>
                        @endfor
                    </div>
                @endif

                <p class="text-slate-600 text-sm leading-relaxed">{{ $t->comment }}</p>

                <footer class="mt-3 text-sm font-semibold text-brand-900">
                    — {{ $t->patient_name }}
                    @if($t->location)
                        <span class="font-normal text-slate-400">, {{ $t->location }}</span>
                    @endif
                </footer>
            </blockquote>
        @endforeach
    </div>
</div>
