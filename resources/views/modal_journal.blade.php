<!-- View Journal Details Modal -->
<div id="viewJournalModal" class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center hidden">
    <div class="bg-white w-full max-w-4xl max-h-[90vh] rounded-2xl shadow-xl flex flex-col mx-4">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Detail Jurnal</h2>
                <p class="text-sm text-gray-500 mt-1">Tanggal: <span id="v_tanggal" class="font-medium"></span></p>
            </div>
            <button onclick="closeViewModal()" class="text-black hover:text-red-500 transition-colors bg-gray-50 hover:bg-red-50 w-8 h-8 rounded-full flex items-center justify-center">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Scrollable Content -->
        <div class="p-6 overflow-y-auto flex-1 space-y-6">
            
            <!-- Key Info Box -->
            <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 grid grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-1">Dibuat Oleh</p>
                    <p class="text-sm text-gray-800 font-medium" id="v_userNama"></p>
                    <p class="text-xs text-gray-500" id="v_groupNama"></p>
                </div>
                <div>
                    <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-1">Anggota Grup</p>
                    <p class="text-xs text-gray-700 leading-relaxed font-medium" id="v_groupMembers"></p>
                </div>
                <div>
                    <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-1">Lokasi & Shift</p>
                    <p class="text-sm text-gray-800 font-medium" id="v_lokasiNama"></p>
                    <p class="text-xs text-gray-500" id="v_shiftNama"></p>
                </div>
                <div>
                    <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-1">Next Shift</p>
                    <p class="text-sm text-gray-800 font-medium" id="v_nextShiftNama"></p>
                </div>
                <div>
                    <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-1">Status</p>
                    <div id="v_statusBadge" class="mt-1 inline-flex"></div>
                </div>
            </div>

            <div id="v_approvalInfo" class="hidden">
                 <div class="border-l-4 border-yellow-400 bg-yellow-50 p-3 rounded-r-lg text-sm text-gray-700">
                     <p><span class="font-bold">Diperbarui Oleh:</span> <span id="v_updatedBy"></span></p>
                     <p><span class="font-bold">Serah Terima Oleh:</span> <span id="v_handoverBy"></span></p>
                     <p><span class="font-bold">Persetujuan Akhir Oleh (PGA):</span> <span id="v_approvedBy"></span></p>
                 </div>
            </div>

            <!-- Catatan Rejection Info -->
            <div id="v_catatanInfo" class="hidden">
                 <div class="border-l-4 border-red-500 bg-red-50 p-4 rounded-r-lg text-sm text-red-800">
                     <p class="font-bold mb-1 flex items-center gap-2"><i class="bi bi-exclamation-circle text-red-500"></i> Alasan Penolakan (Catatan Revisi):</p>
                     <p id="v_catatanText" class="text-gray-800 bg-white/50 p-2 rounded border border-red-100"></p>
                 </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <i class="bi bi-file-text text-blue-500"></i> Laporan Kegiatan
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-xl text-sm text-gray-700 border border-gray-100 whitespace-pre-wrap" id="v_laporan"></div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <i class="bi bi-exclamation-triangle text-orange-500"></i> Kejadian / Temuan
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-xl text-sm text-gray-700 border border-gray-100 whitespace-pre-wrap" id="v_kejadian"></div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <i class="bi bi-clock-history text-purple-500"></i> Lembur
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-xl text-sm text-gray-700 border border-gray-100" id="v_lembur"></div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <i class="bi bi-building-gear text-teal-500"></i> Proyek / Vendor Masuk
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-xl text-sm text-gray-700 border border-gray-100 whitespace-pre-wrap" id="v_vendor"></div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <i class="bi bi-box-seam text-yellow-600"></i> Barang Inventaris
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-xl text-sm text-gray-700 border border-gray-100 whitespace-pre-wrap" id="v_inven"></div>
                    </div>

                    <div id="v_infoTambahanContainer" class="hidden">
                        <h3 class="text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <i class="bi bi-info-circle text-gray-500"></i> Info Tambahan
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-xl text-sm text-gray-700 border border-gray-100 whitespace-pre-wrap" id="v_info"></div>
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            <div id="v_attachmentsContainer" class="hidden pt-4 border-t border-gray-100">
                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="bi bi-paperclip text-gray-500"></i> File Lampiran
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3" id="v_attachmentsList">
                    <!-- Images/Links dynamically populated here -->
                </div>
            </div>

        </div>
        
        <!-- Footer
        <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl flex justify-end">
             <button onclick="closeViewModal()" class="px-5 py-2 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-medium rounded-lg transition-colors">Tutup</button>
        </div> -->
    </div>
</div>

