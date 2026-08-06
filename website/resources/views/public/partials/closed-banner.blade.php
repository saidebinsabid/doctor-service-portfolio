@php use App\Models\Setting; @endphp

{{-- চেম্বার বন্ধ ব্যানার — অ্যাডমিন → সেটিংস → "ছুটির মোড" (holiday_mode) অন করলেই
     সব পেজের উপরে (হেডারের নিচে) প্রকট লাল ব্যানারে "চেম্বার বন্ধ আছে" দেখায়।
     বন্ধ করতে হলে শুধু ছুটির মোড আবার অফ করলেই হবে। --}}
@if(Setting::bool('holiday_mode'))
    <div class="bg-red-600 text-white no-print" role="status">
        <div class="container-x flex items-center justify-center gap-2 py-2.5 text-center
                    text-sm font-semibold leading-snug">
            <x-icon name="clock" class="w-4 h-4 shrink-0"/>
            {{ __('booking.chamber_closed') }}
        </div>
    </div>
@endif
