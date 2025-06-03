<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\User as Staff; // Так как Staff — это User с ролью staff
use App\Models\Service;
use Illuminate\Http\Request;

class AdminAppointmentController extends Controller
{
    /**
     * Отображение всех записей с фильтрами
     */
    public function index(Request $request)
    {
       
        // Загружаем все записи с возможностью фильтрации
        $appointments = Appointment::query()
            ->with([
                'user', 
                'service',
                'staff'  
            ])
            ->when($request->input('staff_id'), function ($query, $staff_id) {
                $query->where('staff_id', $staff_id);
            })
            ->when($request->input('service_id'), function ($query, $service_id) {
                $query->where('service_id', $service_id);
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderByDesc('appointment_time')
            ->paginate(15);

        // Для выпадающих списков
        $staff = Staff::where('role', 'staff')->get(['id', 'name', 'surname']);
        $services = Service::get(['id', 'name']);

        return view('admin.appointments.index', compact('appointments', 'staff', 'services'));
    }

    /**
     * Активация записи
     */
    public function activate($id)
    {
       
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'active']);

        return back()->with('success', 'Запись активирована');
    }

    /**
     * Завершение записи
     */
    public function complete($id)
    {
       

        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'completed']);

        return back()->with('success', 'Запись завершена');
    }

    /**
     * Отмена записи
     */
    public function cancel($id)
    {
       
        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'status' => 'cancelled',
            'rating' => null,
        ]);

        return back()->with('success', 'Запись отменена, оценка сброшена');
    }
}