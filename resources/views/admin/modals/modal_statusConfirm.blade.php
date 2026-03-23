{{-- Modal: Status Confirmation (Vanilla JS Reusable) --}}
<div id="modalStatusConfirm" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 text-center">

        {{-- Icon --}}
        <div id="statusIconContainer" class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4">
            <i id="statusIcon" class="bi text-2xl"></i>
        </div>

        {{-- Judul Dinamis --}}
        <h3 id="statusConfirmTitle" class="text-lg font-bold text-gray-800 mb-1">Ubah Status?</h3>

        {{-- Deskripsi Dinamis --}}
        <p class="text-sm text-gray-500 mb-5">
            Anda yakin ingin mengubah status <span id="statusEntityLabel"></span> 
            <span id="statusNameLabel" class="font-semibold text-gray-700"></span> menjadi 
            <span id="statusNextLabel" class="font-bold"></span>?
        </p>

        {{-- Form Action Dinamis --}}
        <form id="formStatusConfirm" action="" method="POST" class="flex justify-center gap-3">
            @csrf
            @method('PATCH')
            
            <button type="button" onclick="closeModalStatusConfirm()"
                class="px-5 py-2 text-sm border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">
                Batal
            </button>
            <button type="submit" id="btnStatusConfirm"
                class="px-5 py-2 text-sm text-white font-semibold rounded-lg transition">
                Ya, Ubah
            </button>
        </form>
    </div>
</div>
