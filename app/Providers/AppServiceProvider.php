<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Journal;
use App\Models\Shift;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('*', function ($view) {
            // Cek role
            if (!Auth::check() || Auth::user()->role !== 'Satpam') {
                $view->with('journalReminders', collect());
                return;
            }

            // Ambil id grup user yang login
            $groupId = Auth::user()->group_id;

            // Reminder 1 - Jurnal yg harus disubmit
            // Get all shifts ordered by start time to determine "next shift"
            $shifts = Shift::orderBy('mulai_shift')->get();

            if ($shifts->isEmpty()) {
                $view->with('journalReminders', collect());
                return;
            }

            // Get all journals where this group is the next_shift
            $pendingJournals = Journal::with(['location', 'shift'])
                ->where('next_shift', $groupId)
                ->get();

            $reminders = collect();

            foreach ($pendingJournals as $journal) {
                $currentShift = $journal->shift;
                if (!$currentShift) continue;

                // Find the next shift by time order
                $shiftList = $shifts->values(); // re-index
                $currentIndex = $shiftList->search(fn($s) => $s->id === $currentShift->id);

                if ($currentIndex === false) continue;

                $nextIndex = $currentIndex + 1;
                $wrapsAround = $nextIndex >= $shiftList->count();
                $nextShift = $shiftList[$wrapsAround ? 0 : $nextIndex];

                // If wraps around (next shift is earliest shift), the date is tomorrow
                $journalDate = Carbon::parse($journal->tanggal);
                $reminderDate = $wrapsAround ? $journalDate->copy()->addDay() : $journalDate->copy();

                // Check if the group already submitted for this date, location, and next shift
                $alreadySubmitted = Journal::where('group_id', $groupId)
                    ->whereDate('tanggal', $reminderDate)
                    ->where('lokasi_id', $journal->lokasi_id)
                    ->where('shift_id', $nextShift->id)
                    ->exists();

                if (!$alreadySubmitted) {
                    $reminders->push([
                        'type'    => 'submit',
                        'tanggal' => $reminderDate->locale('id')->isoFormat('D MMMM Y'),
                        'lokasi'  => $journal->location->nama_lokasi ?? '-',
                        'shift'   => $nextShift->nama_shift,
                    ]);
                }
            }


            // Reminder 2 - Jurnal yg harus di revisi
            // Rejected journals: remind user to revise
            $rejectedJournals = Journal::with(['location', 'shift'])
                ->where('group_id', $groupId)
                ->where('status', 'Rejected')
                ->get();

            foreach ($rejectedJournals as $rejected) {
                $reminders->push([
                    'type'    => 'rejected',
                    'tanggal' => Carbon::parse($rejected->tanggal)->locale('id')->isoFormat('D MMMM Y'),
                    'lokasi'  => $rejected->location->nama_lokasi ?? '-',
                    'shift'   => $rejected->shift->nama_shift ?? '-',
                ]);
            }

            $view->with('journalReminders', $reminders);
        });
    }
}
