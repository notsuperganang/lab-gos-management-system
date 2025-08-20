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
                <img src="{{ asset('assets/images/logo-fisika-putih.png') }}" 
                     alt="Lab GOS" 
                     class="h-8 w-8 bg-blue-600 p-1 rounded">
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

        <!-- Right side: Notifications, user menu -->
        <div class="flex items-center space-x-4">
            
            <!-- Real-time clock -->
            <div class="hidden md:block text-sm text-gray-500">
                <div x-text="currentTime" class="font-mono"></div>
            </div>

            <!-- Notifications dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open; if(open) fetchNotifications()"
                        class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 relative">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3-3V8a6 6 0 10-12 0v6l-3 3h5a3 3 0 106 0z"/>
                    </svg>
                    <!-- Notification badge -->
                    <span x-show="unreadCount > 0" 
                          x-text="unreadCount > 99 ? '99+' : unreadCount"
                          class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center min-w-0 text-center"
                          style="font-size: 10px; line-height: 1;">
                    </span>
                </button>

                <!-- Notifications dropdown panel -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                     x-cloak>
                    
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Notifications</h3>
                            <button @click="markAllAsRead()" 
                                    class="text-sm text-blue-600 hover:text-blue-500">
                                Mark all read
                            </button>
                        </div>
                    </div>
                    
                    <div class="max-h-96 overflow-y-auto custom-scrollbar">
                        <template x-if="notifications.length === 0">
                            <div class="p-4 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3-3V8a6 6 0 10-12 0v6l-3 3h5a3 3 0 106 0z"/>
                                </svg>
                                <p class="mt-2">No notifications</p>
                            </div>
                        </template>
                        
                        <template x-for="notification in notifications" :key="notification.id">
                            <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer"
                                 :class="{ 'bg-blue-50': !notification.is_read }"
                                 @click="markAsRead(notification.id)">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-2 w-2 rounded-full"
                                             :class="{
                                                'bg-red-500': notification.priority === 'urgent',
                                                'bg-orange-500': notification.priority === 'high',
                                                'bg-blue-500': notification.priority === 'medium',
                                                'bg-gray-400': notification.priority === 'low'
                                             }">
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                        <p class="text-sm text-gray-500" x-text="notification.message"></p>
                                        <p class="text-xs text-gray-400 mt-1" x-text="notification.created_at_human"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <div class="p-3 border-t border-gray-200">
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-500 block text-center">
                            View all notifications
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick actions dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </button>

                <!-- Quick actions dropdown panel -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                     x-cloak>
                    
                    <div class="p-2">
                        <a href="{{ route('admin.equipment.create') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Equipment
                        </a>
                        <a href="{{ route('admin.staff.create') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Add Staff Member
                        </a>
                        <a href="{{ route('admin.articles.create') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Write Article
                        </a>
                        <hr class="my-2">
                        <a href="{{ route('admin.dashboard') }}?refresh=true" 
                           class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Refresh Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- User profile dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="flex items-center space-x-2 p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <div class="h-8 w-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </span>
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Admin User' }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role ?? 'admin') }}</p>
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
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                     x-cloak>
                    
                    <div class="p-2">
                        <div class="px-3 py-2 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'Admin User' }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'admin@labgos.ac.id' }}</p>
                        </div>
                        
                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md mt-2">
                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile Settings
                        </a>
                        
                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Preferences
                        </a>
                        
                        <hr class="my-2">
                        
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center px-3 py-2 text-sm text-red-700 hover:bg-red-50 rounded-md">
                                <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
function headerData() {
    return {
        currentTime: '',
        notifications: [],
        unreadCount: 0,
        
        init() {
            this.updateTime();
            setInterval(() => {
                this.updateTime();
            }, 1000);
            
            // Fetch initial notifications count
            this.fetchNotificationsCount();
            
            // Update notifications count every 30 seconds
            setInterval(() => {
                this.fetchNotificationsCount();
            }, 30000);
        },
        
        updateTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            });
        },
        
        async fetchNotificationsCount() {
            try {
                // Use AdminAPI client for proper authentication
                if (typeof AdminAPI !== 'undefined') {
                    const response = await AdminAPI.getNotifications();
                    if (response && response.success) {
                        // Count notifications or use provided count
                        this.unreadCount = response.data?.length || 0;
                    }
                } else {
                    // Fallback to direct fetch with proper headers
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch('/admin-api/notifications', {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        if (data.success) {
                            this.unreadCount = data.data?.length || 0;
                        }
                    } else {
                        console.warn('Non-JSON response for notifications, likely auth redirect');
                        this.unreadCount = 0;
                    }
                }
            } catch (error) {
                console.error('Error fetching notifications count:', error);
                this.unreadCount = 0;
            }
        },
        
        async fetchNotifications() {
            try {
                // Use AdminAPI client for proper authentication
                if (typeof AdminAPI !== 'undefined') {
                    const response = await AdminAPI.getNotifications();
                    if (response && response.success) {
                        this.notifications = response.data || [];
                        // Update unread count from actual notification data
                        this.unreadCount = this.notifications.filter(n => !n.is_read).length;
                    }
                } else {
                    // Fallback to direct fetch with proper headers
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch('/admin-api/notifications', {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        if (data.success) {
                            this.notifications = data.data || [];
                            this.unreadCount = this.notifications.filter(n => !n.is_read).length;
                        }
                    } else {
                        console.warn('Non-JSON response for notifications, likely auth redirect');
                        this.notifications = [];
                        this.unreadCount = 0;
                    }
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
                this.notifications = [];
                this.unreadCount = 0;
            }
        },
        
        async markAsRead(notificationId) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                await fetch(`/admin-api/notifications/${notificationId}/read`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                // Update local state
                const notification = this.notifications.find(n => n.id === notificationId);
                if (notification && !notification.is_read) {
                    notification.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },
        
        async markAllAsRead() {
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                await fetch('/admin-api/notifications/mark-all-read', {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                // Update local state
                this.notifications.forEach(notification => {
                    notification.is_read = true;
                });
                this.unreadCount = 0;
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        }
    }
}
</script>