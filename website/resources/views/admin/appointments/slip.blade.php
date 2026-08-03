@php use App\Models\Setting; @endphp
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <title>সিরিয়াল স্লিপ — {{ $a->booking_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Hind Siliguri', sans-serif; margin: 0; padding: 24px; color: #1e293b; }
        .slip { max-width: 340px; margin: 0 auto; border: 1.5px solid #1b3a6b; border-radius: 12px; padding: 20px; }
        .head { text-align: center; border-bottom: 1.5px dashed #cbd5e1; padding-bottom: 12px; margin-bottom: 12px; }
        .head h1 { margin: 0; font-size: 16px; color: #1b3a6b; }
        .head p { margin: 2px 0 0; font-size: 12px; color: #64748b; }
        .serial { text-align: center; margin: 14px 0; }
        .serial .n { font-size: 44px; font-weight: 700; color: #1b3a6b; line-height: 1; }
        .serial .c { font-size: 11px; color: #64748b; letter-spacing: 1px; }
        .row { display: flex; justify-content: space-between; font-size: 13px; padding: 4px 0; border-bottom: 1px solid #f1f5f9; }
        .row .l { color: #64748b; }
        .row .v { font-weight: 600; }
        .foot { text-align: center; font-size: 11px; color: #94a3b8; margin-top: 12px; }
        .btns { text-align: center; margin-top: 16px; }
        button, a.btn { font-family: inherit; padding: 8px 16px; border-radius: 8px; border: 1px solid #1b3a6b; background: #1b3a6b; color: #fff; font-weight: 600; cursor: pointer; text-decoration: none; font-size: 13px; }
        @media print { .btns { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="slip">
        <div class="head">
            <h1>{{ Setting::get('doctor_name') }}</h1>
            <p>{{ Setting::get('degrees') }}</p>
            <p>{{ $a->chamber?->name_bn }}</p>
        </div>
        <div class="serial">
            <div class="c">সিরিয়াল নম্বর</div>
            <div class="n">{{ bn_number($a->serial_no) }}</div>
        </div>
        <div class="row"><span class="l">রোগী</span><span class="v">{{ $a->patient_name }}</span></div>
        <div class="row"><span class="l">তারিখ</span><span class="v">{{ fmt_date($a->appointment_date) }}</span></div>
        <div class="row"><span class="l">সময়</span><span class="v">{{ fmt_time($a->slotHm()) }}</span></div>
        <div class="row"><span class="l">ধরন</span><span class="v">{{ $a->visitLabel() }}</span></div>
        <div class="row"><span class="l">বুকিং কোড</span><span class="v" dir="ltr">{{ $a->booking_code }}</span></div>
        <div class="foot">নির্ধারিত সময়ের ১৫ মিনিট আগে উপস্থিত থাকুন।</div>
    </div>
    <div class="btns"><button onclick="window.print()">🖨️ প্রিন্ট করুন</button></div>
</body>
</html>
