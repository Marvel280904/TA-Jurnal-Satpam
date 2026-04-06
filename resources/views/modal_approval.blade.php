<!-- Modal Approval (Approve/Reject) -->
<div id="approvalModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2" id="approvalModalTitle">Confirm Action</h3>
            <p class="text-gray-600 text-sm mb-6" id="approvalModalMessage">Are you sure you want to proceed?</p>
            
            <form id="approvalForm" method="POST" action="">
                @csrf
                <input type="hidden" name="status" id="approvalStatusInput" value="">
                <input type="hidden" name="journal_id" id="approvalJournalIdInput" value="">

                @if ($errors->any() && (old('status') || old('catatan') || old('journal_id')))
                    <div id="alertApprovalError" class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded relative shadow-sm">
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
                @endif
                
                <div id="catatanContainer" class="hidden mb-4">
                    <label for="catatanInput" class="block text-sm font-bold text-gray-700 mb-1">Catatan Penolakan <span class="text-red-500">*</span></label>
                    <textarea  name="catatan" id="catatanInput" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all text-sm" placeholder="Jelaskan alasan kenapa jurnal ini ditolak..."></textarea>
                </div>
                
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closeApprovalModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-bold rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="approvalSubmitBtn" class="px-4 py-2 text-white text-sm font-bold rounded-xl transition-colors">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-open modal jika ada error validasi
    @if($errors->any() && (old('status') || old('catatan') || old('journal_id')))
        window.addEventListener('load', () => {
            const jId = '{{ old('journal_id') }}';
            const action = '{{ old('status') }}';
            if (jId && action) {
                openApprovalModal(jId, action);
                
                // Re-populate catatan if available
                const oldCatatan = `{!! addslashes(old('catatan', '')) !!}`;
                if(oldCatatan) {
                    document.getElementById('catatanInput').value = oldCatatan;
                }
            }
        });
    @endif

    function openApprovalModal(journalId, action) {
        const modal = document.getElementById('approvalModal');
        const form = document.getElementById('approvalForm');
        const statusInput = document.getElementById('approvalStatusInput');
        const title = document.getElementById('approvalModalTitle');
        const submitBtn = document.getElementById('approvalSubmitBtn');
        const catatanContainer = document.getElementById('catatanContainer');
        const catatanInput = document.getElementById('catatanInput');
        
        // Let's also grab message since it was missing in the JS block you provided recently
        const message = document.getElementById('approvalModalMessage');

        // Set action route dynamic, replace PLACEHOLDER with id
        let routeTemplate = "{{ route('pga.journal.approve', ['id' => 'JDID']) }}";
        form.action = routeTemplate.replace('JDID', journalId);
        
        statusInput.value = action;
        const journalIdInput = document.getElementById('approvalJournalIdInput');
        if (journalIdInput) journalIdInput.value = journalId;
        catatanInput.value = ''; // Reset catatan

        if (action === 'Approved') {
            title.textContent = 'Approve Journal';
            message.textContent = 'Apakah anda yakin ingin menyetujui jurnal ini?';
            submitBtn.className = 'px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl transition-colors';
            submitBtn.innerHTML = 'Confirm Approve';
            catatanContainer.classList.add('hidden');
            catatanInput.removeAttribute('required');
        } else {
            title.textContent = 'Reject Journal';
            message.textContent = 'Apakah anda yakin ingin menolak jurnal ini? Tolong berikan alasan penolakan';
            submitBtn.className = 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-colors';
            submitBtn.innerHTML = 'Confirm Reject';
            catatanContainer.classList.remove('hidden');
            catatanInput.setAttribute('required', 'required');
        }

        modal.classList.remove('hidden');
    }

    function closeApprovalModal() {
        document.getElementById('approvalModal').classList.add('hidden');
    }
</script>
