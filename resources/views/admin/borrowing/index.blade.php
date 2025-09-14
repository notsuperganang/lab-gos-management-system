@extends('admin.layouts.app')

@section('title', 'Kelola Permohonan Peminjaman')

@section('content')
<div x-data="borrowRequestsData()" x-init="init()" class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Permohonan Peminjaman</h1>
            <p class="text-sm text-gray-500">Kelola permohonan peminjaman alat laboratorium dari mahasiswa dan peneliti.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="loadRequests()" class="p-2 rounded-md border text-gray-600 hover:bg-gray-50" :disabled="loading">
                <svg :class="{'animate-spin': loading}" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4" x-show="summary">
        <!-- Total Requests -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 h-8 flex items-center">Total Permohonan</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="summary?.total_requests || 0"></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 h-8 flex items-center">Menunggu Review</p>
                    <p class="text-2xl font-bold text-yellow-600" x-text="summary?.pending_requests || 0"></p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 h-8 flex items-center">Disetujui</p>
                    <p class="text-2xl font-bold text-green-600" x-text="summary?.approved_requests || 0"></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 h-8 flex items-center">Sedang Berlangsung</p>
                    <p class="text-2xl font-bold text-blue-600" x-text="summary?.active_requests || 0"></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Rejected -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 h-8 flex items-center">Ditolak</p>
                    <p class="text-2xl font-bold text-red-600" x-text="summary?.rejected_requests || 0"></p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cancelled -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 h-8 flex items-center">Dibatalkan</p>
                    <p class="text-2xl font-bold text-gray-600" x-text="summary?.cancelled_requests || 0"></p>
                </div>
                <div class="p-3 bg-gray-100 rounded-full">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-lg border space-y-4 shadow-sm">
        <div class="flex flex-wrap gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Pencarian</label>
                <div class="relative">
                    <input type="text" 
                           x-model.debounce.400ms="filters.search" 
                           @input="loadRequests()" 
                           placeholder="Cari nama peminjam atau supervisor..." 
                           class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select x-model="filters.status" @change="loadRequests()" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Review</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                    <option value="active">Sedang Berlangsung</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>

            <!-- Date From -->
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                <input type="date" 
                       x-model="filters.date_from" 
                       @change="loadRequests()" 
                       class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
            </div>

            <!-- Date To -->
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Selesai</label>
                <input type="date" 
                       x-model="filters.date_to" 
                       @change="loadRequests()" 
                       class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
            </div>

            <!-- Reset Filters -->
            <div>
                <button @click="resetFilters()" 
                        class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md border">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
        <!-- Loading State -->
        <div x-show="loading" class="flex items-center justify-center py-12">
            <div class="flex items-center space-x-2 text-gray-500">
                <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Memuat data...</span>
            </div>
        </div>

        <!-- Table -->
        <div x-show="!loading" class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Permohonan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supervisor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lama Peminjaman</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="(request, index) in (requests || [])" :key="`request-${request.request_id || request.id || index}`">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="request.request_id"></td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div x-text="getMembersText(request.members)"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div x-text="request.supervisor?.name"></div>
                                <div class="text-xs text-gray-500" x-text="request.supervisor?.email"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div x-text="formatDate(request.borrow_date) + ' - ' + formatDate(request.return_date)"></div>
                                <div class="text-xs text-gray-500" x-text="formatDateTime(request.submitted_at)"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                <div x-text="calculateDuration(request.borrow_date, request.return_date)"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusBadgeClass(request.status)"
                                      class="px-2 py-1 text-xs font-medium rounded-full"
                                      x-text="getStatusText(request.status)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- Detail Button (always available) -->
                                    <button @click="showDetail(request.id)" 
                                            class="text-blue-600 hover:text-blue-900 text-xs px-2 py-1 rounded border border-blue-200 hover:bg-blue-50">
                                        Detail
                                    </button>
                                    
                                    <!-- Status-specific action buttons -->
                                    <template x-if="request.status === 'pending'">
                                        <div class="flex space-x-1">
                                            <button @click="showApproveModal(request.id)" 
                                                    class="text-green-600 hover:text-green-900 text-xs px-2 py-1 rounded border border-green-200 hover:bg-green-50">
                                                Setujui
                                            </button>
                                            <button @click="showDetail(request.id); $nextTick(() => showRejectModal())" 
                                                    class="text-red-600 hover:text-red-900 text-xs px-2 py-1 rounded border border-red-200 hover:bg-red-50">
                                                Tolak
                                            </button>
                                        </div>
                                    </template>
                                    
                                    <template x-if="request.status === 'approved'">
                                        <button @click="showDetail(request.id); $nextTick(() => { statusForm.status = 'active'; showUpdateStatusModalOpen = true; })" 
                                                class="text-blue-600 hover:text-blue-900 text-xs px-2 py-1 rounded border border-blue-200 hover:bg-blue-50">
                                            Mulai
                                        </button>
                                    </template>
                                    
                                    <template x-if="request.status === 'active'">
                                        <button @click="showDetail(request.id); $nextTick(() => { statusForm.status = 'completed'; showUpdateStatusModalOpen = true; })" 
                                                class="text-green-600 hover:text-green-900 text-xs px-2 py-1 rounded border border-green-200 hover:bg-green-50">
                                            Selesai
                                        </button>
                                    </template>
                                    
                                    <!-- PDF Letter for approved/active/completed -->
                                    <template x-if="['approved', 'active', 'completed'].includes(request.status)">
                                        <button @click="downloadLetter(request.id)" 
                                                class="text-purple-600 hover:text-purple-900 text-xs px-2 py-1 rounded border border-purple-200 hover:bg-purple-50">
                                            Surat
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && requests.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada permohonan peminjaman</h3>
            <p class="mt-1 text-sm text-gray-500">Belum ada permohonan peminjaman yang sesuai dengan filter yang dipilih.</p>
        </div>

        <!-- Pagination -->
        <div x-show="!loading && pagination.total > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button @click="previousPage()" 
                            :disabled="!pagination.prev_page_url" 
                            :class="pagination.prev_page_url ? 'text-gray-700 hover:text-gray-500' : 'text-gray-300 cursor-not-allowed'"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md bg-white">
                        Previous
                    </button>
                    <button @click="nextPage()" 
                            :disabled="!pagination.next_page_url" 
                            :class="pagination.next_page_url ? 'text-gray-700 hover:text-gray-500' : 'text-gray-300 cursor-not-allowed'"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md bg-white">
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan <span class="font-medium" x-text="pagination.from || 0"></span> sampai 
                            <span class="font-medium" x-text="pagination.to || 0"></span> dari 
                            <span class="font-medium" x-text="pagination.total || 0"></span> hasil
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <button @click="previousPage()" 
                                    :disabled="!pagination.prev_page_url" 
                                    :class="pagination.prev_page_url ? 'text-gray-500 hover:bg-gray-50' : 'text-gray-300 cursor-not-allowed'"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button @click="nextPage()" 
                                    :disabled="!pagination.next_page_url" 
                                    :class="pagination.next_page_url ? 'text-gray-500 hover:bg-gray-50' : 'text-gray-300 cursor-not-allowed'"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showDetailModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"
         @click="closeDetail()">
        
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showDetailModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-6xl"
                     @click.stop>
                    
                    <!-- Modal Content -->
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Detail Permohonan Peminjaman</h3>
                                <p class="text-sm text-gray-500" x-text="'ID: ' + (selectedRequest?.request_id || '')"></p>
                            </div>
                            <button @click="closeDetail()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div x-show="selectedRequest" class="space-y-6 max-h-[70vh] overflow-y-auto">
                            <!-- Status Timeline -->
                            <div class="mb-6">
                                <h4 class="font-semibold text-gray-900 mb-4">Status Timeline</h4>
                                <div class="relative">
                                    <div class="flex items-center justify-between">
                                        <!-- Pending -->
                                        <div class="flex flex-col items-center text-center relative z-10">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium"
                                                 :class="['pending', 'under_review', 'approved', 'active', 'completed', 'rejected', 'cancelled'].includes(selectedRequest?.status) 
                                                         ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-500'">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span class="text-xs mt-1 font-medium">Diajukan</span>
                                            <span class="text-xs text-gray-500" x-text="formatDate(selectedRequest?.created_at)"></span>
                                        </div>

                                        <!-- Under Review -->
                                        <div class="flex flex-col items-center text-center relative z-10">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium"
                                                 :class="['under_review', 'approved', 'active', 'completed', 'rejected'].includes(selectedRequest?.status) 
                                                         ? 'bg-yellow-500 text-white' : 'bg-gray-300 text-gray-500'">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <span class="text-xs mt-1 font-medium">Review</span>
                                            <span class="text-xs text-gray-500" x-show="selectedRequest?.status !== 'pending'">-</span>
                                        </div>

                                        <!-- Approved/Rejected -->
                                        <div class="flex flex-col items-center text-center relative z-10">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium"
                                                 :class="selectedRequest?.status === 'rejected' || selectedRequest?.status === 'cancelled' 
                                                         ? 'bg-red-500 text-white' 
                                                         : (['approved', 'active', 'completed'].includes(selectedRequest?.status) 
                                                            ? 'bg-green-600 text-white' : 'bg-gray-300 text-gray-500')">
                                                <svg x-show="selectedRequest?.status === 'rejected' || selectedRequest?.status === 'cancelled'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                                <svg x-show="!(['rejected', 'cancelled'].includes(selectedRequest?.status))" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span class="text-xs mt-1 font-medium" 
                                                  x-text="selectedRequest?.status === 'rejected' ? 'Ditolak' : 
                                                          selectedRequest?.status === 'cancelled' ? 'Dibatalkan' : 'Disetujui'"></span>
                                            <span class="text-xs text-gray-500" x-show="selectedRequest?.reviewed_at" x-text="formatDate(selectedRequest?.reviewed_at)"></span>
                                        </div>

                                        <!-- Active -->
                                        <div class="flex flex-col items-center text-center relative z-10" x-show="!(['rejected', 'cancelled'].includes(selectedRequest?.status))">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium"
                                                 :class="['active', 'completed'].includes(selectedRequest?.status) 
                                                         ? 'bg-purple-600 text-white' : 'bg-gray-300 text-gray-500'">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span class="text-xs mt-1 font-medium">Aktif</span>
                                            <span class="text-xs text-gray-500">-</span>
                                        </div>

                                        <!-- Completed -->
                                        <div class="flex flex-col items-center text-center relative z-10" x-show="!(['rejected', 'cancelled'].includes(selectedRequest?.status))">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium"
                                                 :class="selectedRequest?.status === 'completed' 
                                                         ? 'bg-emerald-600 text-white' : 'bg-gray-300 text-gray-500'">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span class="text-xs mt-1 font-medium">Selesai</span>
                                            <span class="text-xs text-gray-500">-</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Progress Line -->
                                    <div class="absolute top-4 left-4 right-4 h-0.5 bg-gray-200 -z-10">
                                        <div class="h-full bg-blue-600 transition-all duration-300"
                                             :style="'width: ' + 
                                                     (selectedRequest?.status === 'pending' ? '0%' :
                                                      selectedRequest?.status === 'under_review' ? '25%' :
                                                      selectedRequest?.status === 'approved' ? '50%' :
                                                      selectedRequest?.status === 'active' ? '75%' :
                                                      selectedRequest?.status === 'completed' ? '100%' :
                                                      selectedRequest?.status === 'rejected' || selectedRequest?.status === 'cancelled' ? '50%' : '0%')">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <!-- Basic Information -->
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            Informasi Dasar
                                        </h4>
                                        <div class="grid grid-cols-1 gap-3">
                                            <div class="flex justify-between py-2 border-b border-gray-200">
                                                <span class="text-gray-600 font-medium">Status:</span>
                                                <span class="font-semibold px-2 py-1 rounded-full text-xs" 
                                                      :class="getStatusBadgeClass(selectedRequest?.status)" 
                                                      x-text="getStatusText(selectedRequest?.status)"></span>
                                            </div>
                                            <div class="flex justify-between py-2 border-b border-gray-200">
                                                <span class="text-gray-600 font-medium">Tanggal Pinjam:</span>
                                                <span class="font-medium text-gray-900" x-text="formatDate(selectedRequest?.borrow_date)"></span>
                                            </div>
                                            <div class="flex justify-between py-2 border-b border-gray-200">
                                                <span class="text-gray-600 font-medium">Tanggal Kembali:</span>
                                                <span class="font-medium text-gray-900" x-text="formatDate(selectedRequest?.return_date)"></span>
                                            </div>
                                            <div class="flex justify-between py-2">
                                                <span class="text-gray-600 font-medium">Durasi:</span>
                                                <span class="font-medium text-gray-900" x-text="calculateDuration(selectedRequest?.borrow_date, selectedRequest?.return_date)"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Purpose -->
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                            </svg>
                                            Tujuan Peminjaman
                                        </h4>
                                        <p class="text-gray-700 leading-relaxed" x-text="selectedRequest?.purpose"></p>
                                    </div>

                                    <!-- Members Section -->
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                            </svg>
                                            Anggota Tim
                                        </h4>
                                        <div class="space-y-2">
                                            <template x-for="(member, index) in selectedRequest?.members || []" :key="'member-' + index">
                                                <div class="flex items-center justify-between py-2 px-3 bg-white rounded border">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                            <span class="text-purple-600 font-medium text-sm" x-text="(index + 1)"></span>
                                                        </div>
                                                        <div>
                                                            <div class="font-medium text-gray-900" x-text="member.name"></div>
                                                            <div class="text-sm text-gray-600" x-text="'NIM: ' + member.nim"></div>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-sm font-medium text-gray-700" x-text="member.study_program"></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-6">
                                    <!-- Supervisor Information -->
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                            Pembimbing
                                        </h4>
                                        <div class="space-y-3">
                                            <div class="flex justify-between py-2 border-b border-gray-200">
                                                <span class="text-gray-600 font-medium">Nama:</span>
                                                <span class="font-medium text-gray-900" x-text="selectedRequest?.supervisor?.name"></span>
                                            </div>
                                            <div class="flex justify-between py-2 border-b border-gray-200">
                                                <span class="text-gray-600 font-medium">NIP:</span>
                                                <span class="font-medium text-gray-900" x-text="selectedRequest?.supervisor?.nip"></span>
                                            </div>
                                            <div class="flex justify-between py-2 border-b border-gray-200">
                                                <span class="text-gray-600 font-medium">Email:</span>
                                                <span class="font-medium text-gray-900 break-all" x-text="selectedRequest?.supervisor?.email"></span>
                                            </div>
                                            <div class="flex justify-between py-2">
                                                <span class="text-gray-600 font-medium">Telepon:</span>
                                                <span class="font-medium text-gray-900" x-text="selectedRequest?.supervisor?.phone"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Equipment Section -->
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Peralatan
                                        </h4>
                                        <div class="space-y-3">
                                            <template x-for="(item, index) in selectedRequest?.equipment_items || []" :key="'equipment-detail-' + (item.id || index)">
                                                <div class="bg-white rounded border p-3">
                                                    <div class="flex justify-between items-start">
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900" x-text="item?.equipment?.name || 'N/A'"></div>
                                                            <div class="text-sm text-gray-600 mt-1" x-text="item?.equipment?.category || 'N/A'"></div>
                                                            <div class="flex items-center space-x-4 mt-2 text-sm">
                                                                <span class="text-blue-600">
                                                                    <strong>Diminta:</strong> <span x-text="item?.quantity_requested || 0"></span>
                                                                </span>
                                                                <span x-show="item?.quantity_approved" class="text-green-600">
                                                                    <strong>Disetujui:</strong> <span x-text="item?.quantity_approved || 0"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div x-show="item?.notes" class="mt-2 pt-2 border-t border-gray-200">
                                                        <div class="text-sm text-gray-600">
                                                            <strong>Catatan:</strong> <span x-text="item?.notes || ''"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Admin Notes -->
                                    <div x-show="selectedRequest?.approval_notes" class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"/>
                                            </svg>
                                            Catatan Admin
                                        </h4>
                                        <div class="bg-white rounded border p-3">
                                            <p class="text-gray-700 leading-relaxed" x-text="selectedRequest?.approval_notes"></p>
                                        </div>
                                    </div>

                                    <!-- Reviewer Information -->
                                    <div x-show="selectedRequest?.reviewer" class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Reviewer
                                        </h4>
                                        <div class="flex justify-between py-2">
                                            <span class="text-gray-600 font-medium">Nama:</span>
                                            <span class="font-medium text-gray-900" x-text="selectedRequest?.reviewer?.name"></span>
                                        </div>
                                        <div class="flex justify-between py-2" x-show="selectedRequest?.reviewed_at">
                                            <span class="text-gray-600 font-medium">Tanggal Review:</span>
                                            <span class="font-medium text-gray-900" x-text="formatDate(selectedRequest?.reviewed_at)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <div class="flex space-x-2">
                            <!-- Dynamic action buttons in detail modal -->
                            <template x-for="action in getAvailableActions(selectedRequest)" :key="action.key">
                                <button @click="
                                    console.log('=== BUTTON CLICK DEBUG ===');
                                    console.log('Action key:', action.key);
                                    console.log('Button context this:', $data);
                                    console.log('Available methods:', Object.getOwnPropertyNames($data).filter(prop => typeof $data[prop] === 'function'));
                                    console.log('showRejectModal type:', typeof showRejectModal);
                                    console.log('showRejectModal function:', showRejectModal);

                                    action.key === 'approve' ? showApproveModal(selectedRequest.id) :
                                    action.key === 'reject' ? (console.log('About to call showRejectModal...'), showRejectModal()) :
                                    action.key === 'updateStatus' ? showUpdateStatusModal() :
                                    action.key === 'pdf' ? downloadPDF(selectedRequest.id) : null"
                                        :class="action.class"
                                        x-text="action.text">
                                </button>
                            </template>
                        </div>
                        <button @click="closeDetail()" 
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div x-show="showApprovalModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"
         @click="closeApprovalModal()">
        
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showApprovalModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl"
                     @click.stop>
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg font-medium text-gray-900">Setujui Permohonan Peminjaman</h3>
                                <p class="text-sm text-gray-500 mt-2">
                                    Permohonan <span class="font-medium" x-text="selectedRequest?.request_id"></span> - Periksa dan sesuaikan jumlah alat yang akan disetujui
                                </p>
                                
                                <!-- Equipment Adjustments Table -->
                                <div class="mt-6" x-show="approvalForm.equipment_adjustments && approvalForm.equipment_adjustments.length > 0">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">Alat yang Diminta</h4>
                                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-300">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tersedia</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diminta</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disetujui</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <template x-for="(item, index) in selectedRequest?.equipment_items || []" :key="'equipment-approval-' + (item.id || index)">
                                                    <tr>
                                                        <td class="px-4 py-3 text-sm">
                                                            <div class="font-medium text-gray-900" x-text="item?.equipment?.name || 'N/A'"></div>
                                                            <div class="text-gray-500" x-text="item?.equipment?.model || 'N/A'"></div>
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-gray-900" x-text="item?.equipment?.available_quantity || 0"></td>
                                                        <td class="px-4 py-3 text-sm text-gray-900" x-text="item?.quantity_requested || 0"></td>
                                                        <td class="px-4 py-3 text-sm">
                                                            <input type="number"
                                                                   x-model.number="approvalForm.equipment_adjustments[index].quantity_approved"
                                                                   :min="0"
                                                                   :max="Math.min(item?.quantity_requested || 0, item?.equipment?.available_quantity || 0)"
                                                                   class="w-20 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"
                                                                   @input="approvalForm.equipment_adjustments[index] && validateEquipmentQuantity(index, $event.target.value)">
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Approval Notes -->
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Persetujuan</label>
                                    <textarea x-model="approvalForm.notes" 
                                              rows="3" 
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" 
                                              placeholder="Tambahkan catatan untuk peminjam (opsional)..."></textarea>
                                </div>
                                
                                <!-- Validation Errors -->
                                <div x-show="Object.keys(errors).length > 0" class="mt-4 p-3 bg-red-50 rounded-md">
                                    <template x-for="(error, index) in Object.values(errors)" :key="'error-' + index">
                                        <div class="text-sm text-red-600" x-text="Array.isArray(error) ? error.join(', ') : error"></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button @click="approve()" 
                                :disabled="loading"
                                :class="loading ? 'opacity-50 cursor-not-allowed' : ''"
                                class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            <span x-show="!loading">Setujui</span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Memproses...
                            </span>
                        </button>
                        <button @click="closeApprovalModal()" 
                                :disabled="loading"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-show="showRejectModalOpen" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"
         @click="closeRejectModal()">
        
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showRejectModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                     @click.stop>
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg font-medium text-gray-900">Tolak Permohonan</h3>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Apakah Anda yakin ingin menolak permohonan peminjaman <span class="font-medium" x-text="selectedRequest?.request_id"></span>?
                                    </p>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                                        <textarea x-model="rejectForm.reason" 
                                                  rows="3" 
                                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" 
                                                  placeholder="Jelaskan alasan penolakan..."></textarea>
                                    </div>
                                    
                                    <div x-show="errors.rejection_reason" class="mt-2 text-sm text-red-600" x-text="errors.rejection_reason"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button @click="reject()" 
                                :disabled="loading || !rejectForm.reason.trim()"
                                :class="(loading || !rejectForm.reason.trim()) ? 'opacity-50 cursor-not-allowed' : ''"
                                class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            <span x-show="!loading">Tolak</span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Memproses...
                            </span>
                        </button>
                        <button @click="closeRejectModal()" 
                                :disabled="loading"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div x-show="showUpdateStatusModalOpen" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"
         @click="closeUpdateStatusModal()">
        
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showUpdateStatusModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                     @click.stop>
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg font-medium text-gray-900">Update Status</h3>
                                <div class="mt-4 space-y-4">
                                    <p class="text-sm text-gray-500">
                                        Update status untuk permohonan <span class="font-medium" x-text="selectedRequest?.request_id"></span>
                                    </p>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Baru <span class="text-red-500">*</span></label>
                                        <select x-model="statusForm.status" 
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Pilih Status</option>
                                            <template x-if="selectedRequest?.status === 'approved'">
                                                <option value="active">Sedang Berlangsung</option>
                                            </template>
                                            <template x-if="selectedRequest?.status === 'active'">
                                                <option value="completed">Selesai</option>
                                            </template>
                                            <option value="cancelled">Dibatalkan</option>
                                        </select>
                                        <div x-show="errors.status" class="mt-1 text-sm text-red-600" x-text="errors.status"></div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin (Opsional)</label>
                                        <textarea x-model="statusForm.admin_notes" 
                                                  rows="3" 
                                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                                  placeholder="Tambahkan catatan..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button @click="updateStatus()" 
                                :disabled="loading || !statusForm.status"
                                :class="(loading || !statusForm.status) ? 'opacity-50 cursor-not-allowed' : ''"
                                class="inline-flex w-full justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            <span x-show="!loading">Update</span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Memproses...
                            </span>
                        </button>
                        <button @click="closeUpdateStatusModal()" 
                                :disabled="loading"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Toast -->
    <div x-show="message" 
         x-transition:enter="transform ease-out duration-300"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-4 right-4 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 z-50">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900" x-text="message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="message = ''" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('borrowRequestsData', () => ({
        // State
        requests: [],
        selectedRequest: null,
        summary: {
            total_requests: 0,
            pending_requests: 0,
            approved_requests: 0,
            active_requests: 0,
            completed_requests: 0,
            rejected_requests: 0,
            cancelled_requests: 0
        },
        pagination: {},
        filters: {
            search: '',
            status: '',
            date_from: '',
            date_to: ''
        },
        
        // UI State
        loading: false,
        showDetailModal: false,
        showApprovalModal: false,
        showRejectModalOpen: false,
        showUpdateStatusModalOpen: false,
        
        // Form data
        approvalForm: { 
            notes: '',
            equipment_adjustments: []
        },
        rejectForm: { reason: '' },
        statusForm: { status: '', admin_notes: '' },
        
        // Error handling
        errors: {},
        message: '',
        
        // Initialize
        async init() {
            console.log('=== ALPINE.JS COMPONENT DEBUG START ===');
            console.log('Component `this` context:', this);
            console.log('Component methods available:');
            console.log('- showApproveModal:', typeof this.showApproveModal, this.showApproveModal);
            console.log('- showRejectModal:', typeof this.showRejectModal, this.showRejectModal);
            console.log('- showUpdateStatusModal:', typeof this.showUpdateStatusModal, this.showUpdateStatusModal);
            console.log('- downloadPDF:', typeof this.downloadPDF, this.downloadPDF);

            // Check if methods are enumerable
            console.log('Enumerable properties:', Object.keys(this));
            console.log('All property names:', Object.getOwnPropertyNames(this));

            console.log('=== ALPINE.JS COMPONENT DEBUG END ===');

            console.log('Initializing borrow requests management...');
            await this.loadRequests();
            await this.loadSummary();
        },
        
        // API Helper
        async apiRequest(endpoint, options = {}) {
            const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
            if (!token) {
                throw new Error('Token tidak ditemukan. Silakan login ulang.');
            }
            
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            };
            
            const response = await fetch(endpoint, { ...defaultOptions, ...options });
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Terjadi kesalahan pada server');
            }
            
            return data;
        },
        
        // Load requests with filters
        async loadRequests(page = 1) {
            this.loading = true;
            this.errors = {};
            
            try {
                const params = new URLSearchParams({
                    page: page,
                    per_page: 10
                });
                
                // Add filters
                if (this.filters.search) params.append('search', this.filters.search);
                if (this.filters.status) params.append('status', this.filters.status);
                if (this.filters.date_from) params.append('date_from', this.filters.date_from);
                if (this.filters.date_to) params.append('date_to', this.filters.date_to);
                
                const response = await this.apiRequest(`/api/admin/requests/borrow?${params}`);
                
                this.requests = response.data || [];
                this.pagination = response.meta?.pagination || {};
                
            } catch (error) {
                console.error('Failed to load requests:', error);
                this.showError(error.message);
            } finally {
                this.loading = false;
            }
        },
        
        // Load summary statistics
        async loadSummary() {
            try {
                const response = await this.apiRequest('/api/admin/requests/borrow?summary=1');
                this.summary = response.data || {};
                console.log('Summary loaded:', this.summary);
            } catch (error) {
                console.error('Failed to load summary:', error);
            }
        },
        
        // Load request detail
        async loadDetail(id) {
            try {
                const response = await this.apiRequest(`/api/admin/requests/borrow/${id}`);
                this.selectedRequest = response.data;
                this.initializeEquipmentAdjustments();
            } catch (error) {
                console.error('Failed to load detail:', error);
                this.showError(error.message);
            }
        },
        
        // Show request detail
        async showDetail(id) {
            await this.loadDetail(id);
            this.showDetailModal = true;
        },
        
        // Initialize equipment adjustments form
        initializeEquipmentAdjustments() {
            try {
                if (!this.selectedRequest?.equipment_items || !Array.isArray(this.selectedRequest.equipment_items)) {
                    this.approvalForm.equipment_adjustments = [];
                    console.warn('No equipment items found for initialization');
                    return;
                }

                this.approvalForm.equipment_adjustments = this.selectedRequest.equipment_items.map((item, index) => ({
                    borrow_request_item_id: item?.id || index,
                    quantity_approved: Math.max(0, item?.quantity_requested || 0),
                    equipment_id: item?.equipment?.id || null
                }));

                console.log('Initialized equipment adjustments:', this.approvalForm.equipment_adjustments);
            } catch (error) {
                console.error('Error initializing equipment adjustments:', error);
                this.approvalForm.equipment_adjustments = [];
                this.showError('Gagal menginisialisasi data peralatan');
            }
        },
        
        // Validate equipment quantity
        validateEquipmentQuantity(index, value) {
            try {
                if (typeof index !== 'number' || index < 0) return;

                const item = this.selectedRequest?.equipment_items?.[index];
                if (!item || !this.approvalForm.equipment_adjustments?.[index]) return;

                const numValue = Math.max(0, parseInt(value) || 0);
                const requestedQty = Math.max(0, item?.quantity_requested || 0);
                const availableQty = Math.max(0, item?.equipment?.available_quantity || 0);
                const maxAvailable = Math.min(requestedQty, availableQty);

                if (numValue > maxAvailable) {
                    this.approvalForm.equipment_adjustments[index].quantity_approved = maxAvailable;
                } else {
                    this.approvalForm.equipment_adjustments[index].quantity_approved = numValue;
                }
            } catch (error) {
                console.error('Error validating equipment quantity:', error);
                this.showError('Gagal memvalidasi jumlah peralatan');
            }
        },
        
        // Close detail modal
        closeDetail() {
            this.showDetailModal = false;
            this.selectedRequest = null;
            this.resetForms();
        },
        
        // Reset filters
        resetFilters() {
            this.filters = {
                search: '',
                status: '',
                date_from: '',
                date_to: ''
            };
            this.loadRequests();
        },
        
        // Pagination
        async previousPage() {
            if (this.pagination.prev_page_url) {
                const page = this.pagination.current_page - 1;
                await this.loadRequests(page);
            }
        },
        
        async nextPage() {
            if (this.pagination.next_page_url) {
                const page = this.pagination.current_page + 1;
                await this.loadRequests(page);
            }
        },
        
        // Utility functions
        getMembersText(members) {
            if (!members || !Array.isArray(members) || members.length === 0) {
                return '-';
            }
            
            const first = members[0];
            const count = members.length;
            
            if (count === 1) {
                return first.name;
            } else {
                return `${first.name} + ${count - 1} lainnya`;
            }
        },
        
        getStatusText(status) {
            const statuses = {
                'pending': 'Menunggu Review',
                'approved': 'Disetujui',
                'rejected': 'Ditolak', 
                'active': 'Sedang Berlangsung',
                'completed': 'Selesai',
                'cancelled': 'Dibatalkan'
            };
            return statuses[status] || status;
        },
        
        getStatusBadgeClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800',
                'active': 'bg-blue-100 text-blue-800',
                'completed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-gray-100 text-gray-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        },
        
        formatDateTime(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        calculateDuration(startDate, endDate) {
            if (!startDate || !endDate) return '-';
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return `${diffDays} hari`;
        },
        
        // Error handling
        showError(message) {
            this.message = message;
            setTimeout(() => {
                this.message = '';
            }, 5000);
        },
        
        resetForms() {
            this.approvalForm = {
                notes: '',
                equipment_adjustments: []
            };
            this.rejectForm = { reason: '' };
            this.statusForm = { status: '', admin_notes: '' };
            this.errors = {};
        },
        
        // Modal actions
        async showApproveModal(id = null) {
            if (id) {
                await this.loadDetail(id);
            }
            this.showApprovalModal = true;
            this.resetForms();
            this.initializeEquipmentAdjustments();
        },
        
        closeApprovalModal() {
            this.showApprovalModal = false;
            // Delay form reset to avoid reactive binding conflicts
            this.$nextTick(() => {
                this.resetForms();
            });
        },
        
        showRejectModal() {
            console.log('=== SHOW REJECT MODAL CALLED ===');
            console.log('Method context `this`:', this);
            console.log('Setting showRejectModalOpen to true...');
            this.showRejectModalOpen = true;
            this.resetForms();
            console.log('=== SHOW REJECT MODAL COMPLETED ===');
        },
        
        closeRejectModal() {
            this.showRejectModalOpen = false;
            // Delay form reset to avoid reactive binding conflicts
            this.$nextTick(() => {
                this.resetForms();
            });
        },
        
        showUpdateStatusModal() {
            this.showUpdateStatusModalOpen = true;
            this.resetForms();
        },
        
        closeUpdateStatusModal() {
            this.showUpdateStatusModalOpen = false;
            // Delay form reset to avoid reactive binding conflicts
            this.$nextTick(() => {
                this.resetForms();
            });
        },
        
        // Action methods
        async approve() {
            if (!this.selectedRequest) return;
            
            this.loading = true;
            this.errors = {};
            
            try {
                const payload = {
                    approval_notes: this.approvalForm.notes || null,
                    equipment_adjustments: this.approvalForm.equipment_adjustments?.filter(adj => 
                        adj.quantity_approved !== undefined && adj.quantity_approved >= 0
                    ) || []
                };
                
                const response = await this.apiRequest(
                    `/api/admin/requests/borrow/${this.selectedRequest.id}/approve`, 
                    {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    }
                );
                
                // Update the request in the list
                const index = this.requests.findIndex(r => r.id === this.selectedRequest.id);
                if (index !== -1) {
                    this.requests[index].status = 'approved';
                    this.requests[index].approval_notes = this.approvalForm.notes;
                    this.requests[index].reviewed_at = new Date().toISOString();
                }
                
                // Update the selected request
                this.selectedRequest.status = 'approved';
                this.selectedRequest.approval_notes = this.approvalForm.notes;
                this.selectedRequest.reviewed_at = new Date().toISOString();
                
                this.showSuccess('Permohonan berhasil disetujui');
                this.closeApprovalModal();
                await this.loadSummary(); // Refresh summary
                
            } catch (error) {
                console.error('Failed to approve request:', error);
                this.handleApiError(error);
            } finally {
                this.loading = false;
            }
        },
        
        async reject() {
            if (!this.selectedRequest || !this.rejectForm.reason.trim()) return;
            
            this.loading = true;
            this.errors = {};
            
            try {
                const payload = {
                    approval_notes: this.rejectForm.reason.trim()
                };
                
                const response = await this.apiRequest(
                    `/api/admin/requests/borrow/${this.selectedRequest.id}/reject`, 
                    {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    }
                );
                
                // Update the request in the list
                const index = this.requests.findIndex(r => r.id === this.selectedRequest.id);
                if (index !== -1) {
                    this.requests[index].status = 'rejected';
                    this.requests[index].reviewed_at = new Date().toISOString();
                }
                
                // Update the selected request
                this.selectedRequest.status = 'rejected';
                this.selectedRequest.reviewed_at = new Date().toISOString();
                
                this.showSuccess('Permohonan berhasil ditolak');
                this.closeRejectModal();
                await this.loadSummary(); // Refresh summary
                
            } catch (error) {
                console.error('Failed to reject request:', error);
                this.handleApiError(error);
            } finally {
                this.loading = false;
            }
        },
        
        async updateStatus() {
            if (!this.selectedRequest || !this.statusForm.status) return;
            
            this.loading = true;
            this.errors = {};
            
            try {
                const payload = {
                    status: this.statusForm.status,
                    admin_notes: this.statusForm.admin_notes || null
                };
                
                const response = await this.apiRequest(
                    `/api/admin/requests/borrow/${this.selectedRequest.id}`, 
                    {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    }
                );
                
                // Update the request in the list
                const index = this.requests.findIndex(r => r.id === this.selectedRequest.id);
                if (index !== -1) {
                    this.requests[index].status = this.statusForm.status;
                }
                
                // Update the selected request
                this.selectedRequest.status = this.statusForm.status;
                
                this.showSuccess('Status berhasil diperbarui');
                this.closeUpdateStatusModal();
                await this.loadSummary(); // Refresh summary
                
            } catch (error) {
                console.error('Failed to update status:', error);
                this.handleApiError(error);
            } finally {
                this.loading = false;
            }
        },
        
        // PDF Letter functionality
        async downloadLetter(requestId) {
            console.log('=== DOWNLOAD LETTER DEBUG START ===');
            console.log('Request ID:', requestId);

            try {
                this.loading = true;
                console.log('Making API request to:', `/api/admin/requests/borrow/${requestId}/letter`);
                const response = await this.apiRequest(`/api/admin/requests/borrow/${requestId}/letter`);

                console.log('API Response:', response);

                if (response.data && response.data.letter_url) {
                    console.log('Letter URL found:', response.data.letter_url);
                    console.log('Letter URL type:', typeof response.data.letter_url);
                    console.log('Letter URL length:', response.data.letter_url.length);

                    // Check if URL is relative or absolute and construct full URL
                    const letterUrl = response.data.letter_url;
                    let fullUrl;

                    if (letterUrl.startsWith('http://') || letterUrl.startsWith('https://')) {
                        fullUrl = letterUrl;
                    } else if (letterUrl.startsWith('/')) {
                        fullUrl = window.location.origin + letterUrl;
                    } else {
                        fullUrl = window.location.origin + '/' + letterUrl;
                    }

                    console.log('Final URL being opened:', fullUrl);
                    console.log('Current origin:', window.location.origin);

                    // Test the URL first
                    console.log('Testing URL accessibility...');
                    fetch(fullUrl, { method: 'HEAD' })
                        .then(testResponse => {
                            console.log('URL test response status:', testResponse.status);
                            console.log('URL test response ok:', testResponse.ok);
                            if (!testResponse.ok) {
                                console.error('URL is not accessible:', testResponse.status, testResponse.statusText);
                            }
                        })
                        .catch(err => {
                            console.error('URL test failed:', err);
                        });

                    // Open the PDF in a new tab
                    console.log('Attempting to open window...');
                    const newWindow = window.open(fullUrl, '_blank');
                    console.log('Window.open result:', newWindow);

                    if (!newWindow) {
                        console.error('Failed to open window - popup might be blocked');
                        this.showError('Popup diblokir. Silakan izinkan popup dan coba lagi.');
                    } else {
                        this.showSuccess('Surat berhasil dibuka');
                    }
                } else {
                    console.error('No letter_url in response:', response);
                    throw new Error('URL surat tidak tersedia');
                }
            } catch (error) {
                console.error('Failed to download letter:', error);
                if (error.message.includes('404')) {
                    this.showError('Surat belum tersedia. Surat akan dibuat secara otomatis setelah permohonan disetujui.');
                } else {
                    this.handleApiError(error);
                }
            } finally {
                this.loading = false;
            }

            console.log('=== DOWNLOAD LETTER DEBUG END ===');
        },
        
        async regenerateLetter(requestId) {
            try {
                this.loading = true;
                const response = await this.apiRequest(
                    `/api/admin/requests/borrow/${requestId}/letter/regenerate`, 
                    { method: 'POST' }
                );
                
                if (response.data && response.data.letter_url) {
                    window.open(response.data.letter_url, '_blank');
                    this.showSuccess('Surat berhasil dibuat ulang dan dibuka');
                } else {
                    throw new Error('Gagal membuat ulang surat');
                }
            } catch (error) {
                console.error('Failed to regenerate letter:', error);
                this.handleApiError(error);
            } finally {
                this.loading = false;
            }
        },
        
        // Enhanced error handling
        handleApiError(error) {
            if (error.message) {
                // Check if it's a validation error
                if (error.message.includes('validation') || error.message.includes('422')) {
                    try {
                        const errorData = JSON.parse(error.message);
                        if (errorData.errors) {
                            this.errors = errorData.errors;
                        } else {
                            this.showError(errorData.message || 'Terjadi kesalahan validasi');
                        }
                    } catch (e) {
                        this.showError(error.message);
                    }
                } else {
                    this.showError(error.message);
                }
            } else {
                this.showError('Terjadi kesalahan yang tidak diketahui');
            }
        },
        
        showSuccess(message) {
            this.message = message;
            setTimeout(() => {
                this.message = '';
            }, 5000);
        },
        
        // Enhanced error handling
        showError(message) {
            this.message = message;
            setTimeout(() => {
                this.message = '';
            }, 8000);
        },

        // Download PDF
        async downloadPDF(id) {
            console.log('=== PDF DOWNLOAD DEBUG START ===');
            console.log('Request ID:', id);
            console.log('Selected request:', this.selectedRequest);

            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                console.log('Token found:', !!token);

                if (!token) {
                    console.error('No token found');
                    this.showError('Token tidak ditemukan. Silakan login ulang.');
                    return;
                }

                const url = `/api/admin/requests/borrow/${id}/letter`;
                console.log('API URL:', url);

                // First, get the letter URL from the API
                console.log('Making API request...');
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                console.log('Response headers:', Object.fromEntries(response.headers.entries()));

                if (response.ok) {
                    const data = await response.json();
                    console.log('Response data:', data);

                    if (data.success && data.data && data.data.letter_url) {
                        console.log('Letter URL:', data.data.letter_url);
                        console.log('Letter URL type:', typeof data.data.letter_url);
                        console.log('Letter URL length:', data.data.letter_url.length);
                        console.log('Current window location:', window.location.href);
                        console.log('Opening PDF in new tab...');

                        // Check if URL is relative or absolute
                        const letterUrl = data.data.letter_url;
                        let fullUrl;

                        if (letterUrl.startsWith('http://') || letterUrl.startsWith('https://')) {
                            fullUrl = letterUrl;
                        } else if (letterUrl.startsWith('/')) {
                            fullUrl = window.location.origin + letterUrl;
                        } else {
                            fullUrl = window.location.origin + '/' + letterUrl;
                        }

                        console.log('Final URL being opened:', fullUrl);

                        // Test the URL first
                        console.log('Testing URL accessibility...');
                        fetch(fullUrl, { method: 'HEAD' })
                            .then(response => {
                                console.log('URL test response status:', response.status);
                                console.log('URL test response ok:', response.ok);
                                console.log('URL test response headers:', Object.fromEntries(response.headers.entries()));
                            })
                            .catch(err => {
                                console.error('URL test failed:', err);
                            });

                        // Open the PDF URL directly in a new tab/window
                        const newWindow = window.open(fullUrl, '_blank');
                        console.log('Window.open result:', newWindow);

                        if (!newWindow) {
                            console.error('Failed to open window - popup might be blocked');
                            this.showError('Popup diblokir. Silakan izinkan popup dan coba lagi.');
                        } else {
                            this.showSuccess('PDF berhasil dibuka.');
                        }
                    } else {
                        console.error('Invalid response structure:', data);
                        this.showError(data.message || 'Gagal mendapatkan URL PDF.');
                    }
                } else {
                    console.error('Response not ok, status:', response.status);
                    const errorData = await response.json().catch((err) => {
                        console.error('Failed to parse error response:', err);
                        return null;
                    });
                    console.log('Error data:', errorData);
                    this.showError(errorData?.message || 'Gagal mengunduh PDF. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('PDF download error:', error);
                console.error('Error stack:', error.stack);
                this.showError('Gagal mengunduh PDF. Silakan coba lagi.');
            }

            console.log('=== PDF DOWNLOAD DEBUG END ===');
        },

        // Get available actions for request
        getAvailableActions(request) {
            console.log('=== GET AVAILABLE ACTIONS DEBUG ===');
            console.log('Request:', request);
            console.log('Request status:', request?.status);
            console.log('Component context in getAvailableActions:', this);
            console.log('showRejectModal method check:', typeof this.showRejectModal);

            try {
                if (!request || typeof request !== 'object') return [];

            const actions = [];

            switch (request.status) {
                case 'pending':
                    actions.push(
                        {
                            key: 'approve',
                            text: 'Setujui',
                            action: 'showApproveModal',
                            class: 'bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium'
                        },
                        {
                            key: 'reject',
                            text: 'Tolak',
                            action: 'showRejectModal',
                            class: 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium'
                        }
                    );
                    break;
                case 'approved':
                    actions.push(
                        {
                            key: 'updateStatus',
                            text: 'Update Status',
                            action: 'showUpdateStatusModal',
                            class: 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium'
                        }
                    );
                    break;
                case 'active':
                    actions.push(
                        {
                            key: 'updateStatus',
                            text: 'Update Status',
                            action: 'showUpdateStatusModal',
                            class: 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium'
                        }
                    );
                    break;
            }

            // PDF action for approved and active requests
            if (['approved', 'active'].includes(request.status)) {
                actions.push({
                    key: 'pdf',
                    text: 'Download PDF',
                    action: 'downloadPDF',
                    class: 'bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium'
                });
            }

            console.log('Final actions array:', actions);
            console.log('=== GET AVAILABLE ACTIONS COMPLETE ===');

            return actions;
            } catch (error) {
                console.error('Error getting available actions:', error);
                return [];
            }
        }
    }));
});
</script>
@endpush