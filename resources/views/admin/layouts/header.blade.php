<!-- Top header -->
<header class="bg-white shadow-sm border-b border-gray-200" x-data="headerData()">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">

        <!-- Left side: Mobile menu button and page title -->
        <div class="flex items-center space-x-4">
            <!-- Mobile menu button -->
            <button @click="$parent.toggleSidebar()"
                    class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Laboratory branding -->
            <div class="flex items-center space-x-3">
                <img src="{{ asset('assets/images/logo-fisika-hitam.png') }}"
                     alt="Lab GOS"
                     class="h-10 w-10 flex-shrink-0 object-contain">
                <div class="hidden sm:block">
                    <h1 class="text-lg font-semibold text-gray-900">
                        @yield('header-title', 'Laboratorium GOS Admin')
                    </h1>
                    <p class="text-sm text-gray-500">
                        @yield('header-subtitle', 'Departemen Fisika FMIPA Universitas Syiah Kuala')
                    </p>
                </div>
            </div>
        </div>

        <!-- Right side: User menu -->
        <div class="flex items-center space-x-4">

            <!-- Real-time clock -->
            <div class="hidden md:block text-sm text-gray-500">
                <div x-text="currentTime" class="font-mono"></div>
            </div>

            <!-- User profile dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="flex items-center space-x-2 p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <div x-show="!user" class="h-8 w-8 bg-gray-300 rounded-full animate-pulse"></div>
                    <template x-if="user">
                        <div>
                            <img x-show="user.avatar_path"
                                 :src="user.avatar_path ? `{{ asset('storage/') }}/${user.avatar_path}` : ''"
                                 :alt="user.name"
                                 class="h-8 w-8 rounded-full object-cover">
                            <div x-show="!user.avatar_path"
                                 class="h-8 w-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium" x-text="user.name ? user.name.charAt(0).toUpperCase() : 'A'"></span>
                            </div>
                        </div>
                    </template>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-medium text-gray-700" x-text="user ? user.name : 'Loading...'"></p>
                        <p class="text-xs text-gray-500" x-text="user ? (user.position || user.role.charAt(0).toUpperCase() + user.role.slice(1)) : 'Admin'"></p>
                    </div>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- User dropdown panel -->
                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                     x-cloak>

                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div x-show="!user" class="h-12 w-12 bg-gray-300 rounded-full animate-pulse"></div>
                            <template x-if="user">
                                <div>
                                    <img x-show="user.avatar_path"
                                         :src="user.avatar_path ? `{{ asset('storage/') }}/${user.avatar_path}` : ''"
                                         :alt="user.name"
                                         class="h-12 w-12 rounded-full object-cover">
                                    <div x-show="!user.avatar_path"
                                         class="h-12 w-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-lg font-medium" x-text="user.name ? user.name.charAt(0).toUpperCase() : 'A'"></span>
                                    </div>
                                </div>
                            </template>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="user ? user.name : 'Loading...'"></p>
                                <p class="text-xs text-gray-500 truncate" x-text="user ? user.email : 'admin@labgos.ac.id'"></p>
                                <p class="text-xs text-blue-600 font-medium" x-text="user ? (user.position || user.role.charAt(0).toUpperCase() + user.role.slice(1).replace('_', ' ')) : 'Admin'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-2">
                        <a href="{{ route('admin.profile.settings') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors duration-200">
                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Pengaturan Profil
                        </a>

                        <hr class="my-2">

                        <button type="button" onclick="handleLogout()"
                                class="w-full flex items-center px-3 py-2 text-sm text-red-700 hover:bg-red-50 rounded-md text-left transition-colors duration-200">
                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar
                        </button>

                        <script>
                        async function handleLogout() {
                            const token = localStorage.getItem('admin_token');

                            if (token) {
                                try {
                                    // Call API logout endpoint
                                    await fetch('/api/admin/logout', {
                                        method: 'POST',
                                        headers: {
                                            'Authorization': `Bearer ${token}`,
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        }
                                    });
                                } catch (error) {
                                    console.error('Logout API call failed:', error);
                                    // Continue with logout even if API call fails
                                }

                                // Clear token from localStorage
                                localStorage.removeItem('admin_token');
                            }

                            // Redirect to admin login page
                            window.location.href = '/admin/login';
                        }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

