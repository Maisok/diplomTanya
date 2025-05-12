<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Command;

class AppointmentStatusUpdate extends Command
{
    protected $signature = 'appointments:update-status';
    protected $description = 'Update appointment statuses to completed if the appointment time has passed';

    public function handle()
    {
        $appointments = Appointment::where('status', 'active')
            ->where('appointment_time', '<=', now())
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->markAsCompleted();
        }

        $this->info('Appointment statuses updated successfully.');
    }
}