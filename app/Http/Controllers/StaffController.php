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
    
        $staff = User::where('role', 'staff')->get();
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
            'name.required' => 'Поле "Имя" обязательно для заполнения',
            'name.max' => 'Имя не должно превышать 50 символов',
            'name.regex' => 'Имя должно содержать только буквы',
            'surname.required' => 'Поле "Фамилия" обязательно для заполнения',
            'surname.max' => 'Фамилия не должна превышать 50 символов',
            'surname.regex' => 'Фамилия должна содержать только буквы',
            'patronymic.regex' => 'Отчество должно содержать только буквы',
            'email.required' => 'Поле "Email" обязательно для заполнения',
            'email.email' => 'Введите корректный email',
            'email.unique' => 'Этот email уже используется',
            'phone.required' => 'Поле "Телефон" обязательно для заполнения',
            'phone.regex' => 'Телефон должен быть в формате: 8 XXX XXX XX XX',
            'phone.unique' => 'Этот телефон уже используется',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif',
            'image.max' => 'Максимальный размер изображения 2MB',
            'branch_id.exists' => 'Выбранный филиал не существует',
            'status.required' => 'Поле "Статус" обязательно для заполнения',
            'status.in' => 'Некорректный статус',
        ];
    
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'surname' => 'required|string|max:50',
            'phone' => [
                'required',
                'string',
                'max:15',
                function ($attribute, $value, $fail) {
                    $normalized = $this->normalizePhone($value);
                    $exists = User::where(DB::raw("REPLACE(REPLACE(phone, ' ', ''), '+', '')"), str_replace([' ', '+'], '', $normalized))->exists();
                    if ($exists) {
                        $fail('Этот телефон уже используется');
                    }
                },
            ],
            'email' => 'nullable|email|max:100|unique:users',
            'password' => 'required|string|min:8',
            'status' => 'required|in:active,inactive',
            'branch_id' => 'required|exists:branches,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages);
    
        // Нормализация телефона (удаляем пробелы, но сохраняем формат для отображения)
        $phone = $this->normalizePhone($validated['phone']);
    
        // Обработка изображения
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('staff_images', 'public');
        }
    
        // Создаем пользователя-сотрудника
        $user = User::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'phone' => $phone,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'status' => $validated['status'],
            'image' => $imagePath,
            'branch_id' => $validated['branch_id'] ?? null, 
        ]);
    
        return redirect()->route('admin.staff.index')
            ->with('success', 'Сотрудник успешно добавлен');
    }


    public function edit(User $staff)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Доступ запрещен');
        }
    
        $branches = Branch::all();
        return view('admin.editstaff', compact('staff', 'branches'));
    }

    public function update(Request $request, User $staff)
{
    if (Auth::user()->role !== 'admin') {
        return redirect()->route('home')->with('error', 'Доступ запрещён');
    }

    // Валидация
    $messages = [
        'name.required' => 'Поле "Имя" обязательно для заполнения',
        'name.max' => 'Имя не должно превышать 50 символов',
        'name.regex' => 'Имя должно содержать только буквы',
        'surname.required' => 'Поле "Фамилия" обязательно для заполнения',
        'surname.max' => 'Фамилия не должна превышать 50 символов',
        'surname.regex' => 'Фамилия должна содержать только буквы',
        'patronymic.regex' => 'Отчество должно содержать только буквы',
        'email.required' => 'Поле "Email" обязательно для заполнения',
        'email.email' => 'Введите корректный email',
        'email.unique' => 'Этот email уже используется',
        'phone.required' => 'Поле "Телефон" обязательно для заполнения',
        'phone.regex' => 'Телефон должен быть в формате: 8 XXX XXX XX XX',
        'phone.unique' => 'Этот телефон уже используется',
        'password.min' => 'Пароль должен содержать минимум 8 символов',
        'image.image' => 'Файл должен быть изображением',
        'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif',
        'image.max' => 'Максимальный размер изображения 2MB',
        'branch_id.exists' => 'Выбранный филиал не существует',
        'status.required' => 'Поле "Статус" обязательно для заполнения',
        'status.in' => 'Некорректный статус',
    ];

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'],
        'surname' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'],
        'email' => [
            'required',
            'email',
            'string',
            'max:191',
            Rule::unique('users')->ignore($staff->id),
        ],
        'patronymic' => [
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
                    $exists = User::where('id', '!=', $staff->id)
                        ->where(DB::raw("REPLACE(REPLACE(phone, ' ', ''), '+', '')"), str_replace([' ', '+'], '', $normalized))
                        ->exists();

                    if ($exists) {
                        $fail('Этот телефон уже используется');
                    }
                }
            },
        ],
        'password' => 'nullable|string|min:8',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'branch_id' => 'required|exists:branches,id',
        'remove_image' => 'nullable|boolean',
        'status' => 'required|in:active,inactive',
    ], $messages);

    // Форматируем телефон
    $normalizedPhone = $this->normalizePhone($validated['phone']);
    $formattedPhone = $this->formatPhoneForDisplay($normalizedPhone);

    // Формируем данные для обновления
    $updateData = [
        'name' => $this->formatName($validated['name']),
        'surname' => $this->formatName($validated['surname']),
        'patronymic' => $this->formatName($validated['patronymic'] ?? ''),
        'phone' => $normalizedPhone,
        'email' => $validated['email'], 
        'branch_id' => $validated['branch_id'],
        'status' => $validated['status'],
    ];

    // Обновляем пароль, если он был передан
    if (!empty($validated['password'])) {
        $updateData['password'] = Hash::make($validated['password']);
    }

    // Удаление изображения
    if ($request->has('remove_image')) {
        if ($staff->image) {
            Storage::disk('public')->delete($staff->image);
        }
        $updateData['image'] = null;
    }

    // Загрузка нового изображения
    if ($request->hasFile('image')) {
        if ($staff->image) {
            Storage::disk('public')->delete($staff->image);
        }
        $updateData['image'] = $request->file('image')->store('staff_images', 'public');
    }

    // Обновляем данные
    $staff->update($updateData);

    return redirect()->route('admin.staff.index')
        ->with('success', 'Данные сотрудника обновлены.');
}

public function destroy(User $staff)
{
    if (Auth::user()->role !== 'admin') {
        return redirect()->route('home')->with('error', 'Доступ запрещен');
    }

    // Проверяем, есть ли услуги у этого сотрудника
    if ($staff->services()->exists()) {
        return back()->with('error', 'Нельзя удалить сотрудника, так как у него есть услуги');
    }

    // Проверяем, есть ли записи
    if ($staff->appointments()->exists()) {
        return back()->with('error', 'Нельзя удалить сотрудника, так как у него есть записи');
    }

    // Удаляем изображение, если оно есть
    if ($staff->image) {
        Storage::disk('public')->delete($staff->image);
    }

    // Удаляем пользователя (или помечаем как deleted — если используется soft delete)
    $staff->delete();

    return back()->with('success', 'Сотрудник успешно удален');
}
}