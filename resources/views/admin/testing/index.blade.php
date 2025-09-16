@extends('admin.layouts.app')

@section('title', 'Kelola Pengujian Lab')

@section('content')
<div x-data="testingRequestsData()" x-init="init()" class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Pengujian Lab</h1>
            <p class="text-sm text-gray-500">Kelola permohonan pengujian sampel dari mahasiswa, peneliti, dan klien eksternal.</p>
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4" x-show="summary">
        <!-- Total Requests -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 h-8 flex items-center">Total Pengujian</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="summary?.total_requests || 0"></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
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
                    <p class="text-2xl font-bold text-blue-600" x-text="summary?.approved_requests || 0"></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Sample Received -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 h-8 flex items-center">Sampel Diterima</p>
                    <p class="text-2xl font-bold text-teal-600" x-text="summary?.sample_received_requests || 0"></p>
                </div>
                <div class="p-3 bg-teal-100 rounded-full">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 h-8 flex items-center">Sedang Diproses</p>
                    <p class="text-2xl font-bold text-purple-600" x-text="summary?.in_progress_requests || 0"></p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 h-8 flex items-center">Selesai</p>
                    <p class="text-2xl font-bold text-green-600" x-text="summary?.completed_requests || 0"></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Rejected & Cancelled Combined -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 h-8 flex items-center">Ditolak/Batal</p>
                    <p class="text-2xl font-bold text-red-600" x-text="(summary?.rejected_requests || 0) + (summary?.cancelled_requests || 0)"></p>
                    <div class="text-xs text-gray-400 mt-1">
                        <span x-text="summary?.rejected_requests || 0"></span> ditolak,
                        <span x-text="summary?.cancelled_requests || 0"></span> batal
                    </div>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                           placeholder="Cari nama klien, organisasi, atau sampel..."
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
                    <option value="sample_received">Sampel Diterima</option>
                    <option value="in_progress">Sedang Diproses</option>
                    <option value="completed">Selesai</option>
                    <option value="rejected">Ditolak</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>

            <!-- Testing Type Filter -->
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Jenis Pengujian</label>
                <select x-model="filters.testing_type" @change="loadRequests()" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Jenis</option>
                    <option value="ftir_spectroscopy">FTIR Spectroscopy</option>
                    <option value="uv_vis_spectroscopy">UV-Vis Spectroscopy</option>
                    <option value="optical_microscopy">Optical Microscopy</option>
                    <option value="custom">Custom Testing</option>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pengujian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sampel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pengujian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="(request, index) in (requests || [])" :key="`request-${request.request_id || request.id || index}`">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="request.request_id"></td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div x-text="request.client_name"></div>
                                <div class="text-xs text-gray-500" x-text="request.client_organization"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div x-text="request.sample_name"></div>
                                <div class="text-xs text-gray-500" x-text="request.sample_quantity"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="flex items-center">
                                    <span x-html="getTestingTypeIcon(request.testing_type)" class="mr-2"></span>
                                    <span x-text="getTestingTypeText(request.testing_type)"></span>
                                </div>
                                <div class="text-xs text-gray-500" x-show="request.urgent_request" x-text="'⚡ Urgent'"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div x-text="formatDate(request.sample_delivery_schedule)"></div>
                                <div class="text-xs text-gray-500" x-show="request.estimated_duration" x-text="request.estimated_duration + ' hari'"></div>
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
                                        <button @click="showDetail(request.id); $nextTick(() => { statusForm.status = 'sample_received'; showUpdateStatusModalOpen = true; })"
                                                class="text-teal-600 hover:text-teal-900 text-xs px-2 py-1 rounded border border-teal-200 hover:bg-teal-50">
                                            Terima Sampel
                                        </button>
                                    </template>

                                    <template x-if="request.status === 'sample_received'">
                                        <button @click="showDetail(request.id); $nextTick(() => { statusForm.status = 'in_progress'; showUpdateStatusModalOpen = true; })"
                                                class="text-purple-600 hover:text-purple-900 text-xs px-2 py-1 rounded border border-purple-200 hover:bg-purple-50">
                                            Mulai Pengujian
                                        </button>
                                    </template>

                                    <template x-if="request.status === 'in_progress'">
                                        <button @click="showDetail(request.id); $nextTick(() => { statusForm.status = 'completed'; showUpdateStatusModalOpen = true; })"
                                                class="text-green-600 hover:text-green-900 text-xs px-2 py-1 rounded border border-green-200 hover:bg-green-50">
                                            Selesai
                                        </button>
                                    </template>

                                    <!-- Letter Download for completed tests with uploaded files -->
                                    <template x-if="request.status === 'completed' && request.result_files_path">
                                        <button @click="downloadResults(request.id)"
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            <p class="mt-2">Tidak ada data pengujian ditemukan</p>
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

            <div class="inline-block w-full max-w-5xl p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl ring-1 ring-gray-200"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-5 sm:px-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Detail Pengujian Lab</h3>
                                <p class="text-blue-100 text-sm">Laboratorium Gelombang, Optik dan Spektroskopi</p>
                            </div>
                        </div>
                        <button @click="showDetailModalOpen = false"
                                class="text-white hover:text-blue-200 transition-colors duration-200 p-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div x-show="selectedRequest" class="px-6 py-6 sm:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Request Information -->
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200 shadow-sm hover:shadow-md transition-all duration-200">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-blue-900">Informasi Permohonan</h4>
                                </div>
                                <dl class="space-y-3">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">ID Pengujian:</dt>
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

                            <!-- Client Information -->
                            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 p-6 rounded-xl border border-amber-200 shadow-sm hover:shadow-md transition-all duration-200">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-amber-900">Informasi Klien</h4>
                                </div>
                                <dl class="space-y-3">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Nama:</dt>
                                        <dd class="text-sm text-gray-900" x-text="selectedRequest?.client_name"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Email:</dt>
                                        <dd class="text-sm text-gray-900" x-text="selectedRequest?.client_email"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Telepon:</dt>
                                        <dd class="text-sm text-gray-900" x-text="selectedRequest?.client_phone"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Organisasi:</dt>
                                        <dd class="text-sm text-gray-900" x-text="selectedRequest?.client_organization"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm text-gray-500">Alamat:</dt>
                                        <dd class="text-sm text-gray-900 mt-1" x-text="selectedRequest?.client_address"></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Sample Information -->
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200 shadow-sm hover:shadow-md transition-all duration-200">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-green-900">Informasi Sampel</h4>
                                </div>
                                <dl class="space-y-3">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Nama Sampel:</dt>
                                        <dd class="text-sm text-gray-900" x-text="selectedRequest?.sample_name"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Kuantitas:</dt>
                                        <dd class="text-sm text-gray-900" x-text="selectedRequest?.sample_quantity"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm text-gray-500">Deskripsi:</dt>
                                        <dd class="text-sm text-gray-900 mt-1" x-text="selectedRequest?.sample_description"></dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Testing Information -->
                            <div class="bg-gradient-to-br from-purple-50 to-violet-50 p-6 rounded-xl border border-purple-200 shadow-sm hover:shadow-md transition-all duration-200">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-purple-900">Informasi Pengujian</h4>
                                </div>
                                <dl class="space-y-3">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Jenis Pengujian:</dt>
                                        <dd class="text-sm text-gray-900 flex items-center">
                                            <span x-html="getTestingTypeIcon(selectedRequest?.testing_type)" class="mr-2"></span>
                                            <span x-text="getTestingTypeText(selectedRequest?.testing_type)"></span>
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Jadwal Pengiriman:</dt>
                                        <dd class="text-sm text-gray-900" x-text="formatDate(selectedRequest?.sample_delivery_schedule)"></dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Estimasi Durasi:</dt>
                                        <dd class="text-sm text-gray-900" x-text="(selectedRequest?.estimated_duration || 0) + ' hari'"></dd>
                                    </div>
                                    <div class="flex justify-between" x-show="selectedRequest?.urgent_request">
                                        <dt class="text-sm text-gray-500">Prioritas:</dt>
                                        <dd class="text-sm text-red-600 font-medium">⚡ Urgent</dd>
                                    </div>
                                    <div class="flex justify-between" x-show="selectedRequest?.cost">
                                        <dt class="text-sm text-gray-500">Estimasi Biaya:</dt>
                                        <dd class="text-sm text-gray-900 font-medium" x-text="formatCurrency(selectedRequest?.cost)"></dd>
                                    </div>
                                </dl>
                            </div>

                        </div>
                    </div>

                    <!-- Full Width Sections -->
                    <div class="mt-8 space-y-6">
                        <!-- Approval Notes - Full Width -->
                        <div class="bg-gradient-to-br from-slate-50 to-gray-50 p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200" x-show="selectedRequest?.approval_notes">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-slate-900">Catatan Persetujuan</h4>
                            </div>
                            <div class="bg-white p-4 rounded-lg border border-slate-100">
                                <p class="text-sm text-gray-700 leading-relaxed" x-text="selectedRequest?.approval_notes"></p>
                            </div>
                        </div>

                        <!-- Results Section - Full Width -->
                        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-6 rounded-xl border border-indigo-200 shadow-sm hover:shadow-md transition-all duration-200" x-show="selectedRequest?.status === 'completed'">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-indigo-900">Hasil Pengujian</h4>
                            </div>

                            <div class="bg-white p-6 rounded-lg border border-indigo-100 space-y-4">
                                <div x-show="selectedRequest?.result_summary">
                                    <h5 class="text-sm font-medium text-gray-900 mb-2">Ringkasan Hasil:</h5>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-700 leading-relaxed" x-text="selectedRequest?.result_summary"></p>
                                    </div>
                                </div>

                                <div x-show="selectedRequest?.result_files_path">
                                    <h5 class="text-sm font-medium text-gray-900 mb-3">Dokumen Hasil:</h5>
                                    <button @click="downloadResults(selectedRequest.id)"
                                            class="inline-flex items-center px-6 py-3 border border-indigo-300 rounded-lg text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm hover:shadow-md">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span class="mr-2">📄</span>
                                        Lihat Surat Hasil Pengujian
                                    </button>
                                </div>

                                <div x-show="selectedRequest?.completion_date" class="mt-4 pt-4 border-t border-indigo-200">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-medium">Pengujian selesai pada:</span>
                                        <span class="ml-2 text-green-600 font-medium" x-text="formatDate(selectedRequest?.completion_date)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-gray-50 -mx-6 -mb-6 px-6 py-6 mt-8 rounded-b-2xl border-t border-gray-200">
                        <div class="flex flex-wrap justify-end gap-3">
                            <template x-if="selectedRequest?.status === 'pending'">
                                <div class="flex flex-wrap gap-3">
                                    <button @click="showRejectModal()"
                                            class="inline-flex items-center px-6 py-3 text-sm font-medium text-red-700 bg-white border-2 border-red-300 rounded-xl hover:bg-red-50 hover:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Tolak Pengujian
                                    </button>
                                    <button @click="showApproveModal(selectedRequest.id)"
                                            class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 border border-transparent rounded-xl hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Setujui Pengujian
                                    </button>
                                </div>
                            </template>

                            <template x-if="selectedRequest?.status === 'approved'">
                                <button @click="statusForm.status = 'sample_received'; showUpdateStatusModalOpen = true;"
                                        class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-teal-600 to-cyan-600 border border-transparent rounded-xl hover:from-teal-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    Terima Sampel
                                </button>
                            </template>

                            <template x-if="selectedRequest?.status === 'sample_received'">
                                <button @click="statusForm.status = 'in_progress'; showUpdateStatusModalOpen = true;"
                                        class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-indigo-600 border border-transparent rounded-xl hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Mulai Pengujian
                                </button>
                            </template>

                            <template x-if="selectedRequest?.status === 'in_progress'">
                                <button @click="statusForm.status = 'completed'; showUpdateStatusModalOpen = true;"
                                        class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 border border-transparent rounded-xl hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                                    </svg>
                                    Selesaikan Pengujian
                                </button>
                            </template>

                            <button @click="showDetailModalOpen = false"
                                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tutup
                            </button>
                        </div>
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
                    <h3 class="text-lg font-medium text-gray-900">Setujui Pengujian</h3>
                    <button @click="showApproveModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="approveRequest()">
                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estimasi Biaya Pengujian (Rp)</label>
                            <input type="number"
                                   x-model="approvalForm.cost"
                                   min="0"
                                   step="1000"
                                   placeholder="Masukkan estimasi biaya pengujian..."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estimasi Durasi (hari)</label>
                            <input type="number"
                                   x-model="approvalForm.estimated_duration"
                                   min="1"
                                   max="30"
                                   placeholder="Masukkan estimasi durasi..."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan Persetujuan (Opsional)</label>
                            <textarea x-model="approvalForm.notes"
                                      rows="3"
                                      placeholder="Masukkan catatan atau instruksi tambahan untuk pengujian..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
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
                            <span x-show="!submitting">Setujui Pengujian</span>
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
                    <h3 class="text-lg font-medium text-gray-900">Tolak Pengujian</h3>
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
                                  placeholder="Masukkan alasan penolakan pengujian..."
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
                            <span x-show="!submitting">Tolak Pengujian</span>
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

            <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Update Status Pengujian</h3>
                    <button @click="showUpdateStatusModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="updateStatus()">
                    <div class="mt-4 space-y-4">
                        <p class="text-sm text-gray-600">
                            Apakah Anda yakin ingin mengubah status pengujian ini menjadi <strong x-text="getStatusText(statusForm.status)"></strong>?
                        </p>

                        <!-- Results upload section for completing tests -->
                        <div x-show="statusForm.status === 'completed'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Upload Hasil Pengujian</label>
                                <input type="file"
                                       @change="handleResultsFile($event)"
                                       multiple
                                       accept=".pdf,.doc,.docx,.xlsx,.xls,.png,.jpg,.jpeg"
                                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-1">Format yang didukung: PDF, DOC, DOCX, XLSX, XLS, PNG, JPG, JPEG</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ringkasan Hasil</label>
                                <textarea x-model="statusForm.result_summary"
                                          rows="3"
                                          placeholder="Masukkan ringkasan hasil pengujian..."
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                            <textarea x-model="statusForm.notes"
                                      rows="3"
                                      placeholder="Masukkan catatan tambahan..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
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
    Alpine.data('testingRequestsData', () => ({
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
            testing_type: '',
            date_from: '',
            date_to: ''
        },

        approvalForm: {
            cost: '',
            estimated_duration: '',
            notes: ''
        },

        rejectionForm: {
            reason: ''
        },

        statusForm: {
            status: '',
            notes: '',
            result_summary: '',
            result_files: []
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

                const response = await fetch(`/api/admin/requests/testing?${params}`, {
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
                    this.pagination = data.meta?.pagination || data.pagination;
                } else {
                    throw new Error(data.message || 'Gagal memuat data pengujian');
                }
            } catch (error) {
                console.error('Failed to load testing requests:', error);
                this.$dispatch('global-error', { message: error.message });
            } finally {
                this.loading = false;
            }
        },

        async loadSummary() {
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) return;

                const response = await fetch('/api/admin/requests/testing?summary=true', {
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
                testing_type: '',
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

                const response = await fetch(`/api/admin/requests/testing/${requestId}`, {
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
                    throw new Error(data.message || 'Gagal memuat detail pengujian');
                }
            } catch (error) {
                console.error('Failed to load testing request details:', error);
                this.$dispatch('global-error', { message: error.message });
            }
        },

        showApproveModal(requestId) {
            if (requestId) {
                this.showDetail(requestId).then(() => {
                    this.$nextTick(() => {
                        this.approvalForm = {
                            cost: '',
                            estimated_duration: '',
                            notes: ''
                        };
                        this.showApproveModalOpen = true;
                    });
                });
            } else {
                this.approvalForm = {
                    cost: '',
                    estimated_duration: '',
                    notes: ''
                };
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

                const response = await fetch(`/api/admin/requests/testing/${this.selectedRequest.id}/approve`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        approval_notes: this.approvalForm.notes,
                        cost: this.approvalForm.cost ? parseFloat(this.approvalForm.cost) : null,
                        estimated_duration: this.approvalForm.estimated_duration ? parseInt(this.approvalForm.estimated_duration) : null
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.showApproveModalOpen = false;
                    this.showDetailModalOpen = false;
                    this.$dispatch('global-success', { message: 'Pengujian berhasil disetujui!' });
                    await this.loadRequests();
                    await this.loadSummary();
                } else {
                    throw new Error(data.message || 'Gagal menyetujui pengujian');
                }
            } catch (error) {
                console.error('Failed to approve testing request:', error);
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

                const response = await fetch(`/api/admin/requests/testing/${this.selectedRequest.id}/reject`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        approval_notes: this.rejectionForm.reason
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.showRejectModalOpen = false;
                    this.showDetailModalOpen = false;
                    this.$dispatch('global-success', { message: 'Pengujian berhasil ditolak!' });
                    await this.loadRequests();
                    await this.loadSummary();
                } else {
                    throw new Error(data.message || 'Gagal menolak pengujian');
                }
            } catch (error) {
                console.error('Failed to reject testing request:', error);
                this.$dispatch('global-error', { message: error.message });
            } finally {
                this.submitting = false;
            }
        },

        async updateStatus() {
            if (!this.selectedRequest || !this.statusForm.status) return;

            console.log('updateStatus called with:', {
                selectedRequestId: this.selectedRequest.id,
                statusFormStatus: this.statusForm.status,
                selectedRequest: this.selectedRequest
            });

            this.submitting = true;
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                // Prepare form data for file upload
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('status', this.statusForm.status);
                if (this.statusForm.notes) {
                    formData.append('approval_notes', this.statusForm.notes);
                }
                if (this.statusForm.result_summary) {
                    formData.append('result_summary', this.statusForm.result_summary);
                }

                // Add result files if any
                if (this.statusForm.result_files && this.statusForm.result_files.length > 0) {
                    console.log('Adding files to FormData:', this.statusForm.result_files);
                    this.statusForm.result_files.forEach((file, index) => {
                        console.log(`Adding file ${index}:`, file.name, file.size, file.type);
                        // Use the same key name for all files, Laravel will automatically create an array
                        formData.append('result_files[]', file);
                    });
                } else {
                    console.log('No files to upload - result_files:', this.statusForm.result_files);
                }

                const apiUrl = `/api/admin/requests/testing/${this.selectedRequest.id}`;
                console.log('Making API request to:', apiUrl);
                console.log('FormData contents:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                console.log('API Response status:', response.status);
                console.log('API Response headers:', response.headers);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.log('API Error response text:', errorText);
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                console.log('API Response data:', data);

                if (data.success) {
                    this.showUpdateStatusModalOpen = false;
                    this.$dispatch('global-success', { message: 'Status pengujian berhasil diupdate!' });

                    // Refresh the selected request data with updated information
                    this.selectedRequest = data.data;

                    // Reset status form
                    this.statusForm = {
                        status: '',
                        notes: '',
                        result_summary: '',
                        result_files: []
                    };

                    // Reload the requests list and summary
                    await this.loadRequests();
                    await this.loadSummary();
                } else {
                    throw new Error(data.message || 'Gagal mengupdate status pengujian');
                }
            } catch (error) {
                console.error('Failed to update testing status:', error);
                this.$dispatch('global-error', { message: error.message });
            } finally {
                this.submitting = false;
                // Reset form
                this.statusForm = {
                    status: '',
                    notes: '',
                    result_summary: '',
                    result_files: []
                };
            }
        },

        handleResultsFile(event) {
            const files = Array.from(event.target.files);
            console.log('Files selected:', files);
            this.statusForm.result_files = files;
            console.log('statusForm.result_files updated:', this.statusForm.result_files);
        },

        async downloadLetter(requestId) {
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const response = await fetch(`/api/admin/requests/testing/${requestId}/letter`, {
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
                a.download = `surat-pengujian-${requestId}.pdf`;
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

        async downloadResults(requestId) {
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const response = await fetch(`/api/admin/requests/testing/${requestId}/results`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/pdf'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                // Get the PDF blob and create a URL
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);

                // Open PDF in new tab
                window.open(url, '_blank');

                // Clean up the URL after a delay
                setTimeout(() => {
                    window.URL.revokeObjectURL(url);
                }, 1000);

                this.$dispatch('global-success', { message: 'Surat hasil pengujian dibuka di tab baru!' });
            } catch (error) {
                console.error('Failed to open results:', error);
                this.$dispatch('global-error', { message: 'Gagal membuka surat: ' + error.message });
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

        formatCurrency(amount) {
            if (!amount) return '-';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        },

        getStatusText(status) {
            const statusMap = {
                'pending': 'Menunggu Review',
                'approved': 'Disetujui',
                'sample_received': 'Sampel Diterima',
                'rejected': 'Ditolak',
                'in_progress': 'Sedang Diproses',
                'completed': 'Selesai',
                'cancelled': 'Dibatalkan'
            };
            return statusMap[status] || status;
        },

        getStatusBadgeClass(status) {
            const statusClasses = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-blue-100 text-blue-800',
                'sample_received': 'bg-teal-100 text-teal-800',
                'rejected': 'bg-red-100 text-red-800',
                'in_progress': 'bg-purple-100 text-purple-800',
                'completed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-gray-100 text-gray-800'
            };
            return statusClasses[status] || 'bg-gray-100 text-gray-800';
        },

        getTestingTypeText(type) {
            const typeMap = {
                'ftir_spectroscopy': 'FTIR Spectroscopy',
                'uv_vis_spectroscopy': 'UV-Vis Spectroscopy',
                'optical_microscopy': 'Optical Microscopy',
                'custom': 'Custom Testing'
            };
            return typeMap[type] || type;
        },

        getTestingTypeIcon(type) {
            const iconMap = {
                'ftir_spectroscopy': '📊',
                'uv_vis_spectroscopy': '🌈',
                'optical_microscopy': '🔬',
                'custom': '⚗️'
            };
            return iconMap[type] || '🧪';
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
