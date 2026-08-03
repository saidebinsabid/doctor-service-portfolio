<?php

namespace App\Http\Controllers\Admin;

use App\Models\Chamber;

/**
 * চেম্বারের মৌলিক তথ্য (নাম, ঠিকানা, ম্যাপ, হটলাইন)।
 * সময়সূচি (schedule) ও ছুটি আলাদা মডিউলে — এখানে শুধু পরিচিতি।
 */
class ChamberController extends ContentCrudController
{
    protected function modelClass(): string
    {
        return Chamber::class;
    }

    protected function config(): array
    {
        return [
            'title'    => 'চেম্বার',
            'singular' => 'চেম্বার',
            'route'    => 'chambers',
            'hint'     => 'চেম্বারের নাম, ঠিকানা, হটলাইন ও ম্যাপ লোকেশন এখানে বদলান। '
                . 'বসার সময়সূচি (schedule) স্বয়ংক্রিয়ভাবে বুকিং স্লট তৈরি করে — সেটি আলাদাভাবে ব্যবস্থাপনা করা হয়।',
            'columns'  => ['name_bn' => 'নাম', 'hotline' => 'হটলাইন'],
            'fields'   => [
                ['name' => 'name', 'label' => 'চেম্বারের নাম', 'type' => 'text',
                    'translatable' => true, 'required' => true],
                ['name' => 'address', 'label' => 'ঠিকানা', 'type' => 'textarea',
                    'translatable' => true, 'max' => 500],
                ['name' => 'hotline', 'label' => 'হটলাইন / সিরিয়াল নম্বর', 'type' => 'text', 'max' => 40],
                ['name' => 'map_query', 'label' => 'গুগল ম্যাপ লোকেশন', 'type' => 'text', 'max' => 255,
                    'hint' => 'ঠিকানা বা যেমন "ইবনে সিনা ডায়াগনস্টিক বাড্ডা" — গুগল ম্যাপে এটাই খুঁজে দেখানো হবে।'],
                ['name' => 'is_active', 'label' => 'ওয়েবসাইটে দেখান', 'type' => 'boolean'],
            ],
        ];
    }
}
