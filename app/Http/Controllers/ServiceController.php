<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\User;
use App\Models\Category;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
   // ServiceController.php

    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home');
        }

        $services = Service::with(['staff', 'category'])->get();
        return view('admin.service', compact('services'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home');
        }
    
        // Получаем только сотрудников
        $staff = User::where('role', 'staff')->get();
        $categories = Category::all();
    
        return view('admin.createservice', compact('staff', 'categories'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home');
        }
    
        // Валидация
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'price' => 'required|numeric|between:0,99999.99',
            'duration' => 'required|integer|min:5|max:300',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'staff_id' => 'required|array',
            'staff_id.*' => 'exists:users,id', // Изменено на users.id
            'status' => 'required|in:active,inactive'
        ], [
            'name.required' => 'Название услуги обязательно для заполнения',
            'name.max' => 'Название услуги не должно превышать 100 символов',
            'description.required' => 'Описание услуги обязательно для заполнения',
            'description.max' => 'Описание услуги не должно превышать 500 символов',
            'price.required' => 'Цена услуги обязательна для заполнения',
            'price.between' => 'Цена должна быть между 0 и 99999.99',
            'duration.required' => 'Продолжительность услуги обязательна для заполнения',
            'duration.min' => 'Продолжительность должна быть не менее 5 минут',
            'duration.max' => 'Продолжительность должна быть не более 300 минут',
            'category_id.required' => 'Необходимо выбрать категорию',
            'category_id.exists' => 'Выбранная категория не существует',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы изображения: jpeg, png, jpg, gif',
            'image.max' => 'Максимальный размер изображения 2MB',
            'staff_id.required' => 'Необходимо выбрать хотя бы одного сотрудника',
            'staff_id.*.exists' => 'Выбранный сотрудник не существует'
        ]);
    
        try {
            DB::beginTransaction();
    
            // Создание услуги
            $serviceData = $validated;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('services', 'public');
                $serviceData['image'] = $imagePath;
            }
            $service = Service::create($serviceData);
    
            // Прикрепляем сотрудников
            $service->staff()->attach($request->staff_id);
    
            DB::commit();
    
            return redirect()->route('admin.services.index')
                ->with('success', 'Услуга успешно добавлена.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Произошла ошибка при создании услуги: ' . $e->getMessage());
        }
    }

    public function edit(Service $service)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home');
        }
    
        $staff = User::where('role', 'staff')->get();
        $categories = Category::all();
    
        // Получаем ID уже назначенных сотрудников
        $selectedStaff = $service->staff->pluck('id')->toArray();
    
        return view('admin.editservice', compact('service', 'staff', 'categories', 'selectedStaff'));
    }
    
    public function update(Request $request, Service $service)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home');
        }
    
        // Валидация
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'price' => 'required|numeric|between:0,99999.99',
            'duration' => 'required|integer|min:5|max:300',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'staff_id' => 'required|array',
            'staff_id.*' => 'exists:users,id', // Изменено на users
            'status' => 'required|in:active,inactive'
        ], [
            'name.required' => 'Название услуги обязательно для заполнения',
            'name.max' => 'Название услуги не должно превышать 100 символов',
            'description.required' => 'Описание услуги обязательно для заполнения',
            'description.max' => 'Описание услуги не должно превышать 500 символов',
            'price.required' => 'Цена обязательна для заполнения',
            'price.between' => 'Цена должна быть между 0 и 99999.99',
            'duration.required' => 'Продолжительность обязательна для заполнения',
            'duration.min' => 'Минимальная продолжительность — 5 минут',
            'duration.max' => 'Максимальная продолжительность — 300 минут',
            'category_id.required' => 'Выберите категорию',
            'category_id.exists' => 'Выбранная категория не существует',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif',
            'image.max' => 'Изображение не должно превышать 2MB',
            'staff_id.required' => 'Необходимо выбрать хотя бы одного сотрудника',
            'staff_id.*.exists' => 'Один или несколько выбранных сотрудников не существуют',
            'status.required' => 'Статус обязателен для заполнения',
        ]);
    
        try {
            DB::beginTransaction();
    
            // Обновляем данные услуги
            $service->fill($request->only(['name', 'description', 'price', 'duration', 'category_id', 'status']));
    
            // Обновляем изображение
            if ($request->hasFile('image')) {
                if ($service->image) {
                    Storage::disk('public')->delete($service->image);
                }
                $imagePath = $request->file('image')->store('services', 'public');
                $service->image = $imagePath;
            }
    
            $service->save();
    
            // Синхронизируем персонал
            $service->staff()->sync($request->staff_id);
    
            DB::commit();
    
            return redirect()->route('admin.services.index')
                ->with('success', 'Услуга успешно обновлена.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Произошла ошибка при обновлении услуги: ' . $e->getMessage());
        }
    }

    public function destroy(Service $service)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home');
        }

        // Проверяем, есть ли связанные записи
        if ($service->appointments()->exists()) {
            return redirect()->route('admin.services.index')
                ->with('error', 'Нельзя удалить услугу, так как у нее есть связанные записи.');
        }

        try {
            DB::beginTransaction();

            // Удаляем связанное изображение
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            
            $service->staff()->detach();
            $service->delete();

            DB::commit();

            return redirect()->route('admin.services.index')
                ->with('success', 'Услуга успешно удалена.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Произошла ошибка при удалении услуги: ' . $e->getMessage());
        }
    }
}