<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * নিজের প্রোফাইল — নাম, ইমেইল ও পাসওয়ার্ড পরিবর্তন।
 */
class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($request->user()->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required'  => 'নাম লিখুন।',
            'email.required' => 'ইমেইল লিখুন।',
            'email.unique'   => 'এই ইমেইল অন্য একটি অ্যাকাউন্টে ব্যবহৃত হচ্ছে।',
        ]);

        $request->user()->update($data);

        return back()->with('success', 'প্রোফাইল হালনাগাদ করা হয়েছে।');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.required'     => 'বর্তমান পাসওয়ার্ড লিখুন।',
            'current_password.current_password' => 'বর্তমান পাসওয়ার্ড সঠিক নয়।',
            'password.required'  => 'নতুন পাসওয়ার্ড লিখুন।',
            'password.confirmed' => 'দুবার লেখা পাসওয়ার্ড মিলছে না।',
            'password.min'       => 'পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে।',
        ]);

        $request->user()->update(['password' => $request->input('password')]);

        ActivityLog::record('password', $request->user(), 'পাসওয়ার্ড পরিবর্তন করেছেন');

        return back()->with('success', 'পাসওয়ার্ড পরিবর্তন করা হয়েছে।');
    }
}
