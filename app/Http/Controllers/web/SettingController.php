<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\AttendanceQuote;
use App\Models\SalaryComponent;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function attendance()
    {
        $quotes = AttendanceQuote::all();
        return view('settings.attendance', [
            'quotes' => $quotes,
        ]);
    }
}
