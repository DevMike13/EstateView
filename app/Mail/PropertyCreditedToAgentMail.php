<?php

namespace App\Mail;

use App\Models\LotReservation;
use App\Models\PurchaseAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropertyCreditedToAgentMail extends Mailable
{
    use Queueable, SerializesModels;

    public LotReservation $reservation;
    public PurchaseAccount $account;
    public string $performedBy;

    public function __construct(
        LotReservation $reservation,
        PurchaseAccount $account,
        string $performedBy
    ) {
        $this->reservation = $reservation;
        $this->account = $account;
        $this->performedBy = $performedBy;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Property Credited to You',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.agent.property-credited',
            with: [
                'reservation' => $this->reservation,
                'account' => $this->account,
                'performedBy' => $this->performedBy,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}