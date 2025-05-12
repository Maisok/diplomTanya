<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Branch;

use App\Models\Service;

class SpecialistController extends Controller
{
    public function index()
    {
        $services = Service::inRandomOrder()->take(8)->get();
        $specialists = Staff::inRandomOrder()->take(4)->get();
        $branches = Branch::all();
        return view('welcome', compact('branches','services', 'specialists'));
    }

    public function all()
    {
        $specialists = Staff::all();
        return view('all-specialists', compact('specialists'));
    }
}
