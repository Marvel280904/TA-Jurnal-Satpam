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
        // Data seluruh jurnal
        $journals = Journal::with(['user', 'group', 'location', 'shift'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Data untuk filter
        $locations = Location::orderBy('nama_lokasi')->get();
        $shifts    = Shift::orderBy('mulai_shift')->get();
        $groups    = Group::orderBy('nama_grup')->get();

        return view('log_history', compact('journals', 'locations', 'shifts', 'groups'));
    }
}
