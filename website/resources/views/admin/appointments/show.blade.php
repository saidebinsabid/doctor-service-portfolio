@extends('admin.layouts.app')
@section('title', 'সিরিয়াল — '.$a->patient_name)
@section('heading', 'সিরিয়াল বিস্তারিত')

@php
    use App\Models\Appointment;
    $badge = fn ($tone) => match ($tone) {
        'amber' => 'badge-amber', 'sky' => 'badge-blue',
        'green' => 'badge-green', 'red' => 'badge-red', default => 'badge-slate',
    };
@endphp

@section('content')

    <a href="{{ route('admin.appointments.index') }}" class="text-sm text-slate-500 hover:text-brand-900 inline-flex items-center gap-1 mb-3">← তালিকায় ফিরে যান</a>

    <div class="grid lg:grid-cols-3 gap-5">
        {{-- রোগীর তথ্য --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="a-card p-5">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-brand-900">{{ $a->patient_name }}</h2>
                        <p class="text-sm text-slate-400" dir="ltr">{{ $a->booking_code }}</p>
                    </div>
                    <span class="badge {{ $badge($a->statusTone()) }}">{{ $a->statusLabel() }}</span>
                </div>
                <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-slate-400">সিরিয়াল নম্বর</dt><dd class="font-bold text-brand-900 text-lg">{{ bn_number($a->serial_no) }}</dd></div>
                    <div><dt class="text-slate-400">তারিখ ও সময়</dt><dd class="font-medium">{{ fmt_date($a->appointment_date) }} — {{ fmt_time($a->slotHm()) }}</dd></div>
                    <div><dt class="text-slate-400">মোবাইল</dt><dd class="font-medium" dir="ltr">{{ $a->patient_phone }}</dd></div>
                    <div><dt class="text-slate-400">বয়স / লিঙ্গ</dt><dd class="font-medium">{{ $a->ageLabel() ?: '—' }} {{ $a->gender ? '· '.($a->gender === 'female' ? 'মেয়ে' : 'ছেলে') : '' }}</dd></div>
                    <div><dt class="text-slate-400">ভিজিটের ধরন</dt><dd class="font-medium">{{ $a->visitLabel() }}</dd></div>
                    <div><dt class="text-slate-400">চেম্বার</dt><dd class="font-medium">{{ $a->chamber?->name_bn ?? '—' }}</dd></div>
                    @if($a->guardian_name)<div><dt class="text-slate-400">অভিভাবক</dt><dd class="font-medium">{{ $a->guardian_name }}</dd></div>@endif
                    @if($a->problem)<div class="sm:col-span-2"><dt class="text-slate-400">সমস্যা</dt><dd class="font-medium">{{ $a->problem }}</dd></div>@endif
                </dl>
            </div>

            {{-- অ্যাডমিন নোট --}}
            <div class="a-card p-5">
                <h3 class="font-bold text-brand-900 mb-3">অভ্যন্তরীণ নোট</h3>
                <form method="POST" action="{{ route('admin.appointments.note', $a) }}">
                    @csrf @method('PATCH')
                    <textarea name="admin_note" class="a-textarea" placeholder="শুধু অ্যাডমিন দেখবেন…">{{ $a->admin_note }}</textarea>
                    <button class="a-btn a-btn-light a-btn-sm mt-2">নোট সংরক্ষণ</button>
                </form>
            </div>
        </div>

        {{-- অ্যাকশন --}}
        <div class="space-y-5">
            {{-- WhatsApp কনফার্মেশন --}}
            <div class="a-card p-5">
                <h3 class="font-bold text-brand-900 mb-2">WhatsApp কনফার্মেশন</h3>
                <p class="a-hint mb-2">রোগীকে পাঠানোর জন্য বার্তা প্রস্তুত:</p>
                <textarea readonly class="a-textarea text-xs" onclick="this.select()">{{ $confirmText }}</textarea>
                <a href="{{ $waLink }}" target="_blank" rel="noopener"
                   class="a-btn w-full mt-2 !text-white" style="background: var(--color-wa-500);">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1012 2zm5.8 14.2c-.2.7-1.4 1.3-2 1.4-.5.1-1.1.1-1.8-.1-.4-.1-1-.3-1.6-.6-2.9-1.3-4.8-4.2-5-4.4-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.1.1.3 0 .5l-.4.5-.3.3c-.1.1-.3.3-.1.5.1.3.7 1.1 1.4 1.8.9.8 1.7 1 2 1.2.2.1.4.1.5-.1l.6-.8c.2-.2.4-.2.6-.1l1.9.9c.2.1.4.2.5.3.1.3.1.7-.1 1.4z"/></svg>
                    WhatsApp-এ পাঠান
                </a>
            </div>

            {{-- স্ট্যাটাস --}}
            <div class="a-card p-5">
                <h3 class="font-bold text-brand-900 mb-3">অবস্থা পরিবর্তন</h3>
                <div class="space-y-2">
                    @foreach(['confirmed' => 'নিশ্চিত করুন', 'completed' => 'সম্পন্ন', 'no_show' => 'অনুপস্থিত', 'cancelled' => 'বাতিল'] as $st => $lbl)
                        @if($a->status !== $st)
                            <form method="POST" action="{{ route('admin.appointments.status', $a) }}"
                                  @if($st === 'cancelled') data-confirm="সিরিয়ালটি বাতিল করবেন?" @endif>
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $st }}">
                                <button class="a-btn {{ $st === 'cancelled' ? 'a-btn-danger' : 'a-btn-light' }} w-full justify-start">{{ $lbl }}</button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- সময় পরিবর্তন --}}
            <div class="a-card p-5">
                <h3 class="font-bold text-brand-900 mb-3">সময় পরিবর্তন</h3>
                <form method="POST" action="{{ route('admin.appointments.reschedule', $a) }}" class="space-y-2">
                    @csrf @method('PATCH')
                    <input type="date" name="appointment_date" value="{{ $a->dateString() }}" class="a-input" required>
                    <input type="time" name="slot_time" value="{{ $a->slotHm() }}" class="a-input" required>
                    <button class="a-btn a-btn-light w-full">নতুন সময়ে সরান</button>
                </form>
            </div>

            <a href="{{ route('admin.appointments.slip', $a) }}" target="_blank" class="a-btn a-btn-light w-full">সিরিয়াল স্লিপ ছাপুন</a>
        </div>
    </div>

@endsection
