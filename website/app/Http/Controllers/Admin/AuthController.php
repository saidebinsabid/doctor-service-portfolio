<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'ইমেইল লিখুন।',
            'email.email'       => 'সঠিক ইমেইল লিখুন।',
            'password.required' => 'পাসওয়ার্ড লিখুন।',
        ]);

        if (! Auth::attempt($data, $request->boolean('remember'))) {
            /* ইমেইল ভুল না পাসওয়ার্ড ভুল — আলাদা করে বলা হয় না।
               বললে কেউ বৈধ ইমেইল খুঁজে বের করতে পারত। */
            throw ValidationException::withMessages([
                'email' => 'ইমেইল বা পাসওয়ার্ড সঠিক নয়।',
            ]);
        }

        /* নিষ্ক্রিয় করা অ্যাকাউন্ট দিয়ে ঢোকা যাবে না */
        if (! $request->user()->is_active) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'এই অ্যাকাউন্টটি বর্তমানে নিষ্ক্রিয়।',
            ]);
        }

        $request->session()->regenerate();       // সেশন ফিক্সেশন ঠেকানো

        $request->user()->forceFill(['last_login_at' => now()])->save();

        ActivityLog::record('login', $request->user(), 'লগইন করেছেন');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        ActivityLog::record('logout', $request->user(), 'লগআউট করেছেন');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
