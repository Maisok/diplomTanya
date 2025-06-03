<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;

class AppointmentsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithEvents
{
    protected $dateFrom;
    protected $dateTo;
    protected $branchId;

    public function __construct($dateFrom = null, $dateTo = null, $branchId = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->branchId = $branchId;
    }

    public function collection()
    {
        return Appointment::query()
            ->where('status', 'completed')
            ->whereBetween('appointment_time', [
                $this->dateFrom ? $this->dateFrom . ' 00:00' : now()->startOfMonth()->toDateTimeString(),
                $this->dateTo ? $this->dateTo . ' 23:59' : now()->endOfMonth()->toDateTimeString()
            ])
            ->when($this->branchId, fn($q) => $q->where('branch_id', $this->branchId))
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
            'Цена',
            'Оценка',
            'Дата завершения'
        ];
    }

    public function map($appointment): array
    {
        return [
            $appointment->id,
            $appointment->service->name ?? 'Неизвестная услуга',
            $appointment->user->email ?? 'Пользователь удален',
            $appointment->staff->surname . ' ' . $appointment->staff->name,
            $appointment->branch->address ?? 'Филиал удален',
            Carbon::parse($appointment->appointment_time)->format('d.m.Y H:i'),
            number_format(optional($appointment->service)->price ?? 0, 0, '', ' ') . ' ₽',
            $appointment->rating ? number_format($appointment->rating, 1) : 'Нет оценки',
            Carbon::parse($appointment->updated_at)->format('d.m.Y H:i'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,     // ID
            'B' => 30,    // Услуга
            'C' => 25,    // Клиент
            'D' => 25,    // Специалист
            'E' => 40,    // Филиал
            'F' => 16,    // Время
            'G' => 12,    // Цена
            'H' => 10,    // Оценка
            'I' => 18,    // Дата завершения
        ];
    }

    /**
     * Добавляем итоговую строку с общей суммой и количеством
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $appointments = $this->collection();

                // Подсчет статистики
                $totalAppointments = $appointments->count();
                $totalRevenue = $appointments->sum(fn($appt) => optional($appt->service)->price ?? 0);

                // Получаем топ сотрудников
                $topStaff = DB::table('appointments')
                    ->select('staff_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(services.price) as revenue'))
                    ->join('services', 'appointments.service_id', '=', 'services.id')
                    ->where('appointments.status', 'completed')
                    ->whereBetween('appointments.appointment_time', [
                        $this->dateFrom ? $this->dateFrom . ' 00:00' : now()->startOfMonth()->toDateString(),
                        $this->dateTo ? $this->dateTo . ' 23:59' : now()->endOfMonth()->toDateString()
                    ])
                    ->when($this->branchId, fn($q) => $q->where('appointments.branch_id', $this->branchId))
                    ->groupBy('staff_id')
                    ->orderByDesc('revenue')
                    ->limit(5)
                    ->get();

                $lastRow = $appointments->count() + 1;

                // Итоговая строка
                $sheet->setCellValue("A$lastRow", "Итого:");
                $sheet->setCellValue("G$lastRow", "$totalRevenue ₽");
                $sheet->setCellValue("H$lastRow", "$totalAppointments записей");

                // Жирный шрифт для итоговой строки
                $sheet->getStyle("A$lastRow:I$lastRow")
                    ->getFont()
                    ->setBold(true);

                // Добавляем топ специалистов
                if ($topStaff->isNotEmpty()) {
                    $sheet->setCellValue("A" . ($lastRow + 2), "Топ специалистов:");

                    $row = $lastRow + 3;
                    foreach ($topStaff as $staff) {
                        $staffName = optional(User::find($staff->staff_id))->name ?? 'Специалист удалён';
                        $sheet->setCellValue("A$row", $staffName);
                        $sheet->setCellValue("B$row", "$staff->count записей");
                        $sheet->setCellValue("C$row", "$staff->revenue ₽");
                        $row++;
                    }
                }
            },
        ];
    }
}