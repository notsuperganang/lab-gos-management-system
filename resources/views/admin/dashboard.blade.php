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

@section('page-actions')
    <button @click="refreshDashboard()" 
            :disabled="loading"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
        <svg class="mr-2 h-4 w-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Refresh Data
    </button>
@endsection

@section('content')
<div x-data="dashboardData" x-ref="dashboard" class="space-y-8">
    
    <!-- Real-time Status Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="h-3 w-3 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-gray-900">System Status: </span>
                    <span class="text-sm text-green-600 font-medium">Operational</span>
                </div>
                <div class="text-sm text-gray-500">
                    Last updated: <span x-text="new Date().toLocaleTimeString('id-ID')"></span>
                </div>
            </div>
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <span>Auto-refresh: <span x-text="(stats.autoRefresh !== undefined ? stats.autoRefresh : true) ? 'ON' : 'OFF'" 
                      :class="(stats.autoRefresh !== undefined ? stats.autoRefresh : true) ? 'text-green-600 font-medium' : 'text-gray-500'"></span></span>
                <button @click="stats.autoRefresh = !stats.autoRefresh" 
                        class="text-blue-600 hover:text-blue-500 font-medium">
                    <span x-text="(stats.autoRefresh !== undefined ? stats.autoRefresh : true) ? 'Disable' : 'Enable'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Pending Requests -->
        <div class="metric-card rounded-lg p-6 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="stat-icon p-3 rounded-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Requests</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900" x-text="stats.summary?.total_pending_requests || 0"></div>
                            <div class="ml-2 flex items-baseline text-sm font-semibold" 
                                 :class="getTrendColor(stats.summary?.pending_trend || 0)">
                                <svg class="self-center flex-shrink-0 h-4 w-4" 
                                     :class="(stats.summary?.pending_trend || 0) >= 0 ? 'transform rotate-180' : ''"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                </svg>
                                <span x-text="Math.abs(stats.summary?.pending_trend || 0) + '%'"></span>
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Total Equipment -->
        <div class="metric-card rounded-lg p-6 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="stat-icon p-3 rounded-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Equipment</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900" x-text="stats.summary?.total_equipment || 0"></div>
                            <div class="ml-2 text-sm text-gray-500">
                                <span x-text="stats.summary?.available_equipment || 0"></span> available
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Active Requests -->
        <div class="metric-card rounded-lg p-6 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="stat-icon p-3 rounded-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Active Requests</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900" x-text="stats.summary?.total_active_requests || 0"></div>
                            <div class="ml-2 text-sm text-green-600">
                                In progress
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Equipment Utilization -->
        <div class="metric-card rounded-lg p-6 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="stat-icon p-3 rounded-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Utilization Rate</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900" x-text="(stats.summary?.equipment_utilization_rate || 0) + '%'"></div>
                            <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-300" 
                                     :style="`width: ${stats.summary?.equipment_utilization_rate || 0}%`"></div>
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Request Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Borrow Requests -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Equipment Borrowing</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Active
                </span>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Pending:</span>
                    <span class="text-sm font-medium text-orange-600" x-text="stats.summary?.pending_borrow_requests || 0"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Active:</span>
                    <span class="text-sm font-medium text-green-600" x-text="stats.summary?.active_borrow_requests || 0"></span>
                </div>
                <div class="pt-2 border-t">
                    @if(Route::has('admin.borrowing.index'))
                    <a href="{{ route('admin.borrowing.index') }}" 
                       class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Manage Borrowing →
                    </a>
                    @else
                    <span class="text-sm text-gray-400">Manage Borrowing</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Visit Requests -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Lab Visits</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Scheduled
                </span>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Pending:</span>
                    <span class="text-sm font-medium text-orange-600" x-text="stats.summary?.pending_visit_requests || 0"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Active:</span>
                    <span class="text-sm font-medium text-green-600" x-text="stats.summary?.active_visit_requests || 0"></span>
                </div>
                <div class="pt-2 border-t">
                    @if(Route::has('admin.visits.index'))
                    <a href="{{ route('admin.visits.index') }}" 
                       class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Manage Visits →
                    </a>
                    @else
                    <span class="text-sm text-gray-400">Manage Visits</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Testing Requests -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Testing Services</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    Analysis
                </span>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Pending:</span>
                    <span class="text-sm font-medium text-orange-600" x-text="stats.summary?.pending_testing_requests || 0"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Active:</span>
                    <span class="text-sm font-medium text-green-600" x-text="stats.summary?.active_testing_requests || 0"></span>
                </div>
                <div class="pt-2 border-t">
                    @if(Route::has('admin.testing.index'))
                    <a href="{{ route('admin.testing.index') }}" 
                       class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Manage Testing →
                    </a>
                    @else
                    <span class="text-sm text-gray-400">Manage Testing</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Request Trends Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Request Trends</h3>
            </div>
            <div class="h-64">
                <canvas id="requestTrendsChart"></canvas>
            </div>
        </div>

        <!-- Equipment Usage Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Equipment Usage</h3>
            </div>
            <div class="h-64">
                <canvas id="equipmentUsageChart"></canvas>
            </div>
        </div>

        <!-- Status Distribution Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Request Status</h3>
            </div>
            <div class="h-64">
                <canvas id="statusDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Insights and Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Most Requested Equipment -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Most Requested Equipment</h3>
            <div class="space-y-4">
                <template x-for="equipment in stats.quick_insights?.most_requested_equipment?.slice(0, 5) || []" :key="equipment.id">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate" x-text="equipment.name"></p>
                            <p class="text-xs text-gray-500" x-text="equipment.category"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 text-right">
                            <p class="text-sm font-medium text-gray-900" x-text="equipment.request_count + ' requests'"></p>
                            <div class="w-20 bg-gray-200 rounded-full h-1.5 mt-1">
                                <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" 
                                     :style="`width: ${Math.min(100, (equipment.request_count / (stats.quick_insights?.most_requested_equipment?.[0]?.request_count || 1)) * 100)}%`"></div>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="!stats.quick_insights?.most_requested_equipment?.length">
                    <div class="text-center text-gray-500 py-4">
                        <p class="text-sm">No equipment requests yet</p>
                    </div>
                </template>
            </div>
        </div>

        <!-- System Alerts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">System Alerts</h3>
            <div class="space-y-3">
                <template x-for="alert in stats.alerts?.slice(0, 5) || []" :key="alert.type">
                    <div class="flex items-start space-x-3 p-3 rounded-lg"
                         :class="{
                             'bg-red-50 border border-red-200': alert.priority === 'high',
                             'bg-yellow-50 border border-yellow-200': alert.priority === 'medium',
                             'bg-blue-50 border border-blue-200': alert.priority === 'low'
                         }">
                        <div class="flex-shrink-0">
                            <div class="h-2 w-2 rounded-full mt-2"
                                 :class="{
                                     'bg-red-500': alert.priority === 'high',
                                     'bg-yellow-500': alert.priority === 'medium',
                                     'bg-blue-500': alert.priority === 'low'
                                 }">
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900" x-text="alert.title"></p>
                            <p class="text-xs text-gray-600" x-text="alert.message"></p>
                            <template x-if="alert.action_url">
                                <a :href="alert.action_url" class="text-xs text-blue-600 hover:text-blue-500 font-medium">
                                    Take Action →
                                </a>
                            </template>
                        </div>
                    </div>
                </template>
                <template x-if="!stats.alerts?.length">
                    <div class="text-center text-gray-500 py-4">
                        <svg class="mx-auto h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm mt-2">All systems operating normally</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                    View All →
                </a>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            <template x-for="activity in recentActivities" :key="activity.id">
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full flex items-center justify-center"
                                 :class="{
                                     'bg-green-100 text-green-600': activity.type === 'approved',
                                     'bg-blue-100 text-blue-600': activity.type === 'created',
                                     'bg-yellow-100 text-yellow-600': activity.type === 'updated',
                                     'bg-red-100 text-red-600': activity.type === 'rejected'
                                 }">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <template x-if="activity.type === 'approved'">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </template>
                                    <template x-if="activity.type === 'created'">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                    </template>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium" x-text="activity.causer?.name || 'System'"></span>
                                <span x-text="activity.description || activity.event || 'performed an action'"></span>
                            </p>
                            <p class="text-xs text-gray-500" x-text="formatDate(activity.created_at)"></p>
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Empty state -->
            <div x-show="!recentActivities || recentActivities.length === 0" class="text-center py-8 text-gray-500">
                <p class="text-sm">No recent activity</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>[x-cloak] { display: none; }</style>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dashboardData', () => ({
        // Initialize empty data - will be loaded via API
        stats: {},
        recentActivities: [],
        loading: false,
        chartsInitialized: false,
        apiToken: localStorage.getItem('admin_token'),
        
        // Initialize when component is ready
        async init() {
            console.log('Dashboard initializing...');
            
            // Check if user is authenticated
            if (!this.apiToken) {
                console.error('No admin token found, redirecting to login');
                window.location.href = '/admin/login';
                return;
            }
            
            // Load dashboard data
            await this.loadDashboardData();
            
            this.$nextTick(() => {
                if (typeof Chart !== 'undefined') {
                    this.initCharts();
                }
            });
        },
        
        // Chart initialization using real data
        initCharts() {
            if (this.chartsInitialized) return;
            
            try {
                console.log('Initializing charts with real data:', this.stats);
                
                // Request Trends Chart - use real request data
                const trendsCanvas = document.getElementById('requestTrendsChart');
                if (trendsCanvas) {
                    const requestData = this.getRequestTrendsData();
                    new Chart(trendsCanvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: requestData.labels,
                            datasets: [{
                                label: 'Total Requests',
                                data: requestData.values,
                                borderColor: '#1E40AF',
                                backgroundColor: 'rgba(30, 64, 175, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } }
                        }
                    });
                }
                
                // Equipment Usage Chart - use real equipment data
                const equipmentCanvas = document.getElementById('equipmentUsageChart');
                if (equipmentCanvas) {
                    const equipmentData = this.getEquipmentUsageData();
                    new Chart(equipmentCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: equipmentData.labels,
                            datasets: [{
                                label: 'Equipment Count',
                                data: equipmentData.values,
                                backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } }
                        }
                    });
                }
                
                // Status Distribution Chart - use real status data
                const statusCanvas = document.getElementById('statusDistributionChart');
                if (statusCanvas) {
                    const statusData = this.getStatusDistributionData();
                    new Chart(statusCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: statusData.labels,
                            datasets: [{
                                data: statusData.values,
                                backgroundColor: ['#F59E0B', '#3B82F6', '#10B981', '#EF4444', '#8B5CF6']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%'
                        }
                    });
                }
                
                this.chartsInitialized = true;
                console.log('Charts initialized successfully with real data');
            } catch (error) {
                console.error('Chart initialization error:', error);
            }
        },
        
        // Get request trends data from stats
        getRequestTrendsData() {
            const summary = this.stats.summary || {};
            return {
                labels: ['Borrow', 'Visit', 'Testing'],
                values: [
                    (summary.pending_borrow_requests || 0) + (summary.active_borrow_requests || 0),
                    (summary.pending_visit_requests || 0) + (summary.active_visit_requests || 0),
                    (summary.pending_testing_requests || 0) + (summary.active_testing_requests || 0)
                ]
            };
        },
        
        // Get equipment usage data from stats
        getEquipmentUsageData() {
            const summary = this.stats.summary || {};
            const total = summary.total_equipment || 1;
            const available = summary.available_equipment || 0;
            const inUse = Math.max(0, total - available);
            
            return {
                labels: ['Available', 'In Use', 'Total'],
                values: [available, inUse, total]
            };
        },
        
        // Get status distribution data from stats
        getStatusDistributionData() {
            const summary = this.stats.summary || {};
            return {
                labels: ['Pending', 'Active'],
                values: [
                    summary.total_pending_requests || 0,
                    summary.total_active_requests || 0
                ]
            };
        },
        
        // Utility functions
        getTrendColor(trend) {
            if (trend > 0) return 'text-red-600';
            if (trend < 0) return 'text-green-600';  
            return 'text-gray-500';
        },
        
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                console.warn('Error formatting date:', dateString);
                return 'Invalid date';
            }
        },
        
        // Load dashboard data from API
        async loadDashboardData() {
            this.loading = true;
            try {
                const response = await fetch('/api/admin/dashboard/stats?refresh_cache=false', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${this.apiToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        console.error('Unauthorized access, redirecting to login');
                        localStorage.removeItem('admin_token');
                        window.location.href = '/admin/login';
                        return;
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                if (data.success) {
                    this.stats = data.data;
                    this.recentActivities = data.data.recent_activities || [];
                    console.log('Dashboard data loaded successfully:', this.stats);
                    
                    // Reinitialize charts with new data
                    this.chartsInitialized = false;
                    this.$nextTick(() => {
                        if (typeof Chart !== 'undefined') {
                            this.initCharts();
                        }
                    });
                } else {
                    throw new Error(data.message || 'Failed to load dashboard data');
                }
            } catch (error) {
                console.error('Failed to load dashboard data:', error);
                // Show user-friendly error message
                alert('Failed to load dashboard data. Please refresh the page or contact support.');
            } finally {
                this.loading = false;
            }
        },

        // Dashboard refresh functionality
        async refreshDashboard() {
            console.log('Refreshing dashboard...');
            this.loading = true;
            try {
                // Force refresh cache
                const response = await fetch('/api/admin/dashboard/stats?refresh_cache=true', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${this.apiToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        console.error('Unauthorized access, redirecting to login');
                        localStorage.removeItem('admin_token');
                        window.location.href = '/admin/login';
                        return;
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                if (data.success) {
                    this.stats = data.data;
                    this.recentActivities = data.data.recent_activities || [];
                    console.log('Dashboard refreshed successfully');
                    
                    // Reinitialize charts with new data
                    this.chartsInitialized = false;
                    this.$nextTick(() => {
                        if (typeof Chart !== 'undefined') {
                            this.initCharts();
                        }
                    });
                } else {
                    throw new Error(data.message || 'Failed to refresh dashboard');
                }
            } catch (error) {
                console.error('Failed to refresh dashboard:', error);
                alert('Failed to refresh dashboard. Please try again.');
            } finally {
                this.loading = false;
            }
        }
    }))
});

// Make functions globally available for compatibility
window.getTrendColor = function(trend) {
    if (trend > 0) return 'text-red-600';
    if (trend < 0) return 'text-green-600';  
    return 'text-gray-500';
};

window.formatDate = function(dateString) {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (error) {
        return 'Invalid date';
    }
};
</script>
@endpush

