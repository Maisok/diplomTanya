<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Command;

class UpdateAppointmentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update appointment statuses based on appointment time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = now()->setTimezone('Asia/Irkutsk');
        $this->info('Current time (Asia/Irkutsk): ' . $now);
    
        $this->info('Checking appointments...');
    
        $updated = Appointment::where('status', 'active')
            ->where('appointment_time', '<=', $now)
            ->update(['status' => 'completed']);
    
        $this->info('Updated ' . $updated . ' appointments.');
    
        return 0;
    }
}