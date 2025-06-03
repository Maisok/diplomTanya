<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;

use App\Models\Service;

class SpecialistController extends Controller
{
    public function index()
    {
        $services = Service::inRandomOrder()->take(8)->get();
        $specialists = User::where('role', 'staff')->inRandomOrder()->take(4)->get();
        $branches = Branch::with('schedule')->get(); 
    
        return view('welcome', compact('branches', 'services', 'specialists'));
    }

    public function all()
    {
        // Получаем всех пользователей с ролью 'staff'
        $specialists = User::where('role', 'staff')->with(['services'])->get();
    
        return view('all-specialists', compact('specialists'));
    }
}
