{{-- বিশেষ চিকিৎসা — প্রচারপত্রে এগুলো আলাদা হাইলাইট বক্সে ছিল।
     ডাক্তারের ইউনিক সেলিং পয়েন্ট, তাই ওয়েবসাইটেও আলাদা সেকশন। --}}
<div class="container-x">
    <div class="section-head">
        <p class="eyebrow"><x-icon name="award" class="w-3.5 h-3.5"/> {{ __('spc.eyebrow') }}</p>
        <h2 class="section-title">{{ __('spc.title') }}</h2>
        <p class="section-sub">{{ __('spc.sub') }}</p>
    </div>

    <div class="grid-auto-lg">
        @foreach($specials as $item)
            <article class="card card-hover p-6 text-center">
                <span class="icon-bubble {{ tone_classes($item->tone) }} !w-14 !h-14 mx-auto !rounded-2xl">
                    <x-icon :name="$item->icon" class="w-7 h-7"/>
                </span>
                <h3 class="mt-4 text-lg">{{ $item->title }}</h3>
                @if($item->description)
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">{{ $item->description }}</p>
                @endif
            </article>
        @endforeach
    </div>
</div>
