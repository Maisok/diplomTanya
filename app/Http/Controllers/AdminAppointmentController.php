<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Staff;
use App\Models\Service;
use Illuminate\Http\Request;

class AdminAppointmentController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }

        $appointments = Appointment::with(['user', 'service', 'staff'])
            ->when($request->staff_id, function($query, $staff_id) {
                return $query->where('staff_id', $staff_id);
            })
            ->when($request->service_id, function($query, $service_id) {
                return $query->where('service_id', $service_id);
            })
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('appointment_time', 'desc')
            ->paginate(15);
            
        $staff = Staff::all();
        $services = Service::all();
        
        return view('admin.appointments.index', compact('appointments', 'staff', 'services'));
    }
    
    public function activate($id)
    {

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        } 

        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'active';
        $appointment->save();
        
        return back()->with('success', 'Запись активирована');
    }
    
    public function complete($id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'completed';
        $appointment->save();
        
        return back()->with('success', 'Запись завершена');
    }
    
    public function cancel($id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }
        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'status' => 'cancelled',
            'rating' => null // Сбрасываем оценку при отмене
        ]);
        
        return back()->with('success', 'Запись отменена, оценка сброшена');
    }
}