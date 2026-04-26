<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PGAController extends Controller
{
    public function dashboard()
    {
        // 4 Summary Cards
        $waiting_approval = Journal::where('status', 'Waiting')->count();
        $approved_today = Journal::where('status', 'Approved')
            ->whereDate('updated_at', Carbon::today())
            ->count();
        $rejected_journals = Journal::where('status', 'Rejected')->count();
        $total_groups = Group::where('status', 'Active')->count();

        // Approval Queue
        $approval_queue = Journal::with(['user', 'group', 'location', 'shift'])
            ->where('status', 'Waiting')
            ->orderBy('created_at', 'ASC')
            ->get();

        // Journal Submissions Last 7 Days
        $last_7_days_data = [];
        for ($i = 0; $i <= 6; $i++) {
            $date = Carbon::today()->subDays($i);
            $count = Journal::whereDate('tanggal', $date)->count();
            $last_7_days_data[] = [
                'date' => $date->format('d F'),
                'count' => $count
            ];
        }

        // Most Active Groups
        $most_active_groups = Group::withCount('users')
            ->where('status', 'Active')
            ->withCount('journals')
            ->orderBy('journals_count', 'desc')
            ->take(5)
            ->get();

        // Most Active Users
        $most_active_users = User::where('role', 'Satpam')
            ->where('status', 'Active')
            ->with('group')
            ->withCount('journals')
            ->orderBy('journals_count', 'desc')
            ->take(5)
            ->get();

        return view('pga.dashboard', compact(
            'waiting_approval',
            'approved_today',
            'rejected_journals',
            'total_groups',
            'approval_queue',
            'last_7_days_data',
            'most_active_groups',
            'most_active_users'
        ));
    }
}
