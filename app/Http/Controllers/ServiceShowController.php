<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Appointment;

class ServiceShowController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        
        $services = Service::query()
            ->when($request->category, function($query, $category) {
                $query->where('category_id', $category);
            })
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->get();

        return view('services', compact('services', 'categories'));
    }

    public function show(Service $service)
    {
        $branches = Branch::whereHas('staff.services', function($query) use ($service) {
            $query->where('services.id', $service->id);
        })->get(['id', 'address']);
    
        // Получаем branch_id из запроса, если он есть
        $branchId = request()->input('branch_id');
    
        $appointments = Appointment::with(['staff', 'user'])
            ->where('service_id', $service->id)
            ->when($branchId, function($query) use ($branchId) {
                $query->whereHas('staff', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            })
            ->where('appointment_time', '>=', now())
            ->orderBy('appointment_time')
            ->get();
        
        return view('cart', [
            'service' => $service,
            'branches' => $branches,
            'appointments' => $appointments,
            'selectedBranchId' => $branchId
        ]);
    }
}