<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Exports\ActiveAppointmentsExport;
use App\Exports\CompletedAppointmentsExport;
use App\Exports\AppointmentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

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


    public function exportCompleted(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'branch_id' => 'nullable|exists:branches,id'
        ]);

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $branchId = $request->input('branch_id');

        return Excel::download(
            new AppointmentsExport($dateFrom, $dateTo, $branchId),
            "completed_appointments_{$dateFrom}_{$dateTo}_" . ($branchId ? 'branch_'.$branchId : 'all_branches') . ".xlsx"
        );
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