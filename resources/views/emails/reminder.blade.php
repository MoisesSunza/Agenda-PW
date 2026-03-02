<div style="font-family: sans-serif; max-width: 600px; margin: 0 auto; border: 2px solid #3490dc; border-radius: 12px; padding: 20px; text-align: center;">
    <h2 style="color: #1d4ed8;">¡Hola, no lo olvides! ⏰</h2>
    <p style="font-size: 16px; color: #334155;">Te escribimos para recordarte que tienes una cita programada para mañana.</p>
    
    <div style="background-color: #eff6ff; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: left;">
        <p><strong>Evento:</strong> {{ $reservation->titulo }}</p>
        <p><strong>Lugar:</strong> {{ $reservation->space->nombre }}</p>
        <p><strong>Hora:</strong> {{ $reservation->hora }}</p>
    </div>

    <p style="font-size: 14px; color: #64748b;">Si no puedes asistir, recuerda cancelarla desde la app para liberar el espacio.</p>
</div>