@extends('layouts.app')

@section('title', 'System Logs')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-5">System Logs</h1>

<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
        <div>
            <h2 class="text-base font-bold text-gray-800">Activity Log</h2>
        </div>

        <div class="w-full md:w-72">
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <i class="bi bi-search text-sm"></i>
                </span>
                <input
                    id="logSearchInput"
                    type="text"
                    placeholder="Search logs..."
                    class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[640px]" id="logsTable">
            <thead>
                <tr class="text-left text-gray-600 font-semibold border-b border-gray-100">
                    <th class="py-3.5 pr-6">Date &amp; Time</th>
                    <th class="py-3.5 pr-6">Admin Name</th>
                    <th class="py-3.5 pr-6">Action Type</th>
                    <th class="py-3.5">Description</th>
                </tr>
            </thead>
            <tbody id="logsTableBody" class="divide-y divide-gray-50">
                @forelse($logs as $log)
                    @php
                        $action = strtoupper($log->aksi);
                        $badgeClass = match (true) {
                            str_contains(strtolower($log->aksi), 'create') => 'bg-emerald-100 text-emerald-700',

                            str_contains(strtolower($log->aksi), 'update') => 'bg-blue-100 text-blue-700',

                            str_contains(strtolower($log->aksi), 'delete') => 'bg-rose-100 text-rose-700',

                            default => 'bg-gray-100 text-gray-700',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition text-black">
                        <td class="py-3.5 pr-6 whitespace-nowrap">
                            {{ $log->created_at->translatedFormat('d F Y, H:i') }}
                        </td>
                        <td class="py-3.5 pr-6 font-semibold whitespace-nowrap">
                            {{ $log->user->nama ?? '-' }}
                        </td>
                        <td class="py-3.5 pr-6 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-[11px] font-semibold {{ $badgeClass }}">
                                {{ $action }}
                            </span>
                        </td>
                        <td class="py-3.5">
                            {{ $log->deskripsi }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-black text-sm">
                            Belum ada aktivitas yang terekam.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    (function () {
        const input = document.getElementById('logSearchInput');
        const table = document.getElementById('logsTableBody');

        if (!input || !table) return;

        input.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    })();
</script>

@endsection

