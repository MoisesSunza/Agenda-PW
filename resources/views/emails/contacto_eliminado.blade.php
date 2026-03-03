<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DM Mono', monospace; background-color: #0d0d0f; color: #e8e6e0; padding: 40px; margin: 0; }
        .container { background: #141418; border: 1px solid #c8a96e; border-radius: 12px; max-width: 600px; margin: 0 auto; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .header { background: #1a1a20; padding: 25px; text-align: center; border-bottom: 1px solid #c8a96e; }
        .header h1 { color: #c8a96e; margin: 0; font-family: 'Playfair Display', serif; font-size: 28px; letter-spacing: 3px; text-transform: uppercase; }
        .content { padding: 40px; line-height: 1.8; }
        .highlight { color: #c8a96e; font-weight: bold; border-left: 3px solid #c8a96e; padding-left: 15px; margin: 20px 0; background: rgba(200, 169, 110, 0.05); padding-top: 10px; padding-bottom: 10px; }
        .footer { background: #0d0d0f; padding: 25px; text-align: center; font-size: 12px; color: #6b6875; border-top: 1px solid #2a2a32; }
        .dev-team { margin-top: 12px; color: #c8a96e; font-style: italic; font-size: 13px; }
    </style>
</head>

<body>
    <div class="container">
        <div class="header"><h1>MiAgenda</h1></div>
        <div class="content">
            <p>Confirmación de baja de contacto:</p>
            <div class="highlight">
                <strong>Nombre:</strong> {{ $nombre }}<br>
                <strong>Acción:</strong> Eliminado permanentemente de la agenda.
            </div>
        </div>
        <div class="footer">
            Desarrollado por:
            <div class="dev-team">Moisés Abraham Sunza Vázquez & Fernando Adriano Sabido Quijano</div>
        </div>
    </div>
</body>
</html>