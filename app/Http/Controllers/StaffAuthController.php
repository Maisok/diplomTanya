<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffAuthController extends Controller
{
    #[Middleware(RedirectIfStaff::class, except: ['logout'])]
    public function __construct()
    {
        // Конструктор можно оставить пустым или использовать для других целей
    }
    #[Middleware(RedirectIfStaff::class)]
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
    
        if (Auth::guard('staff')->attempt([
            'phone' => $request->phone,
            'password' => $request->password
        ], $request->remember)) {
            return redirect()->intended(route('staff.dashboard'));
        }
    
        return back()->withErrors([
            'phone' => 'Неверный номер телефона или пароль.',
        ])->onlyInput('phone');
    }

    #[Middleware(StaffAuthenticated::class)]
    public function logout()
    {
        Auth::guard('staff')->logout();
        return redirect()->route('staff.login');
    }

    #[Middleware(StaffAuthenticated::class)]
    public function dashboard()
    {
        $staff = Auth::guard('staff')->user();
        $appointments = $staff->appointments()->with(['user', 'service'])->get();
    
        return view('staff.dashboard', compact('staff', 'appointments'));
    }
}