{{-- Modal: Add / Edit Location (Unified) --}}
{{-- Requires Alpine state: modalLoc, editLocId, editLocName, editLocAddress --}}
{{-- Add mode  : modalLoc = true (editLocId stays null)                      --}}
{{-- Edit mode : modalLoc = true, editLocId = {id}, editLocName, editLocAddress set --}}

<div id="modalLoc" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">

        {{-- Title berubah sesuai mode --}}
        <h3 id="modalLocTitle" class="text-xl font-bold mb-5 text-gray-800">Add Location</h3>
        
        @if ($errors->any() && (old('nama_lokasi') || old('alamat_lokasi') || old('location_id')))
            <div id="alertError" class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded relative shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-800">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
            </div>
            
            <script>
                setTimeout(() => {
                    const modal = document.getElementById('modalLoc');
                    if (!modal) return;
                    const alert = modal.querySelector('#alertError');
                    if (alert) alert.remove();
                }, 3000);
            </script>
        @endif

        {{-- Form action & _method berubah sesuai mode --}}
        <form id="formLoc" action="" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodLoc" value="POST">
            <input type="hidden" name="location_id" id="inputLocId" value="{{ old('location_id') }}">

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Lokasi <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="inputLocName"
                    name="nama_lokasi"
                    value="{{ old('nama_lokasi') }}"
                    placeholder="Masukkan nama lokasi"
                    class="w-full p-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    required>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Alamat <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="inputLocAddress"
                    name="alamat_lokasi"
                    placeholder="Masukkan alamat lokasi"
                    required
                    class="w-full p-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    rows="3">{{ old('alamat_lokasi') }}</textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button
                    type="button"
                    onclick="closeModalLoc()"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-200 hover:bg-gray-300 text-black transition">
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
