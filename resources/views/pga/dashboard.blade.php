@extends('layouts.app')
@section('title', 'PGA Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Top Cards Requirements:
    - Waiting Approval (journal status = Waiting)
    - Approved Today (journal status = Approved today)
    - Rejected Journals (journal status = Rejected)
    - Total Groups (count tbl groups)
    -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Waiting Approval -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm text-black mb-1">Waiting Approval</p>
                <p class="text-3xl font-bold text-gray-800">{{ $waiting_approval }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                <i class="bi bi-clock text-2xl"></i>
            </div>
        </div>

        <!-- Approved Today -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm text-black mb-1">Approved Today</p>
                <p class="text-3xl font-bold text-gray-800">{{ $approved_today }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-green-100 text-green-500 flex items-center justify-center">
                <i class="bi bi-check-circle text-2xl"></i>
            </div>
        </div>

        <!-- Rejected Journals -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm text-black mb-1">Rejected Journals</p>
                <p class="text-3xl font-bold text-gray-800">{{ $rejected_journals }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-red-100 text-red-500 flex items-center justify-center">
                <i class="bi bi-x-circle text-2xl"></i>
            </div>
        </div>

        <!-- Total Groups -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm text-black mb-1">Total Groups</p>
                <p class="text-3xl font-bold text-gray-800">{{ $total_groups }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-500 flex items-center justify-center">
                <i class="bi bi-people text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Approval Queue Section -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Approval Queue</h2>
        <div class="space-y-4">
            @forelse($approval_queue as $journal)
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-bold text-gray-800">Journal from: {{ $journal->user->nama ?? '-' }}</p>
                            <span class="px-2.5 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-md">Waiting</span>
                        </div>
                        <p class="text-sm text-black mb-0.5"><span class="font-semibold text-black">Location:</span> {{ $journal->location->nama_lokasi ?? '-' }}</p>
                        <p class="text-sm text-black mb-0.5"><span class="font-semibold text-black">Shift:</span> {{ $journal->shift->nama_shift ?? '-' }}</p>
                        <p class="text-sm text-black"><span class="font-semibold text-black">Date:</span> {{ \Carbon\Carbon::parse($journal->tanggal)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="openViewModal({{ $journal->id }})" class="px-3 py-1.5 bg-blue-100 border border-gray-200 text-gray-700 text-sm font-bold rounded-lg hover:bg-blue-200 transition-colors flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-gray-200">
                            <i class="bi bi-eye text-blue-600"></i> View
                        </button>
                        <button onclick="openApprovalModal({{ $journal->id }}, 'Approved')" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition-colors flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <i class="bi bi-check2"></i> Approve
                        </button>
                        <button onclick="openApprovalModal({{ $journal->id }}, 'Rejected')" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg transition-colors flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <i class="bi bi-x"></i> Reject
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-black text-sm">
                    No journals waiting for approval.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 2-Column Grid: Most Active Users & Most Active Groups -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Most Active Users -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-person text-blue-600"></i> Most Active Users
            </h2>
            
            @php
                $max_user_submissions = $most_active_users->max('journals_count');
                if($max_user_submissions == 0) $max_user_submissions = 1;
            @endphp

            <div class="space-y-5">
                @forelse($most_active_users as $index => $user)
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 font-bold flex items-center justify-center flex-shrink-0 text-sm border border-blue-100">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-grow">
                            <p class="text-sm font-bold text-gray-800 mb-0.5">{{ $user->nama }}</p>
                            <div class="flex items-center gap-2 w-full">
                                <div class="flex-grow bg-blue-50 rounded-full h-4 relative overflow-hidden border border-blue-100">
                                    <div class="bg-blue-600 h-full transition-all duration-500 ease-out rounded-full flex items-center" 
                                        style="width: {{ ($user->journals_count / $max_user_submissions) * 100 }}%">
                                        <!-- <span class="text-[10px] pl-2 text-white font-bold leading-none">{{ $user->journals_count }} journals</span> -->
                                    </div>
                                </div>
                                <div class="text-right flex flex-col justify-end min-w-[50px]">
                                    <span class="font-bold text-black leading-none text-right">{{ $user->journals_count }}</span>
                                    <span class="text-sm text-black leading-none text-right mt-0.5">journals</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-black text-center py-4">No active users yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Most Active Groups -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Most Active Groups</h2>
            
            <div class="space-y-4">
                @forelse($most_active_groups as $group)
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-black text-md">{{ $group->nama_grup }}</p>
                            <p class="text-sm text-black">{{ $group->users_count }} members</p>
                        </div>
                        <div class="text-right flex flex-col items-center">
                            <span class="font-bold text-blue-600 text-2xl leading-none block">{{ $group->journals_count }}</span>
                            <span class="text-sm text-black block">journals</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-black text-center py-4">No active groups yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Journal Submissions (Last 7 Days) - Full Width -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Journal Submissions (Last 7 Days)</h2>
        
        @php
            $max_submissions = count($last_7_days_data) > 0 ? max(array_column($last_7_days_data, 'count')) : 0;
            if($max_submissions == 0) $max_submissions = 1; // Prevent division by zero
        @endphp

        <div class="space-y-4">
            @foreach($last_7_days_data as $data)
                <div class="flex items-center gap-4">
                    <div class="w-14 text-sm text-black whitespace-nowrap">{{ $data['date'] }}</div>
                    <div class="flex-grow bg-gray-100 rounded-full h-5 relative overflow-hidden">
                        <div class="bg-[#1a56db] h-full rounded-full transition-all duration-500 ease-out" 
                             style="width: {{ ($data['count'] / $max_submissions) * 100 }}%">
                        </div>
                    </div>
                    <div class="w-6 text-right font-bold text-gray-800">{{ $data['count'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Include Modal Approval -->
@include('modal_approval')

<!-- Include Modal Journal for Viewing -->
@include('modal_journal')

@endsection
