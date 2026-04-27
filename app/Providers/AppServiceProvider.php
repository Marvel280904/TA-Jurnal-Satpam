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
            if (!Auth::check()) {
                $view->with('journalReminders', collect());
                return;
            }

            $role    = Auth::user()->role;
            $groupId = Auth::user()->group_id;
            $reminders = collect();

            // ==========================================
            // SATPAM REMINDERS
            // ==========================================
            if ($role === 'Satpam') {
                $shifts = Shift::where('status', 'Active')->orderBy('mulai_shift')->get();

                if (!$shifts->isEmpty()) {
                    // Reminder 1 - Submit Jurnal (next shift belum submit)
                    $pendingJournals = Journal::with(['location', 'shift'])
                        ->where('next_shift', $groupId)
                        ->whereHas('location', fn($q) => $q->where('status', 'Active'))
                        ->whereHas('shift', fn($q) => $q->where('status', 'Active'))
                        ->get();

                    foreach ($pendingJournals as $journal) {
                        $currentShift = $journal->shift;
                        if (!$currentShift) continue;

                        $shiftList    = $shifts->values();
                        $currentIndex = $shiftList->search(fn($s) => $s->id === $currentShift->id);
                        if ($currentIndex === false) continue;

                        $nextIndex    = $currentIndex + 1;
                        $wrapsAround  = $nextIndex >= $shiftList->count();
                        $nextShift    = $shiftList[$wrapsAround ? 0 : $nextIndex];
                        $journalDate  = Carbon::parse($journal->tanggal);
                        $reminderDate = $wrapsAround ? $journalDate->copy()->addDay() : $journalDate->copy();

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

                    // Reminder 2 - Serah Terima (jurnal pending yang group ini next_shift-nya)
                    $handoverJournals = Journal::with(['location', 'shift'])
                        ->where('next_shift', $groupId)
                        ->where('status', 'Pending')
                        ->get();

                    foreach ($handoverJournals as $journal) {
                        $reminders->push([
                            'type'    => 'handover',
                            'tanggal' => Carbon::parse($journal->tanggal)->locale('id')->isoFormat('D MMMM Y'),
                            'lokasi'  => $journal->location->nama_lokasi ?? '-',
                            'shift'   => $journal->shift->nama_shift ?? '-',
                        ]);
                    }
                }

                // Reminder 3 - Revisi (jurnal Rejected milik grup ini)
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
            }

            // ==========================================
            // PGA REMINDERS
            // ==========================================
            elseif ($role === 'PGA') {
                $waitingJournals = Journal::with(['location', 'shift'])
                    ->where('status', 'Waiting')
                    ->get();

                foreach ($waitingJournals as $journal) {
                    $reminders->push([
                        'type'    => 'waiting',
                        'tanggal' => Carbon::parse($journal->tanggal)->locale('id')->isoFormat('D MMMM Y'),
                        'lokasi'  => $journal->location->nama_lokasi ?? '-',
                        'shift'   => $journal->shift->nama_shift ?? '-',
                    ]);
                }
            }

            $view->with('journalReminders', $reminders);
        });
    }
}
