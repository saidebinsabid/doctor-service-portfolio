<x-mail::message>
# 🔔 নতুন সিরিয়াল বুক হয়েছে

<x-mail::panel>
**{{ $a->patient_name }}** — {{ fmt_date($a->appointment_date, 'bn') }}
({{ fmt_day($a->appointment_date, false, 'bn') }}), {{ fmt_time($a->slotHm(), 'bn') }}
· সিরিয়াল নং **{{ bn_number($a->serial_no) }}**
</x-mail::panel>

| | |
|---|---|
| বুকিং কোড | `{{ $a->booking_code }}` |
| রোগীর নাম | {{ $a->patient_name }} |
@if($a->patient_age !== null)
| বয়স | {{ $a->ageLabel('bn') }} |
@endif
| মোবাইল | {{ $a->patient_phone }} |
@if($a->guardian_name)
| অভিভাবক | {{ $a->guardian_name }} |
@endif
| ভিজিটের ধরন | {{ $a->visitLabel('bn') }} |
| চেম্বার | {{ $a->chamber->name_bn }} |

@if($a->problem)
**সমস্যা:** {{ $a->problem }}
@endif

<x-mail::button :url="route('admin.appointments.show', $a)">
অ্যাডমিন প্যানেলে দেখুন
</x-mail::button>

<x-mail::button :url="'https://wa.me/' . intl_bd_phone($a->patient_phone)" color="success">
রোগীকে হোয়াটসঅ্যাপে বার্তা দিন
</x-mail::button>

<small>এই বার্তাটি ওয়েবসাইট থেকে স্বয়ংক্রিয়ভাবে পাঠানো হয়েছে।</small>
</x-mail::message>
