<?php

namespace App\Http\Controllers\Admin;

use App\Models\Testimonial;

class TestimonialController extends ContentCrudController
{
    protected function modelClass(): string
    {
        return Testimonial::class;
    }

    protected function config(): array
    {
        return [
            'title'    => 'রোগীদের মতামত',
            'singular' => 'মতামত',
            'route'    => 'testimonials',
            /* সাইটে সেকশনটি এখন লুকানো — একটিও অনুমোদিত মতামত নেই।
               ইচ্ছাকৃত সিদ্ধান্ত: প্রকৃত মতামত না পাওয়া পর্যন্ত কিছু
               বানিয়ে লেখা হয়নি। */
            'hint'     => '⚠️ অনুমোদন না করা পর্যন্ত কোনো মতামত ওয়েবসাইটে দেখাবে না। '
                . 'একটিও অনুমোদিত মতামত না থাকলে পুরো সেকশনটিই লুকানো থাকে। '
                . 'রোগীর অনুমতি নিয়ে তবেই মতামত প্রকাশ করুন।',
            'columns'  => ['patient_name' => 'নাম', 'is_approved' => 'অনুমোদিত'],
            'approvable' => true,
            'fields'   => [
                ['name' => 'patient_name', 'label' => 'নাম', 'type' => 'text',
                    'required' => true, 'max' => 100],
                ['name' => 'location', 'label' => 'এলাকা', 'type' => 'text', 'max' => 100,
                    'hint' => 'যেমন: বাড্ডা, ঢাকা'],
                ['name' => 'rating', 'label' => 'রেটিং (১–৫)', 'type' => 'number'],
                ['name' => 'comment', 'label' => 'মতামত', 'type' => 'textarea',
                    'translatable' => true, 'required' => true, 'max' => 1000],
                ['name' => 'is_approved', 'label' => 'অনুমোদন দিন (ওয়েবসাইটে দেখাবে)',
                    'type' => 'boolean'],
            ],
        ];
    }
}
