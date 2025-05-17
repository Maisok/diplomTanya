<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    // Нормализуем номер телефона к формату +7XXXXXXXXXX
    protected function normalizePhone($phone)
    {
        $digits = preg_replace('/\D/', '', $phone);
        
        if (strlen($digits) === 11 && $digits[0] === '8') {
            return '+7' . substr($digits, 1);
        }
        
        if (strlen($digits) === 10) {
            return '+7' . $digits;
        }
        
        return '+' . $digits;
    }

    // Форматируем номер для отображения (8 XXX XXX XX XX)
    protected function formatPhoneForDisplay($phone)
    {
        $digits = preg_replace('/\D/', '', $phone);
        
        if (strlen($digits) === 11 && ($digits[0] === '7' || $digits[0] === '8')) {
            $prefix = $digits[0] === '7' ? '8' : $digits[0];
            return $prefix . ' ' . substr($digits, 1, 3) . ' ' . 
                   substr($digits, 4, 3) . ' ' . substr($digits, 7, 2) . ' ' . substr($digits, 9, 2);
        }
        
        return $phone;
    }

    protected function formatName($name)
    {
        return mb_convert_case(trim($name), MB_CASE_TITLE, 'UTF-8');
    }

    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }

        $staff = Staff::with('branch')->get();
        return view('admin.staff', compact('staff'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }

        $branches = Branch::all();
        return view('admin.createstaff', compact('branches'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }

        $messages = [
            'first_name.required' => 'Поле "Имя" обязательно для заполнения',
            'first_name.max' => 'Имя не должно превышать 50 символов',
            'first_name.regex' => 'Имя должно содержать только буквы',
            'last_name.required' => 'Поле "Фамилия" обязательно для заполнения',
            'last_name.max' => 'Фамилия не должна превышать 50 символов',
            'last_name.regex' => 'Фамилия должна содержать только буквы',
            'middle_name.regex' => 'Отчество должно содержать только буквы',
            'phone.required' => 'Поле "Телефон" обязательно для заполнения',
            'phone.regex' => 'Телефон должен быть в формате: 8 XXX XXX XX XX',
            'phone.unique' => 'Этот номер телефона уже используется',
            'password.required' => 'Поле "Пароль" обязательно для заполнения',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif',
            'image.max' => 'Максимальный размер изображения 2MB',
            'branch_id.exists' => 'Выбранный филиал не существует',
            'status.required' => 'Поле "Статус" обязательно для заполнения',
            'status.in' => 'Некорректный статус',
        ];

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'
            ],
            'last_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'
            ],
            'middle_name' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]*$/u'
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^8 \d{3} \d{3} \d{2} \d{2}$/',
                function ($attribute, $value, $fail) {
                    $normalized = $this->normalizePhone($value);
                    
                    // Проверяем в таблице staff
                    $existsInStaff = Staff::where(DB::raw("REPLACE(REPLACE(phone, ' ', ''), '+', '')"), 
                        str_replace([' ', '+'], '', $normalized))
                        ->exists();
                    
                    // Проверяем в таблице users
                    $existsInUsers = User::where(DB::raw("REPLACE(REPLACE(phone, ' ', ''), '+', '')"), 
                        str_replace([' ', '+'], '', $normalized))
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
        ], $messages);

        $normalizedPhone = $this->normalizePhone($validated['phone']);
        $formattedPhone = $this->formatPhoneForDisplay($normalizedPhone);

        $staff = Staff::create([
            'first_name' => $this->formatName($validated['first_name']),
            'last_name' => $this->formatName($validated['last_name']),
            'middle_name' => $this->formatName($validated['middle_name'] ?? ''),
            'phone' => $normalizedPhone,
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
        $staff->phone = $this->formatPhoneForDisplay($staff->phone);
        return view('admin.editstaff', compact('staff', 'branches'));
    }

    public function update(Request $request, Staff $staff)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }

        $messages = [
            'first_name.required' => 'Поле "Имя" обязательно для заполнения',
            'first_name.max' => 'Имя не должно превышать 50 символов',
            'first_name.regex' => 'Имя должно содержать только буквы',
            'last_name.required' => 'Поле "Фамилия" обязательно для заполнения',
            'last_name.max' => 'Фамилия не должна превышать 50 символов',
            'last_name.regex' => 'Фамилия должна содержать только буквы',
            'middle_name.regex' => 'Отчество должно содержать только буквы',
            'phone.required' => 'Поле "Телефон" обязательно для заполнения',
            'phone.regex' => 'Телефон должен быть в формате: 8 XXX XXX XX XX',
            'phone.unique' => 'Этот номер телефона уже используется',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif',
            'image.max' => 'Максимальный размер изображения 2MB',
            'branch_id.exists' => 'Выбранный филиал не существует',
            'status.required' => 'Поле "Статус" обязательно для заполнения',
            'status.in' => 'Некорректный статус',
        ];

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'
            ],
            'last_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'
            ],
            'middle_name' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]*$/u'
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^8 \d{3} \d{3} \d{2} \d{2}$/',
                function ($attribute, $value, $fail) use ($staff) {
                    $normalized = $this->normalizePhone($value);
                    $currentNormalized = $this->normalizePhone($staff->phone);
                    
                    if ($normalized !== $currentNormalized) {
                        // Проверяем в таблице staff
                        $existsInStaff = Staff::where('id', '!=', $staff->id)
                            ->where(DB::raw("REPLACE(REPLACE(phone, ' ', ''), '+', '')"), 
                            str_replace([' ', '+'], '', $normalized))
                            ->exists();
                        
                        // Проверяем в таблице users
                        $existsInUsers = User::where(DB::raw("REPLACE(REPLACE(phone, ' ', ''), '+', '')"), 
                            str_replace([' ', '+'], '', $normalized))
                            ->exists();
                        
                        if ($existsInStaff || $existsInUsers) {
                            $fail('Этот телефон уже используется');
                        }
                    }
                }
            ],
            'password' => 'nullable|string|min:8',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'branch_id' => 'nullable|exists:branches,id',
            'remove_image' => 'nullable|boolean',
            'status' => 'required|in:active,inactive'
        ], $messages);

        $normalizedPhone = $this->normalizePhone($validated['phone']);
        $formattedPhone = $this->formatPhoneForDisplay($normalizedPhone);

        $updateData = [
            'first_name' => $this->formatName($validated['first_name']),
            'last_name' => $this->formatName($validated['last_name']),
            'middle_name' => $this->formatName($validated['middle_name'] ?? ''),
            'phone' => $normalizedPhone,
            'branch_id' => $validated['branch_id'],
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        if ($request->has('remove_image') && $request->remove_image && $staff->image) {
            Storage::disk('public')->delete($staff->image);
            $updateData['image'] = null;
        }

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
    
        if ($staff->services()->exists()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Нельзя удалить сотрудника, так как у него есть связанные услуги.');
        }
    
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
}