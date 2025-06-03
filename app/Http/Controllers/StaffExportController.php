<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StaffClientsExport;
use Illuminate\Support\Carbon;

class StaffExportController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'staff') {
            return redirect()->route('home')->with('error', 'Доступ запрещён');
        }

        $staffId = auth()->id();

        // Получаем даты из запроса или ставим по умолчанию
        $dateFrom = $request->input('date_from') ?: now()->subMonth()->toDateString();
        $dateTo = $request->input('date_to') ?: now()->toDateString();
        $clientId = $request->input('client_id');

        // Запрашиваем только **завершённые** записи
        $query = Appointment::where('staff_id', $staffId)
            ->where('status', 'completed')
            ->whereBetween('appointment_time', ["$dateFrom 00:00", "$dateTo 23:59"])
            ->with(['user', 'service']);

        if ($clientId) {
            $query->where('user_id', $clientId);
        }

        $appointments = $query->get();

        // Для выпадающего списка клиентов
        $clients = User::whereHas('appointments', function ($q) use ($staffId) {
                $q->where('staff_id', $staffId)->where('status', 'completed');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'surname', 'email']);

        // Статистика
        $totalAppointments = $appointments->count();
        $totalRevenue = $appointments->sum(fn($appt) => optional($appt->service)->price ?? 0);

        return view('staff.exports.clients', compact(
            'appointments',
            'totalAppointments',
            'totalRevenue',
            'dateFrom',
            'dateTo',
            'clients',
            'clientId'
        ));
    }

    public function export(Request $request)
    {
        if (auth()->user()->role !== 'staff') {
            return redirect()->route('home')->with('error', 'Доступ запрещён');
        }

        $staffId = auth()->id();
        $dateFrom = $request->input('date_from') ?: now()->subMonth()->toDateString();
        $dateTo = $request->input('date_to') ?: now()->toDateString();
        $clientId = $request->input('client_id');

        return Excel::download(
            new StaffClientsExport($staffId, $dateFrom, $dateTo, $clientId),
            "clients_{$staffId}_from_{$dateFrom}_to_{$dateTo}" . ($clientId ? "_client_{$clientId}" : "") . ".xlsx"
        );
    }
}