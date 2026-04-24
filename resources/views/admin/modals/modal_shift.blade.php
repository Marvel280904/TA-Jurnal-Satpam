{{-- Modal: Add / Edit Shift (Unified) --}}
{{-- Requires Alpine state: modalShift, editShiftId, editShiftName, editShiftStart, editShiftEnd --}}
{{-- Add mode  : modalShift = true (editShiftId stays null)                                     --}}
{{-- Edit mode : modalShift = true, editShiftId = {id}, editShiftName, Start, End set           --}}

<div id="modalShift" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">

        {{-- Title berubah sesuai mode --}}
        <h3 id="modalShiftTitle" class="text-xl font-bold mb-5 text-gray-800">Add Shift</h3>

        @if ($errors->any() && (old('nama_shift') || old('mulai_shift') || old('shift_id')))
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
                    const modal = document.getElementById('modalShift');
                    if (!modal) return;
                    const alert = modal.querySelector('#alertError');
                    if (alert) alert.remove();
                }, 3000);
            </script>
        @endif

        {{-- Form action & _method berubah sesuai mode --}}
        <form id="formShift" action="" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodShift" value="POST">
            <input type="hidden" name="shift_id" id="inputShiftId" value="{{ old('shift_id') }}">

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Shift <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="inputShiftName"
                    name="nama_shift"
                    value="{{ old('nama_shift') }}"
                    placeholder="Contoh: Shift Pagi"
                    class="w-full p-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    required>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Mulai <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="time"
                        id="inputShiftStart"
                        name="mulai_shift"
                        value="{{ old('mulai_shift') }}"
                        class="w-full p-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Selesai <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="time"
                        id="inputShiftEnd"
                        name="selesai_shift"
                        value="{{ old('selesai_shift') }}"
                        class="w-full p-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        required>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button
                    type="button"
                    onclick="closeModalShift()"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-200 hover:bg-gray-300 text-black">
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
