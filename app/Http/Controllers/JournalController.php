<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Location;
use App\Models\Shift;
use App\Models\Group;
use App\Models\Journal;
use App\Models\Upload;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class JournalController extends Controller
{
    public function create()
    {
        $locations = Location::where('status', 'Active')->get();
        $shifts    = Shift::where('status', 'Active')->get();
        $user      = Auth::user();
        
        // Ambil semua group selain group user yang sedang login
        $groups    = Group::where('id', '!=', $user->group_id)->get();

        return view('satpam.journal_submission', compact('locations', 'shifts', 'groups'));
    }

    public function submitJournal(Request $request)
    {
        $request->validate([
            'lokasi_id'         => 'required|exists:locations,id',
            'shift_id'          => 'required|exists:shifts,id',
            'tanggal'           => 'required|date|before_or_equal:today',
            'next_shift'        => 'required|exists:groups,id',
            'laporan_kegiatan'  => 'required|string',
            'kejadian_temuan'   => 'required|string',
            'lembur'            => 'required|string|max:255',
            'proyek_vendor'     => 'required|string',
            'barang_inven'      => 'required|string',
            'info_tambahan'     => 'nullable|string',
            'files.*'           => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ], [
            'lokasi_id.required'        => 'Lokasi wajib dipilih!',
            'lokasi_id.exists'          => 'Lokasi tidak valid!',
            'shift_id.required'         => 'Shift saat ini wajib dipilih!',
            'shift_id.exists'           => 'Shift saat ini tidak valid!',
            'tanggal.required'          => 'Tanggal wajib diisi!',
            'tanggal.date'              => 'Format tanggal tidak valid!',
            'tanggal.before_or_equal'   => 'Tanggal tidak boleh lebih dari hari ini!',
            'next_shift.required'       => 'Shift berikutnya (Grup) wajib dipilih!',
            'next_shift.exists'         => 'Grup shift berikutnya tidak valid!',
            'laporan_kegiatan.required' => 'Laporan Kegiatan wajib diisi!',
            'kejadian_temuan.required'  => 'Kejadian/Temuan wajib diisi!',
            'lembur.required'           => 'Lembur wajib diisi!',
            'lembur.max'                => 'Lembur maksimal 255 karakter!',
            'proyek_vendor.required'    => 'Proyek/Vendor wajib diisi!',
            'barang_inven.required'     => 'Barang Inven wajib diisi!',
            'files.*.mimes'             => 'Format file tidak didukung. Gunakan PDF, DOC, DOCX, JPG, atau PNG!',
            'files.*.max'               => 'Ukuran file maksimal 10MB!',
        ]);

        $user     = Auth::user();
        $group_id = $user->group_id;

        // Cek duplikat awal (berdasarkan tanggal, lokasi, dan shift)
        $duplicate = Journal::whereDate('tanggal', $request->tanggal)
            ->where('lokasi_id', $request->lokasi_id)
            ->where('shift_id', $request->shift_id)
            ->exists();

        if ($duplicate) {
            $shiftName = Shift::find($request->shift_id)->nama_shift ?? 'terpilih';
            $lokasiName = Location::find($request->lokasi_id)->nama_lokasi ?? 'tercantum';
            return redirect()->back()
                ->withInput()
                ->with('error', "Journal untuk Lokasi: {$lokasiName} pada Shift: {$shiftName} tanggal " . Carbon::parse($request->tanggal)->format('d/m/Y') . " sudah pernah disubmit.");
        }

        DB::beginTransaction();
        try {
            $journal = Journal::create([
                'tanggal'          => $request->tanggal,
                'user_id'          => $user->id,
                'group_id'         => $group_id,
                'lokasi_id'        => $request->lokasi_id,
                'shift_id'         => $request->shift_id,
                'next_shift'       => $request->next_shift,
                'laporan_kegiatan' => $request->laporan_kegiatan,
                'kejadian_temuan'  => $request->kejadian_temuan,
                'lembur'           => $request->lembur,
                'proyek_vendor'    => $request->proyek_vendor,
                'barang_inven'     => $request->barang_inven,
                'info_tambahan'    => $request->info_tambahan,
                'status'           => 'Pending',
            ]);

            // Upload files jika ada
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    Upload::uploadFile($journal->id, $file);
                }
            }

            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Handle Race Condition (Unique Constraint Violation)
            // Error code 23000 is for integrity constraint violation, and message usually contains 'Duplicate entry' or constraint name
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal submit: Journal untuk grup Anda pada tanggal ini baru saja dikirim oleh anggota tim lain. Sistem mencegah duplikasi data.');
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan database: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        return redirect()->route('log-history')
            ->with('success', 'Journal berhasil disubmit!');
    }

    public function viewJournalDetail($id)
    {
        $journal = Journal::with(['user', 'group', 'uploads', 'location', 'shift', 'nextShift', 'updater', 'handover', 'approver'])->find($id);

        if (!$journal) {
            return response()->json(['success' => false, 'message' => 'Journal tidak ditemukan.']);
        }

        // Custom formatting for eager loaded next shift
        $journal->next_shift_rel = $journal->nextShift;

        // Relation mapping assuming relationships exist in User model for these Foreign Keys
        // If not, we will just use the IDs or manually fetch User::find() on the client mapping
        $journal->updater = \App\Models\User::find($journal->updated_by);
        $journal->handover = \App\Models\User::find($journal->handover_by);
        $journal->approver = \App\Models\User::find($journal->approved_by);

        return response()->json(['success' => true, 'data' => $journal]);
    }

    public function handoverApproval(Request $request, $id)
    {
        $journal = Journal::findOrFail($id);
        $user = Auth::user();

        // Security check: Only members of the next_shift group can handover
        if ($user->group_id !== $journal->next_shift) {
            return redirect()->back()->with('error', 'Anda tidak tergabung dalam grup yang ditugaskan untuk serah terima jurnal ini.');
        }

        // Security check: Only if current status is Pending
        if ($journal->status !== 'Pending') {
            return redirect()->back()->with('error', 'Jurnal ini tidak dalam status Pending.');
        }

        $journal->handoverApproval($user->id, 'Waiting');

        return redirect()->back()->with('success', 'Serah terima berhasil. Status jurnal kini Waiting konfirmasi PGA.');
    }

    public function edit(Journal $journal)
    {
        // Only members of the same group can edit, and only if pending or rejected
        if ($journal->group_id !== Auth::user()->group_id || !in_array($journal->status, ['Pending', 'Rejected'])) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengedit jurnal ini.');
        }

        $journal->load('uploads'); // Load existing uploads

        $locations = Location::where('status', 'Active')->get();
        $shifts    = Shift::where('status', 'Active')->get();
        $user      = Auth::user();
        
        // Exclude own team
        $groups    = Group::where('id', '!=', $user->group_id)->get();

        return view('satpam.journalEdit', compact('journal', 'locations', 'shifts', 'groups'));
    }

    public function update(Request $request, Journal $journal)
    {
        if ($journal->group_id !== Auth::user()->group_id || !in_array($journal->status, ['Pending', 'Rejected'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // Cek duplikat (berdasarkan tanggal, lokasi, dan shift), kecualikan jurnal ini sendiri
        $duplicate = Journal::where('id', '!=', $journal->id)
            ->whereDate('tanggal', $request->tanggal)
            ->where('lokasi_id', $request->lokasi_id)
            ->where('shift_id', $request->shift_id)
            ->exists();

        if ($duplicate) {
            $shiftName = Shift::find($request->shift_id)->nama_shift ?? 'terpilih';
            $lokasiName = Location::find($request->lokasi_id)->nama_lokasi ?? 'tercantum';
            return redirect()->back()
                ->withInput()
                ->with('error', "Gagal update: Journal untuk Lokasi: {$lokasiName} pada Shift: {$shiftName} tanggal " . Carbon::parse($request->tanggal)->format('d/m/Y') . " sudah ada di sistem.");
        }

        $request->validate([
            'lokasi_id'         => 'required|exists:locations,id',
            'shift_id'          => 'required|exists:shifts,id',
            'tanggal'           => 'required|date|before_or_equal:today',
            'next_shift'        => 'required|exists:groups,id',
            'laporan_kegiatan'  => 'required|string',
            'kejadian_temuan'   => 'required|string',
            'lembur'            => 'required|string|max:255',
            'proyek_vendor'     => 'required|string',
            'barang_inven'      => 'required|string',
            'info_tambahan'     => 'nullable|string',
            'files.*'           => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ], [
            'lokasi_id.required'        => 'Lokasi wajib dipilih!',
            'lokasi_id.exists'          => 'Lokasi tidak valid!',
            'shift_id.required'         => 'Shift saat ini wajib dipilih!',
            'shift_id.exists'           => 'Shift saat ini tidak valid!',
            'tanggal.required'          => 'Tanggal wajib diisi!',
            'tanggal.date'              => 'Format tanggal tidak valid!',
            'tanggal.before_or_equal'   => 'Tanggal tidak boleh lebih dari hari ini!',
            'next_shift.required'       => 'Shift berikutnya (Grup) wajib dipilih!',
            'next_shift.exists'         => 'Grup shift berikutnya tidak valid!',
            'laporan_kegiatan.required' => 'Laporan Kegiatan wajib diisi!',
            'kejadian_temuan.required'  => 'Kejadian/Temuan wajib diisi!',
            'lembur.required'           => 'Lembur wajib diisi!',
            'lembur.max'                => 'Lembur maksimal 255 karakter!',
            'proyek_vendor.required'    => 'Proyek/Vendor wajib diisi!',
            'barang_inven.required'     => 'Barang Inven wajib diisi!',
            'files.*.mimes'             => 'Format file tidak didukung. Gunakan PDF, DOC, DOCX, JPG, atau PNG!',
            'files.*.max'               => 'Ukuran file maksimal 10MB!',
        ]);

        DB::beginTransaction();
        try {
            $newStatus = $journal->status === 'Rejected' ? 'Waiting' : 'Pending';

            $journal->update([
                'tanggal'          => $request->tanggal,
                'lokasi_id'        => $request->lokasi_id,
                'shift_id'         => $request->shift_id,
                'next_shift'       => $request->next_shift,
                'laporan_kegiatan' => $request->laporan_kegiatan,
                'kejadian_temuan'  => $request->kejadian_temuan,
                'lembur'           => $request->lembur,
                'proyek_vendor'    => $request->proyek_vendor,
                'barang_inven'     => $request->barang_inven,
                'info_tambahan'    => $request->info_tambahan,
                'updated_by'       => Auth::id(),
                'status'           => $newStatus, // reset status based on previous state
            ]);

            // Handle deletions of existing files flagged by user
            if ($request->filled('delete_upload_ids')) {
                $deleteIds = explode(',', $request->delete_upload_ids);
                $uploadsToDelete = \App\Models\Upload::whereIn('id', $deleteIds)
                    ->where('journal_id', $journal->id)
                    ->get();
                foreach ($uploadsToDelete as $upload) {
                    $upload->deleteFile();
                }
            }

            // Add newly uploaded files
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    Upload::uploadFile($journal->id, $file);
                }
            }

            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Handle Race Condition (Unique Constraint Violation)
            if ($e->getCode() == '23000') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal update: Journal untuk grup Anda pada tanggal ini baru saja diperbarui oleh anggota tim lain. Sistem mencegah duplikasi data.');
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan database: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        return redirect()->route('log-history')->with('success', 'Journal berhasil diperbarui.');
    }

    public function downloadPDF($id)
    {
        $journal = Journal::with(['user', 'group', 'location', 'shift', 'nextShift', 'updater', 'handover', 'approver'])->findOrFail($id);
        
        $journal->updater = \App\Models\User::find($journal->updated_by);
        $journal->handover = \App\Models\User::find($journal->handover_by);
        $journal->approver = \App\Models\User::find($journal->approved_by);

        // Retrieve group members for current group + next shift
        $currentGroupMembers = \App\Models\User::where('group_id', $journal->group_id)->pluck('nama')->toArray();
        $nextShiftMembers = \App\Models\User::where('group_id', $journal->next_shift)->pluck('nama')->toArray();

        $data = [
            'journal'             => $journal,
            'currentGroupMembers' => implode(', ', $currentGroupMembers),
            'nextShiftMembers'    => implode(', ', $nextShiftMembers),
            'uploads'             => $journal->uploads()->get(),
        ];

        // Ensure you create resources/views/pdf_journal.blade.php
        $pdf = Pdf::loadView('pdf_journal', $data);
        
        return $pdf->download('Journal-Keamanan-'. Carbon::parse($journal->tanggal)->format('Ymd') . '-' . Str::slug($journal->user->nama ?? 'unknown') . '.pdf');
    }

    public function finalApproval(Request $request, $id)
    {
        $journal = Journal::findOrFail($id);

        // Security check: Only if current status is Waiting
        if ($journal->status !== 'Waiting') {
            return redirect()->back()->with('error', 'Jurnal ini tidak dalam status Waiting. Data mungkin telah berubah.');
        }

        $request->validate([
            'status'  => 'required|in:Approved,Rejected',
            'catatan' => 'required_if:status,Rejected|string',
        ], [
            'catatan.required_if' => 'Catatan wajib diisi jika jurnal ditolak.',
        ]);

        $status = $request->input('status');
        
        $journal->catatan = $request->input('catatan');
        $journal->save();

        $journal->finalApproval(Auth::id(), $status);

        return redirect()->back()->with('success', 'Jurnal berhasil ' . ($status === 'Approved' ? 'disetujui' : 'ditolak') . '.');
    }

    public function deleteJournal($id)
    {
        // Security check: Only PGA can delete
        if (Auth::user()->role !== 'PGA') {
            return redirect()->back()->with('error', 'Hanya PGA yang dapat menghapus jurnal.');
        }

        $journal = Journal::with('uploads')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete associated files
            foreach ($journal->uploads as $upload) {
                $upload->deleteFile(); // This model method already handles storage & DB deletion
            }

            // Delete the journal itself
            $journal->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus jurnal: ' . $e->getMessage());
        }

        return redirect()->route('log-history')->with('success', 'Jurnal berhasil dihapus secara permanen.');
    }
}
