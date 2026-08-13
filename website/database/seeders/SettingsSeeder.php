<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * সাইটের সব একক তথ্য।
 *
 * সূত্র: ✅ = ভিজিটিং কার্ড / প্রচারপত্র / WhatsApp থেকে যাচাইকৃত
 *        ⚠️ = ডেমো মান, প্রকৃত তথ্য পেলে বদলাতে হবে
 */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rows() as $row) {
            Setting::updateOrCreate(
                ['key' => $row['key']],
                collect($row)->except('key')->all(),
            );
        }

        Setting::flush();
    }

    private function rows(): array
    {
        return [
            /* ==================== ডাক্তারের পরিচয় ==================== */
            [
                'key' => 'doctor_name', 'group' => 'doctor', 'type' => 'text', 'sort_order' => 10,
                'label_bn' => 'ডাক্তারের পুরো নাম', 'label_en' => 'Doctor full name',
                'value_bn' => 'প্রফেসর ডা. আবু সুফিয়ান',            // ✅ প্রচারপত্র
                'value_en' => 'Professor Dr. Md. Abu Sufian',        // ✅ ভিজিটিং কার্ড
            ],
            [
                'key' => 'doctor_short', 'group' => 'doctor', 'type' => 'text', 'sort_order' => 20,
                'label_bn' => 'সংক্ষিপ্ত নাম (হেডার ও ফুটারে)', 'label_en' => 'Short name',
                'value_bn' => 'ডা. আবু সুফিয়ান',
                'value_en' => 'Dr. Abu Sufian',
            ],
            [
                'key' => 'degrees', 'group' => 'doctor', 'type' => 'text', 'sort_order' => 30,
                'label_bn' => 'ডিগ্রিসমূহ', 'label_en' => 'Degrees',
                'value_bn' => 'এমবিবিএস, ডিসিএইচ, এফসিপিএস (শিশুরোগ)',  // ✅
                'value_en' => 'MBBS, DCH, FCPS (Pediatrics)',            // ✅
            ],
            [
                'key' => 'specialty', 'group' => 'doctor', 'type' => 'text', 'sort_order' => 40,
                'label_bn' => 'বিশেষত্ব', 'label_en' => 'Specialty',
                'value_bn' => 'সিনিয়র শিশু বিশেষজ্ঞ ও শিশুরোগ পরামর্শক',   // ✅
                'value_en' => 'Senior Child Specialist & Pediatric Consultant',
            ],
            [
                'key' => 'designation', 'group' => 'doctor', 'type' => 'text', 'sort_order' => 50,
                'label_bn' => 'পদবি', 'label_en' => 'Designation',
                'value_bn' => 'অধ্যাপক, শিশু বিভাগ',
                'value_en' => 'Professor, Department of Pediatrics',
            ],
            [
                'key' => 'bmdc', 'group' => 'doctor', 'type' => 'text', 'sort_order' => 60,
                'label_bn' => 'বিএমডিসি রেজিস্ট্রেশন নম্বর', 'label_en' => 'BMDC Reg. No.',
                'value_bn' => '১৮১২৫', 'value_en' => '18125',            // ✅ ভিজিটিং কার্ড
            ],
            [
                'key' => 'tagline', 'group' => 'doctor', 'type' => 'text', 'sort_order' => 70,
                'label_bn' => 'ট্যাগলাইন', 'label_en' => 'Tagline',
                'value_bn' => 'আপনার শিশুর সুস্থতা, আমাদের অঙ্গীকার',      // ✅ প্রচারপত্র
                'value_en' => "Your child's wellbeing, our commitment",
            ],
            [
                'key' => 'intro', 'group' => 'doctor', 'type' => 'textarea', 'sort_order' => 80,
                'label_bn' => 'পরিচিতি (About সেকশন)', 'label_en' => 'Introduction',
                'value_bn' => 'শিশুস্বাস্থ্যে দীর্ঘ পেশাগত অভিজ্ঞতাসম্পন্ন একজন চিকিৎসক। '
                    . 'হবিগঞ্জ মেডিকেল কলেজের প্রতিষ্ঠাতা অধ্যক্ষ এবং ব্রাহ্মণবাড়িয়া মেডিকেল '
                    . 'কলেজের সাবেক অধ্যাপক ও অধ্যক্ষ। আইসিডিডিআর,বি-তে গবেষণা এবং দেশের '
                    . 'শীর্ষস্থানীয় মেডিকেল কলেজগুলোতে অধ্যাপনার অভিজ্ঞতা নিয়ে নবজাতক থেকে '
                    . 'কিশোর বয়স পর্যন্ত শিশুদের সাধারণ ও জটিল রোগের আধুনিক, প্রমাণভিত্তিক '
                    . 'চিকিৎসা ও পরামর্শ প্রদান করেন। অপ্রয়োজনে কোন এন্টিবায়োটিক দেন না।',
                'value_en' => 'A physician with extensive professional experience in child health. '
                    . 'Founder Principal of Habiganj Medical College and former Professor and '
                    . 'Principal of Brahmanbaria Medical College. With a background in research at '
                    . 'icddr,b and teaching at leading medical colleges of the country, he provides '
                    . 'modern, evidence-based treatment and counselling for common and complex '
                    . 'childhood illnesses — from newborns through adolescence. '
                    . 'He never prescribes antibiotics unnecessarily.',
            ],
            [
                'key' => 'doctor_photo', 'group' => 'doctor', 'type' => 'image', 'sort_order' => 90,
                'label_bn' => 'ডাক্তারের ছবি', 'label_en' => 'Doctor photo',
                'hint_bn' => 'বর্গাকার ছবি দিন, ন্যূনতম ৮০০×৮০০ পিক্সেল। ⚠️ এখনো দেওয়া হয়নি।',
                'value_bn' => null,                                       // ⚠️ ডেমো
            ],
            [
                'key' => 'about_photo', 'group' => 'doctor', 'type' => 'image', 'sort_order' => 95,
                'label_bn' => 'পরিচিতি বিভাগের ছবি (ফ্লায়ার/পোস্টার)', 'label_en' => 'About-section image (flyer/poster)',
                'hint_bn' => '"ডাক্তার সম্পর্কে" অংশের বিবরণের নিচে দেখানো হয় — পোর্ট্রেট আকৃতি ভালো মানায়।',
                'value_bn' => null,
            ],

            /* ==================== যোগাযোগ ==================== */
            [
                'key' => 'hotline', 'group' => 'contact', 'type' => 'text', 'sort_order' => 10,
                'label_bn' => 'হটলাইন (কল বাটন)', 'label_en' => 'Hotline',
                'hint_bn' => 'ইবনে সিনার সিরিয়াল লাইন',
                'value_bn' => '09610009614',                              // ✅ কার্ড + প্রচারপত্র
            ],
            [
                'key' => 'whatsapp', 'group' => 'contact', 'type' => 'text', 'sort_order' => 20,
                'label_bn' => 'হোয়াটসঅ্যাপ নম্বর', 'label_en' => 'WhatsApp number',
                'hint_bn' => 'বুকিং কনফার্মেশন এই নম্বরে যাবে',
                'value_bn' => '01327804433',                              // ✅ WhatsApp, ১ আগস্ট
            ],
            [
                'key' => 'email', 'group' => 'contact', 'type' => 'text', 'sort_order' => 30,
                'label_bn' => 'ইমেইল', 'label_en' => 'Email',
                'hint_bn' => '⚠️ এই ইমেইল অ্যাকাউন্টটি এখনো তৈরি হয়নি',
                'value_bn' => 'info@drabusufian.com',                     // ⚠️ ডেমো
            ],
            [
                'key' => 'website', 'group' => 'contact', 'type' => 'text', 'sort_order' => 40,
                'label_bn' => 'ওয়েবসাইট', 'label_en' => 'Website',
                'hint_bn' => '⚠️ ডোমেইনটি এখনো কেনা হয়নি',
                'value_bn' => 'drabusufian.com',                          // ⚠️ ডেমো
            ],
            [
                'key' => 'facebook', 'group' => 'social', 'type' => 'text', 'sort_order' => 10,
                'label_bn' => 'ফেসবুক পেজ', 'label_en' => 'Facebook page',
                'hint_bn' => '⚠️ দুটি লিংক দেওয়া হয়েছিল — কোনটি অফিসিয়াল পেজ তা নিশ্চিত করতে হবে',
                'value_bn' => 'https://www.facebook.com/share/1D69J2tEUT/',  // ✅
            ],
            [
                'key' => 'facebook_alt', 'group' => 'social', 'type' => 'text', 'sort_order' => 20,
                'label_bn' => 'ফেসবুক (দ্বিতীয় লিংক)', 'label_en' => 'Facebook (second)',
                'hint_bn' => 'খালি রাখলে ফুটারে দেখাবে না',
                'value_bn' => 'https://www.facebook.com/share/185mAdFVFB/',  // ✅
            ],
            [
                'key' => 'youtube', 'group' => 'social', 'type' => 'text', 'sort_order' => 30,
                'label_bn' => 'ইউটিউব চ্যানেল', 'label_en' => 'YouTube channel',
                'value_bn' => 'https://youtube.com/@drabusufian-h6m',      // ✅
            ],

            /* ==================== ভিজিট ফি ====================
               ⚠️ প্রকৃত ফি কোনো সোর্সে পাওয়া যায়নি।

               show_fees ইচ্ছাকৃতভাবে বন্ধ রাখা হয়েছে — ভুল ফি প্রকাশ্যে
               থাকলে রোগী সেই টাকা নিয়ে চেম্বারে এসে বিব্রত হবেন, যা
               ডাক্তারের সুনামের প্রশ্ন। প্রকৃত ফি বসিয়ে তবেই চালু করুন। */
            [
                'key' => 'show_fees', 'group' => 'fees', 'type' => 'boolean', 'sort_order' => 10,
                'label_bn' => 'ওয়েবসাইটে ভিজিট ফি দেখাবেন?', 'label_en' => 'Show fees on website?',
                'hint_bn' => '⚠️ নিচের ফি এখনো ডেমো। প্রকৃত ফি বসানোর পর চালু করুন।',
                'value_bn' => '0',
            ],
            [
                'key' => 'fee_new', 'group' => 'fees', 'type' => 'number', 'sort_order' => 20,
                'label_bn' => 'নতুন রোগীর ফি (টাকা)', 'label_en' => 'New patient fee',
                'value_bn' => '1000',                                     // ⚠️ ডেমো
            ],
            [
                'key' => 'fee_followup', 'group' => 'fees', 'type' => 'number', 'sort_order' => 30,
                'label_bn' => 'ফলো-আপ ফি (টাকা)', 'label_en' => 'Follow-up fee',
                'value_bn' => '600',                                      // ⚠️ ডেমো
            ],
            [
                'key' => 'fee_report', 'group' => 'fees', 'type' => 'number', 'sort_order' => 40,
                'label_bn' => 'রিপোর্ট দেখানোর ফি (টাকা)', 'label_en' => 'Report review fee',
                'value_bn' => '400',                                      // ⚠️ ডেমো
            ],
            [
                'key' => 'followup_days', 'group' => 'fees', 'type' => 'number', 'sort_order' => 50,
                'label_bn' => 'ফলো-আপের মেয়াদ (দিন)', 'label_en' => 'Follow-up validity (days)',
                'value_bn' => '15',                                       // ⚠️ ডেমো
            ],
            [
                'key' => 'bkash_number', 'group' => 'fees', 'type' => 'text', 'sort_order' => 60,
                'label_bn' => 'বিকাশ নম্বর (পেমেন্টের জন্য)', 'label_en' => 'bKash number (for payment)',
                'value_bn' => '01327804433', 'value_en' => '01327804433',
            ],
            [
                'key' => 'fee_notice', 'group' => 'fees', 'type' => 'textarea', 'sort_order' => 70,
                'label_bn' => 'ফি/বিকাশ বার্তা (বুকিং সেকশনের বক্সে)',
                'label_en' => 'Fee / bKash notice (booking section box)',
                'value_bn' => 'রোগীদের সাশ্রয়ের জন্য ফি ৮০০ টাকা। বিকাশ করলে ৭০০ টাকায় সিরিয়াল নিতে পারবেন। চেম্বারে পেমেন্ট কপি দেখাবেন।',
                'value_en' => "For patients' convenience the fee is 800 taka. Pay via bKash and get a serial for 700 taka. Show the payment copy at the chamber.",
            ],

            /* ==================== SEO ==================== */
            [
                'key' => 'meta_title', 'group' => 'seo', 'type' => 'text', 'sort_order' => 10,
                'label_bn' => 'পেজ টাইটেল', 'label_en' => 'Page title',
                'value_bn' => 'প্রফেসর ডা. আবু সুফিয়ান — সিনিয়র শিশু বিশেষজ্ঞ, বাড্ডা, ঢাকা',
                'value_en' => 'Professor Dr. Md. Abu Sufian — Child Specialist, Badda, Dhaka',
            ],
            [
                'key' => 'meta_description', 'group' => 'seo', 'type' => 'textarea', 'sort_order' => 20,
                'label_bn' => 'মেটা বিবরণ', 'label_en' => 'Meta description',
                'hint_bn' => 'গুগলে সার্চ ফলাফলে যে লেখাটি দেখাবে। ১৫০-১৬০ অক্ষরের মধ্যে রাখুন।',
                'value_bn' => 'প্রফেসর ডা. আবু সুফিয়ান — এমবিবিএস, ডিসিএইচ, এফসিপিএস (শিশুরোগ)। '
                    . 'সিনিয়র শিশু বিশেষজ্ঞ। চেম্বার: ইবনে সিনা, বাড্ডা, ঢাকা। অনলাইনে সিরিয়াল নিন।',
                'value_en' => 'Professor Dr. Md. Abu Sufian — MBBS, DCH, FCPS (Pediatrics). '
                    . 'Senior child specialist. Chamber: Ibn Sina, Badda, Dhaka. Book your serial online.',
            ],
            [
                'key' => 'og_image', 'group' => 'seo', 'type' => 'image', 'sort_order' => 30,
                'label_bn' => 'শেয়ার করার ছবি', 'label_en' => 'Share image',
                'hint_bn' => 'ফেসবুকে লিংক শেয়ার করলে যে ছবি দেখাবে। ১২০০×৬৩০ পিক্সেল।',
                'value_bn' => null,
            ],

            /* ==================== বুকিং ==================== */
            [
                'key' => 'booking_enabled', 'group' => 'booking', 'type' => 'boolean', 'sort_order' => 10,
                'label_bn' => 'অনলাইন বুকিং চালু আছে?', 'label_en' => 'Online booking enabled?',
                'hint_bn' => 'বন্ধ করলে ক্যালেন্ডারের বদলে "ফোনে যোগাযোগ করুন" বার্তা দেখাবে',
                'value_bn' => '1',
            ],
            [
                'key' => 'holiday_mode', 'group' => 'booking', 'type' => 'boolean', 'sort_order' => 20,
                'label_bn' => 'ছুটির মোড', 'label_en' => 'Holiday mode',
                'hint_bn' => 'চালু করলে সব তারিখে নতুন বুকিং বন্ধ হয়ে যাবে (আগের বুকিং অক্ষত থাকবে)',
                'value_bn' => '0',
            ],
            [
                'key' => 'booking_note', 'group' => 'booking', 'type' => 'textarea', 'sort_order' => 30,
                'label_bn' => 'বুকিং ফর্মের নিচের নোট', 'label_en' => 'Note below booking form',
                'value_bn' => 'নির্ধারিত সময়ের ১৫ মিনিট আগে উপস্থিত থাকার অনুরোধ করা হচ্ছে। '
                    . 'শিশুর আগের প্রেসক্রিপশন, পরীক্ষার রিপোর্ট ও টিকার কার্ড সাথে আনুন।',
                'value_en' => 'Please arrive 15 minutes before your scheduled time. Bring previous '
                    . 'prescriptions, test reports and the immunisation card.',
            ],
            [
                'key' => 'whatsapp_greeting', 'group' => 'booking', 'type' => 'textarea', 'sort_order' => 40,
                'label_bn' => 'হোয়াটসঅ্যাপ বার্তার শুরুর লাইন', 'label_en' => 'WhatsApp message opening',
                'hint_bn' => 'রোগী যে বার্তাটি পাঠাবেন তার প্রথম লাইন',
                'value_bn' => 'আসসালামু আলাইকুম। আমি অনলাইনে সিরিয়াল বুক করেছি।',
                'value_en' => 'Assalamu Alaikum. I have booked a serial online.',
            ],
            [
                'key' => 'notify_whatsapp', 'group' => 'booking', 'type' => 'text', 'sort_order' => 50,
                'label_bn' => 'বুকিং নোটিফিকেশন WhatsApp নম্বর (ডাক্তার/চেম্বার)',
                'label_en' => 'Booking-notification WhatsApp (doctor/chamber)',
                'hint_bn' => 'নতুন বুকিং হলে এই নম্বরে অটো WhatsApp যাবে। নিচের API key-ও লাগবে। খালি রাখলে বন্ধ।',
                'value_bn' => '', 'value_en' => '',
            ],
            [
                'key' => 'callmebot_apikey', 'group' => 'booking', 'type' => 'text', 'sort_order' => 60,
                'label_bn' => 'CallMeBot API key (ফ্রি WhatsApp নোটিফিকেশন)',
                'label_en' => 'CallMeBot API key (free WhatsApp notifications)',
                'hint_bn' => 'ফ্রি: উপরের নম্বর থেকে CallMeBot-এর WhatsApp-এ মেসেজ পাঠিয়ে key নিন — callmebot.com/blog/free-api-whatsapp-messages',
                'value_bn' => '', 'value_en' => '',
            ],
            [
                'key' => 'sms_api_key', 'group' => 'booking', 'type' => 'text', 'sort_order' => 70,
                'label_bn' => 'রোগীর ফোনে অটো SMS — sms.net.bd API key',
                'label_en' => 'Patient auto-SMS — sms.net.bd API key',
                'hint_bn' => 'sms.net.bd → API পেজ থেকে "Generate Key" চেপে key নিন এবং এখানে বসান। খালি থাকলে ফিচারটি বন্ধ, বুকিং স্বাভাবিক চলবে।',
                'value_bn' => '', 'value_en' => '',
            ],
            [
                'key' => 'sms_sender_id', 'group' => 'booking', 'type' => 'text', 'sort_order' => 80,
                'label_bn' => 'SMS Sender ID (মাস্কিং — ঐচ্ছিক)',
                'label_en' => 'SMS Sender ID (mask — optional)',
                'hint_bn' => 'sms.net.bd থেকে অনুমোদিত আপনার Sender ID (যেমন: DrAbuSufian)। না থাকলে খালি রাখুন — নন-মাস্ক নম্বর থেকে যাবে।',
                'value_bn' => '', 'value_en' => '',
            ],
        ];
    }
}
