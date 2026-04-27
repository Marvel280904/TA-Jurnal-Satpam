<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\Location;
use App\Models\Shift;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class LogHistoryController extends Controller
{
    public function viewJournal()
    {
        // For Satpam, view all journals or maybe just their group's history?
        $journals = Journal::with(['user', 'group', 'location', 'shift'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $locations = Location::orderBy('nama_lokasi')->get();
        $shifts    = Shift::orderBy('mulai_shift')->get();
        $groups    = Group::orderBy('nama_grup')->get();

        return view('log_history', compact('journals', 'locations', 'shifts', 'groups'));
    }
}
