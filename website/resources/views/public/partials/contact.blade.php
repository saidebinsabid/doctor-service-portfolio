@php
    use App\Models\Setting;

    $allChambers = ($chambers ?? collect([$chamber ?? null]))->filter();

    $cards = [];

    // প্রতিটি চেম্বারের নিজস্ব হটলাইন — নাম ও সময়সহ (ক্লায়েন্টের অনুরোধে)
    foreach ($allChambers as $c) {
        if ($hot = ($c->hotline ?: Setting::get('hotline'))) {
            $hours = collect(group_schedules($c->schedules))
                ->map(fn ($g) => fmt_time($g['start']) . '–' . fmt_time($g['end']))
                ->unique()->implode(' / ');
            $cards[] = [
                'icon' => 'phone', 'tone' => 'brand',
                'label' => __('chm.hotline') . ' — ' . $c->name,
                'value' => $hot,
                'sub'   => $hours,
                'href'  => 'tel:' . $hot,
            ];
        }
    }

    if (Setting::get('whatsapp')) {
        // ক্লায়েন্টের অনুরোধে WhatsApp নম্বরটি লুকানো — শুধু ক্লিক করলেই চ্যাট খোলে
        $cards[] = [
            'icon' => 'phone', 'tone' => 'green', 'label' => __('common.whatsapp'),
            'value' => __('common.clickToChat'),
            'href' => 'https://wa.me/' . intl_bd_phone(Setting::get('whatsapp')),
        ];
    }
    if (Setting::get('email')) {
        $cards[] = [
            'icon' => 'mail', 'tone' => 'sky', 'label' => __('common.email'),
            'value' => Setting::get('email'),
            'href' => 'mailto:' . Setting::get('email'),
        ];
    }
    // প্রতিটি চেম্বারের ঠিকানা (ম্যাপে যেতে)
    foreach ($allChambers as $c) {
        $cards[] = [
            'icon' => 'pin', 'tone' => 'rose', 'label' => __('chm.address') . ' — ' . $c->name,
            'value' => $c->address,
            'href' => $c->mapDirectionsUrl(),
        ];
    }
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
                    @if(!empty($c['sub']))
                        <span class="block text-[0.72rem] text-slate-500 mt-0.5">{{ $c['sub'] }}</span>
                    @endif
                </span>
            </a>
        @endforeach
    </div>
</div>
