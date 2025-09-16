@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('header-title', 'Admin Dashboard')
@section('header-subtitle', 'Laboratory Management System Overview')

@section('breadcrumbs')
    <li class="flex items-center">
        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"/>
        </svg>
        <span class="ml-4 text-sm font-medium text-gray-500">Dashboard</span>
    </li>
@endsection


@section('content')
<div x-data="dashboardData" class="space-y-6">

    <!-- Welcome Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    <span x-text="getGreeting()"></span>,
                    <span x-text="adminName || 'Admin'"></span>
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kelola sistem laboratorium dengan mudah dan efisien</p>
            </div>
            <div class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                <span x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></span>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Total Pending Requests -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Permohonan Pending</dt>
                        <dd class="text-2xl font-semibold text-gray-900" x-text="stats.summary?.total_pending_requests || 0"></dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Total Equipment -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Alat</dt>
                        <dd class="text-2xl font-semibold text-gray-900" x-text="stats.summary?.total_equipment || 0"></dd>
                        <dd class="text-xs text-gray-500 mt-1">
                            <span x-text="stats.summary?.available_equipment || 0"></span> tersedia
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Active Requests -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Permohonan Aktif</dt>
                        <dd class="text-2xl font-semibold text-gray-900" x-text="stats.summary?.total_active_requests || 0"></dd>
                        <dd class="text-xs text-green-600 mt-1">Sedang berlangsung</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Permintaan</dt>
                        <dd class="text-2xl font-semibold text-gray-900" x-text="(stats.summary?.total_pending_requests || 0) + (stats.summary?.total_active_requests || 0)"></dd>
                        <dd class="text-xs text-gray-500 mt-1">Keseluruhan</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Borrow Requests -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Peminjaman Alat</h3>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Pending:</span>
                    <span class="text-sm font-medium text-orange-600" x-text="stats.summary?.pending_borrow_requests || 0"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Aktif:</span>
                    <span class="text-sm font-medium text-green-600" x-text="stats.summary?.active_borrow_requests || 0"></span>
                </div>
                <div class="pt-3 border-t">
                    @if(Route::has('admin.borrowing.index'))
                    <a href="{{ route('admin.borrowing.index') }}"
                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Kelola Peminjaman
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @else
                    <span class="text-sm text-gray-400">Kelola Peminjaman</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Visit Requests -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Kunjungan Lab</h3>
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Pending:</span>
                    <span class="text-sm font-medium text-orange-600" x-text="stats.summary?.pending_visit_requests || 0"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Aktif:</span>
                    <span class="text-sm font-medium text-green-600" x-text="stats.summary?.active_visit_requests || 0"></span>
                </div>
                <div class="pt-3 border-t">
                    @if(Route::has('admin.visits.index'))
                    <a href="{{ route('admin.visits.index') }}"
                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Kelola Kunjungan
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @else
                    <span class="text-sm text-gray-400">Kelola Kunjungan</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Testing Requests -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Layanan Pengujian</h3>
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Pending:</span>
                    <span class="text-sm font-medium text-orange-600" x-text="stats.summary?.pending_testing_requests || 0"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Aktif:</span>
                    <span class="text-sm font-medium text-green-600" x-text="stats.summary?.active_testing_requests || 0"></span>
                </div>
                <div class="pt-3 border-t">
                    @if(Route::has('admin.testing.index'))
                    <a href="{{ route('admin.testing.index') }}"
                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Kelola Pengujian
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @else
                    <span class="text-sm text-gray-400">Kelola Pengujian</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.equipment.index') }}"
               class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Kelola Alat</p>
                    <p class="text-xs text-gray-500">Tambah & edit alat</p>
                </div>
            </a>

            <a href="{{ route('admin.articles.index') }}"
               class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Kelola Artikel</p>
                    <p class="text-xs text-gray-500">Tulis & edit artikel</p>
                </div>
            </a>

            <a href="{{ route('admin.gallery.index') }}"
               class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Kelola Galeri</p>
                    <p class="text-xs text-gray-500">Upload & atur foto</p>
                </div>
            </a>

            <a href="{{ route('admin.site-settings.index') }}"
               class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="p-2 bg-orange-100 rounded-lg mr-3">
                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Pengaturan</p>
                    <p class="text-xs text-gray-500">Konfigurasi sistem</p>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dashboardData', () => ({
        stats: {},
        adminName: '',
        apiToken: localStorage.getItem('admin_token'),

        async init() {
            if (!this.apiToken) {
                window.location.href = '/admin/login';
                return;
            }

            await this.loadAdminProfile();
            await this.loadDashboardData();
        },

        async loadAdminProfile() {
            try {
                const response = await fetch('/api/admin/profile', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${this.apiToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.adminName = data.data?.user?.name || data.data?.name || 'Admin';
                    }
                }
            } catch (error) {
                // Silently fail, will use 'Admin' as fallback
            }
        },

        async loadDashboardData() {
            try {
                const response = await fetch('/api/admin/dashboard/stats', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${this.apiToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        localStorage.removeItem('admin_token');
                        window.location.href = '/admin/login';
                        return;
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                if (data.success) {
                    this.stats = data.data;
                } else {
                    throw new Error(data.message || 'Failed to load dashboard data');
                }
            } catch (error) {
                alert('Gagal memuat data dashboard. Silakan refresh halaman.');
            }
        },

        getGreeting() {
            const hour = new Date().getHours();

            if (hour >= 5 && hour < 11) {
                return 'Selamat Pagi';
            } else if (hour >= 11 && hour < 15) {
                return 'Selamat Siang';
            } else if (hour >= 15 && hour < 18) {
                return 'Selamat Sore';
            } else {
                return 'Selamat Malam';
            }
        }
    }))
});
</script>
@endpush

