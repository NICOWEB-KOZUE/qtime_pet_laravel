<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoAheadNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Ticket $ticket)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $ticketNumber = $this->ticket->seq_no ?? $this->ticket->id;

        return new Envelope(
            subject: "【平泉どうぶつ病院】まもなく診察です（あと2名／受付No.{$ticketNumber}）",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $ticket = $this->ticket;
        $patient = $ticket->patient;

        return new Content(
            text: 'emails.two-ahead',
            with: [
                'patientName' => $patient?->name ?? 'お客様',
                'ticketNumber' => $ticket->seq_no ?? $ticket->id,
                'session' => $ticket->session === 'AM' ? '午前' : '午後',
                'visitDate' => $ticket->visit_date->format('Y年n月j日'),
                'statusUrl' => route('status', [], true),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
