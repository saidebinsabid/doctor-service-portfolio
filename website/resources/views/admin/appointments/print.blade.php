@php use App\Models\Setting; @endphp
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <title>দিনের তালিকা — {{ fmt_date($date) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; margin: 0; padding: 24px; color: #1e293b; font-size: 13px; }
        h1 { font-size: 18px; color: #1b3a6b; margin: 0; }
        .sub { color: #64748b; margin: 2px 0 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 7px 8px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f1f5f9; font-size: 12px; color: #475569; }
        .btns { text-align: center; margin: 18px 0; }
        button { font-family: inherit; padding: 8px 16px; border-radius: 8px; border: 1px solid #1b3a6b; background: #1b3a6b; color: #fff; font-weight: 600; cursor: pointer; }
        @media print { .btns { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <h1>{{ Setting::get('doctor_name') }} — দিনের সিরিয়াল তালিকা</h1>
    <p class="sub">{{ fmt_date($date) }} ({{ fmt_day($date) }}) · মোট {{ bn_number($list->count()) }} জন</p>
    <table>
        <thead><tr><th>সিরিয়াল</th><th>রোগী</th><th>বয়স</th><th>মোবাইল</th><th>ধরন</th><th>সময়</th></tr></thead>
        <tbody>
            @forelse($list as $a)
                <tr>
                    <td><b>{{ bn_number($a->serial_no) }}</b></td>
                    <td>{{ $a->patient_name }}</td>
                    <td>{{ $a->ageLabel() }}</td>
                    <td dir="ltr">{{ $a->patient_phone }}</td>
                    <td>{{ $a->visitLabel() }}</td>
                    <td>{{ fmt_time($a->slotHm()) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:24px; color:#94a3b8;">এই দিনে কোনো সিরিয়াল নেই।</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="btns"><button onclick="window.print()">🖨️ প্রিন্ট করুন</button></div>
</body>
</html>
