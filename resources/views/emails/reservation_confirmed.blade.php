<div style="font-family: sans-serif; border: 1px solid #eee; padding: 20px; border-radius: 10px;">
    <h2 style="color: #2d3748;">¡Confirmación de Reserva!</h2>
    <p>Hola, te confirmamos que tu evento <strong>{{ $reservation->titulo }}</strong> ha sido registrado con éxito.</p>
    <p><strong>Espacio:</strong> {{ $reservation->space->nombre }}</p>
    <p><strong>Fecha y Hora:</strong> {{ $reservation->fecha }} a las {{ $reservation->hora }}</p>
    <p style="color: #718096; font-size: 0.9em;">Gracias por usar la Agenda Electrónica.</p>
</div>