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
    
    <!-- Chart.js for dashboard charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS only (no conflicting JS) -->
    @if(app()->environment('production'))
        @vite(['resources/css/app.css'])
    @else
        <link rel="stylesheet" href="{{ asset('build/assets/app-RF4t3PKI.css') }}">
    @endif
    
    <!-- Minimal Alpine Components for Layout -->
    <script>
        document.addEventListener('alpine:init', () => {
            // Minimal admin app component for layout
            Alpine.data('adminApp', () => ({
                sidebarOpen: window.innerWidth >= 1024,
                loading: false,
                
                init() {
                    this.handleResize();
                },
                
                handleResize() {
                    this.sidebarOpen = window.innerWidth >= 1024;
                },
                
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                }
            }));
            
            // Minimal sidebar component
            Alpine.data('sidebarData', () => ({
                collapsed: false,
                
                toggleCollapse() {
                    this.collapsed = !this.collapsed;
                }
            }));
            
            // Minimal header component  
            Alpine.data('headerData', () => ({
                currentTime: new Date().toLocaleTimeString(),
                unreadCount: 0,
                
                init() {
                    setInterval(() => {
                        this.currentTime = new Date().toLocaleTimeString();
                    }, 1000);
                }
            }));
            
            console.log('Alpine.js layout components initialized');
        });
    </script>
    
    <!-- Custom Admin Styles -->
    <style>
        [x-cloak] { display: none !important; }
        
        /* Ensure icons display correctly without heroicons */
        .icon-svg {
            display: inline-block;
            width: 1.25rem;
            height: 1.25rem;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        
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

<body class="h-full font-sans antialiased" x-data="adminApp" @resize.window="handleResize()">
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

    @stack('scripts')
</body>
</html>