<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Appointment;
use Illuminate\Support\Facades\Cache;

class ServiceShowController extends Controller
{
    public function index(Request $request)
    {
        // Получаем только те категории, у которых есть активные услуги
        $categories = Category::has('services', '>', 0)
            ->whereHas('services', function ($query) {
                $query->where('status', 'active');
            })
            ->paginate(6); // Показываем по 6 категорий на странице
    
        // Выбираем только активные услуги с фильтрацией по категории и поиску
        $services = Service::where('status', 'active')
            ->when($request->category, function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->get();
    
        return view('services', compact('services', 'categories'));
    }

    public function show(Service $service)
    {
        // Получаем только активные филиалы, где есть сотрудники с этой услугой
        $branches = Branch::where('status', 'active')
            ->whereHas('staff.services', function($query) use ($service) {
                $query->where('services.id', $service->id);
            })
            ->get(['id', 'address']);
    
        // Получаем branch_id из запроса
        $branchId = request()->input('branch_id');
    
        // Получаем записи только на будущее время и по выбранному филиалу (если указан)
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