<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notice;

class NoticeController extends ContentCrudController
{
    protected function modelClass(): string
    {
        return Notice::class;
    }

    protected function config(): array
    {
        return [
            'title'    => 'নোটিশ',
            'singular' => 'নোটিশ',
            'route'    => 'notices',
            'hint'     => '"১৫ আগস্ট চেম্বার বন্ধ থাকবে" জাতীয় ঘোষণা ওয়েবসাইটের '
                . 'একদম উপরে ব্যানার হিসেবে দেখাবে। শুরু ও শেষের সময় দিলে '
                . 'নির্দিষ্ট সময় পেরোলে নিজে থেকেই উঠে যাবে — পরে মনে করে '
                . 'বন্ধ করতে হবে না।',
            'columns'  => ['title_bn' => 'শিরোনাম', 'severity' => 'ধরন'],
            'fields'   => [
                ['name' => 'title', 'label' => 'শিরোনাম', 'type' => 'text',
                    'translatable' => true, 'required' => true],
                ['name' => 'body', 'label' => 'বিস্তারিত', 'type' => 'textarea',
                    'translatable' => true, 'max' => 500],
                ['name' => 'severity', 'label' => 'ধরন', 'type' => 'select',
                    'options' => ['info' => 'সাধারণ (নীল)', 'warning' => 'সতর্কতা (হলুদ)',
                                  'urgent' => 'জরুরি (লাল)'],
                    'required' => true],
                ['name' => 'starts_at', 'label' => 'কবে থেকে দেখাবে', 'type' => 'datetime',
                    'hint' => 'খালি রাখলে এখনই দেখাবে'],
                ['name' => 'ends_at', 'label' => 'কবে পর্যন্ত দেখাবে', 'type' => 'datetime',
                    'hint' => 'খালি রাখলে বন্ধ না করা পর্যন্ত দেখাবে'],
                ['name' => 'is_active', 'label' => 'সক্রিয়', 'type' => 'boolean'],
            ],
        ];
    }
}
