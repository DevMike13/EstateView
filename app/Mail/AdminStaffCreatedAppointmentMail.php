<?php

namespace App\Mail;

use App\Models\ClientAppointment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminStaffCreatedAppointmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public ClientAppointment $appointment;
    public User $user;
    public string $performedBy;

    public function __construct(
        ClientAppointment $appointment,
        User $user,
        string $performedBy
    ) {
        $this->appointment = $appointment;
        $this->user = $user;
        $this->performedBy = $performedBy;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Appointment Scheduled',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.appointments.admin-staff-created-appointment',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}