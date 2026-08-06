@php
    use App\Models\Setting;

    $cards = array_values(array_filter([
        Setting::get('hotline') ? [
            'icon' => 'phone', 'tone' => 'brand', 'label' => __('chm.hotline'),
            'value' => Setting::get('hotline'),
            'href' => 'tel:' . Setting::get('hotline'),
        ] : null,
        Setting::get('whatsapp') ? [
            // ক্লায়েন্টের অনুরোধে WhatsApp নম্বরটি লুকানো — শুধু ক্লিক করলেই চ্যাট খোলে
            'icon' => 'phone', 'tone' => 'green', 'label' => __('common.whatsapp'),
            'value' => __('common.clickToChat'),
            'href' => 'https://wa.me/' . intl_bd_phone(Setting::get('whatsapp')),
        ] : null,
        Setting::get('email') ? [
            'icon' => 'mail', 'tone' => 'sky', 'label' => __('common.email'),
            'value' => Setting::get('email'),
            'href' => 'mailto:' . Setting::get('email'),
        ] : null,
        $chamber ? [
            'icon' => 'pin', 'tone' => 'rose', 'label' => __('chm.address'),
            'value' => $chamber->address,
            'href' => $chamber->mapDirectionsUrl(),
        ] : null,
    ]));
@endphp

<div class="container-x">
    <div class="section-head">
        <p class="eyebrow"><x-icon name="phone" class="w-3.5 h-3.5"/> {{ __('cnt.eyebrow') }}</p>
        <h2 class="section-title">{{ __('cnt.title') }}</h2>
        <p class="section-sub">{{ __('cnt.sub') }}</p>
    </div>

    <div class="grid-auto">
        @foreach($cards as $c)
            <a href="{{ $c['href'] }}"
               @if(Str::startsWith($c['href'], 'http')) target="_blank" rel="noopener" @endif
               class="card card-hover p-5 flex items-start gap-3.5">
                <span class="icon-bubble {{ tone_classes($c['tone']) }}">
                    <x-icon :name="$c['icon']" class="w-5 h-5"/></span>
                <span class="min-w-0">
                    <span class="block text-xs text-slate-500">{{ $c['label'] }}</span>
                    <span class="block font-semibold text-brand-900 text-[0.9rem] mt-0.5 break-words">
                        {{ $c['value'] }}</span>
                </span>
            </a>
        @endforeach
    </div>
</div>
