<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactoEliminadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $nombre) {}

    public function envelope(): Envelope {
        return new Envelope(subject: '🗑️ Contacto eliminado de tu agenda');
    }

    public function content(): Content {
        return new Content(view: 'emails.contacto_eliminado');
    }
}