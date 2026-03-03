<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactoActualizadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $contacto) {}

    public function envelope(): Envelope {
        return new Envelope(subject: '📝 Datos actualizados: ' . $this->contacto->nombre);
    }

    public function content(): Content {
        return new Content(view: 'emails.contacto_actualizado');
    }
}