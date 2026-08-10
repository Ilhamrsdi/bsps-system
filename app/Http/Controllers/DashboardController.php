<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::check() && Auth::user()->isPetugas()) {
            return redirect()->route('petugas.dashboard');
        }

        return view('dashboard.index');
    }
}
