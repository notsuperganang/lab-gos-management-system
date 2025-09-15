@extends('admin.layouts.app')

@section('title', 'Kelola Kunjungan Lab')

@section('content')
<div x-data="visitRequestsData()" x-init="init()" class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Kunjungan Lab</h1>
            <p class="text-sm text-gray-500">Kelola permohonan kunjungan laboratorium dari mahasiswa dan peneliti.</p>
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
                    <p class="text-sm text-gray-500 h-8 flex items-center">Total Kunjungan</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="summary?.total_requests || 0"></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
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

        <!-- Completed -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 h-8 flex items-center">Selesai</p>
                    <p class="text-2xl font-bold text-blue-600" x-text="summary?.completed_requests || 0"></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                           placeholder="Cari nama pengunjung, email, atau institusi..."
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
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>

            <!-- Date From -->
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date"
                       x-model="filters.date_from"
                       @change="loadRequests()"
                       class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
            </div>

            <!-- Date To -->
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Kunjungan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengunjung</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institusi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Kunjungan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="(request, index) in (requests || [])" :key="`request-${request.request_id || request.id || index}`">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="request.request_id"></td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div x-text="request.visitor_name"></div>
                                <div class="text-xs text-gray-500" x-text="request.visitor_email"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div x-text="request.institution"></div>
                                <div class="text-xs text-gray-500" x-text="'Ukuran Grup: ' + request.group_size + ' orang'"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div x-text="formatDate(request.visit_date)"></div>
                                <div class="text-xs text-gray-500" x-text="request.start_time + ' - ' + request.end_time + ' WIB'"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div x-text="getPurposeText(request.visit_purpose)"></div>
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
                                        <button @click="showDetail(request.id); $nextTick(() => { statusForm.status = 'completed'; showUpdateStatusModalOpen = true; })"
                                                class="text-green-600 hover:text-green-900 text-xs px-2 py-1 rounded border border-green-200 hover:bg-green-50">
                                            Selesai
                                        </button>
                                    </template>

                                    <!-- PDF Letter for approved/completed -->
                                    <template x-if="['approved', 'completed'].includes(request.status)">
                                        <button @click="downloadLetter(request.id)"
                                                class="text-purple-600 hover:text-purple-900 text-xs px-2 py-1 rounded border border-purple-200 hover:bg-purple-50">
                                            Surat
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty state -->
                    <tr x-show="!loading && (!requests || requests.length === 0)">
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="mt-2">Tidak ada data kunjungan ditemukan</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div x-show="!loading && pagination && pagination?.total > 0" class="px-6 py-3 border-t bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan
                    <span x-text="pagination?.from || 0"></span>
                    sampai
                    <span x-text="pagination?.to || 0"></span>
                    dari
                    <span x-text="pagination?.total || 0"></span>
                    data
                </div>

                <div class="flex space-x-1">
                    <!-- Previous Page -->
                    <button @click="loadPage((pagination?.current_page || 1) - 1)"
                            :disabled="!pagination?.prev_page_url"
                            :class="pagination?.prev_page_url ? 'text-blue-600 hover:text-blue-900' : 'text-gray-400 cursor-not-allowed'"
                            class="px-3 py-1 text-sm border rounded">
                        Sebelumnya
                    </button>

                    <!-- Page Numbers -->
                    <template x-for="page in getPageNumbers()" :key="page">
                        <button @click="loadPage(page)"
                                :class="page === (pagination?.current_page || 1) ? 'bg-blue-600 text-white' : 'text-blue-600 hover:bg-blue-50'"
                                class="px-3 py-1 text-sm border rounded"
                                x-text="page">
                        </button>
                    </template>

                    <!-- Next Page -->
                    <button @click="loadPage((pagination?.current_page || 1) + 1)"
                            :disabled="!pagination?.next_page_url"
                            :class="pagination?.next_page_url ? 'text-blue-600 hover:text-blue-900' : 'text-gray-400 cursor-not-allowed'"
                            class="px-3 py-1 text-sm border rounded">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showDetailModalOpen"
         @click.away="showDetailModalOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Detail Kunjungan Lab</h3>
                    <button @click="showDetailModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div x-show="selectedRequest" class="mt-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Request Information -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Informasi Permohonan</h4>
                                <dl class="space-y-2">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">ID Kunjungan:</dt>
                                        <dd class="text-sm font-medium text-gray-900" x-text="selectedRequest?.request_id"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Status:</dt>
                                        <dd>
                                            <span :class="getStatusBadgeClass(selectedRequest?.status)"
                                                  class="px-2 py-1 text-xs font-medium rounded-full"
                                                  x-text="getStatusText(selectedRequest?.status)"></span>
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Tanggal Pengajuan:</dt>
                                        <dd class="text-sm text-gray-900" x-text="formatDateTime(selectedRequest?.submitted_at)"></dd>
                                    </div>
                                    <div x-show="selectedRequest?.reviewed_at">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-500">Tanggal Review:</dt>
                                            <dd class="text-sm text-gray-900" x-text="formatDateTime(selectedRequest?.reviewed_at)"></dd>
                                        </div>
                                    </div>
                                    <div x-show="selectedRequest?.reviewer">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-500">Reviewer:</dt>
                                            <dd class="text-sm text-gray-900" x-text="selectedRequest?.reviewer?.name"></dd>
                                        </div>
                                    </div>
                                </dl>
                            </div>

                            <!-- Visitor Information -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Informasi Pengunjung</h4>
                                <dl class="space-y-2">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Nama:</dt>
                                        <dd class="text-sm text-gray-900" x-text="selectedRequest?.visitor_name"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Email:</dt>
                                        <dd class="text-sm text-gray-900" x-text="selectedRequest?.visitor_email"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Telepon:</dt>
                                        <dd class="text-sm text-gray-900" x-text="selectedRequest?.visitor_phone"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Institusi:</dt>
                                        <dd class="text-sm text-gray-900" x-text="selectedRequest?.institution"></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Visit Information -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Informasi Kunjungan</h4>
                                <dl class="space-y-2">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Tanggal Kunjungan:</dt>
                                        <dd class="text-sm text-gray-900" x-text="formatDate(selectedRequest?.visit_date)"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Waktu:</dt>
                                        <dd class="text-sm text-gray-900" x-text="selectedRequest?.start_time + ' - ' + selectedRequest?.end_time + ' WIB'"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Ukuran Grup:</dt>
                                        <dd class="text-sm text-gray-900" x-text="selectedRequest?.group_size + ' orang'"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Tujuan:</dt>
                                        <dd class="text-sm text-gray-900" x-text="getPurposeText(selectedRequest?.visit_purpose)"></dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Purpose Description -->
                            <div class="bg-gray-50 p-4 rounded-lg" x-show="selectedRequest?.purpose_description">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Deskripsi Tujuan</h4>
                                <p class="text-sm text-gray-700" x-text="selectedRequest?.purpose_description"></p>
                            </div>

                            <!-- Special Requirements -->
                            <div class="bg-gray-50 p-4 rounded-lg" x-show="selectedRequest?.special_requirements">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Kebutuhan Khusus</h4>
                                <p class="text-sm text-gray-700" x-text="selectedRequest?.special_requirements"></p>
                            </div>

                            <!-- Equipment Needed -->
                            <div class="bg-gray-50 p-4 rounded-lg" x-show="selectedRequest?.equipment_needed && selectedRequest?.equipment_needed.length > 0">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Alat yang Dibutuhkan</h4>
                                <ul class="list-disc list-inside text-sm text-gray-700">
                                    <template x-for="equipment in selectedRequest?.equipment_needed" :key="equipment">
                                        <li x-text="equipment"></li>
                                    </template>
                                </ul>
                            </div>

                            <!-- Approval Notes -->
                            <div class="bg-gray-50 p-4 rounded-lg" x-show="selectedRequest?.approval_notes">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Catatan Persetujuan</h4>
                                <p class="text-sm text-gray-700" x-text="selectedRequest?.approval_notes"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                        <template x-if="selectedRequest?.status === 'pending'">
                            <div class="flex space-x-2">
                                <button @click="showRejectModal()"
                                        class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 border border-red-300 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    Tolak Kunjungan
                                </button>
                                <button @click="showApproveModal(selectedRequest.id)"
                                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    Setujui Kunjungan
                                </button>
                            </div>
                        </template>

                        <template x-if="selectedRequest?.status === 'approved'">
                            <button @click="statusForm.status = 'completed'; showUpdateStatusModalOpen = true;"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Tandai Selesai
                            </button>
                        </template>

                        <template x-if="['approved', 'completed'].includes(selectedRequest?.status)">
                            <button @click="downloadLetter(selectedRequest.id)"
                                    class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                Download Surat
                            </button>
                        </template>

                        <button @click="showDetailModalOpen = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div x-show="showApproveModalOpen"
         @click.away="showApproveModalOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Setujui Kunjungan</h3>
                    <button @click="showApproveModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="approveRequest()">
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Catatan Persetujuan (Opsional)</label>
                        <textarea x-model="approvalForm.notes"
                                  rows="4"
                                  placeholder="Masukkan catatan atau instruksi tambahan untuk kunjungan..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                        <button type="button"
                                @click="showApproveModalOpen = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="submitting"
                                :class="submitting ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                                class="px-4 py-2 text-sm font-medium text-white border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <span x-show="!submitting">Setujui Kunjungan</span>
                            <span x-show="submitting">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-show="showRejectModalOpen"
         @click.away="showRejectModalOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Tolak Kunjungan</h3>
                    <button @click="showRejectModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="rejectRequest()">
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea x-model="rejectionForm.reason"
                                  rows="4"
                                  required
                                  placeholder="Masukkan alasan penolakan kunjungan..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                        <button type="button"
                                @click="showRejectModalOpen = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="submitting || !rejectionForm.reason"
                                :class="submitting || !rejectionForm.reason ? 'bg-gray-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700'"
                                class="px-4 py-2 text-sm font-medium text-white border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                            <span x-show="!submitting">Tolak Kunjungan</span>
                            <span x-show="submitting">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div x-show="showUpdateStatusModalOpen"
         @click.away="showUpdateStatusModalOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Update Status Kunjungan</h3>
                    <button @click="showUpdateStatusModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="updateStatus()">
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-4">
                            Apakah Anda yakin ingin menandai kunjungan ini sebagai <strong x-text="getStatusText(statusForm.status)"></strong>?
                        </p>

                        <label class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                        <textarea x-model="statusForm.notes"
                                  rows="3"
                                  placeholder="Masukkan catatan tambahan..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                        <button type="button"
                                @click="showUpdateStatusModalOpen = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="submitting"
                                :class="submitting ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                class="px-4 py-2 text-sm font-medium text-white border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span x-show="!submitting">Update Status</span>
                            <span x-show="submitting">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('visitRequestsData', () => ({
        // Data properties
        requests: [],
        summary: null,
        pagination: {},
        loading: false,
        submitting: false,

        // Selected request for modals
        selectedRequest: null,

        // Modal states
        showDetailModalOpen: false,
        showApproveModalOpen: false,
        showRejectModalOpen: false,
        showUpdateStatusModalOpen: false,

        // Form data
        filters: {
            search: '',
            status: '',
            date_from: '',
            date_to: ''
        },

        approvalForm: {
            notes: ''
        },

        rejectionForm: {
            reason: ''
        },

        statusForm: {
            status: '',
            notes: ''
        },

        async init() {
            await this.loadRequests();
            await this.loadSummary();
        },

        async loadRequests(page = 1) {
            this.loading = true;
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const params = new URLSearchParams({
                    page: page,
                    per_page: 15,
                    ...this.filters
                });

                // Remove empty filter values
                Object.keys(this.filters).forEach(key => {
                    if (!this.filters[key]) {
                        params.delete(key);
                    }
                });

                const response = await fetch(`/api/admin/requests/visit?${params}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.requests = data.data;
                    this.pagination = data.meta.pagination || data.pagination;
                } else {
                    throw new Error(data.message || 'Gagal memuat data kunjungan');
                }
            } catch (error) {
                console.error('Failed to load visit requests:', error);
                this.$dispatch('global-error', { message: error.message });
            } finally {
                this.loading = false;
            }
        },

        async loadSummary() {
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) return;

                const response = await fetch('/api/admin/requests/visit?summary=true', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to load summary');

                const data = await response.json();
                if (data.success) {
                    this.summary = data.data;
                }
            } catch (error) {
                console.error('Failed to load summary:', error);
            }
        },

        async loadPage(page) {
            if (page < 1 || (this.pagination && page > this.pagination.last_page)) return;
            await this.loadRequests(page);
        },

        getPageNumbers() {
            if (!this.pagination || !this.pagination.current_page || !this.pagination.last_page) return [];

            const current = this.pagination.current_page;
            const total = this.pagination.last_page;
            const delta = 2;
            const range = [];

            for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
                range.push(i);
            }

            if (current - delta > 2) {
                range.unshift("...");
            }
            if (current + delta < total - 1) {
                range.push("...");
            }

            range.unshift(1);
            if (total > 1) {
                range.push(total);
            }

            return range;
        },

        resetFilters() {
            this.filters = {
                search: '',
                status: '',
                date_from: '',
                date_to: ''
            };
            this.loadRequests();
        },

        async showDetail(requestId) {
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const response = await fetch(`/api/admin/requests/visit/${requestId}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.selectedRequest = data.data;
                    this.showDetailModalOpen = true;
                } else {
                    throw new Error(data.message || 'Gagal memuat detail kunjungan');
                }
            } catch (error) {
                console.error('Failed to load visit request details:', error);
                this.$dispatch('global-error', { message: error.message });
            }
        },

        showApproveModal(requestId) {
            if (requestId) {
                this.showDetail(requestId).then(() => {
                    this.$nextTick(() => {
                        this.approvalForm.notes = '';
                        this.showApproveModalOpen = true;
                    });
                });
            } else {
                this.approvalForm.notes = '';
                this.showApproveModalOpen = true;
            }
        },

        showRejectModal() {
            this.rejectionForm.reason = '';
            this.showRejectModalOpen = true;
        },

        async approveRequest() {
            if (!this.selectedRequest) return;

            this.submitting = true;
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const response = await fetch(`/api/admin/requests/visit/${this.selectedRequest.id}/approve`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        approval_notes: this.approvalForm.notes
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.showApproveModalOpen = false;
                    this.showDetailModalOpen = false;
                    this.$dispatch('global-success', { message: 'Kunjungan berhasil disetujui!' });
                    await this.loadRequests();
                    await this.loadSummary();
                } else {
                    throw new Error(data.message || 'Gagal menyetujui kunjungan');
                }
            } catch (error) {
                console.error('Failed to approve visit request:', error);
                this.$dispatch('global-error', { message: error.message });
            } finally {
                this.submitting = false;
            }
        },

        async rejectRequest() {
            if (!this.selectedRequest || !this.rejectionForm.reason) return;

            this.submitting = true;
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const response = await fetch(`/api/admin/requests/visit/${this.selectedRequest.id}/reject`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        rejection_reason: this.rejectionForm.reason
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.showRejectModalOpen = false;
                    this.showDetailModalOpen = false;
                    this.$dispatch('global-success', { message: 'Kunjungan berhasil ditolak!' });
                    await this.loadRequests();
                    await this.loadSummary();
                } else {
                    throw new Error(data.message || 'Gagal menolak kunjungan');
                }
            } catch (error) {
                console.error('Failed to reject visit request:', error);
                this.$dispatch('global-error', { message: error.message });
            } finally {
                this.submitting = false;
            }
        },

        async updateStatus() {
            if (!this.selectedRequest || !this.statusForm.status) return;

            this.submitting = true;
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const response = await fetch(`/api/admin/requests/visit/${this.selectedRequest.id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status: this.statusForm.status,
                        approval_notes: this.statusForm.notes
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.showUpdateStatusModalOpen = false;
                    this.showDetailModalOpen = false;
                    this.$dispatch('global-success', { message: 'Status kunjungan berhasil diupdate!' });
                    await this.loadRequests();
                    await this.loadSummary();
                } else {
                    throw new Error(data.message || 'Gagal mengupdate status kunjungan');
                }
            } catch (error) {
                console.error('Failed to update visit status:', error);
                this.$dispatch('global-error', { message: error.message });
            } finally {
                this.submitting = false;
            }
        },

        async downloadLetter(requestId) {
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const response = await fetch(`/api/admin/requests/visit/${requestId}/letter`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/pdf'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `surat-kunjungan-${requestId}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                this.$dispatch('global-success', { message: 'Surat berhasil diunduh!' });
            } catch (error) {
                console.error('Failed to download letter:', error);
                this.$dispatch('global-error', { message: 'Gagal mengunduh surat: ' + error.message });
            }
        },

        // Helper methods
        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        formatDateTime(dateTimeString) {
            if (!dateTimeString) return '-';
            const date = new Date(dateTimeString);
            return date.toLocaleString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        getStatusText(status) {
            const statusMap = {
                'pending': 'Menunggu Review',
                'approved': 'Disetujui',
                'rejected': 'Ditolak',
                'completed': 'Selesai',
                'cancelled': 'Dibatalkan'
            };
            return statusMap[status] || status;
        },

        getStatusBadgeClass(status) {
            const statusClasses = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800',
                'completed': 'bg-blue-100 text-blue-800',
                'cancelled': 'bg-gray-100 text-gray-800'
            };
            return statusClasses[status] || 'bg-gray-100 text-gray-800';
        },

        getPurposeText(purpose) {
            const purposeMap = {
                'study-visit': 'Kunjungan Studi',
                'research': 'Penelitian',
                'learning': 'Pembelajaran',
                'internship': 'Magang',
                'others': 'Lainnya'
            };
            return purposeMap[purpose] || purpose;
        }
    }));
});

// Global event listeners for notifications
document.addEventListener('global-success', function(e) {
    showNotification(e.detail.message, 'success');
});

document.addEventListener('global-error', function(e) {
    showNotification(e.detail.message, 'error');
});

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'warning' ? 'bg-yellow-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endpush