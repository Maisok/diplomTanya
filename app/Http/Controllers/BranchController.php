<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\BranchSchedule;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with('schedule')->get();
        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    
    public function store(Request $request)
    {
        // Валидация общих полей
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);
    
        // Проверка графика работы
        $days = [
            'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
        ];
    
        $scheduleData = [];
    
        $daysMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 0,
        ];
        
        foreach ($days as $day) {
            $open = $request->input($day . '_open');
            $close = $request->input($day . '_close');
        
            if (($open && !$close) || (!$open && $close)) {
                throw ValidationException::withMessages([
                    "$day._open" => "Для $day необходимо указать оба времени или оставить поля пустыми.",
                ]);
            }
        
            if ($open && $close) {
                $openTime = Carbon::createFromFormat('H:i', $open);
                $closeTime = Carbon::createFromFormat('H:i', $close);
        
                if ($openTime->lt(\Carbon\Carbon::createFromTime(7, 0))) {
                    throw ValidationException::withMessages([
                        "$day._open" => "Время открытия в $day не может быть раньше 07:00",
                    ]);
                }
        
                if ($closeTime->gt(\Carbon\Carbon::createFromTime(21, 0))) {
                    throw ValidationException::withMessages([
                        "$day._close" => "Время закрытия в $day не может быть позже 21:00",
                    ]);
                }
        
                if ($openTime->gte($closeTime)) {
                    throw ValidationException::withMessages([
                        "$day._open" => "Время открытия должно быть раньше времени закрытия в $day",
                    ]);
                }
        
                $diffMinutes = $closeTime->diffInMinutes($openTime, true); // Получаем разницу в минутах
                if ($diffMinutes < 120) {
                    throw ValidationException::withMessages([
                        "$day._open" => "Филиал должен работать не менее 2 часов в " . trans("days.$day"),
                    ]);
                }
        
                $scheduleData[] = [
                    'day_of_week' => $daysMap[$day], // ✅ Правильное значение
                    'open_time' => $open,
                    'close_time' => $close,
                ];
            }
        }
    
        // Сохраняем филиал
        $branch = Branch::create([
            'address' => $validated['address'],
            'status' => $validated['status'],
            'image' => $request->file('image')->store('branch_images', 'public'),
        ]);
    
        // Сохраняем график
        foreach ($scheduleData as $record) {
            $branch->schedule()->create($record);
        }
    
        return redirect()->route('admin.branches.index')
            ->with('success', 'Филиал успешно добавлен');
    }

    public function show(Branch $branch)
    {
        return view('admin.branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        // Загружаем график работы
        $branch->load('schedule');
    
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {

        $branch = Branch::findOrFail($id);
    
        // Валидация основных данных
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);
    
        // Обновление изображения
        if ($request->hasFile('image')) {
            if ($branch->image) {
                Storage::disk('public')->delete($branch->image);
            }
            $validated['image'] = $request->file('image')->store('branch_images', 'public');
        } else {
            unset($validated['image']);
        }
    
        // Обновление данных филиала
        $branch->update($validated);
    
        // Удаляем старый график
        $branch->schedule()->delete();
    
        // Проверка и сохранение нового графика
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    
        foreach ($days as $day) {
            $open = $request->input($day . '_open');
            $close = $request->input($day . '_close');
        
            if (($open && !$close) || (!$open && $close)) {
                throw ValidationException::withMessages([
                    "$day._open" => "Для " . trans("days.$day") . " необходимо указать оба времени или оставить оба поля пустыми.",
                ]);
            }
        
            if ($open && $close) {
                try {
                    $openTime = \Carbon\Carbon::createFromFormat('H:i', $open);
                    $closeTime = \Carbon\Carbon::createFromFormat('H:i', $close);
                } catch (\Exception $e) {
                    throw ValidationException::withMessages([
                        "$day._open" => "Неверный формат времени для " . trans("days.$day"),
                    ]);
                }
        
                if ($openTime->lt(\Carbon\Carbon::createFromTime(7, 0))) {
                    throw ValidationException::withMessages([
                        "$day._open" => "Время открытия в " . trans("days.$day") . " не может быть раньше 07:00",
                    ]);
                }
        
                if ($closeTime->gt(\Carbon\Carbon::createFromTime(21, 0))) {
                    throw ValidationException::withMessages([
                        "$day._close" => "Время закрытия в " . trans("days.$day") . " не может быть позже 21:00",
                    ]);
                }
        
                if ($openTime >= $closeTime) {
                    throw ValidationException::withMessages([
                        "$day._open" => "Время открытия должно быть раньше времени закрытия в " . trans("days.$day"),
                    ]);
                }
            
                $diffMinutes = $closeTime->diffInMinutes($openTime, true); // Всегда положительное число

                if ($diffMinutes < 120) {
                    throw ValidationException::withMessages([
                        "$day._open" => "Филиал должен работать не менее 2 часов в " . trans("days.$day"),
                    ]);
                }
        
                // Определяем day_of_week
                $dayOfWeekMap = [
                    'monday' => 1,
                    'tuesday' => 2,
                    'wednesday' => 3,
                    'thursday' => 4,
                    'friday' => 5,
                    'saturday' => 6,
                    'sunday' => 0,
                ];
        
                $dayOfWeek = $dayOfWeekMap[$day] ?? 0;
        
                // Сохраняем график
                $branch->schedule()->create([
                    'day_of_week' => $dayOfWeek,
                    'open_time' => $open,
                    'close_time' => $close,
                ]);
            }
        }
    
        return redirect()->route('admin.branches.index')
            ->with('success', 'Филиал обновлён');
    }

    public function destroy(Branch $branch)
    {
        // Проверяем, есть ли у филиала персонал
        if ($branch->users()->exists()) {
            return redirect()->route('admin.branches.index')
                ->with('error', 'Невозможно удалить филиал, так как в нем зарегистрирован персонал.');
        }
    
        Storage::disk('public')->delete($branch->image);
        $branch->delete();
    
        return redirect()->route('admin.branches.index')
            ->with('success', 'Филиал успешно удален');
    }



}