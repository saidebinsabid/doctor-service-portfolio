@php
    use App\Models\Setting;

    /*
    | Schema.org JSON-LD।
    |
    | এর ফলে গুগল সার্চ ফলাফলে ডাক্তারের নাম, বিশেষত্ব, ঠিকানা ও
    | চেম্বারের সময় আলাদাভাবে দেখাতে পারে — শুধু নীল লিংক নয়।
    | "শিশু বিশেষজ্ঞ বাড্ডা" সার্চে এটি বড় পার্থক্য গড়ে।
    |
    | সব তথ্য ডাটাবেস থেকে আসছে, তাই অ্যাডমিন প্যানেলে সময় বদলালে
    | গুগলের কাছে যাওয়া তথ্যও নিজে থেকেই বদলে যায়।
    */

    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    $hours = [];
    foreach ($chambers as $ch) {
        foreach ($ch->schedules->where('is_active', true) as $s) {
            $hours[] = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => $days[$s->day_of_week],
                'opens'  => $s->startHm(),
                'closes' => $s->endHm(),
            ];
        }
    }

    $sameAs = array_values(array_filter([
        Setting::get('facebook'),
        Setting::get('youtube'),
    ]));

    $data = array_filter([
        '@context' => 'https://schema.org',
        '@type'    => 'Physician',
        'name'     => Setting::raw('doctor_name', 'en') ?: Setting::get('doctor_name'),
        'alternateName' => Setting::raw('doctor_name', 'bn'),
        'medicalSpecialty' => 'Pediatric',
        'description' => Setting::raw('meta_description', 'en') ?: Setting::get('meta_description'),
        'url'       => route('home'),
        'telephone' => Setting::get('hotline') ? '+88' . Setting::get('hotline') : null,
        'email'     => Setting::get('email'),
        'address'   => $chamber ? [
            '@type' => 'PostalAddress',
            'streetAddress' => Setting::raw('chamber_street', 'en')
                ?: ($chamber->address_en ?: $chamber->address_bn),
            'addressLocality' => 'Dhaka',
            'addressCountry'  => 'BD',
        ] : null,
        'openingHoursSpecification' => $hours ?: null,
        'sameAs' => $sameAs ?: null,
    ]);
@endphp

<script type="application/ld+json">
{!! json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
