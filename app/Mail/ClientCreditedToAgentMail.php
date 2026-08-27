<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientCreditedToAgentMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $agent;
    public User $client;

    public function __construct(
        User $agent,
        User $client
    ) {
        $this->agent = $agent;
        $this->client = $client;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Client Credited to You'
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.commission.client-credited-to-agent'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}