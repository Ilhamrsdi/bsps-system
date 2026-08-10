<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Tampilkan Halaman Landing / Beranda Publik
     */
    public function index()
    {
        return view('landing.index');
    }
}
