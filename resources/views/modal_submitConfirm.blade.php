<!-- Modal Submit Confirmation -->
<div id="submitConfirmModal" class="fixed inset-0 z-[60] hidden bg-black/50 backdrop-blur-sm flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                <i class="bi bi-question-circle text-blue-500"></i> Konfirmasi Jurnal
            </h3>
            <p class="text-gray-600 text-sm mb-4">
                Apakah anda yakin ingin mengumpulkan jurnal tanggal <span id="confirmDate" class="font-bold text-gray-800"></span> untuk lokasi <span id="confirmLocation" class="font-bold text-gray-800"></span> - shift <span id="confirmShift" class="font-bold text-gray-800"></span>?
            </p>
            <div class="bg-red-50 border border-red-100 p-3 rounded-lg mb-6">
                <p class="text-xs text-red-800 flex items-start gap-2">
                    <i class="bi bi-info-circle-fill mt-0.5"></i>
                    <span>Mohon di cek sekali lagi apakah data yang diisi sudah benar agar tidak ada kesalahan.</span>
                </p>
            </div>
            
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeSubmitConfirmModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-bold rounded-xl transition-colors">
                    Kembali Periksa
                </button>
                <button type="button" onclick="executeSubmitForm()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-colors shadow-md shadow-blue-500/30">
                    Ya, Kumpulkan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openSubmitConfirmModal() {
        const form = document.getElementById('journal-form');

        // Get values from form
        const dateInput = document.getElementById('tanggal').value;
        const locationSelect = document.getElementById('lokasi_id');
        const shiftSelect = document.getElementById('shift_id');
        
        let formattedDate = dateInput;
        if(dateInput) {
            const d = new Date(dateInput);
            // Format to DD/MM/YYYY optionally, or standard local
            formattedDate = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        } else {
            formattedDate = "[-]";
        }

        const locationText = locationSelect.options[locationSelect.selectedIndex] ? locationSelect.options[locationSelect.selectedIndex].text : '[-]';
        const shiftText = shiftSelect.options[shiftSelect.selectedIndex] ? shiftSelect.options[shiftSelect.selectedIndex].text : '[-]';

        document.getElementById('confirmDate').textContent = formattedDate;
        document.getElementById('confirmLocation').textContent = locationText;
        document.getElementById('confirmShift').textContent = shiftText;

        document.getElementById('submitConfirmModal').classList.remove('hidden');
    }

    function closeSubmitConfirmModal() {
        document.getElementById('submitConfirmModal').classList.add('hidden');
    }

    function executeSubmitForm() {
        // Prevent double clicking
        const btn = event.currentTarget;
        btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Memproses...';
        btn.disabled = true;
        
        document.getElementById('journal-form').submit();
    }
</script>
