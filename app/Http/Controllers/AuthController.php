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


    public function yandex() // перенаправляем юзера на яндекс Auth
    {
        return Socialite::driver('yandex')->redirect();
    }
    public function yandexRedirect()
    {
        try {
            // Временное отключение SSL-проверки (только для разработки!)
            $socialite = Socialite::driver('yandex');
            $socialite->setHttpClient(new \GuzzleHttp\Client([
                'verify' => false,
                'timeout' => 30,
                'connect_timeout' => 10,
                'http_errors' => true
            ]));
    
            $yandexUser = $socialite->user();
            
            \Log::info('Yandex user data:', [
                'id' => $yandexUser->id,
                'name' => $yandexUser->name,
                'email' => $yandexUser->email,
                'phone' => $yandexUser->user['default_phone']['number'] ?? null
            ]);
    
            // Проверяем, есть ли пользователь с таким email в системе
            $user = User::where('email', $yandexUser->email)->first();
    
            if (!$user) {
                // Если пользователя нет - регистрируем
                $nameParts = array_filter(explode(' ', $yandexUser->name));
                $surname = $nameParts[0] ?? '';
                $name = $nameParts[1] ?? $yandexUser->nickname ?? 'Пользователь';
    
                $user = User::create([
                    'email' => $yandexUser->email ?? $yandexUser->id.'@yandex.temp',
                    'name' => $name,
                    'surname' => $surname,
                    'phone' => $yandexUser->user['default_phone']['number'] ?? null,
                    'role' => 'user',
                    'yandex_id' => $yandexUser->id // Сохраняем ID Яндекс для будущих авторизаций
                ]);
            } else {
                // Если пользователь есть - обновляем данные из Яндекс (если нужно)
                if (empty($user->yandex_id)) {
                    $user->yandex_id = $yandexUser->id;
                    $user->save();
                }
            }
    
            Auth::login($user, true);
            
            return redirect()->route('main');
    
        } catch (\Exception $e) {
            \Log::error('Yandex auth error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('login')
                   ->withErrors('Ошибка авторизации: '.$e->getMessage());
        }
    }


    public function showRegistrationForm()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'surname' => 'required|string|max:50',
            'email' => 'required|string|max:100',
            'phone' => [
                'required',
                'string',
                'max:15',
                'unique:users',
                'regex:/^8 \d{3} \d{3} \d{2} \d{2}$/'
            ],
            'password' => 'required|string|min:8|confirmed',
            'g-recaptcha-response' => 'required',
        ]);
    
        // Проверка reCAPTCHA
        $client = new Client([
            'verify' => false,
        ]);
        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => '6LfupH4qAAAAANWkAkkjKKI_sPZG0xa7VXhjFtwo',
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ],
        ]);
    
        $body = json_decode((string) $response->getBody());
    
        if (!$body->success) {
            return redirect()->back()->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed.']);
        }
    
        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'phone' => $request->phone,
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


    #[Middleware('concurrent.logins:web')]
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

        $credentials = $request->only('phone', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'phone' => 'The provided credentials do not match our records.',
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
}