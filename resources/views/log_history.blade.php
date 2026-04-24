@extends('layouts.app')
@section('title', 'Log History')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Log History</h1>
            <p class="text-gray-500">View your journal submission history</p>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-4">All Journal Submissions</h2>
        
        <!-- Search & Filter Tab -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="relative w-full md:w-1/2">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchInput" placeholder="Cari tanggal, user, grup, lokasi, atau shift" class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-gray-100">
            </div>
            
            <select id="statusFilter" class="w-full md:w-48 pl-4 pr-8 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-gray-100">
                <option value="All">All Status</option>
                <option value="Pending">Pending</option>
                <option value="Waiting">Waiting</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="journalTable">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-3 px-4 text-sm font-bold text-gray-700">Date</th>
                        <th class="py-3 px-4 text-sm font-bold text-gray-700">User Name</th>
                        <th class="py-3 px-4 text-sm font-bold text-gray-700">Group</th>
                        <th class="py-3 px-4 text-sm font-bold text-gray-700">Location</th>
                        <th class="py-3 px-4 text-sm font-bold text-gray-700">Shift</th>
                        <th class="py-3 px-4 text-sm font-bold text-gray-700">Status</th>
                        <th class="py-3 px-4 text-sm font-bold text-gray-700 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($journals as $journal)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors journal-row text-black" 
                            data-search="{{ strtolower(\Carbon\Carbon::parse($journal->tanggal)->translatedFormat('d F Y')) }} {{ strtolower($journal->user->nama ?? '') }} {{ strtolower($journal->group->nama_grup ?? '') }} {{ strtolower($journal->location->nama_lokasi ?? '') }} {{ strtolower($journal->shift->nama_shift ?? '') }}"
                            data-status="{{ $journal->status }}">
                            <td class="py-3 px-4 text-sm">{{ \Carbon\Carbon::parse($journal->tanggal)->translatedFormat('d F Y') }}</td>
                            <td class="py-3 px-4 text-sm font-medium">{{ $journal->user->nama ?? '-' }}</td>
                            <td class="py-3 px-4 text-sm">{{ $journal->group->nama_grup ?? '-' }}</td>
                            <td class="py-3 px-4 text-sm">{{ $journal->location->nama_lokasi ?? '-' }}</td>
                            <td class="py-3 px-4 text-sm">{{ $journal->shift->nama_shift ?? '-' }}</td>
                            <td class="py-3 px-4 text-sm">
                                @if($journal->status === 'Pending')
                                    @if(auth()->user()->group_id === $journal->next_shift)
                                        <button onclick="openHandoverModal({{ $journal->id }})" class="px-3 py-1 bg-gray-100 text-gray-700 font-bold rounded-full hover:bg-gray-200 transition">Pending (Handover)</button>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 font-bold rounded-full">Pending</span>
                                    @endif
                                @elseif($journal->status === 'Waiting')
                                    @if(auth()->user()->role === 'PGA')
                                        <button onclick="openFinalApprovalModal({{ $journal->id }}, '{{ \Carbon\Carbon::parse($journal->tanggal)->translatedFormat('d F Y') }}', '{{ $journal->location->nama_lokasi ?? '-' }}', '{{ $journal->shift->nama_shift ?? '-' }}')" 
                                            class="px-3 py-1 bg-yellow-100 text-yellow-700 font-bold rounded-full hover:bg-yellow-200 transition">
                                            Waiting (Approval)
                                        </button>
                                    @else
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 font-bold rounded-full">Waiting</span>
                                    @endif
                                @elseif($journal->status === 'Approved')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 font-bold rounded-full">Approved</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-700 font-bold rounded-full">Rejected</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center space-x-2">
                                <button onclick="openViewModal({{ $journal->id }})" class="text-blue-500 hover:text-blue-700 transition" title="View Details">
                                    <i class="bi bi-eye text-lg"></i>
                                </button>
                                @if(($journal->status === 'Pending' || $journal->status === 'Rejected') && $journal->group_id === auth()->user()->group_id)
                                <a href="{{ route('satpam.journal.edit', $journal->id) }}" class="text-yellow-500 hover:text-yellow-700 transition" title="Edit Journal">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </a>
                                @endif
                                <a href="{{ route('journal.download', $journal->id) }}" class="text-red-500 hover:text-red-700 transition" title="Download PDF">
                                    <i class="bi bi-file-earmark-pdf text-lg"></i>
                                </a>
                                {{-- @if(auth()->user()->role === 'PGA')
                                <button onclick="openDeleteModal({{ $journal->id }}, '{{ \Carbon\Carbon::parse($journal->tanggal)->translatedFormat('d F Y') }}', '{{ $journal->location->nama_lokasi ?? '-' }}', '{{ $journal->shift->nama_shift ?? '-' }}')" class="text-red-600 hover:text-red-800 transition" title="Delete Journal">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                                @endif --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-black">No journal submissions found.</td>
                        </tr>
                    @endforelse
                    {{-- JS-controlled no-results row --}}
                    <tr id="no-results-row" style="display:none;">
                        <td colspan="7" class="py-6 text-center text-black">No journal submissions found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('modal_journal')

<!-- Handover Confirmation Modal -->
<div id="handoverModal" class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl transform scale-95 transition-transform duration-300 p-6 mx-4">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Konfirmasi Serah Terima</h2>
        <p class="text-black mb-6">Apakah Anda yakin ingin melakukan serah terima jurnal ini? Status jurnal akan berubah menjadi <span class="font-bold text-yellow-600">Waiting</span> untuk disetujui PGA.</p>
        
        <form id="handoverForm" method="POST" action="">
            @csrf
            <div class="flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeHandoverModal()" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Ya, Serah Terima</button>
            </div>
        </form>
    </div>
</div>

{{-- <!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl transform scale-95 transition-transform duration-300 p-6 mx-4">
        <div class="flex items-center gap-3 text-red-600 mb-4">
            <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
            <h2 class="text-xl font-bold">Hapus Jurnal?</h2>
        </div>
        <p class="text-gray-600 mb-2">Apakah Anda yakin ingin menghapus jurnal ini secara permanen?</p>
        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 mb-6">
            <p class="text-sm text-gray-700"><strong>Tanggal:</strong> <span id="deleteDate"></span></p>
            <p class="text-sm text-gray-700"><strong>Lokasi:</strong> <span id="deleteLocation"></span></p>
            <p class="text-sm text-gray-700"><strong>Shift:</strong> <span id="deleteShift"></span></p>
        </div>
        <p class="text-sm text-red-500 mb-6 italic">*Tindakan ini tidak dapat dibatalkan dan semua lampiran akan ikut terhapus.</p>
        
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors shadow-sm focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Ya, Hapus Permanen</button>
            </div>
        </form>
    </div>
</div> --}}

<!-- Final Approval Modal (PGA Only) -->
<div id="finalApprovalModal" class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl transform scale-95 transition-transform duration-300 p-6 mx-4">
        <div class="flex items-center gap-3 text-blue-600 mb-4">
            <i class="bi bi-check2-circle text-2xl"></i>
            <h2 class="text-xl font-bold">Final Approval Journal</h2>
        </div>
        
        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 mb-4 text-sm text-black">
            <p><strong>Tanggal:</strong> <span id="approveDate"></span></p>
            <p><strong>Lokasi:</strong> <span id="approveLocation"></span></p>
            <p><strong>Shift:</strong> <span id="approveShift"></span></p>
        </div>

        <form id="finalApprovalForm" method="POST" action="">
            @csrf
            <input type="hidden" name="journal_id" id="finalJournalIdInput" value="">
            <input type="hidden" name="modal_date" id="finalModalDateInput" value="">
            <input type="hidden" name="modal_location" id="finalModalLocationInput" value="">
            <input type="hidden" name="modal_shift" id="finalModalShiftInput" value="">

            @if ($errors->any() && (old('status') || old('catatan') || old('journal_id')))
                <div id="alertFinalApprovalError" class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded relative shadow-sm">
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
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Status Konfirmasi <span class="text-red-500">*</span></label>
                    <select name="status" id="finalStatusSelect" required onchange="toggleCatatanTextarea()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white text-sm">
                        <option value="Approved">Approve</option>
                        <option value="Rejected">Reject</option>
                    </select>
                </div>

                <div id="finalCatatanContainer" class="hidden">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Catatan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="catatan" id="finalCatatanTextarea" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm"
                        placeholder="Alasan jurnal ditolak..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeFinalApprovalModal()" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Frontend Search & Filter Logic
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput  = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const rows         = document.querySelectorAll('.journal-row');
        const noResultsRow = document.getElementById('no-results-row');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const statusTerm = statusFilter.value;
            let visibleCount = 0;

            rows.forEach(row => {
                const searchableText = row.getAttribute('data-search');
                const rowStatus      = row.getAttribute('data-status');

                const matchesSearch = !searchTerm || searchableText.includes(searchTerm);
                const matchesStatus = (statusTerm === 'All' || rowStatus === statusTerm);

                const visible = matchesSearch && matchesStatus;
                row.style.display = visible ? '' : 'none';
                if (visible) visibleCount++;
            });

            // Toggle no-results row
            noResultsRow.style.display = (visibleCount === 0) ? '' : 'none';
        }

        if(searchInput) searchInput.addEventListener('input', filterTable);
        if(statusFilter) statusFilter.addEventListener('change', filterTable);
    });

    // Handover Modal Logic
    const handoverModal = document.getElementById('handoverModal');
    const handoverForm = document.getElementById('handoverForm');
    
    function openHandoverModal(journalId) {
        handoverForm.action = `/satpam/journal/handover/${journalId}`;
        handoverModal.classList.remove('hidden');
        setTimeout(() => {
            handoverModal.classList.remove('opacity-0');
            handoverModal.firstElementChild.classList.remove('scale-95');
        }, 10);
    }

    function closeHandoverModal() {
        handoverModal.classList.add('opacity-0');
        handoverModal.firstElementChild.classList.add('scale-95');
        setTimeout(() => {
            handoverModal.classList.add('hidden');
        }, 300);
    }

    /* // Delete Modal Logic
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteDateText = document.getElementById('deleteDate');
    const deleteLocationText = document.getElementById('deleteLocation');
    const deleteShiftText = document.getElementById('deleteShift');

    function openDeleteModal(journalId, date, location, shift) {
        deleteForm.action = `/pga/journal/${journalId}`;
        deleteDateText.textContent = date;
        deleteLocationText.textContent = location;
        deleteShiftText.textContent = shift;
        
        deleteModal.classList.remove('hidden');
        setTimeout(() => {
            deleteModal.classList.remove('opacity-0');
            deleteModal.firstElementChild.classList.remove('scale-95');
        }, 10);
    }

    function closeDeleteModal() {
        deleteModal.classList.add('opacity-0');
        deleteModal.firstElementChild.classList.add('scale-95');
        setTimeout(() => {
            deleteModal.classList.add('hidden');
        }, 300);
    } */

    // Final Approval Modal Logic (PGA)
    const finalApprovalModal = document.getElementById('finalApprovalModal');
    const finalApprovalForm = document.getElementById('finalApprovalForm');
    const finalStatusSelect = document.getElementById('finalStatusSelect');
    const finalCatatanContainer = document.getElementById('finalCatatanContainer');
    const finalCatatanTextarea = document.getElementById('finalCatatanTextarea');
    const approveDateText = document.getElementById('approveDate');
    const approveLocationText = document.getElementById('approveLocation');
    const approveShiftText = document.getElementById('approveShift');

    function openFinalApprovalModal(journalId, date, location, shift) {
        finalApprovalForm.action = `/pga/journal/${journalId}/approve`;
        approveDateText.textContent = date;
        approveLocationText.textContent = location;
        approveShiftText.textContent = shift;
        
        document.getElementById('finalJournalIdInput').value = journalId;
        document.getElementById('finalModalDateInput').value = date;
        document.getElementById('finalModalLocationInput').value = location;
        document.getElementById('finalModalShiftInput').value = shift;

        // Reset form
        finalStatusSelect.value = 'Approved';
        finalCatatanContainer.classList.add('hidden');
        finalCatatanTextarea.value = '';
        finalCatatanTextarea.removeAttribute('required');

        finalApprovalModal.classList.remove('hidden');
        setTimeout(() => {
            finalApprovalModal.classList.remove('opacity-0');
            finalApprovalModal.firstElementChild.classList.remove('scale-95');
        }, 10);
    }

    function toggleCatatanTextarea() {
        if (finalStatusSelect.value === 'Rejected') {
            finalCatatanContainer.classList.remove('hidden');
            finalCatatanTextarea.setAttribute('required', 'required');
        } else {
            finalCatatanContainer.classList.add('hidden');
            finalCatatanTextarea.removeAttribute('required');
        }
    }

    function closeFinalApprovalModal() {
        finalApprovalModal.classList.add('opacity-0');
        finalApprovalModal.firstElementChild.classList.add('scale-95');
        setTimeout(() => {
            finalApprovalModal.classList.add('hidden');
        }, 300);
    }

    // Auto-open modal jika ada error validasi
    window.addEventListener('load', () => {
        const jId = '{{ old('journal_id') }}';
        const mDate = '{{ old('modal_date') }}';
        const mLocation = '{{ old('modal_location') }}';
        const mShift = '{{ old('modal_shift') }}';
        if (jId) {
            openFinalApprovalModal(jId, mDate, mLocation, mShift);
            // Set the correct status based on old value
            const oldStatus = '{{ old('status') }}';
            if (oldStatus) {
                document.getElementById('finalStatusSelect').value = oldStatus;
                toggleCatatanTextarea();
            }
            
            // Re-populate catatan if available
            const oldCatatan = `{!! addslashes(old('catatan', '')) !!}`;
            if(oldCatatan) {
                document.getElementById('finalCatatanTextarea').value = oldCatatan;
            }
        }
    });
</script>
@endsection
