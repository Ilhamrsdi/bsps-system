<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DummyVervalService;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = DummyVervalService::getStats();
        $vervalList = DummyVervalService::getVervalData();
        $recentActivities = DummyVervalService::getRecentActivities();

        return view('dashboard.index', compact('stats', 'vervalList', 'recentActivities'));
    }
}
