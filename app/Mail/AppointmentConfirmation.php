<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Branch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $service;
    public $staff;
    public $branch;

    public function __construct(Appointment $appointment, Service $service, Staff $staff, Branch $branch)
    {
        $this->appointment = $appointment;
        $this->service = $service;
        $this->staff = $staff;
        $this->branch = $branch;
    }

    public function build()
    {
        return $this->subject('Подтверждение записи на услугу')
                    ->view('emails.appointment_confirmation');
    }
}