<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Location;
use App\Models\Shift;
use App\Models\Group;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $data = [
            'active_user' => User::where('status', 'Active')->count(),
            'active_locations' => Location::where('status', 'Active')->count(),
            'active_shifts' => Shift::where('status', 'Active')->count(),
            'active_groups' => Group::where('status', 'Active')->count(),
            'recent_logs' => SystemLog::viewLog()->take(5),
            'satpam_users' => User::where('role', 'Satpam')->where('status', 'Active')->orderBy('nama')->get(),
        ];

        return view('admin.dashboard', $data);
    }
}