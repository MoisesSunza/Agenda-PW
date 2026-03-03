<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactoRegistradoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $contacto) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📇 Nuevo contacto guardado: ' . $this->contacto->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contacto_nuevo', // Nombre de la vista Blade
        );
    }
}