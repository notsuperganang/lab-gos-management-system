<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 sidebar-transition"
     :class="sidebarOpen ? 'w-64' : 'w-16'"
     x-data="sidebarData()">

    <!-- Sidebar content -->
    <div class="flex flex-col h-full gradient-admin">
        <!-- Logo section -->
        <div class="flex items-center justify-center h-16 px-4 border-b border-blue-800">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('assets/images/logo-fisika-putih.png') }}"
                     alt="Lab GOS"
                     class="h-10 w-10 flex-shrink-0 object-contain">
                <div class="text-white font-bold text-lg transition-opacity duration-300"
                     :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                    <span x-show="sidebarOpen" x-transition>Lab GOS</span>
                </div>
            </div>
        </div>

        <!-- Navigation menu -->
        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto custom-scrollbar">

            <!-- Main Dashboard -->
            <div class="mb-6">
                <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider transition-opacity duration-300"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                    <span x-show="sidebarOpen" x-transition>Main</span>
                </h3>

                <a href="{{ route('admin.dashboard') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v14l-4-2-4 2V5z"/>
                    </svg>
                    <span class="transition-opacity duration-300"
                          :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                        <span x-show="sidebarOpen" x-transition>Dashboard</span>
                    </span>
                </a>
            </div>

            <!-- PROFIL DAN PUBLIKASI Section -->
            <div class="mb-6" x-data="{ open: {{ request()->routeIs('admin.site-settings.*', 'admin.staff.*', 'admin.articles.*', 'admin.gallery.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md text-blue-100 hover:bg-blue-700 hover:text-white transition-colors duration-200">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <span class="flex-1 text-left transition-opacity duration-300"
                          :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                        <span x-show="sidebarOpen" x-transition>Profil & Publikasi</span>
                    </span>
                    <svg x-show="sidebarOpen"
                         :class="{ 'rotate-90': open }"
                         class="ml-2 h-4 w-4 transform transition-transform duration-200"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <div x-show="open && sidebarOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="ml-6 mt-2 space-y-1">

                    @if(Route::has('admin.site-settings.index'))
                    <a href="{{ route('admin.site-settings.index') }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.site-settings.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                    @else
                    <span class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed">
                    @endif
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Konfigurasi Website
                    @if(Route::has('admin.site-settings.index'))
                    </a>
                    @else
                    </span>
                    @endif

                    <a href="{{ route('admin.staff.index') }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.staff.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        Kelola Staff
                    </a>

                    <a href="{{ route('admin.articles.index') }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.articles.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        Kelola Artikel
                    </a>

                    <a href="{{ route('admin.gallery.index') }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.gallery.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Kelola Galeri
                    </a>
                </div>
            </div>

            <!-- SARANA DAN PENJADWALAN Section -->
            <div class="mb-6" x-data="{ open: {{ request()->routeIs('admin.equipment.*', 'admin.visit-schedule.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md text-blue-100 hover:bg-blue-700 hover:text-white transition-colors duration-200">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <span class="flex-1 text-left transition-opacity duration-300"
                          :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                        <span x-show="sidebarOpen" x-transition>Sarana & Penjadwalan</span>
                    </span>
                    <svg x-show="sidebarOpen"
                         :class="{ 'rotate-90': open }"
                         class="ml-2 h-4 w-4 transform transition-transform duration-200"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <div x-show="open && sidebarOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="ml-6 mt-2 space-y-1">

                    <a href="{{ route('admin.equipment.index') }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.equipment.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Kelola Alat Laboratorium
                    </a>

                    <a href="{{ route('admin.visit-schedule.index') }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.visit-schedule.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Kelola Jadwal Kunjungan
                    </a>
                </div>
            </div>

            <!-- LAYANAN LABORATORIUM Section -->
            <div class="mb-6" x-data="{ open: {{ request()->routeIs('admin.borrowing.*', 'admin.visits.*', 'admin.testing.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md text-blue-100 hover:bg-blue-700 hover:text-white transition-colors duration-200">
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="flex-1 text-left transition-opacity duration-300"
                          :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                        <span x-show="sidebarOpen" x-transition>Layanan Laboratorium</span>
                    </span>
                    <svg x-show="sidebarOpen"
                         :class="{ 'rotate-90': open }"
                         class="ml-2 h-4 w-4 transform transition-transform duration-200"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <div x-show="open && sidebarOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="ml-6 mt-2 space-y-1">

                    @if(Route::has('admin.borrowing.index'))
                    <a href="{{ route('admin.borrowing.index') }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.borrowing.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                    @else
                    <span class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed">
                    @endif
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Kelola Peminjaman Alat
                    @if(Route::has('admin.borrowing.index'))
                    </a>
                    @else
                    </span>
                    @endif

                    <a href="{{ route('admin.visits.index') }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.visits.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Kelola Kunjungan Lab
                    </a>

                    <a href="{{ route('admin.testing.index') }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.testing.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        Kelola Pengujian Lab
                    </a>
                </div>
            </div>

            <!-- USER MANAGEMENT Section (Super Admin Only) -->
            @if(auth()->user() && (auth()->user()->role === 'super_admin' || (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('super_admin'))))
            <div class="mb-6">
                <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider transition-opacity duration-300"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                    <span x-show="sidebarOpen" x-transition>Administration</span>
                </h3>

                @if(Route::has('superadmin.users.index'))
                <a href="{{ route('superadmin.users.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('superadmin.users.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                @else
                <span class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed">
                @endif
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    <span class="transition-opacity duration-300"
                          :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                        <span x-show="sidebarOpen" x-transition>User Management</span>
                    </span>
                @if(Route::has('superadmin.users.index'))
                </a>
                @else
                </span>
                @endif
            </div>
            @endif
        </nav>

        <!-- Sidebar toggle button -->
        <div class="flex-shrink-0 p-4 border-t border-blue-800">
            <button @click="$parent.toggleSidebar()"
                    class="w-full flex items-center justify-center p-2 text-blue-100 hover:bg-blue-700 hover:text-white rounded-md transition-colors duration-200">
                <svg :class="sidebarOpen ? 'rotate-180' : ''"
                     class="h-5 w-5 transform transition-transform duration-200"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
                <span class="ml-2 transition-opacity duration-300"
                      :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">
                    <span x-show="sidebarOpen" x-transition>Collapse</span>
                </span>
            </button>
        </div>
    </div>
</div>

<!-- Mobile sidebar overlay -->
<div x-show="sidebarOpen && window.innerWidth < 1024"
     @click="sidebarOpen = false"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden"
     x-cloak>
</div>

<script>
function sidebarData() {
    return {
        // Add any sidebar-specific data here
    }
}
</script>
