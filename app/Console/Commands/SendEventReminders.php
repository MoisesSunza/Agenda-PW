<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Mail\EventoProximoMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    // Nombre con el que llamarás al comando
    protected $signature = 'app:send-event-reminders';

    // Descripción para la lista de artisan
    protected $description = 'Envía correos 10 min antes de un evento - Dev: Moisés & Fernando';

    public function handle()
    {
        // 1. Obtenemos la hora exacta que será dentro de 10 minutos (HH:mm)
        // Usamos la zona horaria de Campeche configurada en tu app.php
        $ahora = \Carbon\Carbon::now('America/Merida');
        $diezMinutosDespues = $ahora->copy()->addMinutes(10)->format('H:i');
        $fechaHoy = $ahora->toDateString();

        $this->info("Buscando eventos que inicien exactamente a las: $diezMinutosDespues");

        // 2. Buscamos eventos que coincidan exactamente con esa hora
        // El 'like' con % al final ignora los segundos de la base de datos
        $eventos = \App\Models\Event::where('fecha_inicio', $fechaHoy)
                        ->where('hora', 'like', $diezMinutosDespues . '%')
                        ->with('user')
                        ->get();

        if ($eventos->isEmpty()) {
            return; // Si no hay nada en este minuto exacto, terminamos silenciosamente
        }

        foreach ($eventos as $evento) {
            if ($evento->user) {
                try {
                    \Illuminate\Support\Facades\Mail::to($evento->user->email)
                        ->send(new \App\Mail\EventoProximoMail($evento));
                    
                    $this->info("Recordatorio enviado con éxito a: {$evento->user->email}");
                } catch (\Exception $e) {
                    \Log::error("Error de envío en el minuto $diezMinutosDespues: " . $e->getMessage());
                }
            }
        }
    }
}