<?php

namespace App\Http\Controllers\Admin;

use App\Models\GalleryItem;

class GalleryController extends ContentCrudController
{
    protected function modelClass(): string
    {
        return GalleryItem::class;
    }

    protected function config(): array
    {
        return [
            'title'    => 'গ্যালারি',
            'singular' => 'ছবি',
            'route'    => 'gallery',
            'hint'     => 'চেম্বারের ছবি যোগ করলে হোমপেজে গ্যালারি সেকশনটি দেখা যাবে। '
                . 'একটিও ছবি না থাকলে সেকশনটি নিজে থেকেই লুকানো থাকে। '
                . 'ইউটিউব ভিডিও যোগ করতে "ধরন" থেকে ভিডিও বেছে লিংক দিন।',
            'columns'  => ['title_bn' => 'শিরোনাম', 'type' => 'ধরন'],
            'fields'   => [
                ['name' => 'type', 'label' => 'ধরন', 'type' => 'select',
                    'options' => ['photo' => 'ছবি', 'video' => 'ইউটিউব ভিডিও'],
                    'required' => true],
                ['name' => 'title', 'label' => 'শিরোনাম', 'type' => 'text', 'translatable' => true],
                ['name' => 'file_path', 'label' => 'ছবি', 'type' => 'image',
                    'hint' => 'JPG, PNG বা WebP — সর্বোচ্চ ৪ MB। বড় ছবি নিজে থেকেই ছোট করা হবে।'],
                ['name' => 'youtube_url', 'label' => 'ইউটিউব লিংক', 'type' => 'url',
                    'hint' => 'শুধু ভিডিওর ক্ষেত্রে'],
                ['name' => 'is_active', 'label' => 'ওয়েবসাইটে দেখান', 'type' => 'boolean'],
            ],
        ];
    }
}
