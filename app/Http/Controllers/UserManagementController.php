<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    // ─── viewUser ───────────────────────────────────────────────────────────────

    public function viewUser()
    {
        $users = User::orderBy('role')->get();
        return view('admin.user_management', compact('users'));
    }

    // ─── Add ─────────────────────────────────────────────────────────────────

    public function addUser(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255|unique:users,nama',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:Satpam,PGA',
        ], [
            'nama.required' => 'Nama wajib diisi!',
            'nama.max' => 'Nama maksimal 255 karakter!',
            'nama.unique' => 'Nama sudah digunakan!',
            'username.required' => 'Username wajib diisi!',
            'username.max' => 'Username maksimal 255 karakter!',
            'username.unique' => 'Username sudah ada!',
            'password.min' => 'Password minimal 6 karakter!',
            'role.required' => 'Role wajib diisi!',
            'role.in' => 'Role tidak valid!',
        ]);

        $user = User::create([
            'nama'     => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'group_id' => null,
        ]);

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Create',
            'deskripsi' => "Admin menambah user baru: {$user->username} ({$user->role})",
        ]);

        return redirect()->route('admin.user-management')->with('success', 'User berhasil ditambahkan!');
    }

    // ─── Edit ────────────────────────────────────────────────────────────────

    public function editUser(Request $request, User $user)
    {
        $request->validate([
            'nama'     => 'required|string|max:255|unique:users,nama,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role'     => 'required|in:Satpam,PGA',
        ], [
            'nama.required' => 'Nama wajib diisi!',
            'nama.max' => 'Nama maksimal 255 karakter!',
            'nama.unique' => 'Nama sudah digunakan!',
            'username.required' => 'Username wajib diisi!',
            'username.max' => 'Username maksimal 255 karakter!',
            'username.unique' => 'Username sudah ada!',
            'password.min' => 'Password minimal 6 karakter!',
            'role.required' => 'Role wajib diisi!',
            'role.in' => 'Role tidak valid!',
        ]);

        $data = [
            'nama'     => $request->nama,
            'username' => $request->username,
            'role'     => $request->role,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Update',
            'deskripsi' => "Admin mengubah data user: {$user->username}",
        ]);

        return redirect()->route('admin.user-management')->with('success', 'User berhasil diperbarui!');
    }

    // ─── Delete ──────────────────────────────────────────────────────────────

    public function deleteUser(User $user)
    {
        // Cegah hapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.user-management')->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $nama = $user->nama;
        $user->delete();

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Delete',
            'deskripsi' => "Admin menghapus user: {$nama}",
        ]);

        return redirect()->route('admin.user-management')->with('success', 'User berhasil dihapus!');
    }

    // ─── Update Status ───────────────────────────────────────────────────────

    public function updateUserStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.user-management')->with('error', 'Tidak dapat mengubah status akun sendiri!');
        }

        $user->status = $user->status === 'Active' ? 'Inactive' : 'Active';
        $user->save();

        SystemLog::recordLog([
            'user_id'   => auth()->id(),
            'aksi'      => 'Update',
            'deskripsi' => "Admin mengubah status user: {$user->username} menjadi {$user->status}",
        ]);

        return redirect()->route('admin.user-management')->with('success', "Status user diubah menjadi {$user->status}!");
    }
}
