<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class GroupManagementController extends Controller
{
    // ─── viewGroup ───────────────────────────────────────────────────────────────

    public function viewGroup()
    {
        $groups = Group::with('users')->orderBy('nama_grup')->get();
        // Ambil semua user Satpam untuk checkbox di modal (add/edit)
        $satpam_users = User::where('role', 'Satpam')->orderBy('nama')->get();
        
        return view('admin.group_management', compact('groups', 'satpam_users'));
    }

    // ─── Add ─────────────────────────────────────────────────────────────────

    public function addGroup(Request $request)
    {
        $request->validate([
            'nama_grup'  => 'required|string|max:255|unique:groups,nama_grup',
            'satpam_ids' => 'nullable|array',
            'satpam_ids.*' => 'exists:users,id'
        ], [
            'nama_grup.required' => 'Nama Group wajib diisi!',
            'nama_grup.max' => 'Nama Group maksimal 255 karakter!',
            'nama_grup.unique' => 'Nama Group sudah ada!',
            'satpam_ids.exists' => 'Satpam tidak valid!',
        ]);

        $group = Group::create([
            'nama_grup' => $request->nama_grup,
        ]);

        // Assign user ke grup ini
        if ($request->has('satpam_ids')) {
            User::whereIn('id', $request->satpam_ids)->update(['group_id' => $group->id]);
        }

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Create',
            'deskripsi' => "Admin membuat grup baru: {$group->nama_grup}",
        ]);

        return redirect()->route('admin.group-management')->with('success', 'Grup berhasil ditambahkan!');
    }

    // ─── Edit ────────────────────────────────────────────────────────────────

    public function editGroup(Request $request, Group $group)
    {
        $request->validate([
            'nama_grup'  => 'required|string|max:255|unique:groups,nama_grup,' . $group->id,
            'satpam_ids' => 'nullable|array',
            'satpam_ids.*' => 'exists:users,id'
        ], [
            'nama_grup.required' => 'Nama Group wajib diisi!',
            'nama_grup.max' => 'Nama Group maksimal 255 karakter!',
            'nama_grup.unique' => 'Nama Group sudah ada!',
            'satpam_ids.exists' => 'Satpam tidak valid!',
        ]);

        $group->update([
            'nama_grup' => $request->nama_grup,
        ]);

        // Reset semua member grup ini agar yang di-uncheck terhapus dari grup
        User::where('group_id', $group->id)->update(['group_id' => null]);

        // Re-assign member yang di-check
        if ($request->has('satpam_ids')) {
            User::whereIn('id', $request->satpam_ids)->update(['group_id' => $group->id]);
        }

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Update',
            'deskripsi' => "Admin mengubah data grup: {$group->nama_grup}",
        ]);

        return redirect()->route('admin.group-management')->with('success', 'Grup berhasil diperbarui!');
    }

    // ─── Delete ──────────────────────────────────────────────────────────────

    public function deleteGroup(Group $group)
    {
        $nama = $group->nama_grup;
        
        // Reset member yang ada di grup ini menjadi null
        User::where('group_id', $group->id)->update(['group_id' => null]);
        
        $group->delete();

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Delete',
            'deskripsi' => "Admin menghapus grup: {$nama}",
        ]);

        return redirect()->route('admin.group-management')->with('success', 'Grup berhasil dihapus!');
    }
}
