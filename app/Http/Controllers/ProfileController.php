<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Models\Appointment;
use App\Mail\EmailVerification;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        $upcomingAppointments = Appointment::with('staff', 'service', 'branch')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('appointment_time', '>=', now())
            ->orderBy('appointment_time')
            ->get();
    
        $pastAppointments = Appointment::with('staff', 'service', 'branch')
            ->where('user_id', $user->id)
            ->where(function($query) {
                $query->where('status', 'completed')
                      ->orWhere('status', 'cancelled')
                      ->orWhere('appointment_time', '<', now());
            })
            ->orderBy('appointment_time', 'desc')
            ->get();
    
        return view('profile', compact('user', 'upcomingAppointments', 'pastAppointments'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
    
        $messages = [
            'name.required' => 'Поле "Имя" обязательно для заполнения',
            'name.max' => 'Имя не должно превышать 50 символов',
            'name.regex' => 'Имя должно начинаться с заглавной буквы и содержать только буквы и дефисы',
            'surname.max' => 'Фамилия не должна превышать 50 символов',
            'surname.regex' => 'Фамилия должна начинаться с заглавной буквы и содержать только буквы и дефисы',
            'phone.required' => 'Поле "Телефон" обязательно для заполнения',
            'phone.regex' => 'Телефон должен быть в формате: 8 999 123 45 67',
            'phone.unique' => 'Этот номер телефона уже используется',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
        ];
    
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[А-ЯЁA-Z][а-яёa-z\-]+$/u'
            ],
            'surname' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[А-ЯЁA-Z][а-яёa-z\-]+$/u'
            ],
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users', 'phone')->ignore($user->id),
                'regex:/^8 \d{3} \d{3} \d{2} \d{2}$/'
            ],
            'password' => 'nullable|string|min:8',
        ], $messages);
    
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->phone = $request->phone;
    
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    
        $user->save();
    
        return redirect()->route('profile.show')->with('success', 'Профиль успешно обновлен.');
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:100|unique:users,email,'.Auth::id()
        ]);

        $code = rand(100000, 999999);
        $user = Auth::user();
        
        session(['email_verification_code' => $code]);
        session(['email_to_verify' => $request->email]);

        try {
            Mail::to($request->email)->send(new EmailVerification($code));

            return response()->json([
                'success' => true,
                'message' => 'Код подтверждения отправлен на ваш email'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отправке письма: '.$e->getMessage()
            ]);
        }
    }

    public function updateEmail(Request $request)
    {
        $user = Auth::user();
        
        // Для пользователей Yandex просто обновляем email без подтверждения
        if ($user->yandex_id) {
            $request->validate([
                'email' => 'required|email|max:100|unique:users,email,'.$user->id,
            ]);
    
            $user->email = $request->email;
            $user->email_verified_at = now(); // Автоматически подтверждаем для Yandex
            $user->save();
    
            return redirect()->route('profile.show')
                ->with('success', 'Email успешно обновлен (автоподтверждение для Yandex)');
        }
    
        // Стандартная логика для обычных пользователей
        $request->validate([
            'email' => 'required|email|max:100|unique:users,email,'.$user->id,
            'verification_code' => 'required|numeric'
        ]);
    
        if ($request->verification_code != session('email_verification_code')) {
            return back()->with('error', 'Неверный код подтверждения');
        }
    
        if ($request->email != session('email_to_verify')) {
            return back()->with('error', 'Email не совпадает с тем, на который был отправлен код');
        }
    
        $user->email = $request->email;
        $user->email_verified_at = now();
        $user->save();
    
        $request->session()->forget(['email_verification_code', 'email_to_verify']);
    
        return redirect()->route('profile.show')
            ->with('success', 'Email успешно подтвержден и обновлен');
    }
}