@extends('layouts.app')
@section('title', 'Group Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Group Information</h1>
            <p class="text-gray-500 text-sm">View and monitor all security groups</p>
        </div>
    </div>

    <!-- Groups Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        @forelse($groups as $group)
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="mb-5">
                    <h2 class="text-lg font-bold text-gray-800">{{ $group->nama_grup }}</h2>
                    <p class="text-sm text-black">{{ $group->users->count() }} members</p>
                </div>

                <div class="space-y-3">
                    @forelse($group->users as $member)
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-bold text-sm flex items-center justify-center flex-shrink-0">
                                    {{ collect(explode(' ', $member->nama))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('') }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 leading-tight">{{ $member->nama }}</p>
                                    <p class="text-xs text-black">{{ $member->username }}</p>
                                </div>
                            </div>
                            <div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider transition-colors duration-200 
                                    {{ $member->status === 'Active' 
                                    ? 'bg-green-200 text-green-700' 
                                    : 'bg-gray-200 text-gray-500' }}">
                                    {{ $member->status }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-black py-2">No members in this group.</p>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-6 text-gray-500">
                No groups found.
            </div>
        @endforelse
    </div>
</div>
@endsection
