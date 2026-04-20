@extends('layouts.app')

@section('title', 'Lokasi & Shift Management')

@section('content')


    {{-- Page Title --}}
    <h1 class="text-2xl font-bold text-gray-800 mb-5">Lokasi & Shift Management</h1>

    {{-- Tabs --}}
    <div class="flex gap-2 mb-5">
        <button
            onclick="switchTab('locations')"
            id="tab-locations"
            class="tab-btn px-4 py-1.5 rounded-full text-sm font-medium transition {{ request('tab', 'locations') === 'locations' ? 'bg-gray-800 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
            Locations
        </button>
        <button
            onclick="switchTab('shifts')"
            id="tab-shifts"
            class="tab-btn px-4 py-1.5 rounded-full text-sm font-medium transition {{ request('tab', 'locations') === 'shifts' ? 'bg-gray-800 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
            Shifts
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- TAB: LOCATIONS --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div id="section-locations" class="tab-content {{ request('tab', 'locations') === 'locations' ? '' : 'hidden' }}">
        <div class="bg-white rounded-xl shadow-sm p-6">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-base font-bold text-gray-800">Location Management</h2>
                {{-- Add: reset edit state lalu buka modal --}}
                <button
                    onclick="openModalLoc()"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <span class="text-lg leading-none">+</span> Add Location
                </button>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-700 font-bold border-b border-gray-100">
                            <th class="pb-3 pr-6">Location Name</th>
                            <th class="pb-3 pr-6">Address</th>
                            <th class="pb-3 pr-6">Status</th>
                            <th class="pb-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @forelse($locations as $loc)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5 pr-6 font-semibold text-gray-800">{{ $loc->nama_lokasi }}</td>
                            <td class="py-3.5 pr-6 text-gray-800">{{ $loc->alamat_lokasi }}</td>
                            <td class="py-3.5 pr-6">
                                {{-- Clickable status badge --}}
                                <form action="{{ route('admin.location.toggle', $loc->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="button"
                                        onclick="openModalStatusConfirm('{{ route('admin.location.toggle', $loc->id) }}', '{{ addslashes($loc->nama_lokasi) }}', 'Lokasi', '{{ $loc->status }}')"
                                        class="px-3 py-1 rounded-lg text-xs font-bold transition
                                            {{ $loc->status === 'Active'
                                                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                                        title="Klik untuk ubah status">
                                        {{ $loc->status }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    {{-- Edit: set data lalu buka modal yang sama --}}
                                    <button
                                        onclick="openModalLoc({{ $loc->id }}, '{{ addslashes($loc->nama_lokasi) }}', '{{ addslashes($loc->alamat_lokasi) }}')"
                                        class="text-gray-500 hover:text-blue-600 transition text-base"
                                        title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    {{-- Delete --}}
                                    {{-- <button
                                        onclick="openModalDelete('/admin/location/{{ $loc->id }}', '{{ addslashes($loc->nama_lokasi) }}', 'Lokasi')"
                                        class="text-red-400 hover:text-red-600 transition text-base"
                                        title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400 text-sm">
                                Belum ada data lokasi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- TAB: SHIFTS --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div id="section-shifts" class="tab-content {{ request('tab', 'locations') === 'shifts' ? '' : 'hidden' }}">
        <div class="bg-white rounded-xl shadow-sm p-6">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-base font-bold text-gray-800">Shift Management</h2>
                {{-- Add: reset edit state lalu buka modal --}}
                <button
                    onclick="openModalShift()"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <span class="text-lg leading-none">+</span> Add Shift
                </button>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-700 font-bold border-b border-gray-100">
                            <th class="pb-3 pr-6">Shift Name</th>
                            <th class="pb-3 pr-6">Start Time</th>
                            <th class="pb-3 pr-6">End Time</th>
                            <th class="pb-3 pr-6">Status</th>
                            <th class="pb-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @forelse($shifts as $shift)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5 pr-6 font-semibold text-gray-800">{{ $shift->nama_shift }}</td>
                            <td class="py-3.5 pr-6 text-gray-800">{{ \Carbon\Carbon::parse($shift->mulai_shift)->format('H:i') }}</td>
                            <td class="py-3.5 pr-6 text-gray-800">{{ \Carbon\Carbon::parse($shift->selesai_shift)->format('H:i') }}</td>
                            <td class="py-3.5 pr-6">
                                {{-- Clickable status badge --}}
                                <form action="{{ route('admin.shift.toggle', $shift->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="button"
                                        onclick="openModalStatusConfirm('{{ route('admin.shift.toggle', $shift->id) }}', '{{ addslashes($shift->nama_shift) }}', 'Shift', '{{ $shift->status }}')"
                                        class="px-3 py-1 rounded-lg text-xs font-bold transition
                                            {{ $shift->status === 'Active'
                                                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                                        title="Klik untuk ubah status">
                                        {{ $shift->status }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    {{-- Edit: set data lalu buka modal yang sama --}}
                                    <button
                                        onclick="openModalShift({{ $shift->id }}, '{{ addslashes($shift->nama_shift) }}', '{{ $shift->mulai_shift }}', '{{ $shift->selesai_shift }}')"
                                        class="text-gray-500 hover:text-blue-600 transition text-base"
                                        title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    {{-- Delete --}}
                                    {{-- <button
                                        onclick="openModalDelete('/admin/shift/{{ $shift->id }}', '{{ addslashes($shift->nama_shift) }}', 'Shift')"
                                        class="text-red-400 hover:text-red-600 transition text-base"
                                        title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 text-sm">
                                Belum ada data shift.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- SHARED MODALS via @include --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    @include('admin.modals.modal_location')
    @include('admin.modals.modal_shift')
    @include('admin.modals.modal_delete')
    @include('admin.modals.modal_statusConfirm')

<script>
    function switchTab(tab) {
        // Hide all sections
        const tabs = document.querySelectorAll('.tab-content');
        tabs.forEach(t => t.classList.add('hidden'));

        // Reset all buttons
        const btns = document.querySelectorAll('.tab-btn');
        btns.forEach(b => {
            b.classList.remove('bg-gray-800', 'text-white', 'shadow-sm');
            b.classList.add('bg-white', 'text-gray-600', 'border-gray-300');
        });

        // Show selected section
        document.getElementById('section-' + tab).classList.remove('hidden');

        // Style selected button
        const selectedBtn = document.getElementById('tab-' + tab);
        selectedBtn.classList.remove('bg-white', 'text-gray-600', 'border-gray-300');
        selectedBtn.classList.add('bg-gray-800', 'text-white', 'shadow-sm');
        
        // Update URL parameter without reload
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
    }

    /* Modal Location */
    function openModalLoc(id = null, name = '', address = '') {
        const modal = document.getElementById('modalLoc');
        const form = document.getElementById('formLoc');
        const title = document.getElementById('modalLocTitle');
        const methodInput = document.getElementById('methodLoc');
        const inputId = document.getElementById('inputLocId');

        form.reset();

        document.getElementById('inputLocName').value = name;
        document.getElementById('inputLocAddress').value = address;
        if (inputId) inputId.value = id || '';

        if (id) {
            title.innerText = 'Edit Location';
            form.action = `/admin/location/${id}`;
            methodInput.value = 'PUT';
        } else {
            title.innerText = 'Add Location';
            form.action = "{{ route('admin.location.store') }}";
            methodInput.value = 'POST';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalLoc() {
        const modal = document.getElementById('modalLoc');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    /* Modal Shift */
    function openModalShift(id = null, name = '', start = '', end = '') {
        const modal = document.getElementById('modalShift');
        const form = document.getElementById('formShift');
        const title = document.getElementById('modalShiftTitle');
        const methodInput = document.getElementById('methodShift');
        const inputId = document.getElementById('inputShiftId');

        form.reset();

        document.getElementById('inputShiftName').value = name;
        document.getElementById('inputShiftStart').value = start;
        document.getElementById('inputShiftEnd').value = end;
        if (inputId) inputId.value = id || '';

        if (id) {
            title.innerText = 'Edit Shift';
            form.action = `/admin/shift/${id}`;
            methodInput.value = 'PUT';
        } else {
            title.innerText = 'Add Shift';
            form.action = "{{ route('admin.shift.store') }}";
            methodInput.value = 'POST';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalShift() {
        const modal = document.getElementById('modalShift');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    /* Modal Delete */
    /* function openModalDelete(actionUrl, name, entity) {
        const modal = document.getElementById('modalDelete');
        const form = document.getElementById('formDelete');
        
        if(modal && form) {
            form.action = actionUrl;
            document.getElementById('deleteTitle').innerText = 'Hapus ' + entity + '?';
            document.getElementById('deleteEntityLabel').innerText = entity.toLowerCase();
            document.getElementById('deleteNameLabel').innerText = `"${name}"`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModalDelete() {
        const modal = document.getElementById('modalDelete');
        if(modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    } */

    /* Modal Status Confirm */
    function openModalStatusConfirm(actionUrl, name, entity, currentStatus) {
        const modal = document.getElementById('modalStatusConfirm');
        const form = document.getElementById('formStatusConfirm');
        const icon = document.getElementById('statusIcon');
        const container = document.getElementById('statusIconContainer');
        const nextStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
        const btn = document.getElementById('btnStatusConfirm');

        if(modal && form) {
            form.action = actionUrl;
            document.getElementById('statusEntityLabel').innerText = entity.toLowerCase();
            document.getElementById('statusNameLabel').innerText = `"${name}"`;
            document.getElementById('statusNextLabel').innerText = nextStatus;
            
            // UI Tweaks based on next status
            if (nextStatus === 'Active') {
                container.className = 'w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4';
                icon.className = 'bi bi-check-circle text-green-600 text-2xl';
                btn.className = 'px-5 py-2 text-sm bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition';
            } else {
                container.className = 'w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-4';
                icon.className = 'bi bi-exclamation-circle text-yellow-600 text-2xl';
                btn.className = 'px-5 py-2 text-sm bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition';
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModalStatusConfirm() {
        const modal = document.getElementById('modalStatusConfirm');
        if(modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // Auto-open modal Loc/Shift jika ada error validasi
    @if($errors->any())
        window.onload = () => {
            if ('{{ old('nama_lokasi') }}' || '{{ old('alamat_lokasi') }}' || '{{ old('location_id') }}') {
                const locId = '{{ old('location_id', '') }}';
                const locName = `{!! addslashes(old('nama_lokasi', '')) !!}`;
                const locAddress = `{!! addslashes(old('alamat_lokasi', '')) !!}`;
                openModalLoc(locId ? locId : null, locName, locAddress);
            } else if ('{{ old('nama_shift') }}' || '{{ old('mulai_shift') }}' || '{{ old('shift_id') }}') {
                const shiftId = '{{ old('shift_id', '') }}';
                const shiftName = `{!! addslashes(old('nama_shift', '')) !!}`;
                const shiftStart = '{{ old('mulai_shift', '') }}';
                const shiftEnd = '{{ old('selesai_shift', '') }}';
                openModalShift(shiftId ? shiftId : null, shiftName, shiftStart, shiftEnd);
                switchTab('shifts');
            }
        };
    @endif
</script>

@endsection
