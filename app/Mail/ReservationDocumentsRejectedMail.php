<?php

namespace App\Mail;

use App\Models\LotReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationDocumentsRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public LotReservation $reservation;
    public string $performedBy;

    public function __construct(
        LotReservation $reservation,
        string $performedBy
    ) {
        $this->reservation = $reservation;
        $this->performedBy = $performedBy;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reservation Fee Rejected',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.reservations.documents-rejected',
            with: [
                'reservation' => $this->reservation,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}