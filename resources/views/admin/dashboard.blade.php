@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-black">Total Users</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $total_user }}</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">
                <i class="bi bi-people text-blue-500 text-2xl"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-black">Active Locations</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $active_locations }}</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center">
                <i class="bi bi-geo-alt text-green-500 text-2xl"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-black">Active Shifts</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $active_shifts }}</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-purple-100 flex items-center justify-center">
                <i class="bi bi-clock text-purple-500 text-2xl"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-black">Total Groups</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $total_groups }}</h3>
            </div>
            <div class="w-14 h-14 rounded-xl bg-orange-100 flex items-center justify-center">
                <i class="bi bi-person-badge text-orange-500 text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white p-6 rounded-xl shadow-sm mb-6">
        <h4 class="text-base font-bold text-gray-800 mb-4">Quick Actions</h4>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <button onclick="openModalUser()" class="bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-lg flex flex-col items-center justify-center transition">
                <span class="text-2xl font-light mb-1">+</span>
                <span class="text-sm font-semibold">Add User</span>
            </button>
            <button onclick="openModalLoc()" class="bg-green-600 hover:bg-green-700 text-white py-4 rounded-lg flex flex-col items-center justify-center transition">
                <span class="text-2xl font-light mb-1">+</span>
                <span class="text-sm font-semibold">Add Location</span>
            </button>
            <button onclick="openModalShift()" class="bg-purple-600 hover:bg-purple-700 text-white py-4 rounded-lg flex flex-col items-center justify-center transition">
                <span class="text-2xl font-light mb-1">+</span>
                <span class="text-sm font-semibold">Add Shift</span>
            </button>
            <button onclick="openModalGroup()" class="bg-orange-600 hover:bg-orange-700 text-white py-4 rounded-lg flex flex-col items-center justify-center transition">
                <span class="text-2xl font-light mb-1">+</span>
                <span class="text-sm font-semibold">Create Group</span>
            </button>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h4 class="text-base font-bold text-gray-800 mb-4">Recent Activity</h4>
        <div class="space-y-4">
            @forelse($recent_logs as $log)
            @php
                $action = strtoupper($log->aksi);
                $badgeClass = match (true) {
                    str_contains(strtolower($log->aksi), 'create') => 'bg-emerald-100 text-emerald-700',

                    str_contains(strtolower($log->aksi), 'update') => 'bg-blue-100 text-blue-700',

                    str_contains(strtolower($log->aksi), 'delete') => 'bg-rose-100 text-rose-700',

                    default => 'bg-gray-100 text-gray-700',
                };
            @endphp
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-3">
                    <span class="mt-1.5 w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
                    <div>
                        <p class="text-sm font-semibold text-black">{{ $log->user->nama }}</p>
                        <p class="text-sm text-black">{{ $log->deskripsi }}</p>
                        <span class="inline-block mt-1 text-xs font-bold uppercase tracking-wide px-2 py-0.5 rounded {{ $badgeClass }}">{{ $action }}</span>
                    </div>
                </div>
                <span class="text-xs text-black font-semibold whitespace-nowrap ml-4">{{ $log->created_at->translatedFormat('d F Y, H:i') }}</span>
            </div>
            @empty
                <span class="text-md text-black whitespace-nowrap">Belum ada aktivitas yang terekam.</span>
            @endforelse
        </div>
    </div>

    {{-- Modals --}}
    @include('admin.modals.modal_user')
    @include('admin.modals.modal_location')
    @include('admin.modals.modal_shift')
    @include('admin.modals.modal_group')

    <script>
        // Modal User (Add/Edit)
        function openModalUser(id = null, nama = '', username = '', role = 'Admin') {
            const modal = document.getElementById('modalUser');
            const form = document.getElementById('formUser');
            const title = document.getElementById('modalTitle');
            const methodInput = document.getElementById('methodField');
            const passNote = document.getElementById('passwordNote');
            const passReq = document.getElementById('passwordInput');
            const userIdInput = document.getElementById('inputUserId');

            form.reset();

            document.getElementById('inputNama').value = nama;
            document.getElementById('inputUsername').value = username;
            document.getElementById('inputRole').value = role;

            if (!id) {
                title.innerText = 'Add User';
                form.action = "{{ route('admin.user.store') }}";
                methodInput.value = 'POST';
                if (userIdInput) userIdInput.value = '';
                passNote.classList.add('hidden');
                passReq.required = true;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModalUser() {
            document.getElementById('modalUser').classList.add('hidden');
            document.getElementById('modalUser').classList.remove('flex');
        }

        // Modal Location (Add/Edit) - same as Location & Shift page
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

            if (!id) {
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

        // Modal Shift (Add/Edit) - same as Location & Shift page
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

            if (!id) {
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

        // Modal Group (Add/Edit) - same as Group Management page
        function openModalGroup(id = null, nama = '', members = []) {
            const modal = document.getElementById('modalGroup');
            const form = document.getElementById('formGroup');
            const title = document.getElementById('modalGroupTitle');
            const methodInput = document.getElementById('methodGroup');
            const inputNama = document.getElementById('inputNamaGrup');
            const groupIdInput = document.getElementById('inputGroupId');
            const checkboxes = document.querySelectorAll('.satpam-checkbox');
            const labels = document.querySelectorAll('.satpam-label');

            form.reset();
            checkboxes.forEach(cb => {
                cb.checked = members.includes(cb.value);
            });

            let visibleCount = 0;
            labels.forEach(label => {
                const userGroupId = label.getAttribute('data-group-id');
                if (!userGroupId || userGroupId == id) {
                    label.classList.remove('hidden');
                    label.classList.add('flex');
                    visibleCount++;
                } else {
                    label.classList.remove('flex');
                    label.classList.add('hidden');
                }
            });

            const emptyMessage = document.getElementById('emptySatpamMessage');
            if (emptyMessage) {
                if (visibleCount === 0) {
                    emptyMessage.classList.remove('hidden');
                } else {
                    emptyMessage.classList.add('hidden');
                }
            }

            if (inputNama) inputNama.value = nama;

            if (!id) {
                title.innerText = 'Add Group';
                form.action = "{{ route('admin.group.store') }}";
                methodInput.value = 'POST';
                if (groupIdInput) groupIdInput.value = '';
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModalGroup() {
            document.getElementById('modalGroup').classList.add('hidden');
            document.getElementById('modalGroup').classList.remove('flex');
        }

        // Auto-open modal jika ada error validasi
        @if($errors->any())
            window.onload = () => {
                if ('{{ old('nama_grup') }}' || @json(old('satpam_ids')) || '{{ old('group_id') }}') {
                    const id = '{{ old('group_id', '') }}';
                    const nama = `{!! addslashes(old('nama_grup', '')) !!}`;
                    const satpamIds = @json(old('satpam_ids', []));
                    openModalGroup(id ? id : null, nama, satpamIds.map(String));
                } else if ('{{ old('nama_lokasi') }}' || '{{ old('alamat_lokasi') }}' || '{{ old('location_id') }}') {
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
                } else {
                    const userId = '{{ old('user_id', '') }}';
                    const nama = `{!! addslashes(old('nama', '')) !!}`;
                    const username = `{!! addslashes(old('username', '')) !!}`;
                    const role = '{{ old('role', 'Admin') }}';
                    openModalUser(userId ? userId : null, nama, username, role);
                }
            };
        @endif
    </script>

</div>
@endsection