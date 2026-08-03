<div class="container-x max-w-3xl">
    <div class="section-head">
        <p class="eyebrow"><x-icon name="book" class="w-3.5 h-3.5"/> {{ __('faq.eyebrow') }}</p>
        <h2 class="section-title">{{ __('faq.title') }}</h2>
    </div>

    {{-- <details> ব্যবহার করায় জাভাস্ক্রিপ্ট ছাড়াই খোলে-বন্ধ হয় --}}
    <div class="space-y-3">
        @foreach($faqs as $i => $faq)
            <details class="card overflow-hidden group" @if($i === 0) open @endif>
                <summary class="flex items-center justify-between gap-4 px-5 py-4 cursor-pointer
                                list-none font-semibold text-brand-900 text-[0.95rem]
                                hover:bg-brand-50 transition">
                    {{ $faq->question }}
                    <svg class="w-5 h-5 shrink-0 text-sky2-500 transition-transform group-open:rotate-180"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></svg>
                </summary>
                <div class="px-5 pb-4 -mt-1 text-sm text-slate-600 leading-relaxed">
                    {{ $faq->answer }}
                </div>
            </details>
        @endforeach
    </div>
</div>
