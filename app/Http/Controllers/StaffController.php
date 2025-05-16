<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    // Нормализуем номер телефона к формату 79248235181 (11 цифр, начинается с 7)
    protected function normalizePhone($phone)
    {
        // Удаляем все нецифровые символы
        $digits = preg_replace('/[^0-9]/', '', $phone);
        
        // Приводим к формату 7XXXXXXXXXX
        if (strlen($digits) === 11) {
            if ($digits[0] === '8') {
                $digits = '7' . substr($digits, 1);
            }
        } elseif (strlen($digits) === 10) {
            $digits = '7' . $digits;
        }
        
        return $digits;
    }

    // Форматируем номер для отображения (8 924 823 51 81)
    protected function formatPhone($phone)
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($digits) === 11) {
            return '8 ' . substr($digits, 1, 3) . ' ' . substr($digits, 4, 3) . ' ' . 
                   substr($digits, 7, 2) . ' ' . substr($digits, 9, 2);
        }
        return $phone;
    }

    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $staff = Staff::with('branch')->get();
            return view('admin.staff', compact('staff'));
        }
        return redirect()->route('home');
    }

    public function create()
    {
        if (Auth::user()->role === 'admin') {
            $branches = Branch::all();
            return view('admin.createstaff', compact('branches'));
        }
        return redirect()->route('home')->with('error', 'Доступ запрещен');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:50|regex:/^[a-zA-Zа-яА-Я\s]+$/u',
            'last_name' => 'required|string|max:50|regex:/^[a-zA-Zа-яА-Я\s]+$/u',
            'middle_name' => 'nullable|string|max:50|regex:/^[a-zA-Zа-яА-Я\s]*$/u',
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $normalized = $this->normalizePhone($value);
                    
                    if (strlen($normalized) !== 11) {
                        $fail('Номер телефона должен содержать 11 цифр');
                        return;
                    }
                    
                    // Проверяем в таблице staff
                    $existsInStaff = Staff::where(DB::raw("REGEXP_REPLACE(phone, '[^0-9]', '')"), 
                        preg_replace('/[^0-9]/', '', $normalized))
                        ->exists();
                    
                    // Проверяем в таблице users
                    $existsInUsers = User::where(DB::raw("REGEXP_REPLACE(phone, '[^0-9]', '')"), 
                        preg_replace('/[^0-9]/', '', $normalized))
                        ->exists();
                    
                    if ($existsInStaff || $existsInUsers) {
                        $fail('Этот телефон уже используется');
                    }
                }
            ],
            'password' => 'required|string|min:8',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:active,inactive'
        ], [
            'first_name.regex' => 'Имя должно содержать только буквы и пробелы',
            'last_name.regex' => 'Фамилия должна содержать только буквы и пробелы',
            'middle_name.regex' => 'Отчество должно содержать только буквы и пробелы',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif',
            'image.max' => 'Максимальный размер изображения 2MB',
            'branch_id.exists' => 'Выбранный филиал не существует',
            'phone.*' => 'Некорректный формат телефона или номер уже используется',
        ]);

        // Нормализуем телефон перед сохранением
        $normalizedPhone = $this->normalizePhone($validated['phone']);
        $formattedPhone = $this->formatPhone($normalizedPhone);

        $staff = Staff::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'],
            'phone' => $formattedPhone,
            'password' => Hash::make($validated['password']),
            'branch_id' => $validated['branch_id'], 
            'status' => $validated['status']
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('staff', 'public');
            $staff->image = $imagePath;
            $staff->save();
        }

        return redirect()->route('admin.staff.index')
            ->with('success', 'Сотрудник успешно добавлен.');
    }

    public function edit(Staff $staff)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }

        $branches = Branch::all();
        return view('admin.editstaff', compact('staff', 'branches'));
    }

    public function update(Request $request, Staff $staff)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }
    
        $validated = $request->validate([
            'first_name' => 'required|string|max:50|regex:/^[a-zA-Zа-яА-Я\s]+$/u',
            'last_name' => 'required|string|max:50|regex:/^[a-zA-Zа-яА-Я\s]+$/u',
            'middle_name' => 'nullable|string|max:50|regex:/^[a-zA-Zа-яА-Я\s]*$/u',
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($staff) {
                    $normalized = $this->normalizePhone($value);
                    $currentNormalized = $this->normalizePhone($staff->phone);
                    
                    if (strlen($normalized) !== 11) {
                        $fail('Номер телефона должен содержать 11 цифр');
                        return;
                    }
                    
                    if ($normalized !== $currentNormalized) {
                        if (Staff::where('id', '!=', $staff->id)
                            ->whereRaw("REGEXP_REPLACE(phone, '[^0-9]', '') = ?", 
                            [preg_replace('/[^0-9]/', '', $normalized)])
                            ->exists()) {
                            $fail('Этот телефон уже используется другим сотрудником');
                        }
                        
                        if (User::whereRaw("REGEXP_REPLACE(phone, '[^0-9]', '') = ?", 
                            [preg_replace('/[^0-9]/', '', $normalized)])
                            ->exists()) {
                            $fail('Этот телефон уже используется пользователем');
                        }
                    }
                }
            ],
            'password' => 'nullable|string|min:8',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'branch_id' => 'nullable|exists:branches,id',
            'remove_image' => 'nullable|boolean',
            'status' => 'required|in:active,inactive'
        ], [
            'first_name.regex' => 'Имя должно содержать только буквы и пробелы',
            'last_name.regex' => 'Фамилия должна содержать только буквы и пробелы',
            'middle_name.regex' => 'Отчество должно содержать только буквы и пробелы',
            'phone.*' => 'Некорректный формат телефона или номер уже используется',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif',
            'image.max' => 'Максимальный размер изображения 2MB',
            'branch_id.exists' => 'Выбранный филиал не существует',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
        ]);

        $normalizedPhone = $this->normalizePhone($validated['phone']);
    $formattedPhone = $this->formatPhone($normalizedPhone);

    // Подготовка данных для обновления
    $updateData = [
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'middle_name' => $validated['middle_name'],
        'phone' => $formattedPhone,
        'branch_id' => $validated['branch_id'], 
        'status' => $validated['status'],
    ];

    // Обновляем пароль, если он был указан
    if (!empty($validated['password'])) {
        $updateData['password'] = Hash::make($validated['password']);
    }

    // Удаление изображения
    if ($request->has('remove_image') && $request->remove_image && $staff->image) {
        Storage::disk('public')->delete($staff->image);
        $updateData['image'] = null;
    }

    // Обновление изображения
    if ($request->hasFile('image')) {
        if ($staff->image) {
            Storage::disk('public')->delete($staff->image);
        }
        $updateData['image'] = $request->file('image')->store('staff', 'public');
    }

    $staff->update($updateData);

    return redirect()->route('admin.staff.index')
        ->with('success', 'Данные сотрудника обновлены.');
    }

    public function destroy(Staff $staff)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }
    
        // Проверяем, есть ли у сотрудника связанные услуги
        if ($staff->services()->exists()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Нельзя удалить сотрудника, так как у него есть связанные услуги.');
        }
    
        // Проверяем, есть ли у сотрудника связанные записи
        if ($staff->appointments()->exists()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Нельзя удалить сотрудника, так как у него есть связанные записи.');
        }
    
        if ($staff->image) {
            Storage::disk('public')->delete($staff->image);
        }
        
        $staff->delete();
        
        return redirect()->route('admin.staff.index')
            ->with('success', 'Сотрудник успешно удален.');
    }

    public function completeAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'completed';
        $appointment->save();

        return back()->with('success', 'Запись успешно завершена');
    }

    public function cancelAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'cancelled';
        $appointment->save();

        return back()->with('success', 'Запись успешно отменена');
    }
}