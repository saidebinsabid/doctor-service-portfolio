<?php

namespace App\Http\Controllers\Admin;

use App\Models\Qualification;

class QualificationController extends ContentCrudController
{
    protected function modelClass(): string
    {
        return Qualification::class;
    }

    protected function config(): array
    {
        return [
            'title'    => 'শিক্ষাগত যোগ্যতা',
            'singular' => 'যোগ্যতা',
            'route'    => 'qualifications',
            'hint'     => 'প্রতিষ্ঠান বা সাল খালি রাখলে ওয়েবসাইটে শুধু ডিগ্রির নামটাই '
                . 'দেখাবে — ফাঁকা ঘর বা "অজানা" লেখা দেখাবে না।',
            'columns'  => ['degree_bn' => 'ডিগ্রি', 'institution_bn' => 'প্রতিষ্ঠান'],
            'fields'   => [
                ['name' => 'degree', 'label' => 'ডিগ্রি', 'type' => 'text',
                    'translatable' => true, 'required' => true],
                ['name' => 'institution', 'label' => 'প্রতিষ্ঠান', 'type' => 'text',
                    'translatable' => true],
                ['name' => 'year', 'label' => 'সাল', 'type' => 'text', 'max' => 20],
                ['name' => 'is_active', 'label' => 'ওয়েবসাইটে দেখান', 'type' => 'boolean'],
            ],
        ];
    }
}
