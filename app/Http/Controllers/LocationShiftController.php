<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Shift;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class LocationShiftController extends Controller
{
    // ─── Index ───────────────────────────────────────────────────────────────

    public function view()
    {
        $locations = Location::orderBy('created_at', 'desc')->get();
        $shifts    = Shift::orderBy('created_at', 'desc')->get();

        return view('admin.location_shift', compact('locations', 'shifts'));
    }

    // ─── Location ─────────────────────────────────────────────────────────────

    public function addLocation(Request $request)
    {
        $request->validate([
            'nama_lokasi'   => 'required|string|max:255',
            'alamat_lokasi' => 'required|string',
        ], [
            'nama_lokasi.required'   => 'Nama lokasi wajib diisi.',
            'nama_lokasi.max'   => 'Nama lokasi maksimal 255 karakter.',
            'alamat_lokasi.required' => 'Alamat lokasi wajib diisi.',
        ]);

        // Cek duplikasi: nama dan alamat tidak boleh sama
        $exists = Location::where('nama_lokasi', $request->nama_lokasi)
            ->where('alamat_lokasi', $request->alamat_lokasi)
            ->exists();

        if ($exists) {
            return back()->withErrors(['nama_lokasi' => 'Lokasi dengan nama dan alamat tersebut sudah ada.'])->withInput();
        }

        $location = Location::create([
            'nama_lokasi'   => $request->nama_lokasi,
            'alamat_lokasi' => $request->alamat_lokasi,
        ]);

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Create',
            'deskripsi' => "Admin menambah lokasi: {$location->nama_lokasi}",
        ]);

        return redirect()->route('admin.location-shift')->with('success', 'Lokasi berhasil ditambahkan!');
    }

    public function editLocation(Request $request, Location $location)
    {
        $request->validate([
            'nama_lokasi'   => 'required|string|max:255',
            'alamat_lokasi' => 'required|string',
        ], [
            'nama_lokasi.required'   => 'Nama lokasi wajib diisi.',
            'nama_lokasi.max'   => 'Nama lokasi maksimal 255 karakter.',
            'alamat_lokasi.required' => 'Alamat lokasi wajib diisi.',
        ]);

        // Cek duplikasi: nama dan alamat tidak boleh sama (kecuali data ini sendiri)
        $exists = Location::where('nama_lokasi', $request->nama_lokasi)
            ->where('alamat_lokasi', $request->alamat_lokasi)
            ->where('id', '!=', $location->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['nama_lokasi' => 'Lokasi dengan nama dan alamat tersebut sudah ada.'])->withInput();
        }

        $location->update([
            'nama_lokasi'   => $request->nama_lokasi,
            'alamat_lokasi' => $request->alamat_lokasi,
        ]);

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Update',
            'deskripsi' => "Admin mengubah lokasi: {$location->nama_lokasi}",
        ]);

        return redirect()->route('admin.location-shift')->with('success', 'Lokasi berhasil diperbarui!');
    }

    public function updateLocationStatus(Location $location)
    {
        $newStatus = $location->status === 'Active' ? 'Inactive' : 'Active';
        $location->update(['status' => $newStatus]);

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Change Status',
            'deskripsi' => "Admin mengubah status lokasi '{$location->nama_lokasi}' menjadi {$newStatus}",
        ]);

        return redirect()->route('admin.location-shift')->with('success', "Status lokasi diubah menjadi {$newStatus}!");
    }

    public function deleteLocation(Location $location)
    {
        $nama = $location->nama_lokasi;
        $location->delete();

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Delete',
            'deskripsi' => "Admin menghapus lokasi: {$nama}",
        ]);

        return redirect()->route('admin.location-shift')->with('success', 'Lokasi berhasil dihapus!');
    }

    // ─── Shift ────────────────────────────────────────────────────────────────

    public function addShift(Request $request)
    {
        $request->validate([
            'nama_shift'    => 'required|string|max:255',
            'mulai_shift'   => 'required',
            'selesai_shift' => 'required',
        ], [
            'nama_shift.required'    => 'Nama shift wajib diisi.',
            'nama_shift.max'   => 'Nama shift maksimal 255 karakter.',
            'mulai_shift.required'   => 'Jam mulai shift wajib diisi.',
            'selesai_shift.required' => 'Jam selesai shift wajib diisi.',
        ]);

        // Cek duplikasi: nama, mulai, dan selesai shift tidak boleh sama
        $exists = Shift::where('nama_shift', $request->nama_shift)
            ->where('mulai_shift', $request->mulai_shift)
            ->where('selesai_shift', $request->selesai_shift)
            ->exists();

        if ($exists) {
            return back()->withErrors(['nama_shift' => 'Shift dengan nama, jam mulai, dan jam selesai tersebut sudah ada.'])->withInput();
        }

        $shift = Shift::create([
            'nama_shift'    => $request->nama_shift,
            'mulai_shift'   => $request->mulai_shift,
            'selesai_shift' => $request->selesai_shift,
        ]);

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Create',
            'deskripsi' => "Admin menambah shift: {$shift->nama_shift}",
        ]);

        return redirect()->route('admin.location-shift', ['tab' => 'shifts'])->with('success', 'Shift berhasil ditambahkan!');
    }

    public function editShift(Request $request, Shift $shift)
    {
        $request->validate([
            'nama_shift'    => 'required|string|max:255',
            'mulai_shift'   => 'required',
            'selesai_shift' => 'required',
        ], [
            'nama_shift.required'    => 'Nama shift wajib diisi.',
            'nama_shift.max'   => 'Nama shift maksimal 255 karakter.',
            'mulai_shift.required'   => 'Jam mulai shift wajib diisi.',
            'selesai_shift.required' => 'Jam selesai shift wajib diisi.',
        ]);

        // Cek duplikasi: nama, mulai, dan selesai shift tidak boleh sama (kecuali data ini sendiri)
        $exists = Shift::where('nama_shift', $request->nama_shift)
            ->where('mulai_shift', $request->mulai_shift)
            ->where('selesai_shift', $request->selesai_shift)
            ->where('id', '!=', $shift->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['nama_shift' => 'Shift dengan nama, jam mulai, dan jam selesai tersebut sudah ada.'])->withInput();
        }

        $shift->update([
            'nama_shift'    => $request->nama_shift,
            'mulai_shift'   => $request->mulai_shift,
            'selesai_shift' => $request->selesai_shift,
        ]);

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Update',
            'deskripsi' => "Admin mengubah shift: {$shift->nama_shift}",
        ]);

        return redirect()->route('admin.location-shift', ['tab' => 'shifts'])->with('success', 'Shift berhasil diperbarui!');
    }

    public function updateShiftStatus(Shift $shift)
    {
        $newStatus = $shift->status === 'Active' ? 'Inactive' : 'Active';
        $shift->update(['status' => $newStatus]);

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Change Status',
            'deskripsi' => "Admin mengubah status shift '{$shift->nama_shift}' menjadi {$newStatus}",
        ]);

        return redirect()->route('admin.location-shift', ['tab' => 'shifts'])->with('success', "Status shift diubah menjadi {$newStatus}!");
    }

    public function deleteShift(Shift $shift)
    {
        $nama = $shift->nama_shift;
        $shift->delete();

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Delete',
            'deskripsi' => "Admin menghapus shift: {$nama}",
        ]);

        return redirect()->route('admin.location-shift', ['tab' => 'shifts'])->with('success', 'Shift berhasil dihapus!');
    }
}
