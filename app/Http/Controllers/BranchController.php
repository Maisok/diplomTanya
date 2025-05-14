<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }
        $validated = $this->validateSchedule($request);
        
        $imagePath = $request->file('image')->store('branches', 'public');
        $validated['image'] = $imagePath;
    
        Branch::create($validated);
    
        return redirect()->route('admin.branches.index')
            ->with('success', 'Филиал успешно добавлен');
    }

    public function show(Branch $branch)
    {
        return view('admin.branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $this->validateSchedule($request, false, $branch);
        
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($branch->image);
            $imagePath = $request->file('image')->store('branches', 'public');
            $validated['image'] = $imagePath;
        }
    
        $branch->update($validated);
    
        return redirect()->route('admin.branches.index')
            ->with('success', 'Филиал успешно обновлен');
    }

    public function destroy(Branch $branch)
    {
        // Проверяем, есть ли у филиала персонал
        if ($branch->staff()->exists()) {
            return redirect()->route('admin.branches.index')
                ->with('error', 'Невозможно удалить филиал, так как в нем зарегистрирован персонал.');
        }
    
        Storage::disk('public')->delete($branch->image);
        $branch->delete();
    
        return redirect()->route('admin.branches.index')
            ->with('success', 'Филиал успешно удален');
    }

        private function validateSchedule(Request $request, $requireImage = true, Branch $branch = null)
        {
        $rules = [
            'address' => [
                'required',
                'string',
                'max:255',
                'regex:/^г\.[а-яА-ЯёЁ\s-]+, ул\.[а-яА-ЯёЁ\s-]+, д\.\d+[а-яА-Я]?$/u',
                function ($attribute, $value, $fail) use ($branch) {
                    $query = Branch::where('address', $value);
                    if ($branch) {
                        $query->where('id', '!=', $branch->id);
                    }
                    if ($query->exists()) {
                        $fail('Филиал с таким адресом уже существует');
                    }
                }
            ],
    
            // Понедельник
            'monday_open' => 'nullable|date_format:H:i|after_or_equal:08:00|before_or_equal:21:00',
            'monday_close' => [
                'nullable',
                'date_format:H:i',
                'required_with:monday_open',
                'after:monday_open',
                'before_or_equal:21:00',
                'min_work_hours'
            ],
            
            // Вторник
            'tuesday_open' => 'nullable|date_format:H:i|after_or_equal:08:00|before_or_equal:21:00',
            'tuesday_close' => [
                'nullable',
                'date_format:H:i',
                'required_with:tuesday_open',
                'after:tuesday_open',
                'before_or_equal:21:00',
                'min_work_hours'
            ],
            
            // Среда
            'wednesday_open' => 'nullable|date_format:H:i|after_or_equal:08:00|before_or_equal:21:00',
            'wednesday_close' => [
                'nullable',
                'date_format:H:i',
                'required_with:wednesday_open',
                'after:wednesday_open',
                'before_or_equal:21:00',
                'min_work_hours'
            ],
            
            // Четверг
            'thursday_open' => 'nullable|date_format:H:i|after_or_equal:08:00|before_or_equal:21:00',
            'thursday_close' => [
                'nullable',
                'date_format:H:i',
                'required_with:thursday_open',
                'after:thursday_open',
                'before_or_equal:21:00',
                'min_work_hours'
            ],
            
            // Пятница
            'friday_open' => 'nullable|date_format:H:i|after_or_equal:08:00|before_or_equal:21:00',
            'friday_close' => [
                'nullable',
                'date_format:H:i',
                'required_with:friday_open',
                'after:friday_open',
                'before_or_equal:21:00',
                'min_work_hours'
            ],
            
            // Суббота
            'saturday_open' => 'nullable|date_format:H:i|after_or_equal:08:00|before_or_equal:21:00',
            'saturday_close' => [
                'nullable',
                'date_format:H:i',
                'required_with:saturday_open',
                'after:saturday_open',
                'before_or_equal:21:00',
                'min_work_hours'
            ],
            
            // Воскресенье
            'sunday_open' => 'nullable|date_format:H:i|after_or_equal:08:00|before_or_equal:21:00',
            'sunday_close' => [
                'nullable',
                'date_format:H:i',
                'required_with:sunday_open',
                'after:sunday_open',
                'before_or_equal:21:00',
                'min_work_hours'
            ],
        ];

        if ($requireImage) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        }

        $messages = [
            'monday_close.after' => 'Время закрытия в понедельник должно быть позже времени открытия.',
            'tuesday_close.after' => 'Время закрытия во вторник должно быть позже времени открытия.',
            'tuesday_close.after' => 'Время закрытия в среду должно быть позже времени открытия.',
            'tuesday_close.after' => 'Время закрытия в четверг должно быть позже времени открытия.',
            'tuesday_close.after' => 'Время закрытия во пятницу должно быть позже времени открытия.',
            'tuesday_close.after' => 'Время закрытия во субботу должно быть позже времени открытия.',
            'tuesday_close.after' => 'Время закрытия во воскресение должно быть позже времени открытия.',
            'address.regex' => 'Адрес должен быть в формате: "г.Город, ул.Улица, д.Номер"',
        ];

        return $request->validate($rules);
    }

    protected function validateWorkingHours($request, $day, $closeTime, $fail)
    {
        $openTime = $request->input("{$day}_open");
        
        if ($openTime && $closeTime) {
            try {
                $start = Carbon::createFromFormat('H:i', $openTime);
                $end = Carbon::createFromFormat('H:i', $closeTime);
                
                // Проверяем что время закрытия действительно после открытия
                if ($end <= $start) {
                    return; // Эта проверка уже есть в rules
                }
                
                // Проверяем минимальное время работы (2 часа = 120 минут)
                if ($end->diffInMinutes($start) < 120) {
                    $dayNames = [
                        'monday' => 'понедельник',
                        'tuesday' => 'вторник',
                        'wednesday' => 'среду',
                        'thursday' => 'четверг',
                        'friday' => 'пятницу',
                        'saturday' => 'субботу',
                        'sunday' => 'воскресенье'
                    ];
                    $fail("Минимальное время работы - 2 часа (".$openTime." - ".$closeTime.")");
                }
            } catch (\Exception $e) {
                $fail("Ошибка при обработке времени работы");
            }
        }
    }
}