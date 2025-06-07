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
        ->when($request->sort, function ($query, $order) {
            if ($order === 'asc') {
                $query->orderBy('price', 'asc');
            } elseif ($order === 'desc') {
                $query->orderBy('price', 'desc');
            }
        });
        
    $services = $services->get();
    
        return view('services', compact('services', 'categories'));
    }
    
    public function show(Service $service)
    {
        // Получаем всех сотрудников по этой услуге
        $staffList = $service->staff;
    
        // Получаем филиалы этих сотрудников и фильтруем только active
        $branches = $staffList
            ->pluck('branch')               // Собираем все филиалы
            ->filter(fn($branch) => $branch && $branch->status === 'active')  // Только активные
            ->unique('id')                  // Уникальные филиалы
            ->values();                     // Сбрасываем ключи
    
        // Получаем записи на приём (будущие)
        $appointments = $service->appointments()
            ->where('appointment_time', '>=', now())
            ->with(['staff'])
            ->get();
    
        return view('cart', compact('service', 'branches', 'appointments'));
    }
}