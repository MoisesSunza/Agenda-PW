<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Reservation;
use App\Mail\ReservationConfirmed; 
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendReservationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reservation-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Buscamos reservas para el día de mañana que estén activas
        $tomorrow = Carbon::tomorrow()->toDateString();
        $reservations = Reservation::where('fecha', $tomorrow)
                                    ->where('status', 'activa')
                                    ->get();

        foreach ($reservations as $reservation) {
            Mail::to($reservation->user->email)->send(new App\Mail\ReservationReminder($reservation));
        }

        $this->info('Recordatorios enviados con éxito.');
    }
}
