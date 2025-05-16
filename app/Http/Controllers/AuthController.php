<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{


    public function yandex()
    {
        return Socialite::driver('yandex')->redirect();
    }
    public function yandexRedirect()
    {
        try {
            $socialite = Socialite::driver('yandex');
            $socialite->setHttpClient(new \GuzzleHttp\Client([
                'verify' => false, // Отключаем проверку SSL
                'timeout' => 30,
                'connect_timeout' => 10,
            ]));
    
            $yandexUser = $socialite->user();
    
            \Log::info('Yandex User Data:', (array)$yandexUser);
    
            // Нормализуем телефон
            $phone = isset($yandexUser->user['default_phone']['number']) 
                ? $this->normalizePhone($yandexUser->user['default_phone']['number'])
                : null;
    
            // Ищем пользователя по yandex_id или email
            $user = User::where('yandex_id', $yandexUser->id)
                ->orWhere('email', $yandexUser->email)
                ->first();
    
            if (!$user) {
                // Создаём нового пользователя
                $nameParts = explode(' ', $yandexUser->name);
                
                $user = User::create([
                    'yandex_id' => $yandexUser->id,
                    'email' => $yandexUser->email,
                    'name' => $nameParts[1] ?? $yandexUser->nickname ?? 'User',
                    'surname' => $nameParts[0] ?? '',
                    'phone' => $phone,
                    'password' => Hash::make(Str::random(16)),
                ]);
            } else {
                // Обновляем данные пользователя, если они изменились
                $updated = false;
                $nameParts = explode(' ', $yandexUser->name);
    
                if ($user->email !== $yandexUser->email) {
                    $user->email = $yandexUser->email;
                    $updated = true;
                }
    
                if ($user->name !== ($nameParts[1] ?? '')) {
                    $user->name = $nameParts[1] ?? $user->name;
                    $updated = true;
                }
    
                if ($user->surname !== ($nameParts[0] ?? '')) {
                    $user->surname = $nameParts[0] ?? $user->surname;
                    $updated = true;
                }
    
                if ($phone && $user->phone !== $phone) {
                    $user->phone = $phone;
                    $updated = true;
                }
    
                if ($updated) {
                    $user->save();
                }
            }
    
            Auth::login($user, true);
            return redirect()->route('home');
    
        } catch (\Exception $e) {
            \Log::error('Yandex Auth Error: '.$e->getMessage());
            return redirect()->route('login')->withErrors([
                'yandex' => 'Ошибка авторизации через Яндекс. Попробуйте ещё раз.'
            ]);
        }
    }


    public function showRegistrationForm()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
{
    $messages = [
        'name.required' => 'Поле "Имя" обязательно для заполнения',
        'name.max' => 'Имя не должно превышать 50 символов',
        'surname.required' => 'Поле "Фамилия" обязательно для заполнения',
        'surname.max' => 'Фамилия не должна превышать 50 символов',
        'email.required' => 'Поле "Email" обязательно для заполнения',
        'email.email' => 'Введите корректный email адрес',
        'email.max' => 'Email не должен превышать 100 символов',
        'email.unique' => 'Этот email уже зарегистрирован',
        'phone.required' => 'Поле "Телефон" обязательно для заполнения',
        'phone.regex' => 'Телефон должен быть в формате: 8 999 123 45 67',
        'password.required' => 'Поле "Пароль" обязательно для заполнения',
        'password.min' => 'Пароль должен содержать минимум 8 символов',
        'password.confirmed' => 'Пароли не совпадают',
        'g-recaptcha-response.required' => 'Подтвердите, что вы не робот',
    ];

    $request->validate([
        'name' => ['required', 'string', 'max:50', 'regex:/^[А-ЯЁA-Z][а-яёa-z\-]+$/u'],
        'surname' => ['required', 'string', 'max:50', 'regex:/^[А-ЯЁA-Z][а-яёa-z\-]+$/u'],
        'email' => 'required|string|email|max:100|unique:users',
        'phone' => [
            'required',
            'string',
            'max:15',
            'regex:/^8 \d{3} \d{3} \d{2} \d{2}$/',
            function ($attribute, $value, $fail) {
                $normalizedPhone = $this->normalizePhone($value);
                if (User::where('phone', $normalizedPhone)->exists()) {
                    $fail('Этот номер телефона уже зарегистрирован.');
                }
            },
        ],
        'password' => 'required|string|min:8|confirmed',
        'g-recaptcha-response' => 'required',
    ], $messages);

    // Проверка reCAPTCHA
    $client = new Client(['verify' => false]);
    $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
        'form_params' => [
            'secret' => '6LfupH4qAAAAANWkAkkjKKI_sPZG0xa7VXhjFtwo',
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ],
    ]);

    $body = json_decode((string)$response->getBody());
    if (!$body->success) {
        return redirect()->back()->withInput()->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed.']);
    }

    $user = User::create([
        'name' => $request->name,
        'surname' => $request->surname,
        'phone' => $this->normalizePhone($request->phone),
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    Auth::login($user);
    return redirect('/');
}

    public function showLoginForm()
    {
        return view('auth.auth');
    }


    public function login(Request $request)
    {
        $request->validate([
            'phone' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^8 \d{3} \d{3} \d{2} \d{2}$/', $value)) {
                        $fail('Номер телефона должен быть в формате 8 888 888 88 88');
                    }
                },
            ],
            'password' => 'required|string',
        ]);
    
        $normalizedPhone = $this->normalizePhone($request->phone);
        
        $user = User::where('phone', $normalizedPhone)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('/');
        }
    
        return back()->withErrors([
            'phone' => 'Неверный номер телефона или пароль',
        ]);
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect('/');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Отправка ссылки для сброса
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $status = Password::sendResetLink(
            $request->only('email')
        );
        
        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    // Показ формы сброса пароля
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    // Обработка сброса пароля
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
                
                event(new PasswordReset($user));
            }
        );
        
        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    protected function normalizePhone($phone)
    {
        // Удаляем все нецифровые символы
        $digits = preg_replace('/\D/', '', $phone);
        
        // Если номер начинается с 8 и имеет длину 11 цифр (российский номер)
        if (strlen($digits) === 11 && $digits[0] === '8') {
            return '+7' . substr($digits, 1);
        }
        
        // В остальных случаях просто возвращаем цифры с плюсом
        return '+' . $digits;
    }
}