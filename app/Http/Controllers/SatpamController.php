<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SatpamController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $group_id = $user->group_id;
        $today = Carbon::today();

        // Journals to submit today:
        // Get ALL journals where this group is the next_shift,
        // compute the actual reminder date (handling shift wrap-around),
        // and only count those whose reminderDate equals today.
        $shifts = \App\Models\Shift::orderBy('mulai_shift')->get()->values();

        $allPending = Journal::with(['shift'])
            ->where('next_shift', $group_id)
            ->get();

        $journals_to_submit = 0;
        foreach ($allPending as $journal) {
            $currentShift = $journal->shift;
            if (!$currentShift || $shifts->isEmpty()) continue;

            $currentIndex = $shifts->search(fn($s) => $s->id === $currentShift->id);
            if ($currentIndex === false) continue;

            $nextIndex    = $currentIndex + 1;
            $wrapsAround  = $nextIndex >= $shifts->count();
            $nextShift    = $shifts[$wrapsAround ? 0 : $nextIndex];
            $journalDate  = \Carbon\Carbon::parse($journal->tanggal);
            $reminderDate = $wrapsAround ? $journalDate->copy()->addDay() : $journalDate->copy();

            // Only count if the reminder is for today
            if (!$reminderDate->isSameDay($today)) continue;

            $alreadySubmitted = Journal::where('group_id', $group_id)
                ->whereDate('tanggal', $reminderDate)
                ->where('lokasi_id', $journal->lokasi_id)
                ->where('shift_id', $nextShift->id)
                ->exists();

            if (!$alreadySubmitted) {
                $journals_to_submit++;
            }
        }

        // Pending journals
        $pending_journals = Journal::where('group_id', $group_id)
            ->where('status', 'Pending')
            ->count();

        // Waiting approval untuk serah terima jurnal
        $waiting_approval = Journal::where('next_shift', $group_id)
            ->where('status', 'Pending')
            ->count();

        // My Group
        $my_group = User::where('group_id', $group_id)
            ->whereNotNull('group_id')
            ->get();

        // Recent Submissions
        $recent_submissions = Journal::with(['user', 'group', 'location', 'shift', 'nextShift'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $noGroup = $user->group_id === null;

        return view('satpam.dashboard', compact(
            'journals_to_submit',
            'pending_journals',
            'waiting_approval',
            'my_group',
            'recent_submissions',
            'noGroup'
        ));
    }
}
