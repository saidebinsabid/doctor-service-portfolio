<div class="container-x">
    <div class="section-head">
        <p class="eyebrow">{{ __('gal.eyebrow') }}</p>
        <h2 class="section-title">{{ __('gal.title') }}</h2>
    </div>

    <div class="grid-auto">
        @foreach($gallery as $item)
            @if($item->type === 'video' && $item->youtubeId())
                <figure class="card overflow-hidden card-hover">
                    <div class="aspect-video">
                        <iframe class="w-full h-full" loading="lazy"
                                src="https://www.youtube-nocookie.com/embed/{{ $item->youtubeId() }}"
                                title="{{ $item->title }}" allowfullscreen
                                referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    </div>
                    @if($item->title)
                        <figcaption class="px-4 py-3 text-sm text-slate-600">{{ $item->title }}</figcaption>
                    @endif
                </figure>
            @elseif($item->url())
                <figure class="card overflow-hidden card-hover">
                    <img src="{{ $item->thumbUrl() }}" alt="{{ $item->title }}"
                         loading="lazy" width="480" height="360"
                         class="w-full aspect-[4/3] object-cover">
                    @if($item->title)
                        <figcaption class="px-4 py-3 text-sm text-slate-600">{{ $item->title }}</figcaption>
                    @endif
                </figure>
            @endif
        @endforeach
    </div>
</div>
