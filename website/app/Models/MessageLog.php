<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * কোন রোগীকে কখন কী পাঠানো হয়েছে।
 * পরে বিতর্ক হলে ("আমাকে তো জানানো হয়নি") এখানেই প্রমাণ থাকে।
 */
class MessageLog extends Model
{
    protected $fillable = [
        'appointment_id', 'channel', 'recipient', 'template',
        'body', 'status', 'provider_response', 'sent_at',
    ];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public const CHANNEL_LABELS = [
        'whatsapp' => 'হোয়াটসঅ্যাপ',
        'sms'      => 'এসএমএস',
        'email'    => 'ইমেইল',
    ];

    public const STATUS_LABELS = [
        'queued' => ['bn' => 'অপেক্ষমাণ', 'tone' => 'amber'],
        'sent'   => ['bn' => 'পাঠানো হয়েছে', 'tone' => 'green'],
        'failed' => ['bn' => 'ব্যর্থ', 'tone' => 'red'],
        /* manual = রোগীকে বার্তাটি দেখানো হয়েছে, তিনি নিজে Send চাপবেন (Phase 1) */
        'manual' => ['bn' => 'রোগী নিজে পাঠাবেন', 'tone' => 'sky'],
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function channelLabel(): string
    {
        return self::CHANNEL_LABELS[$this->channel] ?? $this->channel;
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status]['bn'] ?? $this->status;
    }

    public function statusTone(): string
    {
        return self::STATUS_LABELS[$this->status]['tone'] ?? 'slate';
    }
}
