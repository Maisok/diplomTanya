<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\preg_replace;



class AdminController extends Controller
{
    public function index_main()
    {
        if(Auth::user()->role === 'admin') {
        return view('admin.dashboard');
        } else {
            return redirect()->route('home');
        }
    }

    public function index()
    {
        if(Auth::user()->role !== 'super_admin') {
            return redirect()->route('home');
        }
        $admins = User::where('role', 'admin')->get();
        return view('admins.index', compact('admins'));
    }

    public function create()
    {
        if(Auth::user()->role !== 'super_admin') {
            return redirect()->route('home');
        }
        return view('admins.create');
    }

    public function store(Request $request)
    {
        if(Auth::user()->role !== 'super_admin') {
            return redirect()->route('home');
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'],
            'surname' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'],
            'email' => 'required|string|email|max:100|unique:users',
            'phone' => [
                'required',
                'string',
                'max:15',
                'regex:/^8 \d{3} \d{3} \d{2} \d{2}$/',
                function ($attribute, $value, $fail) {
                    $normalizedPhone = $this->normalizePhone($value);
                    $phoneDigits = preg_replace('/[^0-9]/', '', $normalizedPhone);
                
                    $existsInUsers = \App\Models\User::whereRaw("REGEXP_REPLACE(phone, '[^0-9]', '') = ?", [$phoneDigits])->exists();
                
                    $existsInStaff = \App\Models\Staff::whereRaw("REGEXP_REPLACE(phone, '[^0-9]', '') = ?", [$phoneDigits])->exists();
                
                    if ($existsInUsers || $existsInStaff) {
                        $fail('Этот номер телефона уже зарегистрирован.');
                    }
                },
            ],
            'password' => 'required|string|min:8|confirmed',
        ],[
          'phone.regex'=>'Формат номер телефона 8 888 888 88 88'
        ]);

        User::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'phone' => $this->normalizePhone($validated['phone']), // Сохраняем в формате +7...
            'password' => bcrypt($validated['password']),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admins.index')->with('success', 'Администратор успешно создан.');
    }

    public function edit(User $user)
    {
        if(Auth::user()->role !== 'super_admin') {
            return redirect()->route('home');
        }

        return view('admins.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if(Auth::user()->role !== 'super_admin') {
            return redirect()->route('home');
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'],
            'surname' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u'],
            'email' => 'required|string|email|max:100|unique:users,email,' . $user->id,
            'phone' => [
                'required',
                'string',
                'max:15',
                'regex:/^8 \d{3} \d{3} \d{2} \d{2}$/',
                function ($attribute, $value, $fail) use ($user) {
                    $normalized = $this->normalizePhone($value);
                
                    $phoneDigits = preg_replace('/[^0-9]/', '', $normalized);
                
                    $existsInUsers = \App\Models\User::where('id', '!=', $user->id)
                        ->whereRaw("REGEXP_REPLACE(phone, '[^0-9]', '') = ?", [$phoneDigits])
                        ->exists();
                
                    $existsInStaff = \App\Models\Staff::whereRaw("REGEXP_REPLACE(phone, '[^0-9]', '') = ?", [$phoneDigits])
                        ->exists();
                
                    if ($existsInUsers || $existsInStaff) {
                        $fail('Этот телефон уже используется.');
                    }
                },
            ],
            'password' => 'nullable|string|min:8',
        ],[
            'phone.regex'=>'Формат номер телефона 8 888 888 88 88'
          ]);

          $user->update([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'phone' => $this->normalizePhone($validated['phone']), // Сохраняем в формате +7...
            'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
        ]);

        return redirect()->route('admins.index')->with('success', 'Администратор успешно обновлен.');
    }

    public function destroy(User $user)
    {
        if(!Auth::user()->role !== 'super_admin') {
            return redirect()->route('home');
        }

        $user->delete();

        return redirect()->route('admins.index')->with('success', 'Администратор удален.');
    }

protected function normalizePhone($phone)
{
    // Удаляем все нецифровые символы
    $digits = preg_replace('/\D/', '', $phone);

    // Если номер начинается с 8 и имеет длину 11 цифр (российский номер)
    if (strlen($digits) === 11 && $digits[0] === '8') {
        return '+7' . substr($digits, 1);
    }

    // Если номер начинается с +7 и имеет длину 12 цифр
    if (strlen($digits) === 12 && strpos($phone, '+7') === 0) {
        return '+' . $digits;
    }

    // Для других форматов просто добавляем + в начало
    return $digits ? '+' . $digits : null;
}
}