@extends('layouts.app')

@section('title', 'Group Management')

@section('content')


    {{-- Page Title --}}
    <h1 class="text-2xl font-bold text-gray-800 mb-5">Group Management</h1>

    {{-- Table Card --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <h2 class="text-base font-bold text-gray-800">Groups</h2>
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                {{-- Search Input --}}
                <div class="relative w-full sm:w-64">
                    <input type="text" id="groupSearchInput" placeholder="Search group..." 
                        class="w-full pl-9 pr-3 py-2 bg-gray-50 text-sm border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-500 block transition-all"
                        autocomplete="off">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="bi bi-search text-gray-500"></i>
                    </div>
                </div>
                <button
                    onclick="openModalGroup()"
                    class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition w-full sm:w-auto">
                    <span class="text-lg leading-none">+</span> Add Group
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-700 font-bold border-b border-gray-100">
                        <th class="pb-3 pr-6">Group Name</th>
                        <th class="pb-3 pr-6">Members</th>
                        <th class="pb-3 pr-6">Status</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="groupTableBody" class="divide-y divide-gray-300">
                    @forelse($groups as $group)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3.5 pr-6 font-semibold text-gray-800">
                            {{ $group->nama_grup }}
                        </td>
                        <td class="py-3.5 pr-6">
                            <button 
                                onclick="openModalViewMembers('{{ addslashes($group->nama_grup) }}', {{ json_encode($group->users->map(function($u) { return ['name' => $u->nama, 'username' => $u->username, 'role' => $u->role]; })) }})"
                                class="flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium transition cursor-pointer">
                                <i class="bi bi-people"></i> 
                                {{ $group->users->count() }} members
                            </button>
                        </td>
                        <td class="py-3.5 pr-6">
                            <form action="{{ route('admin.group.toggle', $group->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="button"
                                    onclick="openModalStatusConfirm('{{ route('admin.group.toggle', $group->id) }}', '{{ addslashes($group->nama_grup) }}', 'Group', '{{ $group->status ?? 'Active' }}')"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition
                                        {{ ($group->status ?? 'Active') === 'Active'
                                            ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                            : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                                    title="Klik untuk ubah status">
                                    {{ $group->status ?? 'Active' }}
                                </button>
                            </form>
                        </td>
                        <td class="py-3.5 text-right">
                            <div class="flex items-center justify-end gap-3">
                                {{-- Edit --}}
                                @php
                                    // Extract member IDs for Alpine state
                                    $memberIds = $group->users->pluck('id');
                                @endphp
                                <button
                                    onclick="openModalGroup({{ $group->id }}, '{{ addslashes($group->nama_grup) }}', {{ json_encode($memberIds->map(fn($id) => (string)$id)) }})"
                                    class="text-gray-500 hover:text-blue-600 transition text-base"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                {{-- Delete --}}
                                {{-- <button
                                    onclick="openModalDelete('/admin/group/{{ $group->id }}', '{{ addslashes($group->nama_grup) }}', 'Group')"
                                    class="text-red-400 hover:text-red-600 transition text-base"
                                    title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button> --}}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-black text-md">
                            Belum ada data grup.
                        </td>
                    </tr>
                    @endforelse
                    {{-- No results row --}}
                    <tr id="groupNoResults" class="hidden">
                        <td colspan="4" class="py-8 text-center text-black text-md">
                            Group not found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL VIEW MEMBERS (khusus halaman ini) --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <div id="modalViewMembers" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/50">
        
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 flex flex-col max-h-[90vh]">
            
            <div class="flex items-center justify-between mb-5">
                <h3 id="modalViewMembersTitle" class="text-xl font-bold text-gray-800 whitespace-nowrap overflow-hidden text-ellipsis">Group Members</h3>
            </div>

            <div class="overflow-y-auto flex-1 pr-2 space-y-3 mb-5">
                <div id="viewMembersEmpty" class="hidden text-center py-6 text-gray-400 text-sm border-2 border-dashed border-gray-200 rounded-xl">
                    Tidak ada member di grup ini.
                </div>
                
                <div id="viewMembersContainer" class="space-y-3">
                    <!-- Javascript will populate members here -->
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-gray-100">
                <button onclick="closeModalViewMembers()" 
                    class="px-5 py-2 text-sm border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium rounded-lg transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- MODALS via @include --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    @include('admin.modals.modal_group')
    @include('admin.modals.modal_delete')
    @include('admin.modals.modal_statusConfirm')

<script>
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
        
        // Filter visible satpam based on group assignment
        let visibleCount = 0;
        labels.forEach(label => {
            const userGroupId = label.getAttribute('data-group-id');
            // Show if it belongs to no group (empty string) OR if it belongs to the currently edited group (id)
            if (!userGroupId || userGroupId == id) {
                label.classList.remove('hidden');
                label.classList.add('flex');
                visibleCount++;
            } else {
                label.classList.remove('flex');
                label.classList.add('hidden');
            }
        });

        // message untuk di tampilkan jika tidak ada satpam yang tersedia
        const emptyMessage = document.getElementById('emptySatpamMessage');
        if (emptyMessage) {
            if (visibleCount === 0) {
                emptyMessage.classList.remove('hidden');
            } else {
                emptyMessage.classList.add('hidden');
            }
        }

        inputNama.value = nama;

        if (id) {
            title.innerText = 'Edit Group';
            form.action = `/admin/group/${id}`;
            methodInput.value = 'PUT';
            if (groupIdInput) groupIdInput.value = id;
        } else {
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

    function openModalViewMembers(groupName, membersList) {
        const modal = document.getElementById('modalViewMembers');
        const title = document.getElementById('modalViewMembersTitle');
        const container = document.getElementById('viewMembersContainer');
        const emptyState = document.getElementById('viewMembersEmpty');

        title.innerText = 'Group Members - ' + groupName;
        container.innerHTML = '';

        if (membersList.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
            membersList.forEach(member => {
                const initials = member.name.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();
                
                const item = document.createElement('div');
                item.className = 'flex items-center gap-4 p-3 bg-white border border-gray-100 shadow-sm rounded-xl';
                item.innerHTML = `
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 font-bold flex items-center justify-center flex-shrink-0">
                        ${initials}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">${member.name}</p>
                        <p class="text-xs text-black mt-0.5">${member.username} • ${member.role.toLowerCase()}</p>
                    </div>
                `;
                container.appendChild(item);
            });
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalViewMembers() {
        document.getElementById('modalViewMembers').classList.add('hidden');
        document.getElementById('modalViewMembers').classList.remove('flex');
    }

    /* function openModalDelete(actionUrl, name, entity = 'Group') {
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

    @if($errors->any())
        window.onload = () => {
            const id = '{{ old('group_id', '') }}';
            const nama = `{!! addslashes(old('nama_grup', '')) !!}`;
            const satpamIds = @json(old('satpam_ids', []));
            openModalGroup(id ? id : null, nama, satpamIds.map(String));
        };
    @endif

    // logic search
    (function () {
        function setupSearch(inputId, tableBodyId, noResultsId) {
            const input = document.getElementById(inputId);
            const tableBody = document.getElementById(tableBodyId);
            const noResultsRow = document.getElementById(noResultsId);
            if (!input || !tableBody) return;

            input.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                const rows = tableBody.querySelectorAll('tr');
                let anyVisible = false;

                rows.forEach(row => {
                    if (row.id === noResultsId || row.querySelector('td[colspan]')) return;
                    
                    const text = row.textContent.toLowerCase();
                    const match = text.includes(query);
                    row.style.display = match ? '' : 'none';
                    if (match) anyVisible = true;
                });

                if (noResultsRow) {
                    noResultsRow.classList.toggle('hidden', anyVisible || query === '');
                }
            });
        }

        setupSearch('groupSearchInput', 'groupTableBody', 'groupNoResults');
    })();
</script>

@endsection
