<div id="modalUser" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">

        <div class="flex justify-between items-center mb-5">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Add User</h3>
        </div>

        @if ($errors->any() && (old('nama') || old('username') || old('role')))
            <div id="alertError" class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded relative shadow-sm">
                <div class="flex justify-between items-start">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-800">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
            </div>

            <script>
                setTimeout(() => {
                    const modal = document.getElementById('modalUser');
                    if (!modal) return;
                    const alert = modal.querySelector('#alertError');
                    if (alert) alert.remove();
                }, 2000);
            </script>
        @endif

        <form id="formUser" action="" method="POST">
            @csrf
            <input type="hidden" name="user_id" id="inputUserId" value="{{ old('user_id') }}">
            <input type="hidden" name="_method" id="methodField" value="POST">

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="inputNama" value="{{ old('nama') }}" placeholder="Masukan Nama Lengkap" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm" required>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" id="inputUsername" value="{{ old('username') }}" placeholder="Masukan Username" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm" required>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password <span id="passwordRequired" class="text-red-500">*</span> <span id="passwordMinHint" class="text-gray-500 font-normal text-xs">(Minimal 6 Karakter)</span>
                    <span id="passwordNote" class="text-gray-400 font-normal text-xs hidden">(kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" name="password" id="passwordInput" placeholder="Masukan Password" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm">
            </div>

            <div class="mb-5" id="roleContainer">
                <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                <select name="role" id="inputRole" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm" required>
                    <option value="Satpam" {{ old('role') == 'Satpam' ? 'selected' : '' }}>Satpam</option>
                    <option value="PGA" {{ old('role') == 'PGA' ? 'selected' : '' }}>PGA</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModalUser()" class="px-4 py-2 text-sm rounded-lg bg-gray-200 hover:bg-gray-300 text-black transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">Save</button>
            </div>
        </form>
    </div>
</div>