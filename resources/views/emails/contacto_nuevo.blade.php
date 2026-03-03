<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'DM Mono', monospace; background-color: #0d0d0f; color: #e8e6e0; padding: 20px; }
        .container { background: #141418; border: 1px solid #2a2a32; padding: 30px; border-radius: 12px; max-width: 500px; margin: auto; }
        h1 { color: #c8a96e; font-family: 'Playfair Display', serif; border-bottom: 1px solid #c8a96e; padding-bottom: 10px; }
        .info { margin: 20px 0; font-size: 0.9rem; line-height: 1.6; }
        .label { color: #6b6875; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; }
        .footer { font-size: 0.7rem; color: #6b6875; margin-top: 30px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>MiAgenda</h1>
        <p>Has registrado un nuevo contacto exitosamente:</p>
        
        <div class="info">
            <span class="label">Nombre:</span><br>
            <strong>{{ $contacto->nombre }}</strong><br><br>
            
            <span class="label">Correo:</span><br>
            {{ $contacto->correo }}<br><br>
            
            <span class="label">Teléfono:</span><br>
            {{ $contacto->telefono ?? 'No proporcionado' }}
        </div>

        <div class="footer">
            Generado automáticamente por MiAgenda — Software Engineering UAC.
        </div>
    </div>
</body>
</html>