@extends('admin.layouts.app')
@section('title', 'সাইট সেটিংস')
@section('heading', 'সাইট সেটিংস')

@section('content')

    <p class="text-sm text-slate-500 mb-4 max-w-2xl">হোমপেজের নাম, পরিচিতি, যোগাযোগ, ফি ও অন্যান্য টেক্সট এখান থেকে বদলান। দ্বিভাষিক ঘরে ইংরেজি খালি রাখলে সাইটে বাংলাটাই দেখাবে।</p>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6 max-w-3xl">
        @csrf @method('PUT')

        @foreach($groupLabels as $groupKey => $groupLabel)
            @php $rows = $groups[$groupKey] ?? collect(); @endphp
            @if($rows->isNotEmpty())
                <section class="a-card overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-brand-100 bg-brand-50/40">
                        <h2 class="font-bold text-brand-900">{{ $groupLabel }}</h2>
                    </div>
                    <div class="p-5 space-y-5">
                        @foreach($rows as $s)
                            @php $n = "settings[{$s->key}]"; @endphp
                            <div>
                                @if($s->type === 'boolean')
                                    <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                        <input type="checkbox" name="{{ $n }}[bn]" value="1" class="mt-0.5 rounded border-brand-300"
                                               {{ old("settings.{$s->key}.bn", $s->value_bn) ? 'checked' : '' }}>
                                        <span class="text-sm font-medium text-slate-700">{{ $s->label_bn }}</span>
                                    </label>

                                @elseif($s->type === 'image')
                                    <label class="a-label">{{ $s->label_bn }}</label>
                                    @if($s->value_bn)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($s->value_bn) }}" alt=""
                                             class="w-24 h-24 object-cover rounded-lg border border-brand-100 mb-2">
                                    @endif
                                    <input type="file" name="{{ $n }}[file]" accept="image/*" class="a-input !py-2">

                                @elseif($s->type === 'number')
                                    <label class="a-label">{{ $s->label_bn }}</label>
                                    <input type="number" name="{{ $n }}[bn]" value="{{ old("settings.{$s->key}.bn", $s->value_bn) }}" class="a-input max-w-xs">

                                @elseif($s->type === 'textarea')
                                    <label class="a-label">{{ $s->label_bn }}</label>
                                    <div class="grid sm:grid-cols-2 gap-3">
                                        <div><span class="block text-[.7rem] font-semibold text-brand-400 mb-1">বাংলা</span>
                                            <textarea name="{{ $n }}[bn]" class="a-textarea">{{ old("settings.{$s->key}.bn", $s->value_bn) }}</textarea></div>
                                        <div><span class="block text-[.7rem] font-semibold text-slate-400 mb-1">English</span>
                                            <textarea name="{{ $n }}[en]" class="a-textarea" dir="ltr">{{ old("settings.{$s->key}.en", $s->value_en) }}</textarea></div>
                                    </div>

                                @else
                                    <label class="a-label">{{ $s->label_bn }}</label>
                                    <div class="grid sm:grid-cols-2 gap-3">
                                        <div><span class="block text-[.7rem] font-semibold text-brand-400 mb-1">বাংলা</span>
                                            <input type="text" name="{{ $n }}[bn]" value="{{ old("settings.{$s->key}.bn", $s->value_bn) }}" class="a-input"></div>
                                        <div><span class="block text-[.7rem] font-semibold text-slate-400 mb-1">English</span>
                                            <input type="text" name="{{ $n }}[en]" value="{{ old("settings.{$s->key}.en", $s->value_en) }}" class="a-input" dir="ltr"></div>
                                    </div>
                                @endif

                                @if($s->hint_bn)<p class="a-hint">{{ $s->hint_bn }}</p>@endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach

        <div class="sticky bottom-0 bg-slate-50/80 backdrop-blur py-3 -mx-4 px-4 lg:mx-0 lg:px-0 lg:static lg:bg-transparent lg:py-0">
            <button type="submit" class="a-btn a-btn-primary">সব সেটিংস সংরক্ষণ করুন</button>
        </div>
    </form>

@endsection
