<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\User;
use App\Models\Shift;
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
        
        // ambil semua data shift yang aktif dari database, 
        // urut berdasarkan waktu mulai shift.
        // get()->values() memastikan hasilnya menjadi sebuah Collection array dengan index berurutan dari 0.
        $shifts = Shift::where('status', 'Active')->orderBy('mulai_shift')->get()->values();

        // ambil semua jurnal (beserta relasi shift-nya) di mana grup login saat ini 
        // yang ditugaskan sebagai shift berikutnya (next_shift). 
        // difilter jurnal berdasarkan lokasi dan shift yang aktif.
        $allPending = Journal::with(['shift'])
            ->where('next_shift', $group_id)
            ->whereHas('location', fn($q) => $q->where('status', 'Active'))
            ->whereHas('shift', fn($q) => $q->where('status', 'Active'))
            ->get();

        $journals_to_submit = 0;
        
        // Melakukan looping (iterasi) untuk setiap jurnal yang didapat dari variabel $allPending.
        // mengecek satu per satu apakah jurnal ini harus di-submit oleh grup user pada hari ini.
        foreach ($allPending as $journal) {
            $currentShift = $journal->shift;
            
            // skip loop jika jurnal ini tidak memiliki data shift atau master shift-nya kosong.
            if (!$currentShift || $shifts->isEmpty()) continue;

            // Mencari dan ambil urutan index shift dari jurnal saat ini di dalam daftar $shifts yang ada.
            $currentIndex = $shifts->search(fn($s) => $s->id === $currentShift->id);
            if ($currentIndex === false) continue;

            // Menentukan shift selanjutnya.
            // $nextIndex adalah index shift dari jurnal saat ini ditambah 1.
            $nextIndex    = $currentIndex + 1;
            
            // $wrapsAround mengecek apakah shift selanjutnya melompat ke hari berikutnya (misal shift malam ke pagi).
            // Jika $nextIndex sama dengan atau lebih besar dari jumlah shift, artinya kembali ke shift pertama (index 0).
            $wrapsAround  = $nextIndex >= $shifts->count();
            $nextShift    = $shifts[$wrapsAround ? 0 : $nextIndex];
            
            $journalDate  = \Carbon\Carbon::parse($journal->tanggal);
            
            // Menentukan tanggal reminder untuk submit jurnal.
            // Jika terjadi $wrapsAround (pergantian hari), maka tanggalnya ditambah 1 hari (addDay).
            // Jika tidak, tanggalnya tetap sama dengan tanggal jurnal tersebut.
            $reminderDate = $wrapsAround ? $journalDate->copy()->addDay() : $journalDate->copy();

            // Memastikan bahwa reminderDate sama dengan hari ini ($today). Jika tidak, loop akan di-skip.
            // Only count if the reminder is for today
            if (!$reminderDate->isSameDay($today)) continue;

            // Terakhir, mengecek ke database apakah grup ini sudah men-submit jurnal 
            // untuk lokasi, shift selanjutnya, dan tanggal yang telah dihitung sebelumnya ($reminderDate).
            $alreadySubmitted = Journal::where('group_id', $group_id)
                ->whereDate('tanggal', $reminderDate)
                ->where('lokasi_id', $journal->lokasi_id)
                ->where('shift_id', $nextShift->id)
                ->exists();

            // Jika jurnal tersebut belum pernah di-submit sebelumnya, maka nilai $journals_to_submit ditambah 1.
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
