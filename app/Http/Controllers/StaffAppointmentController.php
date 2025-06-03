<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User as Staff; // <-- сотрудники это User с ролью staff
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffAppointmentController extends Controller
{
    /**
     * Просмотр записей для сотрудника
     */
    public function index(Request $request)
    {
        // Проверяем, что пользователь — это специалист
        if (Auth::user()->role !== 'staff') {
            return redirect()->route('home')->with('error', 'Доступ запрещён');
        }

        // Получаем ID текущего мастера
        $staffId = Auth::id();

        // Запрашиваем только его записи
        $appointments = Appointment::with(['user', 'service'])
            ->where('staff_id', $staffId)
            ->when($request->input('service_id'), function ($query, $service_id) {
                return $query->where('service_id', $service_id);
            })
            ->when($request->input('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderByDesc('appointment_time')
            ->paginate(15);

        // Для фильтрации услуг в шаблоне
        $services = Service::whereHas('staff', function ($query) use ($staffId) {
            $query->where('users.id', $staffId);
        })->get(['id', 'name']);

        // Текущий мастер не нужен в списке, поэтому передаём пустой массив
        $staff = [];

        return view('admin.appointments.index', compact('appointments', 'staff', 'services'));
    }
}