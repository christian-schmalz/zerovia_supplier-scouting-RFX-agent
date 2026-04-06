<?php

namespace App\Mail;

use App\Models\RfqDocument;
use App\Models\RfqRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RfqMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly RfqDocument  $rfq,
        public readonly RfqRecipient $recipient,
        public readonly string       $introText = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('zerovia.rfq.submission_email'),
            subject: "[{$this->rfq->reference_nr}] Angebotsanfrage – ZEROvia Supplier Scouting",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.rfq',
            with: [
                'rfq'       => $this->rfq,
                'recipient' => $this->recipient,
                'supplier'  => $this->recipient->supplier,
                'introText' => $this->introText,
                'trackUrl'  => route('rfq.track', [
                    'rfq'       => $this->rfq->id,
                    'recipient' => $this->recipient->id,
                ]),
            ],
        );
    }
}
