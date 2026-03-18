@extends('layouts.app')
@section('title', 'Satpam Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Top Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Journals to Submit Today -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Journals to Submit Today</p>
                <p class="text-3xl font-bold text-gray-800">{{ $journals_to_submit }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                <i class="bi bi-file-earmark-text text-2xl"></i>
            </div>
        </div>

        <!-- Pending Journals -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Pending Journals</p>
                <p class="text-3xl font-bold text-gray-800">{{ $pending_journals }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-500 flex items-center justify-center">
                <i class="bi bi-clock text-2xl"></i>
            </div>
        </div>

        <!-- Waiting Approval -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Waiting Approval</p>
                <p class="text-3xl font-bold text-gray-800">{{ $waiting_approval }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                <i class="bi bi-check2-circle text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- My Group Section -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="bi bi-people text-gray-600"></i> My Group - {{ auth()->user()->group->nama_grup ?? 'No Group' }}
        </h2>
        
        <div class="flex flex-wrap gap-4">
            @forelse($my_group as $member)
                <div class="flex items-center gap-3 bg-gray-50 px-4 py-3 rounded-xl border border-gray-100 min-w-[200px]">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-bold text-sm flex items-center justify-center flex-shrink-0">
                        {{ collect(explode(' ', $member->nama))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('') }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ $member->nama }}</p>
                        <p class="text-xs text-gray-500">{{ strtolower($member->role) }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No members found.</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Submissions -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Recent Submissions</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-3 px-4 text-lg font-bold text-gray-700">Date</th>
                        <th class="py-3 px-4 text-lg font-bold text-gray-700">User Name</th>
                        <th class="py-3 px-4 text-lg font-bold text-gray-700">Group</th>
                        <th class="py-3 px-4 text-lg font-bold text-gray-700">Location</th>
                        <th class="py-3 px-4 text-lg font-bold text-gray-700">Shift</th>
                        <th class="py-3 px-4 text-lg font-bold text-gray-700">Next Shift</th>
                        <th class="py-3 px-4 text-lg font-bold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_submissions as $submission)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4 text-md text-gray-600">{{ \Carbon\Carbon::parse($submission->tanggal)->translatedFormat('d F Y') }}</td>
                            <td class="py-3 px-4 text-md text-gray-800">{{ $submission->user->nama ?? '-' }}</td>
                            <td class="py-3 px-4 text-md text-gray-600">{{ $submission->group->nama_grup ?? '-' }}</td>
                            <td class="py-3 px-4 text-md text-gray-600">{{ $submission->location->nama_lokasi ?? '-' }}</td>
                            <td class="py-3 px-4 text-md text-gray-600">{{ $submission->shift->nama_shift ?? '-' }}</td>
                            <td class="py-3 px-4 text-md text-gray-600">{{ $submission->nextShift->nama_grup ?? '-' }}</td>
                            <td class="py-3 px-4">
                                @if($submission->status === 'Pending')
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-md font-bold rounded-full">{{ $submission->status }}</span>
                                @elseif($submission->status === 'Waiting')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-md font-bold rounded-full">{{ $submission->status }}</span>
                                @elseif($submission->status === 'Approved')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-md font-bold rounded-full">{{ $submission->status }}</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-md font-bold rounded-full">{{ $submission->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 text-center text-md text-gray-500">No recent submissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
