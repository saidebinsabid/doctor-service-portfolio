<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'email'   => ['nullable', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
            'website' => ['prohibited'],           // হানিপট
        ]);

        ContactMessage::create([
            ...collect($data)->except('website')->all(),
            'phone'      => normalize_bd_phone($data['phone'] ?? '') ?: null,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', __('contact.sent'));
    }
}
