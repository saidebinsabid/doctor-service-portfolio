@extends('admin.layouts.app')
@section('title', 'ম্যানুয়াল সিরিয়াল')
@section('heading', 'ম্যানুয়াল সিরিয়াল যোগ')

@section('content')
    <div class="max-w-xl">
        <a href="{{ route('admin.appointments.index') }}" class="text-sm text-slate-500 hover:text-brand-900 mb-3 inline-block">← তালিকায় ফিরে যান</a>
        <div class="a-card p-5 lg:p-6">
            <p class="a-hint mb-4">ফোনে বা সরাসরি আসা রোগীর সিরিয়াল এখানে যোগ করুন। এটি স্বয়ংক্রিয়ভাবে "নিশ্চিত" হিসেবে যুক্ত হবে।</p>
            <form method="POST" action="{{ route('admin.appointments.store') }}" class="space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-3">
                    <div><label class="a-label">চেম্বার <span class="text-red-500">*</span></label>
                        <select name="chamber_id" class="a-select" required>
                            @foreach($chambers as $c)<option value="{{ $c->id }}" @selected(old('chamber_id') == $c->id)>{{ $c->name_bn }}</option>@endforeach
                        </select></div>
                    <div><label class="a-label">তারিখ <span class="text-red-500">*</span></label>
                        <input type="date" name="appointment_date" value="{{ old('appointment_date', $date) }}" class="a-input" required></div>
                    <div><label class="a-label">সময় <span class="text-red-500">*</span></label>
                        <input type="time" name="slot_time" value="{{ old('slot_time') }}" class="a-input" required></div>
                    <div><label class="a-label">ভিজিটের ধরন <span class="text-red-500">*</span></label>
                        <select name="visit_type" class="a-select" required>
                            <option value="new">নতুন রোগী</option><option value="followup">ফলো-আপ</option><option value="report">রিপোর্ট দেখানো</option>
                        </select></div>
                </div>
                <hr class="border-brand-100">
                <div class="grid sm:grid-cols-2 gap-3">
                    <div><label class="a-label">রোগীর নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="patient_name" value="{{ old('patient_name') }}" class="a-input" required></div>
                    <div><label class="a-label">মোবাইল <span class="text-red-500">*</span></label>
                        <input type="text" name="patient_phone" value="{{ old('patient_phone') }}" class="a-input" dir="ltr" required></div>
                    <div class="grid grid-cols-2 gap-2">
                        <div><label class="a-label">বয়স</label><input type="number" name="patient_age" value="{{ old('patient_age') }}" class="a-input"></div>
                        <div><label class="a-label">একক</label><select name="patient_age_unit" class="a-select"><option value="year">বছর</option><option value="month">মাস</option><option value="day">দিন</option></select></div>
                    </div>
                    <div><label class="a-label">লিঙ্গ</label>
                        <select name="gender" class="a-select"><option value="">—</option><option value="male">ছেলে</option><option value="female">মেয়ে</option></select></div>
                    <div><label class="a-label">অভিভাবকের নাম</label><input type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="a-input"></div>
                    <div class="sm:col-span-2"><label class="a-label">সমস্যা</label><textarea name="problem" class="a-textarea">{{ old('problem') }}</textarea></div>
                </div>
                <button class="a-btn a-btn-primary">সিরিয়াল যোগ করুন</button>
            </form>
        </div>
    </div>
@endsection
