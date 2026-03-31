{{-- Modal: Add / Edit Group (Unified) --}}
{{-- Requires Alpine state di parent x-data:           --}}
{{--   modalGroup       : false                        --}}
{{--   editGroupId      : null   → null = Add, id = Edit   --}}
{{--   editGroupName    : ''     → nama grup               --}}
{{--   editGroupMembers : []     → array of selected user IDs --}}

<div id="modalGroup" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">

        <h3 id="modalGroupTitle" class="text-xl font-bold mb-5 text-gray-800">Add Group</h3>

        @if ($errors->any() && (old('nama_grup') || old('satpam_ids')))
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
                    const modal = document.getElementById('modalGroup');
                    if (!modal) return;
                    const alert = modal.querySelector('#alertError');
                    if (alert) alert.remove();
                }, 2000);
            </script>
        @endif

        <form id="formGroup" action="" method="POST">
            @csrf
            <input type="hidden" name="group_id" id="inputGroupId" value="{{ old('group_id') }}">
            <input type="hidden" name="_method" id="methodGroup" value="POST">

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Grup <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="inputNamaGrup"
                    name="nama_grup"
                    value="{{ old('nama_grup') }}"
                    placeholder="Masukkan nama grup"
                    class="w-full p-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Anggota Satpam
                </label>
                {{-- Scrollable list of Satpam members --}}
                <div class="border border-gray-300 rounded-lg max-h-48 overflow-y-auto divide-y divide-gray-100 relative">
                    <p id="emptySatpamMessage" class="hidden text-sm text-gray-400 px-4 py-5 text-center">Tidak ada user Satpam yang tersedia.</p>
                    @foreach($satpam_users as $satpam)
                        <label class="satpam-label flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition" data-group-id="{{ $satpam->group_id }}">
                            <input 
                                type="checkbox" 
                                name="satpam_ids[]" 
                                value="{{ $satpam->id }}" 
                                {{ in_array($satpam->id, old('satpam_ids', [])) ? 'checked' : '' }}
                                class="satpam-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-800 font-medium">{{ $satpam->nama }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button
                    type="button"
                    onclick="closeModalGroup()"
                    class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
