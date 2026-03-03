<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventoEliminadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $titulo) {}

    public function envelope(): Envelope {
        return new Envelope(subject: '🗑️ Evento cancelado');
    }

    public function content(): Content {
        return new Content(view: 'emails.evento_eliminado');
    }
}