<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** নতুন সিরিয়াল বুক হলে চেম্বারের ইমেইলে যে বার্তা যায় */
class AppointmentCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'নতুন সিরিয়াল — ' . $this->appointment->patient_name
                . ' (' . fmt_date($this->appointment->appointment_date, 'bn') . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointment-created',
            with: ['a' => $this->appointment->load('chamber')],
        );
    }
}