<script>
    const viewJournalModal = document.getElementById('viewJournalModal');
    
    function formatDate(dateString) {
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    }

    function getStatusBadge(status) {
        if(status === 'Pending') return `<span class="px-3 py-1 bg-gray-100 text-gray-700 font-bold rounded-full text-xs">Pending</span>`;
        if(status === 'Waiting') return `<span class="px-3 py-1 bg-yellow-100 text-yellow-700 font-bold rounded-full text-xs">Waiting</span>`;
        if(status === 'Approved') return `<span class="px-3 py-1 bg-green-100 text-green-700 font-bold rounded-full text-xs">Approved</span>`;
        return `<span class="px-3 py-1 bg-red-100 text-red-700 font-bold rounded-full text-xs">Rejected</span>`;
    }

    function openViewModal(id) {
        // Fetch journal data via AJAX
        fetch(`/journal/view/${id}`)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const journal = data.data;

                    // Populate fields
                    document.getElementById('v_tanggal').textContent = formatDate(journal.tanggal);
                    document.getElementById('v_userNama').textContent = journal.user.nama;
                    document.getElementById('v_groupNama').textContent = journal.group.nama_grup;
                    document.getElementById('v_groupMembers').textContent = journal.group_members_names || '-';
                    document.getElementById('v_lokasiNama').textContent = journal.location.nama_lokasi;
                    document.getElementById('v_shiftNama').textContent = journal.shift.nama_shift;
                    document.getElementById('v_nextShiftNama').textContent = journal.next_shift_rel ? journal.next_shift_rel.nama_grup : '-';
                    document.getElementById('v_statusBadge').innerHTML = getStatusBadge(journal.status);

                    // Trackers (updated_by etc)
                    const approvalBox = document.getElementById('v_approvalInfo');
                    approvalBox.classList.remove('hidden');
                    document.getElementById('v_updatedBy').textContent = journal.updater ? journal.updater.nama : 'Tidak Ada';
                    document.getElementById('v_handoverBy').textContent = journal.handover ? journal.handover.nama : 'Tidak Ada';
                    document.getElementById('v_approvedBy').textContent = journal.approver ? journal.approver.nama : 'Tidak Ada';

                    // Catatan Penolakan
                    const catatanBox = document.getElementById('v_catatanInfo');
                    if (journal.catatan) {
                        catatanBox.classList.remove('hidden');
                        document.getElementById('v_catatanText').textContent = journal.catatan;
                    } else {
                        catatanBox.classList.add('hidden');
                    }

                    document.getElementById('v_laporan').textContent = journal.laporan_kegiatan;
                    document.getElementById('v_kejadian').textContent = journal.kejadian_temuan;
                    document.getElementById('v_lembur').textContent = journal.lembur;
                    document.getElementById('v_vendor').textContent = journal.proyek_vendor;
                    document.getElementById('v_inven').textContent = journal.barang_inven;

                    const infoContainer = document.getElementById('v_infoTambahanContainer');
                    if(journal.info_tambahan) {
                        infoContainer.classList.remove('hidden');
                        document.getElementById('v_info').textContent = journal.info_tambahan;
                    } else {
                        infoContainer.classList.add('hidden');
                    }

                    // Attachments
                    const attachContainer = document.getElementById('v_attachmentsContainer');
                    const attachList = document.getElementById('v_attachmentsList');
                    attachList.innerHTML = ''; // clear

                    if(journal.uploads && journal.uploads.length > 0) {
                        attachContainer.classList.remove('hidden');
                        journal.uploads.forEach(upload => {
                            const isImage = upload.file_path.match(/\.(jpeg|jpg|gif|png)$/i) != null;
                            // Remove 'public/' from the stored path to get the correct URL under /storage/
                            const cleanPath = upload.file_path.replace(/^public\//, '');
                            const filePath = `/storage/${cleanPath}`; 
                            const fileName = upload.file_name || cleanPath.split('/').pop() || 'Attachment';
                            
                            let html = `
                                <a href="${filePath}" target="_blank" class="block group relative rounded-lg border border-gray-200 overflow-hidden hover:border-blue-500 transition-colors">
                            `;
                            
                            if(isImage) {
                                html += `<img src="${filePath}" class="w-full h-24 object-cover">
                                         <div class="p-2 text-xs text-center text-gray-600 truncate bg-white border-t border-gray-100">${fileName}</div>`;
                            } else {
                                html += `<div class="w-full h-24 bg-gray-50 flex flex-col items-center justify-center text-gray-500 group-hover:text-blue-500">
                                            <i class="bi bi-file-earmark-text text-3xl"></i>
                                         </div>
                                         <div class="p-2 text-xs font-medium text-center text-gray-700 truncate bg-white border-t border-gray-100" title="${fileName}">${fileName}</div>`;
                            }
                            html += `
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                        <i class="bi bi-box-arrow-up-right text-white opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    </div>
                                </a>
                            `;
                            attachList.insertAdjacentHTML('beforeend', html);
                        });
                    } else {
                        attachContainer.classList.add('hidden');
                    }

                    // Show Modal
                    viewJournalModal.classList.remove('hidden');
                    setTimeout(() => {
                        viewJournalModal.classList.remove('opacity-0');
                        viewJournalModal.firstElementChild.classList.remove('scale-95');
                    }, 10);
                } else {
                    alert('Gagal mengambil data jurnal.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
            });
    }

    function closeViewModal() {
        viewJournalModal.classList.add('opacity-0');
        viewJournalModal.firstElementChild.classList.add('scale-95');
        setTimeout(() => {
            viewJournalModal.classList.add('hidden');
        }, 300);
    }
</script>
