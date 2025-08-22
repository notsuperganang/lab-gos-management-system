@extends('admin.layouts.app')

@section('title', 'Kelola Jadwal Kunjungan')

@section('content')
<div x-data="visitSchedule()" x-init="init()" class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Jadwal Kunjungan</h1>
            <p class="text-sm text-gray-500">Kelola ketersediaan waktu dan blokir slot kunjungan laboratorium.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="refreshData()" class="p-2 rounded-md border text-gray-600 hover:bg-gray-50" :disabled="loading">
                <svg :class="{'animate-spin': loading}" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Main Layout: Calendar + Slot Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-200px)]">
        
        <!-- Left Panel: Calendar (2/3 width) -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border p-6">
            <!-- Calendar Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    <span x-text="currentMonthName"></span> <span x-text="currentYear"></span>
                </h2>
                <div class="flex items-center gap-2">
                    <button @click="previousMonth()" class="p-2 rounded-md border text-gray-600 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="nextMonth()" class="p-2 rounded-md border text-gray-600 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <button @click="goToToday()" class="px-3 py-2 text-sm rounded-md border text-gray-600 hover:bg-gray-50">
                        Hari Ini
                    </button>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1">
                <!-- Day Headers -->
                <template x-for="day in dayHeaders">
                    <div class="p-3 text-center text-sm font-medium text-gray-500 border-b">
                        <span x-text="day"></span>
                    </div>
                </template>

                <!-- Calendar Days -->
                <template x-for="week in calendarWeeks" :key="week.weekIndex">
                    <template x-for="day in week.days" :key="day.date">
                        <div 
                            @click="selectDate(day)"
                            :class="{
                                'bg-blue-50 border-blue-200': day.isSelected,
                                'bg-gray-50 text-gray-400': day.isOtherMonth,
                                'bg-emerald-50 border-emerald-200': day.isToday && !day.isSelected,
                                'cursor-pointer hover:bg-gray-50': day.isSelectable,
                                'cursor-not-allowed opacity-50': !day.isSelectable
                            }"
                            class="relative p-3 border rounded-md transition-colors duration-200">
                            
                            <div class="text-sm font-medium" x-text="day.dayNumber"></div>
                            
                            <!-- Day Summary Indicators -->
                            <div x-show="day.summary && !day.isOtherMonth" class="mt-1 space-y-1">
                                <div x-show="day.summary.booked_slots > 0" class="text-xs bg-red-100 text-red-700 px-1 rounded">
                                    <span x-text="day.summary.booked_slots"></span> Terpakai
                                </div>
                                <div x-show="day.summary.blocked_slots > 0" class="text-xs bg-amber-100 text-amber-700 px-1 rounded">
                                    <span x-text="day.summary.blocked_slots"></span> Diblokir
                                </div>
                                <div x-show="day.summary.available_slots > 0" class="text-xs bg-emerald-100 text-emerald-700 px-1 rounded">
                                    <span x-text="day.summary.available_slots"></span> Tersedia
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
            </div>

            <!-- Calendar Legend -->
            <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-600">
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-emerald-100 border border-emerald-200 rounded"></div>
                    <span>Hari Ini</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-blue-100 border border-blue-200 rounded"></div>
                    <span>Dipilih</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-red-100 rounded"></div>
                    <span>Ada Booking</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-amber-100 rounded"></div>
                    <span>Diblokir</span>
                </div>
            </div>
        </div>

        <!-- Right Panel: Daily Slot Management (1/3 width) -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div x-show="!selectedDate" class="text-center text-gray-500 mt-8">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p>Pilih tanggal di kalender untuk mengelola slot waktu</p>
            </div>

            <div x-show="selectedDate" class="space-y-4">
                <!-- Selected Date Header -->
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Slot Tanggal <span x-text="selectedDateFormatted"></span>
                    </h3>
                    <p class="text-sm text-gray-500" x-text="selectedDayName"></p>
                    
                    <!-- Date Summary -->
                    <div x-show="daySlots?.length > 0" class="mt-2 flex gap-4 text-xs">
                        <span class="text-emerald-600">
                            <span x-text="availableSlotsCount"></span> Tersedia
                        </span>
                        <span class="text-red-600">
                            <span x-text="bookedSlotsCount"></span> Terpakai
                        </span>
                        <span class="text-amber-600">
                            <span x-text="blockedSlotsCount"></span> Diblokir
                        </span>
                    </div>
                </div>

                <!-- Slot List -->
                <div x-show="loadingSlots" class="text-center py-8">
                    <div class="animate-spin w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full mx-auto"></div>
                    <p class="text-sm text-gray-500 mt-2">Memuat slot...</p>
                </div>

                <div x-show="!loadingSlots && daySlots?.length > 0" class="space-y-2 max-h-96 overflow-y-auto">
                    <template x-for="slot in daySlots" :key="slot.start_time">
                        <div 
                            :class="{
                                'border-emerald-200 bg-emerald-50': slot.status === 'available',
                                'border-red-200 bg-red-50': slot.status === 'booked',
                                'border-amber-200 bg-amber-50': slot.status === 'blocked'
                            }"
                            class="border rounded-lg p-3">
                            
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="font-medium text-sm">
                                        <span x-text="slot.start_time.substring(0,5)"></span> - 
                                        <span x-text="slot.end_time.substring(0,5)"></span> WIB
                                    </div>
                                    
                                    <div class="text-xs mt-1">
                                        <span 
                                            :class="{
                                                'text-emerald-600': slot.status === 'available',
                                                'text-red-600': slot.status === 'booked',
                                                'text-amber-600': slot.status === 'blocked'
                                            }"
                                            x-text="getStatusText(slot.status)">
                                        </span>
                                    </div>

                                    <!-- Show booking info -->
                                    <div x-show="slot.status === 'booked' && slot.visit_request" class="text-xs text-gray-600 mt-1">
                                        <div x-text="slot.visit_request?.visitor_name || 'Booking aktif'"></div>
                                    </div>

                                    <!-- Show block reason -->
                                    <div x-show="slot.status === 'blocked' && slot.blocked_info?.reason" class="text-xs text-gray-600 mt-1">
                                        <div x-text="slot.blocked_info.reason"></div>
                                    </div>
                                </div>

                                <!-- Toggle Switch (only for available/blocked slots) -->
                                <div x-show="slot.status !== 'booked'" class="ml-3">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            :checked="slot.status === 'blocked'"
                                            @change="toggleSlot(slot)"
                                            :disabled="togglingSlot === slot.start_time"
                                            class="sr-only peer">
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="!loadingSlots && (!daySlots || daySlots.length === 0)" class="text-center py-8 text-gray-500">
                    <p>Tidak ada slot tersedia untuk tanggal ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center gap-3">
            <div class="animate-spin w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full"></div>
            <span>Memuat data...</span>
        </div>
    </div>

    <!-- Success Toast -->
    <div x-show="showSuccessToast" 
         x-transition:enter="transform ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transform ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed bottom-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span x-text="successMessage"></span>
        </div>
    </div>

    <!-- Error Toast -->
    <div x-show="showErrorToast"
         x-transition:enter="transform ease-out duration-300" 
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transform ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span x-text="errorMessage"></span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('visitSchedule', () => ({
        // State
        loading: false,
        loadingSlots: false,
        togglingSlot: null,
        currentDate: new Date(),
        selectedDate: null,
        monthData: null,
        daySlots: [],
        
        // Toast messages
        showSuccessToast: false,
        showErrorToast: false,
        successMessage: '',
        errorMessage: '',

        // Calendar data
        dayHeaders: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        calendarWeeks: [],

        async init() {
            console.log('Visit Schedule component initialized');
            await this.loadCurrentMonth();
        },

        // Computed properties
        get currentYear() {
            return this.currentDate.getFullYear();
        },

        get currentMonth() {
            return this.currentDate.getMonth() + 1;
        },

        get currentMonthName() {
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            return months[this.currentDate.getMonth()];
        },

        get selectedDateFormatted() {
            if (!this.selectedDate) return '';
            const date = new Date(this.selectedDate.date);
            return date.toLocaleDateString('id-ID', { 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric' 
            });
        },

        get selectedDayName() {
            if (!this.selectedDate) return '';
            const date = new Date(this.selectedDate.date);
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            return days[date.getDay()];
        },

        get availableSlotsCount() {
            return this.daySlots?.filter(slot => slot.status === 'available').length || 0;
        },

        get bookedSlotsCount() {
            return this.daySlots?.filter(slot => slot.status === 'booked').length || 0;
        },

        get blockedSlotsCount() {
            return this.daySlots?.filter(slot => slot.status === 'blocked').length || 0;
        },

        // Calendar navigation
        async previousMonth() {
            this.currentDate.setMonth(this.currentDate.getMonth() - 1);
            this.currentDate = new Date(this.currentDate);
            await this.loadCurrentMonth();
        },

        async nextMonth() {
            this.currentDate.setMonth(this.currentDate.getMonth() + 1);
            this.currentDate = new Date(this.currentDate);
            await this.loadCurrentMonth();
        },

        async goToToday() {
            this.currentDate = new Date();
            await this.loadCurrentMonth();
        },

        // Data loading
        async loadCurrentMonth() {
            this.loading = true;
            try {
                const response = await this.apiCall('GET', `/api/admin/visit/calendar?year=${this.currentYear}&month=${this.currentMonth}`);
                
                if (response.success) {
                    this.monthData = response.data;
                    this.buildCalendarGrid();
                } else {
                    throw new Error(response.message || 'Failed to load month data');
                }
            } catch (error) {
                console.error('Error loading month:', error);
                this.showError('Gagal memuat data kalender: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        async selectDate(day) {
            if (!day.isSelectable || day.isOtherMonth) return;

            this.selectedDate = day;
            this.loadingSlots = true;
            
            try {
                const response = await this.apiCall('GET', `/api/admin/visit/availability?date=${day.date}`);
                
                if (response.success) {
                    this.daySlots = response.data.slots || [];
                } else {
                    throw new Error(response.message || 'Failed to load day slots');
                }
            } catch (error) {
                console.error('Error loading day slots:', error);
                this.showError('Gagal memuat slot: ' + error.message);
                this.daySlots = [];
            } finally {
                this.loadingSlots = false;
            }
        },

        async toggleSlot(slot) {
            if (slot.status === 'booked') return;

            this.togglingSlot = slot.start_time;
            
            try {
                const response = await this.apiCall('PUT', '/api/admin/visit/blocks/toggle', {
                    date: this.selectedDate.date,
                    start_time: slot.start_time,
                    end_time: slot.end_time,
                    reason: slot.status === 'available' ? 'Diblokir oleh admin' : null
                });

                if (response.success) {
                    // Update slot status
                    slot.status = response.data.status;
                    if (response.data.status === 'blocked') {
                        slot.blocked_info = response.data.slot;
                    } else {
                        slot.blocked_info = null;
                    }

                    this.showSuccess(response.message);
                    
                    // Refresh month data to update calendar
                    await this.loadCurrentMonth();
                } else {
                    throw new Error(response.message || 'Failed to toggle slot');
                }
            } catch (error) {
                console.error('Error toggling slot:', error);
                this.showError('Gagal mengubah status slot: ' + error.message);
            } finally {
                this.togglingSlot = null;
            }
        },

        async refreshData() {
            await this.loadCurrentMonth();
            if (this.selectedDate) {
                await this.selectDate(this.selectedDate);
            }
        },

        // Calendar grid building
        buildCalendarGrid() {
            const year = this.currentYear;
            const month = this.currentMonth;
            const firstDay = new Date(year, month - 1, 1);
            const lastDay = new Date(year, month, 0);
            const today = new Date();
            
            // Get first day of calendar (might be from previous month)
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());
            
            const weeks = [];
            let currentWeekStart = new Date(startDate);
            
            for (let week = 0; week < 6; week++) {
                const days = [];
                
                for (let day = 0; day < 7; day++) {
                    const currentDate = new Date(currentWeekStart);
                    currentDate.setDate(currentDate.getDate() + day);
                    
                    const isCurrentMonth = currentDate.getMonth() === month - 1;
                    const isToday = currentDate.toDateString() === today.toDateString();
                    const isWeekend = currentDate.getDay() === 0 || currentDate.getDay() === 6;
                    const isPast = currentDate < today.setHours(0, 0, 0, 0);
                    const isSelectable = isCurrentMonth && !isWeekend && !isPast;
                    
                    const dateStr = currentDate.toISOString().split('T')[0];
                    const daySummary = this.monthData?.daily_summary?.find(d => d.date === dateStr);
                    
                    const dayObj = {
                        date: dateStr,
                        dayNumber: currentDate.getDate(),
                        isCurrentMonth,
                        isOtherMonth: !isCurrentMonth,
                        isToday,
                        isWeekend,
                        isPast,
                        isSelectable,
                        isSelected: this.selectedDate?.date === dateStr,
                        summary: daySummary
                    };
                    
                    days.push(dayObj);
                }
                
                weeks.push({
                    weekIndex: week,
                    days
                });
                
                currentWeekStart.setDate(currentWeekStart.getDate() + 7);
            }
            
            this.calendarWeeks = weeks;
        },

        // Utility functions
        getStatusText(status) {
            const statusMap = {
                'available': 'Tersedia',
                'booked': 'Terpakai',
                'blocked': 'Diblokir'
            };
            return statusMap[status] || status;
        },

        showSuccess(message) {
            this.successMessage = message;
            this.showSuccessToast = true;
            setTimeout(() => {
                this.showSuccessToast = false;
            }, 3000);
        },

        showError(message) {
            this.errorMessage = message;
            this.showErrorToast = true;
            setTimeout(() => {
                this.showErrorToast = false;
            }, 5000);
        },

        // API helper
        async apiCall(method, url, data = null) {
            const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
            if (!token) {
                throw new Error('Token tidak ditemukan. Silakan login ulang.');
            }

            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            };

            if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(url, options);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `HTTP ${response.status}`);
            }

            return result;
        }
    }));
});
</script>
@endpush