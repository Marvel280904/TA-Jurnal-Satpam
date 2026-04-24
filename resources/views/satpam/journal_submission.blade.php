@extends('layouts.app')

@section('title', 'Journal Submission')

@section('content')
<div class="{{ $noGroup ? 'blur-sm pointer-events-none select-none' : '' }}">
    <div class="max-w-4xl mx-auto">

    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Journal Submission</h1>
        <p class="text-sm text-gray-500 mt-1">Submit your operational security journal report</p>
    </div>

    <!-- {{-- Error Alert --}}
    @if(session('error'))
        <div class="mb-5 p-4 bg-red-50 border border-red-200 border-l-4 border-l-red-500 rounded-lg flex items-start gap-3" id="error-alert">
            <i class="bi bi-exclamation-triangle-fill text-red-500 text-lg flex-shrink-0 mt-0.5"></i>
            <div class="flex-1">
                <p class="font-semibold text-red-700 text-sm">Terjadi Kesalahan</p>
                <p class="text-red-600 text-sm mt-0.5">{{ session('error') }}</p>
            </div>
            <button onclick="this.closest('#error-alert').remove()" class="text-red-400 hover:text-red-600">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>
    @endif -->

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 border-l-4 border-l-red-500 rounded-lg" id="validation-alert">
            <div class="flex items-center gap-2 mb-2">
                <i class="bi bi-exclamation-triangle-fill text-red-500"></i>
                <p class="font-semibold text-red-700 text-sm">Mohon perbaiki kesalahan berikut:</p>
                <button onclick="this.closest('#validation-alert').remove()" class="ml-auto text-red-400 hover:text-red-600">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-red-600">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('satpam.journal.submit') }}" method="POST" enctype="multipart/form-data" id="journal-form">
        @csrf

        {{-- Basic Information --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-5">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800 text-base">Basic Information</h2>
            </div>
            <div class="px-6 py-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Location --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Location <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="lokasi_id" id="lokasi_id" required
                                class="w-full appearance-none bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 pr-9 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition
                                {{ $errors->has('lokasi_id') ? 'border-red-400 bg-red-50' : '' }}">
                                <option value="" disabled selected>Select location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('lokasi_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->nama_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                <i class="bi bi-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal" id="tanggal" required
                            value="{{ old('tanggal', date('Y-m-d')) }}"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition
                            {{ $errors->has('tanggal') ? 'border-red-400 bg-red-50' : '' }}">
                    </div>

                    {{-- Current Shift --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Current Shift <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="shift_id" id="shift_id" required
                                class="w-full appearance-none bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 pr-9 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition
                                {{ $errors->has('shift_id') ? 'border-red-400 bg-red-50' : '' }}">
                                <option value="" disabled selected>Select current shift</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->nama_shift }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                <i class="bi bi-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Next Shift --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Next Shift <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="next_shift" id="next_shift" required
                                class="w-full appearance-none bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 pr-9 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition
                                {{ $errors->has('next_shift') ? 'border-red-400 bg-red-50' : '' }}">
                                <option value="" disabled selected>Select next shift</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ old('next_shift') == $group->id ? 'selected' : '' }}>
                                        {{ $group->nama_grup }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                <i class="bi bi-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Report Details --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-5">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800 text-base">Report Details</h2>
            </div>
            <div class="px-6 py-5 space-y-5">

                {{-- Laporan Kegiatan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Laporan Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="laporan_kegiatan" id="laporan_kegiatan" rows="4"
                        required
                        placeholder="Deskripsikan kegiatan operasional yang dilakukan..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none">{{ old('laporan_kegiatan') }}</textarea>
                </div>

                {{-- Laporan Kejadian --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Laporan Kejadian <span class="text-red-500">*</span>
                    </label>
                    <textarea name="kejadian_temuan" id="kejadian_temuan" rows="3"
                        required
                        placeholder="Deskripsikan kejadian khusus jika ada..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none">{{ old('kejadian_temuan') }}</textarea>
                </div>

                {{-- Lembur --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Lembur <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="lembur" id="lembur"
                        required
                        value="{{ old('lembur') }}"
                        placeholder="e.g., 2 jam atau Tidak ada"
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                {{-- Proyek/Vendor --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Proyek/Vendor <span class="text-red-500">*</span>
                    </label>
                    <textarea name="proyek_vendor" id="proyek_vendor" rows="3"
                        required
                        placeholder="Informasi tentang vendor atau proyek yang sedang berjalan..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none">{{ old('proyek_vendor') }}</textarea>
                </div>

                {{-- Barang Inventaris --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Barang Inventaris <span class="text-red-500">*</span>
                    </label>
                    <textarea name="barang_inven" id="barang_inven" rows="3"
                        required
                        placeholder="Status barang inventaris..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none">{{ old('barang_inven') }}</textarea>
                </div>

                {{-- Informasi Tambahan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Informasi Tambahan</label>
                    <textarea name="info_tambahan" id="info_tambahan" rows="3"
                        placeholder="Informasi tambahan lainnya..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none">{{ old('info_tambahan') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Upload File --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800 text-base">Upload File</h2>
            </div>
            <div class="px-6 py-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Attach File <span class="text-gray-400 font-normal">(Optional)</span>
                </label>

                {{-- Drop zone --}}
                <div id="drop-zone"
                    class="relative border-2 border-dashed border-gray-200 rounded-lg bg-gray-50 hover:border-blue-400 hover:bg-blue-50 transition cursor-pointer group"
                    onclick="document.getElementById('file-input').click()">
                    <input type="file" id="file-input" name="files[]" multiple
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        class="sr-only"
                        onchange="handleFileSelect(this)">
                    <div class="flex flex-col items-center justify-center py-8 px-4 text-center">
                        <i class="bi bi-cloud-upload text-3xl text-gray-300 group-hover:text-blue-400 transition mb-2"></i>
                        <p class="text-sm font-medium text-gray-600 group-hover:text-blue-600">
                            Klik untuk memilih file atau drag & drop
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Supported formats: PDF, DOC, DOCX, JPG, PNG &bull; Maks. 10MB per file</p>
                    </div>
                </div>

                {{-- File List --}}
                <ul id="file-list" class="mt-3 space-y-2"></ul>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end mb-8">
            <button type="button" onclick="openSubmitConfirmModal()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm px-6 py-3 rounded-lg shadow transition">
                <i class="bi bi-send-fill"></i>
                Submit Journal
            </button>
        </div>
    </form>
</div>
</div>

<!-- Include Modal Confirm Submit -->
@include('modal_submitConfirm')

@if($noGroup)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-[2px]">
    <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-md w-full text-center border border-gray-100">
        <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="bi bi-exclamation-triangle text-orange-500 text-4xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Akses Dibatasi</h2>
        <p class="text-black mb-8 leading-relaxed">
            Anda belum memiliki grup, harap kontak <b>Admin</b> untuk dikelompokan ke dalam grup!
        </p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-6 rounded-xl transition shadow-lg">
                Logout
            </button>
        </form>
    </div>
</div>
@endif

<script>
    // ── Drag & Drop ────────────────────────────────────────────────────────────
    const dropZone = document.getElementById('drop-zone');

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-400', 'bg-blue-50');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        const dt   = e.dataTransfer;
        const inp  = document.getElementById('file-input');
        // Merge dropped files with already selected files
        mergeFiles(dt.files);
    });

    // ── File Management ────────────────────────────────────────────────────────
    let selectedFiles = []; // Array of File objects

    function handleFileSelect(input) {
        mergeFiles(input.files);
        // Reset input so the same file can be selected again if removed
        input.value = '';
    }

    function mergeFiles(newFiles) {
        Array.from(newFiles).forEach(file => {
            // Prevent duplicates by name+size
            const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
            if (!exists) {
                selectedFiles.push(file);
            }
        });
        renderFileList();
        syncInputFiles();
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        renderFileList();
        syncInputFiles();
    }

    function renderFileList() {
        const list = document.getElementById('file-list');
        list.innerHTML = '';

        if (selectedFiles.length === 0) return;

        selectedFiles.forEach((file, idx) => {
            const ext  = file.name.split('.').pop().toLowerCase();
            const icon = getFileIcon(ext);
            const size = formatSize(file.size);

            const li = document.createElement('li');
            li.className = 'flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 group';
            li.innerHTML = `
                <span class="text-xl text-blue-500">${icon}</span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-700 truncate">${escapeHtml(file.name)}</p>
                    <p class="text-xs text-gray-400">${size}</p>
                </div>
                <button type="button"
                    onclick="removeFile(${idx})"
                    title="Hapus file"
                    class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>`;
            list.appendChild(li);
        });
    }

    function syncInputFiles() {
        // Rebuild the file input's files using DataTransfer
        const dt  = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        document.getElementById('file-input').files = dt.files;
    }

    function getFileIcon(ext) {
        const icons = {
            pdf:  '<i class="bi bi-file-earmark-pdf-fill text-red-500"></i>',
            doc:  '<i class="bi bi-file-earmark-word-fill text-blue-600"></i>',
            docx: '<i class="bi bi-file-earmark-word-fill text-blue-600"></i>',
            jpg:  '<i class="bi bi-file-earmark-image-fill text-green-500"></i>',
            jpeg: '<i class="bi bi-file-earmark-image-fill text-green-500"></i>',
            png:  '<i class="bi bi-file-earmark-image-fill text-green-500"></i>',
        };
        return icons[ext] || '<i class="bi bi-file-earmark-fill text-gray-400"></i>';
    }

    function formatSize(bytes) {
        if (bytes < 1024)       return bytes + ' B';
        if (bytes < 1024*1024)  return (bytes/1024).toFixed(1) + ' KB';
        return (bytes/(1024*1024)).toFixed(1) + ' MB';
    }

    function escapeHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
</script>
@endsection
