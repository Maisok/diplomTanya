<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffAuthController extends Controller
{
    public function __construct()
    {
        // Middleware можно указать через атрибуты, как и сделано ниже
    }

    #[Middleware('redirect.if.staff')]
    public function showLoginForm()
    {
        return view('staff.login');
    }

    #[Middleware('concurrent.logins:staff')]
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // Нормализуем телефон
        $normalizedPhone = $this->normalizePhone($request->phone);

        // Находим сотрудника по нормализованному номеру
        $staff = Staff::where('phone', $normalizedPhone)->first();

        if ($staff && Hash::check($request->password, $staff->password)) {
            Auth::guard('staff')->login($staff);
            return redirect()->intended(route('staff.dashboard'));
        }

        return back()->withErrors([
            'phone' => 'Неверный номер телефона или пароль.',
        ])->onlyInput('phone');
    }

    #[Middleware('staff.authenticated')]
    public function logout()
    {
        Auth::guard('staff')->logout();
        return redirect()->route('staff.login');
    }

    #[Middleware('staff.authenticated')]
    public function dashboard()
    {
        $staff = Auth::guard('staff')->user();
        $appointments = $staff->appointments()->with(['user', 'service'])->get();

        return view('staff.dashboard', compact('staff', 'appointments'));
    }

    /**
     * Приводит телефон к международному формату +7XXXXXXXXXX
     */
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
}