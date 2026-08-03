@php use App\Models\Setting; @endphp
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>লগইন — {{ Setting::get('doctor_short', 'ডা. আবু সুফিয়ান') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css'])
</head>
<body class="min-h-screen grid place-items-center p-4"
      style="background: radial-gradient(60rem 30rem at 50% -10%, var(--color-brand-100), transparent 60%), var(--color-slate-50);">

    <div class="w-full max-w-sm">
        <div class="text-center mb-6">
            <span class="inline-grid place-items-center w-14 h-14 rounded-2xl bg-brand-900 text-white mb-3">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 15v-4a4 4 0 00-4-4V2M8 15a4 4 0 008 0v-3a5 5 0 015-5V2M8 15v3a4 4 0 108 0v-1"/></svg>
            </span>
            <h1 class="text-xl font-bold text-brand-900">{{ Setting::get('doctor_short', 'ডা. আবু সুফিয়ান') }}</h1>
            <p class="text-sm text-slate-500 mt-1">অ্যাডমিন প্যানেলে লগইন করুন</p>
        </div>

        <div class="a-card p-6">
            @if($errors->any())
                <div class="flash flash-error mb-4">⚠️ {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="a-label" for="email">ইমেইল</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                           class="a-input" autocomplete="username" autofocus required
                           placeholder="admin@drabusufian.com">
                </div>
                <div>
                    <label class="a-label" for="password">পাসওয়ার্ড</label>
                    <input id="password" name="password" type="password"
                           class="a-input" autocomplete="current-password" required placeholder="••••••••">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600 select-none">
                    <input type="checkbox" name="remember" value="1" class="rounded border-brand-200">
                    মনে রাখুন
                </label>
                <button type="submit" class="a-btn a-btn-primary w-full !py-2.5">লগইন করুন</button>
            </form>
        </div>

        <p class="text-center mt-5">
            <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-brand-900">← ওয়েবসাইটে ফিরে যান</a>
        </p>
    </div>
</body>
</html>
