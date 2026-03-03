<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventoProximoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $evento) {}

    public function envelope(): Envelope {
        return new Envelope(subject: '🔔 ¡Atención! Tu evento inicia en 10 minutos');
    }

    public function content(): Content {
        return new Content(view: 'emails.evento_proximo');
    }
}