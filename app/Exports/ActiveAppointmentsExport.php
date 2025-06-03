<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Support\Carbon;

class ActiveAppointmentsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths
{
    public function collection()
    {
        return Appointment::where('status', 'active')
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
            'Статус',
            'Оценка',
            'Дата создания',
            'Последнее изменение'
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
            ucfirst($appointment->status),
            $appointment->rating ? number_format($appointment->rating, 1) : 'Нет оценки',
            Carbon::parse($appointment->created_at)->format('d.m.Y H:i'),
            Carbon::parse($appointment->updated_at)->format('d.m.Y H:i'),
        ];
    }

    // Устанавливаем ширину колонок
    public function columnWidths(): array
    {
        return [
            'A' => 5,     // ID
            'B' => 30,    // Услуга
            'C' => 25,    // Клиент
            'D' => 25,    // Специалист
            'E' => 40,    // Филиал
            'F' => 16,    // Время
            'G' => 12,    // Статус
            'H' => 10,    // Оценка
            'I' => 18,    // Дата создания
            'J' => 18,    // Последнее изменение
        ];
    }
}