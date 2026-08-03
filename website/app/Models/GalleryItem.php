<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GalleryItem extends Model
{
    use HasTranslations, Orderable;

    protected array $translatable = ['title'];

    protected $fillable = [
        'type', 'title_bn', 'title_en',
        'file_path', 'thumb_path', 'youtube_url', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function url(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public function thumbUrl(): ?string
    {
        return $this->thumb_path
            ? Storage::url($this->thumb_path)
            : $this->url();
    }

    /** ইউটিউব লিংক থেকে ভিডিও আইডি বের করা */
    public function youtubeId(): ?string
    {
        if (! $this->youtube_url) {
            return null;
        }

        preg_match(
            '~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~',
            $this->youtube_url,
            $m
        );

        return $m[1] ?? null;
    }
}
