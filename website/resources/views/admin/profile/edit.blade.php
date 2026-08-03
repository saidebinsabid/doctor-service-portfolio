@extends('admin.layouts.app')
@section('title', 'প্রোফাইল')
@section('heading', 'আমার প্রোফাইল')

@section('content')
    <div class="max-w-xl space-y-6">

        {{-- প্রোফাইল তথ্য --}}
        <div class="a-card p-5 lg:p-6">
            <h2 class="font-bold text-brand-900 mb-4">অ্যাকাউন্টের তথ্য</h2>
            <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
                @csrf @method('PUT')
                <div><label class="a-label">নাম</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="a-input" required>
                    @error('name')<p class="a-error">{{ $message }}</p>@enderror</div>
                <div><label class="a-label">ইমেইল</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="a-input" dir="ltr" required>
                    @error('email')<p class="a-error">{{ $message }}</p>@enderror</div>
                <div><label class="a-label">ফোন</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="a-input" dir="ltr">
                    @error('phone')<p class="a-error">{{ $message }}</p>@enderror</div>
                <div class="text-xs text-slate-400">ভূমিকা: {{ $user->roleLabel() }}</div>
                <button class="a-btn a-btn-primary">সংরক্ষণ করুন</button>
            </form>
        </div>

        {{-- পাসওয়ার্ড --}}
        <div class="a-card p-5 lg:p-6">
            <h2 class="font-bold text-brand-900 mb-4">পাসওয়ার্ড পরিবর্তন</h2>
            <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
                @csrf @method('PUT')
                <div><label class="a-label">বর্তমান পাসওয়ার্ড</label>
                    <input type="password" name="current_password" class="a-input" autocomplete="current-password" required>
                    @error('current_password')<p class="a-error">{{ $message }}</p>@enderror</div>
                <div><label class="a-label">নতুন পাসওয়ার্ড</label>
                    <input type="password" name="password" class="a-input" autocomplete="new-password" required>
                    @error('password')<p class="a-error">{{ $message }}</p>@enderror</div>
                <div><label class="a-label">নতুন পাসওয়ার্ড আবার লিখুন</label>
                    <input type="password" name="password_confirmation" class="a-input" autocomplete="new-password" required></div>
                <button class="a-btn a-btn-primary">পাসওয়ার্ড বদলান</button>
            </form>
        </div>

    </div>
@endsection
