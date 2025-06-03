<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use App\Models\Appointment;
use Illuminate\Support\Carbon;

class StaffClientsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths
{
    protected $staffId;
    protected $dateFrom;
    protected $dateTo;
    protected $clientId;

    public function __construct(int $staffId, string $dateFrom = null, string $dateTo = null, int $clientId = null)
    {
        $this->staffId = $staffId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->clientId = $clientId;
    }

    public function collection()
    {
        $query = Appointment::where('staff_id', $this->staffId)
            ->whereBetween('appointment_time', ["$this->dateFrom 00:00", "$this->dateTo 23:59"])
            ->with(['user', 'service']);

        if ($this->clientId) {
            $query->where('user_id', $this->clientId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Клиент',
            'Email',
            'Телефон',
            'Услуга',
            'Время',
            'Цена',
            'Статус',
            'Оценка'
        ];
    }

    public function map($row): array
    {
        return [
            optional($row->user)->name ?? 'Клиент удален',
            optional($row->user)->email ?? '—',
            optional($row->user)->phone ?? '—',
            optional($row->service)->name ?? 'Неизвестная услуга',
            Carbon::parse($row->appointment_time)->format('d.m.Y H:i'),
            number_format(optional($row->service)->price ?? 0, 0, '', ' ') . ' ₽',
            ucfirst($row->status),
            $row->rating ? number_format($row->rating, 1) : 'Нет оценки',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 18,
            'D' => 30,
            'E' => 16,
            'F' => 12,
            'G' => 12,
            'H' => 10,
        ];
    }
}