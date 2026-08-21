<?php

namespace App\Mail;

use App\Models\CommissionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommissionPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public CommissionRequest $commissionRequest;

    public function __construct(
        CommissionRequest $commissionRequest
    ) {
        $this->commissionRequest =
            $commissionRequest;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Commission Payment Released'
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.commission.commission-paid'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}