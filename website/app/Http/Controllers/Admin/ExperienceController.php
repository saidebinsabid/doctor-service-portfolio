<?php

namespace App\Http\Controllers\Admin;

use App\Models\Experience;

class ExperienceController extends ContentCrudController
{
    protected function modelClass(): string
    {
        return Experience::class;
    }

    protected function config(): array
    {
        return [
            'title'    => 'পেশাগত অভিজ্ঞতা',
            'singular' => 'অভিজ্ঞতা',
            'route'    => 'experiences',
            'hint'     => 'হোমপেজের টাইমলাইনে যে ক্রমে সাজানো আছে সেই ক্রমেই দেখাবে।',
            'columns'  => ['position_bn' => 'পদ', 'organization_bn' => 'প্রতিষ্ঠান'],
            'fields'   => [
                ['name' => 'position', 'label' => 'পদবি', 'type' => 'text',
                    'translatable' => true, 'required' => true],
                ['name' => 'organization', 'label' => 'প্রতিষ্ঠান', 'type' => 'text',
                    'translatable' => true, 'required' => true],
                ['name' => 'period', 'label' => 'সময়কাল', 'type' => 'text', 'max' => 60,
                    'hint' => 'যেমন: ২০১৮ – ২০২২। না জানা থাকলে খালি রাখুন।'],
                ['name' => 'icon', 'label' => 'আইকন', 'type' => 'icon'],
                ['name' => 'is_current', 'label' => 'বর্তমানে কর্মরত', 'type' => 'boolean'],
                ['name' => 'is_active', 'label' => 'ওয়েবসাইটে দেখান', 'type' => 'boolean'],
            ],
        ];
    }
}
