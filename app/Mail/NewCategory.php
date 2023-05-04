<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCategory extends Mailable
{
    use Queueable, SerializesModels;

    private mixed $categoryName;
    private mixed $categoryVisibility;


    /**
     * Create a new message instance.
     *
     * @param $categoryName
     * @return void
     */
    public function __construct(protected User $user, $categoryName, $categoryVisibility)
    {
        $this->categoryName = $categoryName;
        $this->categoryVisibility = $categoryVisibility;
    }

    /**
     * Get the message envelope.
     *
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Demande d\'ajout d\'une categorie',
        );
    }

    /**
     * Get the message content definition.
     *
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.new_category',
            with: [
                "user" => $this->user,
                "categoryName" => $this->categoryName,
                "categoryVisibility" => $this->categoryVisibility
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
        return [];
    }
}
