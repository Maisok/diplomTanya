<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Support\Carbon;

class CompletedAppointmentsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths
{
    public function collection()
    {
        return Appointment::where('status', 'completed')
            ->with(['service', 'user', 'staff', 'branch'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Услуга',
            'Клиент',
            'Специалист',
            'Филиал',
            'Время записи',
            'Оценка',
            'Статус',
            'Дата завершения'
        ];
    }

    public function map($appointment): array
    {
        return [
            $appointment->id,
            $appointment->service?->name ?? 'Неизвестная услуга',
            $appointment->user?->email ?? 'Пользователь удален',
            optional($appointment->staff)->name ?? 'Специалист удален',
            optional($appointment->branch)->address ?? 'Филиал удален',
            Carbon::parse($appointment->appointment_time)->format('d.m.Y H:i'),
            $appointment->rating ? number_format($appointment->rating, 1) : 'Нет оценки',
            ucfirst($appointment->status),
            Carbon::parse($appointment->updated_at)->format('d.m.Y H:i'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 25,
            'D' => 25,
            'E' => 40,
            'F' => 16,
            'G' => 10,
            'H' => 12,
            'I' => 18,
        ];
    }
}