<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sistem Jurnal Operasional Keamanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans" x-data="{ sidebarOpen: false }">

    {{-- Full-width Header --}}
    <header class="fixed top-0 left-0 right-0 h-16 bg-white border-b z-50 flex items-center justify-between px-4 md:px-6">
        {{-- Left: Toggle (mobile) + Shield + Title --}}
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-500 hover:text-blue-600 mr-1">
                <i class="bi bi-list text-2xl"></i>
            </button>
            <!-- Logo -->
            <!-- Mini logo on mobile, full logo on desktop -->
            <img src="{{ asset('minilogo-aica.png') }}" alt="Logo" class="h-10 w-auto block md:hidden flex-shrink-0">
            <img src="{{ asset('logo-aica.png') }}" alt="Logo" class="h-10 w-auto hidden md:block flex-shrink-0">
            <div>
                <p class="font-bold text-base text-gray-800 leading-tight">
                    @if(auth()->user()->role === 'Admin')
                        Admin Dashboard
                    @elseif(auth()->user()->role === 'Satpam')
                        Satpam Dashboard
                    @else
                        PGA Dashboard
                    @endif
                </p>
                <p class="text-xs text-black hidden sm:block">Sistem Jurnal Operasional Keamanan</p>
            </div>
        </div>

        {{-- Right: Bell + User --}}
        <div class="flex items-center gap-3 md:gap-4">
            @if(auth()->user()->role === 'Satpam' || auth()->user()->role === 'PGA')
                <div class="relative" id="bellWrapper">
                    <button id="bellBtn" onclick="toggleBellDropdown()" class="relative text-gray-400 hover:text-blue-600 focus:outline-none">
                        <i class="bi bi-bell text-xl text-black"></i>
                        @if($journalReminders->count() > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                                {{ $journalReminders->count() }}
                            </span>
                        @endif
                    </button>

                    {{-- Reminder Dropdown --}}
                    <div id="bellDropdown" class="hidden absolute -right-10 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-[999] overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <span class="font-bold text-gray-800 text-sm">📋 Reminder</span>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            @forelse($journalReminders as $reminder)
                                <div class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition">
                                    <div class="flex items-start gap-3">
                                        @if($reminder['type'] === 'rejected')
                                            <div class="mt-0.5 w-7 h-7 rounded-full bg-red-100 text-red-500 flex items-center justify-center flex-shrink-0">
                                                <i class="bi bi-x-circle text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-red-700 mb-0.5">Harap revisi Jurnal</p>
                                                <p class="text-xs text-black"><span class="font-semibold">Tanggal:</span> {{ $reminder['tanggal'] }}</p>
                                                <p class="text-xs text-black"><span class="font-semibold">Lokasi:</span> {{ $reminder['lokasi'] }}</p>
                                                <p class="text-xs text-black"><span class="font-semibold">Shift:</span> {{ $reminder['shift'] }}</p>
                                            </div>
                                        @elseif($reminder['type'] === 'handover')
                                            <div class="mt-0.5 w-7 h-7 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center flex-shrink-0">
                                                <i class="bi bi-arrow-left-right text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-blue-700 mb-0.5">Harap Serah Terima Jurnal</p>
                                                <p class="text-xs text-black"><span class="font-semibold">Tanggal:</span> {{ $reminder['tanggal'] }}</p>
                                                <p class="text-xs text-black"><span class="font-semibold">Lokasi:</span> {{ $reminder['lokasi'] }}</p>
                                                <p class="text-xs text-black"><span class="font-semibold">Shift:</span> {{ $reminder['shift'] }}</p>
                                            </div>
                                        @elseif($reminder['type'] === 'waiting')
                                            <div class="mt-0.5 w-7 h-7 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center flex-shrink-0">
                                                <i class="bi bi-clock-history text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-yellow-700 mb-0.5">Harap Melakukan Persetujuan Jurnal</p>
                                                <p class="text-xs text-black"><span class="font-semibold">Tanggal:</span> {{ $reminder['tanggal'] }}</p>
                                                <p class="text-xs text-black"><span class="font-semibold">Lokasi:</span> {{ $reminder['lokasi'] }}</p>
                                                <p class="text-xs text-black"><span class="font-semibold">Shift:</span> {{ $reminder['shift'] }}</p>
                                            </div>
                                        @else
                                            <div class="mt-0.5 w-7 h-7 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center flex-shrink-0">
                                                <i class="bi bi-exclamation-circle text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-800 mb-0.5">Harap kumpulkan Jurnal</p>
                                                <p class="text-xs text-black"><span class="font-semibold">Tanggal:</span> {{ $reminder['tanggal'] }}</p>
                                                <p class="text-xs text-black"><span class="font-semibold">Lokasi:</span> {{ $reminder['lokasi'] }}</p>
                                                <p class="text-xs text-black"><span class="font-semibold">Shift:</span> {{ $reminder['shift'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-black text-sm">
                                    <i class="bi bi-check-circle text-2xl block mb-2 text-green-400"></i>
                                    Tidak ada reminder saat ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-blue-200 text-blue-600 font-bold flex items-center justify-center flex-shrink-0">
                    {{ collect(explode(' ', auth()->user()->nama))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('') }}
                </div>
                <div class="hidden sm:block">
                    <p class="text-sm font-bold text-gray-800 leading-tight">{{ auth()->user()->nama }}</p>
                    <p class="text-xs text-black-500">{{ auth()->user()->role }}</p>
                </div>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
            <button type="button" onclick="showLogoutModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                <i class="bi bi-box-arrow-right text-xl"></i>
            </button>
        </div>
    </header>

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/40 z-30 md:hidden"></div>

    {{-- Sidebar --}}
    <aside class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-56 bg-slate-900 text-white z-40 flex flex-col
                  transition-transform duration-300
                  -translate-x-full md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        <nav class="mt-4 px-3 space-y-1 flex-1">
            @if(auth()->user()->role === 'Admin')
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="bi bi-grid text-lg"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
                <a href="{{ route('admin.location-shift') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('admin.location-shift') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="bi bi-geo-alt text-lg"></i>
                    <span class="text-sm">Location & Shift</span>
                </a>
                <a href="{{ route('admin.user-management') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('admin.user-management') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="bi bi-person text-lg"></i>
                    <span class="text-sm">User Management</span>
                </a>
                <a href="{{ route('admin.group-management') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('admin.group-management') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="bi bi-people text-lg"></i>
                    <span class="text-sm">Group Management</span>
                </a>
                <a href="{{ route('admin.system-logs') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('admin.system-logs') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="bi bi-journal-text text-lg"></i>
                    <span class="text-sm">System Logs</span>
                </a>
            @elseif(auth()->user()->role === 'Satpam')
                <a href="{{ route('satpam.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('satpam.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="bi bi-grid text-lg"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
                <a href="{{ route('satpam.journal-submission') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('satpam.journal-submission') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="bi bi-journal-plus text-lg"></i>
                    <span class="text-sm">Journal Submission</span>
                </a>
                <a href="{{ route('log-history') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('log-history') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="bi bi-clock-history text-lg"></i>
                    <span class="text-sm">Log History</span>
                </a>
            @elseif(auth()->user()->role === 'PGA')
                <a href="{{ route('pga.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('pga.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="bi bi-grid text-lg"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
                <a href="{{ route('pga.groups-details') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('pga.groups-details') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="bi bi-people text-lg"></i>
                    <span class="text-sm">Group Details</span>
                </a>
                <a href="{{ route('log-history') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('log-history') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="bi bi-clock-history text-lg"></i>
                    <span class="text-sm">Log History</span>
                </a>
            @endif
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="pt-16 md:pl-56 min-h-screen">
        <main class="p-4 md:p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 flex justify-between items-center rounded">
                    <span><i class="bi bi-check-circle-fill mr-2"></i>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 flex justify-between items-center rounded">
                    <span><i class="bi bi-exclamation-triangle-fill mr-2"></i>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- Logout Confirmation Modal --}}
    <div id="logoutModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity" onclick="hideLogoutModal()"></div>
        
        {{-- Modal Content --}}
        <div class="relative bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full border border-gray-100 z-10">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="bi bi-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-gray-900">Konfirmasi Logout</h3>
                        <div class="mt-2">
                            <p class="text-md text-black">Apakah Anda yakin ingin keluar dari sistem? Anda harus login kembali untuk mengakses dashboard.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                <button type="button" 
                        onclick="document.getElementById('logout-form').submit()"
                        class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                    Ya, Logout
                </button>
                <button type="button" 
                        onclick="hideLogoutModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm transition-all">
                    Batal
                </button>
            </div>
        </div>
    </div>

</body>
<script>
    // Bell Dropdown Toggle
    function toggleBellDropdown() {
        const dropdown = document.getElementById('bellDropdown');
        if (dropdown) dropdown.classList.toggle('hidden');
    }

    // Close bell dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('bellWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById('bellDropdown')?.classList.add('hidden');
        }
    });

    // Logout Modal Logic
    function showLogoutModal() {
        const modal = document.getElementById('logoutModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden'; // Prevent scroll
        }
    }

    function hideLogoutModal() {
        const modal = document.getElementById('logoutModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = ''; // Restore scroll
        }
    }
</script>
</html>