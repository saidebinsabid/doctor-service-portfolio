@extends('admin.layouts.app')
@php $isEdit = $item->exists; @endphp
@section('title', ($isEdit ? 'সম্পাদনা' : 'নতুন').' — '.$cfg['title'])
@section('heading', $cfg['title'])

@php
    $tones = ['' => 'ডিফল্ট (নেভি)', 'sky' => 'আকাশি', 'blue' => 'নীল', 'teal' => 'টিল',
        'green' => 'সবুজ', 'emerald' => 'পান্না', 'lime' => 'লাইম', 'amber' => 'অ্যাম্বার',
        'orange' => 'কমলা', 'red' => 'লাল', 'rose' => 'গোলাপি', 'violet' => 'বেগুনি',
        'indigo' => 'ইন্ডিগো', 'cyan' => 'সায়ান', 'slate' => 'ধূসর'];
@endphp

@section('content')

    <div class="max-w-2xl">
        <a href="{{ route('admin.'.$cfg['route'].'.index') }}" class="text-sm text-slate-500 hover:text-brand-900 inline-flex items-center gap-1 mb-3">
            ← {{ $cfg['title'] }} তালিকায় ফিরে যান
        </a>

        <div class="a-card p-5 lg:p-6">
            <h2 class="text-lg font-bold text-brand-900 mb-1">{{ $isEdit ? $cfg['singular'].' সম্পাদনা' : 'নতুন '.$cfg['singular'] }}</h2>
            <p class="text-xs text-slate-400 mb-5">দ্বিভাষিক ঘরে বাংলা আবশ্যক; ইংরেজি খালি রাখলে সাইটে বাংলাটাই দেখাবে।</p>

            <form method="POST"
                  action="{{ $isEdit ? route('admin.'.$cfg['route'].'.update', $item) : route('admin.'.$cfg['route'].'.store') }}"
                  enctype="multipart/form-data" class="space-y-5">
                @csrf
                @if($isEdit) @method('PUT') @endif

                @foreach($cfg['fields'] as $field)
                    @php
                        $name = $field['name'];
                        $type = $field['type'] ?? 'text';
                        $req  = $field['required'] ?? false;
                        $trans = $field['translatable'] ?? false;
                    @endphp

                    <div>
                        {{-- দ্বিভাষিক text/textarea --}}
                        @if($trans)
                            <label class="a-label">{{ $field['label'] }} @if($req)<span class="text-red-500">*</span>@endif</label>
                            <div class="grid sm:grid-cols-2 gap-3">
                                <div>
                                    <span class="block text-[.7rem] font-semibold text-brand-400 mb-1">বাংলা</span>
                                    @if($type === 'textarea')
                                        <textarea name="{{ $name }}_bn" class="a-textarea" {{ $req ? 'required' : '' }}>{{ old($name.'_bn', $item->{$name.'_bn'}) }}</textarea>
                                    @else
                                        <input type="text" name="{{ $name }}_bn" value="{{ old($name.'_bn', $item->{$name.'_bn'}) }}" class="a-input" {{ $req ? 'required' : '' }}>
                                    @endif
                                    @error($name.'_bn')<p class="a-error">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <span class="block text-[.7rem] font-semibold text-slate-400 mb-1">English (ঐচ্ছিক)</span>
                                    @if($type === 'textarea')
                                        <textarea name="{{ $name }}_en" class="a-textarea" dir="ltr">{{ old($name.'_en', $item->{$name.'_en'}) }}</textarea>
                                    @else
                                        <input type="text" name="{{ $name }}_en" value="{{ old($name.'_en', $item->{$name.'_en'}) }}" class="a-input" dir="ltr">
                                    @endif
                                    @error($name.'_en')<p class="a-error">{{ $message }}</p>@enderror
                                </div>
                            </div>

                        {{-- boolean --}}
                        @elseif($type === 'boolean')
                            <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                <input type="checkbox" name="{{ $name }}" value="1" class="mt-0.5 rounded border-brand-300"
                                       {{ old($name, $item->{$name}) ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-slate-700">{{ $field['label'] }}</span>
                            </label>

                        {{-- image --}}
                        @elseif($type === 'image')
                            <label class="a-label">{{ $field['label'] }}</label>
                            @if($item->{$name})
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($item->{$name}) }}" alt=""
                                     class="w-28 h-28 object-cover rounded-lg border border-brand-100 mb-2">
                            @endif
                            <input type="file" name="{{ $name }}" accept="image/*" class="a-input !py-2">

                        {{-- select --}}
                        @elseif($type === 'select')
                            <label class="a-label">{{ $field['label'] }} @if($req)<span class="text-red-500">*</span>@endif</label>
                            <select name="{{ $name }}" class="a-select" {{ $req ? 'required' : '' }}>
                                @foreach(($field['options'] ?? []) as $val => $opt)
                                    <option value="{{ $val }}" @selected(old($name, $item->{$name}) == $val)>{{ $opt }}</option>
                                @endforeach
                            </select>

                        {{-- tone (রঙ) --}}
                        @elseif($type === 'tone')
                            <label class="a-label">{{ $field['label'] }}</label>
                            <select name="{{ $name }}" class="a-select">
                                @foreach($tones as $val => $opt)
                                    <option value="{{ $val }}" @selected(old($name, $item->{$name}) == $val)>{{ $opt }}</option>
                                @endforeach
                            </select>

                        {{-- datetime --}}
                        @elseif($type === 'datetime')
                            @php $dv = $item->{$name}; $dv = $dv instanceof \DateTimeInterface ? $dv->format('Y-m-d\TH:i') : $dv; @endphp
                            <label class="a-label">{{ $field['label'] }}</label>
                            <input type="datetime-local" name="{{ $name }}" value="{{ old($name, $dv) }}" class="a-input">

                        {{-- number / url / icon / text --}}
                        @else
                            <label class="a-label">{{ $field['label'] }} @if($req)<span class="text-red-500">*</span>@endif</label>
                            <input type="{{ $type === 'number' ? 'number' : ($type === 'url' ? 'url' : 'text') }}"
                                   name="{{ $name }}" value="{{ old($name, $item->{$name}) }}" class="a-input"
                                   {{ $req ? 'required' : '' }}
                                   @if($type === 'icon') placeholder="যেমন: heart, shield, syringe" @endif>
                        @endif

                        @if(!empty($field['hint']))<p class="a-hint">{{ $field['hint'] }}</p>@endif
                        @error($name)<p class="a-error">{{ $message }}</p>@enderror
                    </div>
                @endforeach

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="a-btn a-btn-primary">{{ $isEdit ? 'সংরক্ষণ করুন' : 'যোগ করুন' }}</button>
                    <a href="{{ route('admin.'.$cfg['route'].'.index') }}" class="a-btn a-btn-light">বাতিল</a>
                </div>
            </form>
        </div>
    </div>

@endsection
