@extends('admin.layouts.app')
@section('title', $cfg['title'])
@section('heading', $cfg['title'])

@php
    use Illuminate\Support\Str;
    // কলামের মান কীভাবে দেখাবে তা ঠিক করতে সংশ্লিষ্ট field খুঁজে নেওয়া
    $fieldFor = function (string $key) use ($cfg) {
        foreach ($cfg['fields'] as $f) {
            if ($f['name'] === $key) return $f;
            if (($f['translatable'] ?? false) && $f['name'] . '_bn' === $key) return ['type' => 'text'];
        }
        return ['type' => 'text'];
    };
    $activeField = collect($cfg['fields'])->firstWhere('name', 'is_active') ? 'is_active'
        : (collect($cfg['fields'])->firstWhere('name', 'is_approved') ? 'is_approved' : null);
@endphp

@section('content')

    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
        <div class="max-w-2xl">
            @if(!empty($cfg['hint']))
                <p class="text-sm text-slate-500">{{ $cfg['hint'] }}</p>
            @endif
        </div>
        <a href="{{ route('admin.'.$cfg['route'].'.create') }}" class="a-btn a-btn-primary shrink-0">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            নতুন {{ $cfg['singular'] }}
        </a>
    </div>

    <div class="a-card overflow-hidden">
        @if($items->isEmpty())
            <p class="px-5 py-10 text-center text-slate-500 text-sm">এখনো কোনো {{ $cfg['singular'] }} যোগ করা হয়নি।</p>
        @else
            <div class="overflow-x-auto">
                <table class="a-table">
                    <thead>
                        <tr>
                            <th class="w-10">#</th>
                            @foreach($cfg['columns'] as $key => $label)<th>{{ $label }}</th>@endforeach
                            @if($activeField)<th>অবস্থা</th>@endif
                            <th class="text-end">কাজ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $i => $item)
                            <tr>
                                <td class="text-slate-400 text-sm">{{ bn_number($i + 1) }}</td>
                                @foreach($cfg['columns'] as $key => $label)
                                    @php $f = $fieldFor($key); @endphp
                                    <td>
                                        @if(($f['type'] ?? '') === 'boolean')
                                            @if($item->{$key})<span class="badge badge-green">হ্যাঁ</span>@else<span class="badge badge-slate">না</span>@endif
                                        @elseif(($f['type'] ?? '') === 'select')
                                            <span class="badge badge-blue">{{ $f['options'][$item->{$key}] ?? $item->{$key} }}</span>
                                        @else
                                            <span class="text-slate-800">{{ Str::limit($item->{$key}, 60) ?: '—' }}</span>
                                        @endif
                                    </td>
                                @endforeach
                                @if($activeField)
                                    <td>
                                        @if($item->{$activeField})
                                            <span class="badge badge-green">● দেখাচ্ছে</span>
                                        @else
                                            <span class="badge badge-slate">লুকানো</span>
                                        @endif
                                    </td>
                                @endif
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.'.$cfg['route'].'.edit', $item) }}" class="a-btn a-btn-light a-btn-sm">সম্পাদনা</a>
                                        <form method="POST" action="{{ route('admin.'.$cfg['route'].'.destroy', $item) }}"
                                              data-confirm="এই {{ $cfg['singular'] }} মুছে ফেলবেন?">
                                            @csrf @method('DELETE')
                                            <button class="a-btn a-btn-danger a-btn-sm">মুছুন</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
