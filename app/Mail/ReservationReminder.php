<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Propiedad pública para que Blade la reconozca automáticamente.
     */
    public $reservation;

    /**
     * El constructor recibe la reserva que se va a recordar.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Definimos el asunto del correo.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏰ Recordatorio de tu reserva: ' . $this->reservation->titulo,
        );
    }

    /**
     * Vinculamos con la vista que creamos anteriormente.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}