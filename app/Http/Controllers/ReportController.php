<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CompletedAppointmentsExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function completed(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещён');
        }

        // Получаем фильтры
        $branchId = $request->input('branch_id');
        $dateFrom = $request->input('date_from') ?: now()->startOfMonth()->toDateString();
        $dateTo = $request->input('date_to') ?: now()->endOfMonth()->toDateString();

        // Записи
        $query = Appointment::where('status', 'completed')
            ->whereBetween('appointment_time', ["$dateFrom 00:00", "$dateTo 23:59"])
            ->with(['service', 'user', 'staff', 'branch']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $appointments = $query->get();

        // Общая выручка
        $totalRevenue = $appointments->sum(fn($appt) => optional($appt->service)->price ?? 0);

        // Общее количество
        $totalAppointments = $appointments->count();

        // Топ специалистов
        $topStaff = DB::table('appointments')
            ->join('users', 'appointments.staff_id', '=', 'users.id')
            ->where('appointments.status', 'completed')
            ->whereBetween('appointments.appointment_time', ["$dateFrom 00:00", "$dateTo 23:59"])
            ->when($branchId, fn($q) => $q->where('appointments.branch_id', $branchId))
            ->selectRaw('users.id, users.name, users.surname, COUNT(*) AS count')
            ->groupBy('users.id', 'users.name', 'users.surname')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Для выпадающего списка филиалов
        $branches = Branch::all(['id', 'address']);

        return view('admin.reports.completed', compact(
            'appointments',
            'totalRevenue',
            'totalAppointments',
            'topStaff',
            'branches'
        ));
    }
}