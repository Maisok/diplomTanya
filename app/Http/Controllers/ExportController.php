<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Exports\ActiveAppointmentsExport;
use App\Exports\CompletedAppointmentsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportActiveAppointments()
    {

        if(Auth::user()->role === 'admin') {
            return Excel::download(new ActiveAppointmentsExport, 'active_appointments.xlsx');
        }
        else{
            return redirect()->route('home');
        }


       
    }

    public function exportCompletedAppointments()
    {

        if(Auth::user()->role === 'admin') {
            return Excel::download(new CompletedAppointmentsExport, 'completed_appointments.xlsx');

        }
        else{
            return redirect()->route('home');
        }


       }
}