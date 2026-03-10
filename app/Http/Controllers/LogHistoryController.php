<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use Illuminate\Support\Facades\Auth;

class LogHistoryController extends Controller
{
    public function viewJournal()
    {
        // For Satpam, view all journals or maybe just their group's history?
        // User requested: "semua journal yang ada dalam tabel journals"
        $journals = Journal::with(['user', 'group', 'location', 'shift'])->orderBy('tanggal', 'desc')->get();

        return view('log_history', compact('journals'));
    }
}
