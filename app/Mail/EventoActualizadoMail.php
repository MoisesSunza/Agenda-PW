<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventoActualizadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $evento) {}

    public function envelope(): Envelope {
        return new Envelope(subject: '✏️ Cambio en tu evento: ' . $this->evento->titulo);
    }

    public function content(): Content {
        return new Content(view: 'emails.evento_actualizado');
    }
}