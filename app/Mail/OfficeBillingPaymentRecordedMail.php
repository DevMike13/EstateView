<?php

namespace App\Mail;

use App\Models\BillingPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfficeBillingPaymentRecordedMail extends Mailable
{
    use Queueable, SerializesModels;

    public BillingPayment $payment;

    public function __construct(BillingPayment $payment)
    {
        $this->payment = $payment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Office Payment Recorded',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.billings.office-payment-recorded',
            with: [
                'payment' => $this->payment,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}