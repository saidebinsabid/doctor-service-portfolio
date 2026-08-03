<div class="container-x">
    <div class="section-head">
        <p class="eyebrow"><x-icon name="stetho" class="w-3.5 h-3.5"/> {{ __('srv.eyebrow') }}</p>
        <h2 class="section-title text-balance-x">{{ __('srv.title') }}</h2>
        <p class="section-sub">{{ __('srv.sub') }}</p>
    </div>

    {{-- grid-auto: আইটেম সংখ্যা যাই হোক — ১৪টি হোক বা ২২টি —
         লেআউট নিজে থেকেই সাজে, ফাঁকা ঘর বা ভাঙা সারি থাকে না --}}
    <div class="grid-auto">
        @foreach($services as $service)
            <article class="card card-hover p-4 flex items-start gap-3.5">
                <span class="icon-bubble {{ tone_classes($service->tone) }}">
                    <x-icon :name="$service->icon" class="w-5 h-5"/>
                </span>
                <div class="pt-1">
                    <p class="text-[0.9rem] font-medium text-slate-700 leading-relaxed">
                        {{ $service->title }}</p>
                    @if($service->description)
                        <p class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $service->description }}</p>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</div>
