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

            <!-- Loading State -->
            <div x-show="calendarWeeks.length === 0" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 class="text-center py-8">
                <div class="animate-spin w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full mx-auto"></div>
                <p class="text-sm text-gray-500 mt-2">Memuat kalender...</p>
            </div>

            <!-- Calendar Grid (only show when fully loaded) -->
            <div x-show="calendarWeeks.length > 0"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="grid grid-cols-7 gap-1">
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
                                'bg-gray-50 text-gray-500 opacity-60': day.isPast && day.isCurrentMonth && !day.isSelected,
                                'cursor-pointer hover:bg-gray-50': day.isSelectable,
                                'cursor-not-allowed opacity-50': !day.isSelectable
                            }"
                            class="relative p-3 border rounded-md transition-colors duration-200">
                            
                            <div class="text-sm font-medium" x-text="day.dayNumber"></div>
                            
                            <!-- Day Summary Indicators -->
                            <template x-if="day.summary && !day.isOtherMonth">
                                <div class="mt-1 space-y-1">
                                    <!-- Booked slots (always show if > 0) -->
                                    <template x-if="day.summary.booked_slots > 0">
                                        <div class="text-xs bg-red-100 text-red-700 px-1 rounded">
                                            <span x-text="day.summary.booked_slots"></span> Terpakai
                                        </div>
                                    </template>
                                    
                                    <!-- Blocked slots (always show if > 0) -->
                                    <template x-if="day.summary.blocked_slots > 0">
                                        <div class="text-xs bg-amber-100 text-amber-700 px-1 rounded">
                                            <span x-text="day.summary.blocked_slots"></span> Diblokir
                                        </div>
                                    </template>
                                    
                                    <!-- Expired slots (past dates or today's expired slots) -->
                                    <template x-if="day.summary.expired_slots > 0">
                                        <div class="text-xs bg-gray-100 text-gray-600 px-1 rounded">
                                            <span x-text="day.summary.expired_slots"></span> Berakhir
                                        </div>
                                    </template>
                                    
                                    <!-- Available slots (only show if > 0 and not a past date) -->
                                    <template x-if="day.summary.available_slots > 0 && !day.isPast">
                                        <div class="text-xs bg-emerald-100 text-emerald-700 px-1 rounded">
                                            <span x-text="day.summary.available_slots"></span> Tersedia
                                        </div>
                                    </template>
                                    
                                    <!-- Past dates indicator -->
                                    <template x-if="day.isPast && day.isCurrentMonth">
                                        <div class="text-xs bg-gray-100 text-gray-500 px-1 rounded">
                                            Sudah Lewat
                                        </div>
                                    </template>
                                </div>
                            </template>
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
                    <template x-if="daySlots && daySlots.length > 0">
                        <div class="mt-2 flex flex-wrap gap-4 text-xs">
                            <span class="text-emerald-600">
                                <span x-text="availableSlotsCount || 0"></span> Tersedia
                            </span>
                            <span class="text-red-600">
                                <span x-text="bookedSlotsCount || 0"></span> Terpakai
                            </span>
                            <span class="text-amber-600">
                                <span x-text="blockedSlotsCount || 0"></span> Diblokir
                            </span>
                            <template x-if="expiredSlotsCount > 0">
                                <span class="text-gray-500">
                                    <span x-text="expiredSlotsCount"></span> Berakhir
                                </span>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Slot List -->
                <div x-show="loadingSlots" class="text-center py-8">
                    <div class="animate-spin w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full mx-auto"></div>
                    <p class="text-sm text-gray-500 mt-2">Memuat slot...</p>
                </div>

                <div x-show="!loadingSlots && daySlots && daySlots.length > 0" class="space-y-2 max-h-96 overflow-y-auto">
                    <template x-for="slot in daySlots" :key="slot.start_time">
                        <div 
                            :class="{
                                'border-emerald-200 bg-emerald-50': slot.status === 'available' && !slot.isExpired,
                                'border-red-200 bg-red-50': slot.status === 'booked',
                                'border-amber-200 bg-amber-50': slot.status === 'blocked',
                                'border-gray-200 bg-gray-50 opacity-75': slot.isExpired
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
                                                'text-emerald-600': slot.status === 'available' && !slot.isExpired,
                                                'text-red-600': slot.status === 'booked',
                                                'text-amber-600': slot.status === 'blocked',
                                                'text-gray-500': slot.isExpired
                                            }"
                                            x-text="getStatusText(slot)">
                                        </span>
                                    </div>

                                    <!-- Show booking info -->
                                    <div x-show="slot.status === 'booked' && slot.visit_request" class="text-xs text-gray-600 mt-1">
                                        <div x-text="slot.visit_request?.visitor_name || 'Booking aktif'"></div>
                                    </div>

                                    <!-- Show block reason -->
                                    <template x-if="slot.status === 'blocked' && slot.blocked_info?.reason">
                                        <div class="text-xs text-gray-600 mt-1">
                                            <div x-text="slot.blocked_info?.reason"></div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Toggle Switch (only for available/blocked slots, not expired) -->
                                <div x-show="slot.status !== 'booked' && !slot.isExpired" class="ml-3">
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
                    <template x-if="selectedDate && selectedDate.isWeekend">
                        <div>
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="font-medium">Laboratorium Tutup</p>
                            <p class="text-sm">Kunjungan laboratorium hanya tersedia pada hari Senin - Jumat</p>
                        </div>
                    </template>
                    <template x-if="!selectedDate || !selectedDate.isWeekend">
                        <p>Tidak ada slot tersedia untuk tanggal ini</p>
                    </template>
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
        today: null,
        initialized: false,
        
        // Toast messages
        showSuccessToast: false,
        showErrorToast: false,
        successMessage: '',
        errorMessage: '',

        // Calendar data
        dayHeaders: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        calendarWeeks: [],

        async init() {
            // Prevent double initialization
            if (this.initialized) {
                return;
            }
            this.initialized = true;
            
            // Set today and auto-select it
            this.today = this.ymd(new Date());
            this.selectedDate = { date: this.today };

            await this.loadCurrentMonth();

            // After loading month data, find today's day object and check if it's selectable
            if (this.calendarWeeks.length > 0) {
                const todayDay = this.calendarWeeks
                    .flatMap(week => week.days)
                    .find(day => day.date === this.today);

                if (todayDay && todayDay.isSelectable) {
                    this.selectedDate = todayDay;
                    await this.loadDaySlots(this.today);
                } else {
                    // If today is weekend or not selectable, find the next available weekday
                    const nextAvailableDay = this.calendarWeeks
                        .flatMap(week => week.days)
                        .find(day => day.isCurrentMonth && day.isSelectable);

                    if (nextAvailableDay) {
                        this.selectedDate = nextAvailableDay;
                        await this.loadDaySlots(nextAvailableDay.date);
                    }
                }
            }
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
            return this.daySlots?.filter(slot => slot.status === 'available' && !slot.isExpired).length || 0;
        },

        get bookedSlotsCount() {
            return this.daySlots?.filter(slot => slot.status === 'booked').length || 0;
        },

        get blockedSlotsCount() {
            return this.daySlots?.filter(slot => slot.status === 'blocked').length || 0;
        },

        get expiredSlotsCount() {
            return this.daySlots?.filter(slot => slot.isExpired).length || 0;
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
            // Trigger calendar refresh for reactive selection updates
            this.buildCalendarGrid();

            // Only load slots for weekdays (isSelectable already ensures this)
            if (day.isSelectable) {
                await this.loadDaySlots(day.date);
            }
        },

        async toggleSlot(slot) {
            if (slot.status === 'booked' || slot.isExpired) return;

            this.togglingSlot = slot.start_time;
            
            try {
                // Ensure proper time format with seconds
                const startTime = slot.start_time.includes(':') && slot.start_time.split(':').length === 3 
                    ? slot.start_time 
                    : slot.start_time + ':00';
                const endTime = slot.end_time.includes(':') && slot.end_time.split(':').length === 3 
                    ? slot.end_time 
                    : slot.end_time + ':00';

                const response = await this.apiCall('PUT', '/api/admin/visit/blocks/toggle', {
                    date: this.selectedDate.date,
                    start_time: startTime,
                    end_time: endTime,
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
                
                // Handle validation errors specifically
                if (error.validationErrors) {
                    const errorMsg = Object.values(error.validationErrors).flat().join('; ');
                    this.showError('Validasi gagal: ' + errorMsg);
                } else {
                    this.showError('Gagal mengubah status slot: ' + error.message);
                }
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
            const todayDate = new Date();
            
            // Calculate first Sunday of the calendar grid (Sunday-first)
            const startDate = new Date(firstDay);
            const dayOfWeek = firstDay.getDay(); // 0 = Sunday, 1 = Monday, etc.
            startDate.setDate(1 - dayOfWeek); // Move to first Sunday
            
            const weeks = [];
            
            // Build 6 weeks of calendar
            for (let week = 0; week < 6; week++) {
                const days = [];
                
                // Build 7 days for this week
                for (let dayOffset = 0; dayOffset < 7; dayOffset++) {
                    // Calculate the actual date for this cell
                    const cellDate = new Date(startDate);
                    const totalOffset = (week * 7) + dayOffset;
                    cellDate.setDate(startDate.getDate() + totalOffset);
                    
                    const isCurrentMonth = cellDate.getMonth() === month - 1;
                    const isToday = this.ymd(cellDate) === this.today;
                    const isWeekend = cellDate.getDay() === 0 || cellDate.getDay() === 6;
                    const isPast = cellDate < new Date(todayDate.getFullYear(), todayDate.getMonth(), todayDate.getDate());
                    const isSelectable = isCurrentMonth && !isWeekend && !isPast;
                    
                    const dateStr = this.ymd(cellDate);
                    const rawSummary = this.monthData?.daily_summary?.find(d => d.date === dateStr);
                    
                    // Enhanced summary calculation considering time-based availability
                    const enhancedSummary = this.calculateEnhancedSummary(rawSummary, dateStr, isPast, isToday);
                    
                    const dayObj = {
                        date: dateStr,
                        dayNumber: cellDate.getDate(),
                        isCurrentMonth,
                        isOtherMonth: !isCurrentMonth,
                        isToday,
                        isWeekend,
                        isPast,
                        isSelectable,
                        isSelected: this.selectedDate?.date === dateStr,
                        summary: enhancedSummary
                    };
                    
                    days.push(dayObj);
                }
                
                weeks.push({
                    weekIndex: week,
                    days
                });
            }
            
            this.calendarWeeks = weeks;
        },

        // Local time helper functions
        ymd(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },

        parseYMD(dateStr) {
            const [year, month, day] = dateStr.split('-').map(Number);
            return new Date(year, month - 1, day);
        },

        // Time helper functions
        parseTimeToMinutes(timeStr) {
            // Convert "HH:MM:SS" or "HH:MM" to minutes since midnight
            const parts = timeStr.split(':');
            const hours = parseInt(parts[0], 10);
            const minutes = parseInt(parts[1], 10);
            return (hours * 60) + minutes;
        },

        getCurrentTimeMinutes() {
            const now = new Date();
            return (now.getHours() * 60) + now.getMinutes();
        },

        isSlotExpired(slot, selectedDate) {
            // Only check for expiration if the selected date is today
            if (selectedDate !== this.today) {
                return false;
            }
            
            const currentMinutes = this.getCurrentTimeMinutes();
            const slotEndMinutes = this.parseTimeToMinutes(slot.end_time);
            
            return slotEndMinutes <= currentMinutes;
        },

        calculateEnhancedSummary(rawSummary, dateStr, isPast, isToday) {
            // Default summary structure
            const defaultSummary = {
                available_slots: 0,
                booked_slots: 0,
                blocked_slots: 0,
                expired_slots: 0,
                total_slots: 0,
                real_availability: 0
            };

            if (!rawSummary) {
                return defaultSummary;
            }

            // Start with raw data
            let summary = {
                available_slots: rawSummary.available_slots || 0,
                booked_slots: rawSummary.booked_slots || 0,
                blocked_slots: rawSummary.blocked_slots || 0,
                expired_slots: 0,
                total_slots: rawSummary.total_slots || 0,
                real_availability: rawSummary.available_slots || 0
            };

            // For past dates: all non-booked slots are considered "expired/unavailable"
            if (isPast) {
                summary.expired_slots = summary.available_slots + summary.blocked_slots;
                summary.available_slots = 0;
                summary.real_availability = 0;
            }
            // For today: calculate expired slots based on current time
            else if (isToday && dateStr === this.today) {
                // Estimate expired slots (we don't have individual slot times here, 
                // so we'll estimate based on current time vs typical slot schedule)
                const currentMinutes = this.getCurrentTimeMinutes();
                const operatingStartMinutes = 8 * 60; // 08:00
                const operatingEndMinutes = 16 * 60; // 16:00
                const lunchStartMinutes = 12 * 60; // 12:00
                const lunchEndMinutes = 13 * 60; // 13:00
                
                if (currentMinutes > operatingStartMinutes) {
                    // Calculate how many slots have likely expired
                    const totalOperatingMinutes = (operatingEndMinutes - operatingStartMinutes) - (lunchEndMinutes - lunchStartMinutes);
                    const passedMinutes = Math.min(currentMinutes - operatingStartMinutes, totalOperatingMinutes);
                    const estimatedExpiredSlots = Math.floor(passedMinutes / 60); // 1-hour slots
                    
                    // Don't exceed available slots
                    summary.expired_slots = Math.min(estimatedExpiredSlots, summary.available_slots);
                    summary.available_slots = Math.max(0, summary.available_slots - summary.expired_slots);
                    summary.real_availability = summary.available_slots;
                }
            }

            return summary;
        },

        async loadDaySlots(dateStr) {
            // Check if it's a weekend before making API call
            const date = new Date(dateStr);
            const isWeekend = date.getDay() === 0 || date.getDay() === 6;

            if (isWeekend) {
                console.log('Skipping weekend date:', dateStr);
                this.daySlots = [];
                this.loadingSlots = false;
                return;
            }

            this.loadingSlots = true;

            try {
                const response = await this.apiCall('GET', `/api/admin/visit/availability?date=${dateStr}`);

                if (response.success) {
                    // Process slots to add expiration status
                    this.daySlots = (response.data.slots || []).map(slot => {
                        const isExpired = this.isSlotExpired(slot, dateStr);
                        return {
                            ...slot,
                            isExpired,
                            isToggleable: slot.status !== 'booked' && !isExpired
                        };
                    });
                } else {
                    throw new Error(response.message || 'Failed to load day slots');
                }
            } catch (error) {
                console.error('Error loading day slots:', error);

                // Handle specific weekend error gracefully
                if (error.message && error.message.includes('weekdays')) {
                    console.log('Weekend date selected, slots not available');
                    this.daySlots = [];
                } else {
                    this.showError('Gagal memuat slot: ' + error.message);
                    this.daySlots = [];
                }
            } finally {
                this.loadingSlots = false;
            }
        },

        // Utility functions
        getStatusText(slot) {
            if (slot.isExpired) {
                return 'Sudah Berakhir';
            }
            
            const statusMap = {
                'available': 'Tersedia',
                'booked': 'Terpakai',
                'blocked': 'Diblokir'
            };
            return statusMap[slot.status] || slot.status;
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
                // Handle validation errors (422)
                if (response.status === 422 && result.errors) {
                    const error = new Error(result.message || 'Validation failed');
                    error.validationErrors = result.errors;
                    throw error;
                }
                throw new Error(result.message || `HTTP ${response.status}`);
            }

            return result;
        }
    }));
});
</script>
@endpush