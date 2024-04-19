<?php

namespace App\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendToken extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    const tokenTypes = [
        'forgotPassword' => [
            'Subject' => 'Forgot password Token',
            'text' => 'Please enter token provided to reset your password',
            'tokenWarning' => 'Token expires in 15 minutes'
        ],
    ];

    /**
     * Create a new message instance.
     */
    public function __construct(public string $token, private string $tokenType)
    {
        if (!$token || !$tokenType) {
            throw new Exception("token or tokenType not provided");
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Send Token',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.sendtoken',
            with: [
                'token' => $this->token,
                'mailDeatils' => self::tokenTypes[$this->tokenType]
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
        return [];
    }
}
