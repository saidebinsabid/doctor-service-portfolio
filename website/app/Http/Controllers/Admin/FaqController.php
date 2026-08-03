<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;

class FaqController extends ContentCrudController
{
    protected function modelClass(): string
    {
        return Faq::class;
    }

    protected function config(): array
    {
        return [
            'title'    => 'সচরাচর জিজ্ঞাসা',
            'singular' => 'প্রশ্ন',
            'route'    => 'faqs',
            'hint'     => 'রোগীরা যেসব প্রশ্ন বারবার ফোনে করেন সেগুলো এখানে যোগ করলে '
                . 'ফোনের চাপ কমে। গুগল সার্চেও সাহায্য করে।',
            'columns'  => ['question_bn' => 'প্রশ্ন'],
            'fields'   => [
                ['name' => 'question', 'label' => 'প্রশ্ন', 'type' => 'text',
                    'translatable' => true, 'required' => true],
                ['name' => 'answer', 'label' => 'উত্তর', 'type' => 'textarea',
                    'translatable' => true, 'required' => true, 'max' => 2000],
                ['name' => 'is_active', 'label' => 'ওয়েবসাইটে দেখান', 'type' => 'boolean'],
            ],
        ];
    }
}
