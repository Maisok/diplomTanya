<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class AdminController extends Controller
{
    public function index()
    {
        if(Auth::user()->role === 'admin') {
        return view('admin.dashboard');
        } else {
            return redirect()->route('home');
        }
    }
}