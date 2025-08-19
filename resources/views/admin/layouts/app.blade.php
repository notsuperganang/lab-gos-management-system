<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Admin Lab GOS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Heroicons -->
    <script src="https://unpkg.com/heroicons@2.0.18/24/outline/index.js" type="module"></script>
    
    <!-- Chart.js for dashboard charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Admin Styles -->
    <style>
        [x-cloak] { display: none !important; }
        
        .sidebar-transition {
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
        }
        
        .main-content-transition {
            transition: margin-left 0.3s ease-in-out;
        }
        
        .gradient-admin {
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 50%, #1E40AF 100%);
        }
        
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .metric-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
        }
        
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .stat-icon {
            background: linear-gradient(135deg, #FDB813 0%, #f59e0b 100%);
        }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="h-full font-sans antialiased" x-data="adminApp()" @resize.window="handleResize()">
    <div class="min-h-full flex">
        <!-- Sidebar -->
        @include('admin.layouts.sidebar')

        <!-- Main content area -->
        <div class="flex-1 flex flex-col main-content-transition" 
             :class="sidebarOpen ? 'ml-64' : 'ml-16'">
            
            <!-- Top header -->
            @include('admin.layouts.header')

            <!-- Main content -->
            <main class="flex-1 pb-8">
                <!-- Page header with breadcrumbs and actions -->
                <div class="bg-white shadow-sm border-b border-gray-200">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="py-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:leading-9 sm:truncate">
                                        @yield('page-title', 'Dashboard')
                                    </h1>
                                    @hasSection('breadcrumbs')
                                        <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                                            <nav class="flex" aria-label="Breadcrumb">
                                                <ol class="flex items-center space-x-4">
                                                    @yield('breadcrumbs')
                                                </ol>
                                            </nav>
                                        </div>
                                    @endif
                                </div>
                                @hasSection('page-actions')
                                    <div class="flex-shrink-0 flex">
                                        @yield('page-actions')
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page content -->
                <div class="px-4 sm:px-6 lg:px-8 py-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Loading overlay -->
    <div x-show="loading" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="text-gray-700 font-medium">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Toast notifications -->
    <div x-show="toast.show" 
         x-transition:enter="transform transition ease-in-out duration-500"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-500"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed top-4 right-4 z-50 max-w-sm w-full"
         x-cloak>
        <div class="bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 p-4"
             :class="{
                'border-l-4 border-green-500': toast.type === 'success',
                'border-l-4 border-red-500': toast.type === 'error',
                'border-l-4 border-yellow-500': toast.type === 'warning',
                'border-l-4 border-blue-500': toast.type === 'info'
             }">
            <div class="flex">
                <div class="flex-shrink-0">
                    <template x-if="toast.type === 'success'">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </template>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900" x-text="toast.title"></p>
                    <p class="mt-1 text-sm text-gray-500" x-text="toast.message" x-show="toast.message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="hideToast()" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Admin App Script -->
    <script>
        function adminApp() {
            return {
                sidebarOpen: true,
                loading: false,
                toast: {
                    show: false,
                    type: 'info',
                    title: '',
                    message: ''
                },
                
                init() {
                    // Initialize based on screen size
                    this.handleResize();
                    
                    // Set up CSRF token for API calls
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                },
                
                handleResize() {
                    if (window.innerWidth < 1024) {
                        this.sidebarOpen = false;
                    } else {
                        this.sidebarOpen = true;
                    }
                },
                
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },
                
                showToast(type, title, message = '') {
                    this.toast = {
                        show: true,
                        type: type,
                        title: title,
                        message: message
                    };
                    
                    setTimeout(() => {
                        this.hideToast();
                    }, 5000);
                },
                
                hideToast() {
                    this.toast.show = false;
                },
                
                setLoading(state) {
                    this.loading = state;
                },
                
                async apiCall(url, options = {}) {
                    this.setLoading(true);
                    try {
                        const response = await fetch(url, {
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                ...options.headers
                            },
                            ...options
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'API request failed');
                        }
                        
                        return data;
                    } catch (error) {
                        this.showToast('error', 'Error', error.message);
                        throw error;
                    } finally {
                        this.setLoading(false);
                    }
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>