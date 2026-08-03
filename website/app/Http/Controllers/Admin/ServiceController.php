<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;

class ServiceController extends ContentCrudController
{
    protected function modelClass(): string
    {
        return Service::class;
    }

    protected function config(): array
    {
        return [
            'title'    => 'সেবাসমূহ',
            'singular' => 'সেবা',
            'route'    => 'services',
            'hint'     => 'ওয়েবসাইটে যত ইচ্ছা সেবা যোগ করা যাবে — লেআউট নিজে থেকেই '
                . 'সাজিয়ে নেবে। "বিশেষ চিকিৎসা" চিহ্নিত করলে আলাদা সেকশনে দেখাবে।',
            'columns'  => ['title_bn' => 'সেবা', 'is_special' => 'বিশেষ'],
            'fields'   => [
                ['name' => 'title', 'label' => 'সেবার নাম', 'type' => 'text',
                    'translatable' => true, 'required' => true],
                ['name' => 'description', 'label' => 'সংক্ষিপ্ত বিবরণ', 'type' => 'textarea',
                    'translatable' => true, 'max' => 500,
                    'hint' => 'বিশেষ চিকিৎসার কার্ডে দেখানো হয়। সাধারণ সেবায় খালি রাখলেও চলবে।'],
                ['name' => 'icon', 'label' => 'আইকন', 'type' => 'icon'],
                ['name' => 'tone', 'label' => 'আইকনের রঙ', 'type' => 'tone'],
                ['name' => 'is_special', 'label' => 'বিশেষ চিকিৎসা হিসেবে দেখান', 'type' => 'boolean'],
                ['name' => 'is_active', 'label' => 'ওয়েবসাইটে দেখান', 'type' => 'boolean'],
            ],
        ];
    }
}
