<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompletedAppointmentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Appointment::where('status', 'completed')->with('service', 'user')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Service ID',
            'User ID',
            'Service Name',
            'User Name',
            'Appointment Time',
            'Status',
            'Created At',
            'Updated At',
        ];
    }

    public function map($appointment): array
    {
        return [
            $appointment->id,
            $appointment->service_id,
            $appointment->user_id,
            $appointment->service->name,
            $appointment->user->name,
            $appointment->appointment_time,
            $appointment->status,
            $appointment->created_at,
            $appointment->updated_at,
        ];
    }
}