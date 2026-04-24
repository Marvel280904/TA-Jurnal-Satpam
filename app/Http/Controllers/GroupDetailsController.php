<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;

class GroupDetailsController extends Controller
{
    public function viewGroup()
    {
        $groups = Group::where('status', 'Active')->with('users')->get();
        return view('pga.group_details', compact('groups'));
    }
}
