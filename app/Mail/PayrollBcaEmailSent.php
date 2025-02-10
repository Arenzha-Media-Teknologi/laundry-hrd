<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class PayrollBcaEmailSent extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $content;
    public $files;
    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($subject, $content, $files)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->files = $files;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        // return $this->content;
        return new Content(
            view: 'emails.payroll-bca-email-sent',
            // text: $this->content,
            with: [
                'content' => $this->content,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        $attachments = collect($this->files)->map(function ($file) {
            return Attachment::fromData(function () use ($file) {
                return $file['content'];
            }, $file['name'])->withMime($file['mime']);
        })->all();

        return $attachments;

        // return [];
    }
}
