<?php

namespace App\Mail;

use App\Models\BillingNotice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Str;

class BillingNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notice;

    /**
     * Create a new message instance.
     */
     public function __construct(BillingNotice $notice)
    {
        $this->notice = $notice;

        $this->notice->loadMissing([
            'user',
            'billing',
            'purchaseAccount',
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getNoticeTitle(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.notices.notice',
            with: [
                'notice' => $this->notice,
                'noticeTitle' => $this->getNoticeTitle(),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (! $this->notice->pdf_path) {
            return [];
        }

        $clientName = $this->notice->user?->name ?? 'Client';

        $fileName = Str::slug(
            $this->getNoticeTitle() . '-' . $clientName
        ) . '.pdf';

        return [
            Attachment::fromStorageDisk(
                config('billing_notices.disk', 'local'),
                $this->notice->pdf_path
            )
                ->as($fileName)
                ->withMime('application/pdf'),
        ];
    }

    private function getNoticeTitle(): string
    {
        return match ($this->notice->notice_type) {
            'monthly_payment' => 'Notice of Monthly Payment',
            'non_payment' => 'Notice of Non-Payment',
            'cancellation' => 'Notice of Cancellation',
            'forfeiture' => 'Notice of Forfeiture',
            default => 'Billing Notice',
        };
    }
}
