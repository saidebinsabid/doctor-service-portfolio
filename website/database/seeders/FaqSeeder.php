<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'সিরিয়াল কীভাবে নেব?',
                'How do I book a serial?',
                'এই ওয়েবসাইটের ক্যালেন্ডার থেকে তারিখ ও সময় বেছে নিয়ে ফর্মটি পূরণ করুন। '
                . 'এরপর "হোয়াটসঅ্যাপে কনফার্ম করুন" বাটনে চাপ দিলে আপনার সিরিয়ালের তথ্যসহ '
                . 'একটি বার্তা তৈরি হয়ে যাবে — শুধু Send চাপলেই হবে। চাইলে সরাসরি হটলাইনে '
                . 'ফোন করেও সিরিয়াল নিতে পারেন।',
                'Pick a date and time from the calendar on this website and fill in the form. '
                . 'Then tap "Confirm on WhatsApp" — a message with your serial details is created '
                . 'automatically, you just press Send. You may also call the hotline directly.',
            ],
            [
                'প্রতিদিন কতজন রোগী দেখা হয়?',
                'How many patients are seen each day?',
                'প্রতিদিন সর্বোচ্চ ২৫টি সিরিয়াল দেওয়া হয়। সিরিয়াল শেষ হয়ে গেলে ক্যালেন্ডারে '
                . 'সেই তারিখটি বন্ধ দেখাবে।',
                'A maximum of 25 serials are issued per day. Once they are full, that date shows '
                . 'as closed on the calendar.',
            ],
            [
                'কত সময় আগে চেম্বারে পৌঁছাব?',
                'How early should I arrive?',
                'আপনার নির্ধারিত সময়ের অন্তত ১৫ মিনিট আগে পৌঁছানোর অনুরোধ করা হচ্ছে। '
                . 'ওয়েবসাইটে দেখানো সময়টি আনুমানিক — রোগীর অবস্থা অনুযায়ী কিছুটা আগে-পরে হতে পারে।',
                'Please arrive at least 15 minutes before your scheduled time. The time shown is '
                . 'approximate and may shift slightly depending on patient needs.',
            ],
            [
                'সাথে কী কী নিয়ে যাব?',
                'What should I bring?',
                'শিশুর আগের প্রেসক্রিপশন, করানো পরীক্ষার রিপোর্ট, টিকার কার্ড এবং বর্তমানে '
                . 'চলমান ওষুধের তালিকা সাথে আনুন।',
                "Bring the child's previous prescriptions, any test reports, the immunisation card, "
                . 'and a list of current medicines.',
            ],
            [
                'সিরিয়াল বাতিল বা পরিবর্তন করা যাবে?',
                'Can I cancel or reschedule?',
                'হ্যাঁ। হোয়াটসঅ্যাপ নম্বরে আপনার বুকিং কোডসহ বার্তা পাঠালে সিরিয়াল বাতিল বা '
                . 'পরিবর্তন করে দেওয়া হবে। আসতে না পারলে আগেভাগে জানালে অন্য একজন রোগী '
                . 'সেই সিরিয়ালটি পেতে পারেন।',
                'Yes. Send a message with your booking code to the WhatsApp number and it will be '
                . 'cancelled or rescheduled. Letting us know early frees the slot for another patient.',
            ],
            [
                'বুকিং করেছি কি না কীভাবে দেখব?',
                'How can I check my booking?',
                'ওয়েবসাইটের "সিরিয়াল দেখুন" পাতায় আপনার বুকিং কোড ও মোবাইল নম্বর দিলেই '
                . 'সিরিয়ালের বর্তমান অবস্থা দেখতে পাবেন।',
                'Go to the "Check serial" page and enter your booking code and mobile number to see '
                . 'the current status of your appointment.',
            ],
        ];

        foreach ($rows as $i => [$qBn, $qEn, $aBn, $aEn]) {
            Faq::updateOrCreate(
                ['question_bn' => $qBn],
                [
                    'question_en' => $qEn,
                    'answer_bn'   => $aBn,
                    'answer_en'   => $aEn,
                    'sort_order'  => ($i + 1) * 10,
                    'is_active'   => true,
                ],
            );
        }
    }
}
