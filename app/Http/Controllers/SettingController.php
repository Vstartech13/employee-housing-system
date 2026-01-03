<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Just show information about auto-generated employee IDs
        return view('settings.index');
    }
}
