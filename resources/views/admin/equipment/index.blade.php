@extends('admin.layouts.app')

@section('title', 'Kelola Alat Laboratorium')

@section('content')
<div x-data="equipmentData()" x-init="init()" class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Alat Laboratorium</h1>
            <p class="text-sm text-gray-500">Kelola inventaris dan data alat laboratorium.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-md shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                <span>Tambah Alat</span>
            </button>
            <button @click="refresh()" class="p-2 rounded-md border text-gray-600 hover:bg-gray-50" :disabled="loading">
                <svg :class="{'animate-spin': loading}" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" x-show="summary">
        <!-- Total Equipment -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Alat</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="summary?.total_equipment || 0"></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Units -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Unit</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="summary?.total_units || 0"></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Utilization Rate -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tingkat Penggunaan</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <span x-text="summary?.utilization_rate || 0"></span>%
                    </p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <div class="bg-white p-6 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Perlu Perhatian</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <span x-text="(summary?.alerts?.low_availability_count || 0) + (summary?.alerts?.maintenance_needed_count || 0)"></span>
                    </p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-lg border space-y-4 shadow-sm">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Pencarian</label>
                <div class="relative">
                    <input type="text" x-model.debounce.400ms="filters.search" @input="debouncedFetch()" placeholder="Cari nama, model, atau lokasi..." class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500" />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    </div>
                </div>
            </div>
            <div class="w-48">
                <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                <select x-model="filters.category_id" @change="fetchItems()" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Semua Kategori</option>
                    <template x-for="category in categories" :key="category.id">
                        <option :value="category.id" x-text="category.name"></option>
                    </template>
                </select>
            </div>
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select x-model="filters.status" @change="fetchItems()" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="retired">Tidak Aktif</option>
                </select>
            </div>
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">Kondisi</label>
                <select x-model="filters.condition_status" @change="fetchItems()" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Semua Kondisi</option>
                    <option value="excellent">Sangat Baik</option>
                    <option value="good">Baik</option>
                    <option value="fair">Cukup</option>
                    <option value="poor">Buruk</option>
                </select>
            </div>
            <div class="flex items-center gap-2 mb-1">
                <button @click="resetFilters()" class="text-xs text-gray-600 hover:text-gray-900 underline">Reset</button>
            </div>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-500">
            <div>
                <span x-text="`Menampilkan ${pagination.from || 0}-${pagination.to || 0} dari ${pagination.total || 0} alat`"></span>
            </div>
            <div class="flex items-center gap-2">
                <label>Per Halaman:</label>
                <select x-model.number="pagination.per_page" @change="changePerPage()" class="rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Equipment Table -->
    <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="item in items" :key="item.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                <img class="h-12 w-12 rounded-lg object-cover border"
                                    :src="item.image_url"
                                    :alt="item.name"
                                    x-on:error="$event.target.src='/assets/images/placeholder.svg'" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="item.name"></div>
                                        <div class="text-sm text-gray-500">
                                            <span x-text="item.model || '-'"></span>
                                            <span x-show="item.manufacturer" x-text="`${item.manufacturer ? ' • ' + item.manufacturer : ''}`"></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="item.category?.name || '-'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <span x-text="item.available_quantity"></span> / <span x-text="item.total_quantity"></span>
                                </div>
                                <div class="text-xs text-gray-500">Tersedia / Total</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="{
                                          'bg-green-100 text-green-800': item.status === 'active',
                                          'bg-yellow-100 text-yellow-800': item.status === 'maintenance',
                                          'bg-red-100 text-red-800': item.status === 'retired'
                                      }">
                                    <span x-text="item.status === 'active' ? 'Aktif' : item.status === 'maintenance' ? 'Maintenance' : 'Tidak Aktif'"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="{
                                          'bg-green-100 text-green-800': item.condition_status === 'excellent',
                                          'bg-blue-100 text-blue-800': item.condition_status === 'good',
                                          'bg-yellow-100 text-yellow-800': item.condition_status === 'fair',
                                          'bg-red-100 text-red-800': item.condition_status === 'poor'
                                      }">
                                    <span x-text="item.condition_status === 'excellent' ? 'Sangat Baik' : item.condition_status === 'good' ? 'Baik' : item.condition_status === 'fair' ? 'Cukup' : 'Buruk'"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="item.location || '-'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="edit(item)" class="text-blue-600 hover:text-blue-900 p-1 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(item)" class="text-red-600 hover:text-red-900 p-1 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="p-8 text-center">
            <div class="inline-flex items-center gap-2 text-gray-500">
                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Memuat data...</span>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && items.length === 0" class="p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada alat ditemukan</h3>
            <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan alat laboratorium baru.</p>
            <div class="mt-6">
                <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Alat
                </button>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div x-show="pagination.last_page > 1" class="bg-white px-4 py-3 rounded-lg border shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-700">
                <span>Halaman</span>
                <span class="font-medium" x-text="pagination.current_page"></span>
                <span>dari</span>
                <span class="font-medium" x-text="pagination.last_page"></span>
            </div>
            <div class="flex items-center gap-1">
                <button @click="goToPage(pagination.current_page - 1)"
                        :disabled="pagination.current_page <= 1"
                        class="px-3 py-1 text-sm rounded border disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                    Sebelumnya
                </button>
                <template x-for="page in getVisiblePages()" :key="page">
                    <button @click="goToPage(page)"
                            :class="page === pagination.current_page ? 'bg-emerald-600 text-white' : 'border hover:bg-gray-50'"
                            class="px-3 py-1 text-sm rounded">
                        <span x-text="page"></span>
                    </button>
                </template>
                <button @click="goToPage(pagination.current_page + 1)"
                        :disabled="pagination.current_page >= pagination.last_page"
                        class="px-3 py-1 text-sm rounded border disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal()"></div>
            <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" x-text="`${form.id ? 'Edit Alat' : 'Tambah Alat Baru'}`"></h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="save()" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Alat *</label>
                            <input type="text" x-model="form.name" required
                                   class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                   :class="{'border-red-300': errors.name}"
                                   placeholder="Masukkan nama alat" />
                            <p x-show="errors.name" x-text="errors.name?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                            <select x-model="form.category_id" required
                                    class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    :class="{'border-red-300': errors.category_id}">
                                <option value="">Pilih Kategori</option>
                                <template x-for="category in categories" :key="category.id">
                                    <option :value="category.id" x-text="category.name"></option>
                                </template>
                            </select>
                            <p x-show="errors.category_id" x-text="errors.category_id?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Model -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                            <input type="text" x-model="form.model"
                                   class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                   placeholder="Model alat" />
                        </div>

                        <!-- Manufacturer -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                            <input type="text" x-model="form.manufacturer"
                                   class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                   placeholder="Pembuat alat" />
                        </div>

                        <!-- Location -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                            <input type="text" x-model="form.location"
                                   class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                   placeholder="Lokasi penyimpanan" />
                        </div>

                        <!-- Total Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Total *</label>
                            <input type="number" x-model.number="form.total_quantity" required min="1"
                                   class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                   :class="{'border-red-300': errors.total_quantity}" />
                            <p x-show="errors.total_quantity" x-text="errors.total_quantity?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Available Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tersedia *</label>
                            <input type="number" x-model.number="form.available_quantity" required min="0"
                                   :max="form.total_quantity || 9999"
                                   class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                   :class="{'border-red-300': errors.available_quantity}" />
                            <div class="mt-1 text-xs text-gray-500" x-show="form.id">
                                <span>💡 Catatan: Jumlah tersedia tidak boleh melebihi jumlah total dikurangi unit yang sedang dipinjam</span>
                            </div>
                            <p x-show="errors.available_quantity" x-text="errors.available_quantity?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select x-model="form.status" required
                                    class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    :class="{'border-red-300': errors.status}">
                                <option value="">Pilih Status</option>
                                <option value="active">Aktif</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="retired">Tidak Aktif</option>
                            </select>
                            <p x-show="errors.status" x-text="errors.status?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Condition -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi *</label>
                            <select x-model="form.condition_status" required
                                    class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    :class="{'border-red-300': errors.condition_status}">
                                <option value="">Pilih Kondisi</option>
                                <option value="excellent">Sangat Baik</option>
                                <option value="good">Baik</option>
                                <option value="fair">Cukup</option>
                                <option value="poor">Buruk</option>
                            </select>
                            <p x-show="errors.condition_status" x-text="errors.condition_status?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Purchase Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembelian</label>
                            <input type="date" x-model="form.purchase_date"
                                   class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" />
                        </div>

                        <!-- Purchase Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Pembelian</label>
                            <input type="number" x-model.number="form.purchase_price" min="0" step="0.01"
                                   class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                   placeholder="0.00" />
                        </div>

                        <!-- Image Upload -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Alat</label>
                            <input type="file" @change="handleImageUpload($event)" accept="image/*"
                                   class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                   :class="{'border-red-300': errors.image}" />
                            <p class="text-xs text-gray-500 mt-1">Format: JPEG, PNG, JPG, GIF, WebP. Maksimal 5MB.</p>
                            <p x-show="errors.image" x-text="errors.image?.[0]" class="text-red-500 text-xs mt-1"></p>

                            <div x-show="imagePreview || (form.id && form.image_url)" class="mt-2">
                          <img :src="imagePreview || form.image_url"
                              class="h-20 w-20 rounded-lg object-cover border"
                              x-on:error="$event.target.src='/assets/images/placeholder.svg'" />
                                <button type="button" @click="removeImage()" class="text-red-600 text-xs mt-1 hover:underline">
                                    Hapus Gambar
                                </button>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea x-model="form.notes" rows="3"
                                      class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                      placeholder="Catatan tambahan tentang alat"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t">
                        <button type="button" @click="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" :disabled="saving"
                                class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-md hover:bg-emerald-700 disabled:opacity-50">
                            <span x-show="!saving" x-text="`${form.id ? 'Update Alat' : 'Simpan Alat'}`"></span>
                            <span x-show="saving" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Menyimpan...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showDeleteModal = false"></div>
            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-shrink-0 w-10 h-10 mx-auto bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Hapus Alat</h3>
                        <p class="text-sm text-gray-500">
                            Apakah Anda yakin ingin menghapus alat
                            <span class="font-medium" x-text="deleteItem?.name"></span>?
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Batal
                    </button>
                    <button @click="deleteConfirmed()" :disabled="deleting"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 disabled:opacity-50">
                        <span x-show="!deleting">Hapus</span>
                        <span x-show="deleting" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Menghapus...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('equipmentData', () => ({
        // State
        items: [],
        categories: [],
        summary: null,
        loading: false,
        saving: false,
        deleting: false,

        // Pagination
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 20,
            total: 0,
            from: 0,
            to: 0,
        },

        // Filters
        filters: {
            search: '',
            category_id: '',
            status: '',
            condition_status: '',
            sort_by: 'created_at',
            sort_order: 'desc',
        },

        // Modals
        showModal: false,
        showDeleteModal: false,

        // Form
        form: {
            id: null,
            name: '',
            category_id: '',
            model: '',
            manufacturer: '',
            total_quantity: 1,
            available_quantity: 1,
            status: 'active',
            condition_status: 'excellent',
            purchase_date: '',
            purchase_price: '',
            location: '',
            notes: '',
            image: null,
            image_url: '',
            remove_image: false,
        },

        errors: {},
        imagePreview: null,
        deleteItem: null,

        // Initialization
        async init() {
            this.loading = true;
            try {
                await Promise.all([
                    this.fetchItems(),
                    this.fetchCategories(),
                    this.fetchSummary()
                ]);
            } catch (error) {
                console.error('Failed to initialize:', error);
                this.showError('Gagal memuat data.');
            } finally {
                this.loading = false;
            }
        },

        // Fetch data methods
        async fetchItems() {
            try {
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    ...this.filters
                });

                // Remove empty params
                for (const [key, value] of [...params.entries()]) {
                    if (!value) params.delete(key);
                }

                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const response = await fetch(`/api/admin/equipment?${params.toString()}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.items = data.data || [];
                    if (data.meta?.pagination) {
                        this.pagination = {
                            ...this.pagination,
                            ...data.meta.pagination,
                            prev_page_url: data.meta.pagination.prev_page_url,
                            next_page_url: data.meta.pagination.next_page_url
                        };
                    }
                } else {
                    throw new Error(data.message || 'Gagal memuat data alat.');
                }
            } catch (error) {
                console.error('Error fetching equipment:', error);
                this.showError(error.message || 'Gagal memuat data alat.');
            }
        },

        async fetchCategories() {
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const response = await fetch('/api/admin/equipment/categories', {
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
                    this.categories = data.data || [];
                } else {
                    throw new Error(data.message || 'Gagal memuat kategori.');
                }
            } catch (error) {
                console.error('Error fetching categories:', error);
                this.showError(error.message || 'Gagal memuat kategori.');
            }
        },

        async fetchSummary() {
            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const response = await fetch('/api/admin/equipment/summary', {
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
                    this.summary = data.data || {};
                } else {
                    throw new Error(data.message || 'Gagal memuat ringkasan.');
                }
            } catch (error) {
                console.error('Error fetching summary:', error);
                // Don't show error for summary as it's not critical
            }
        },

        // Search and filter methods
        debouncedFetch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.pagination.current_page = 1;
                this.fetchItems();
            }, 400);
        },

        resetFilters() {
            this.filters = {
                search: '',
                category_id: '',
                status: '',
                condition_status: '',
                sort_by: 'created_at',
                sort_order: 'desc',
            };
            this.pagination.current_page = 1;
            this.fetchItems();
        },

        changePerPage() {
            this.pagination.current_page = 1;
            this.fetchItems();
        },

        // Pagination methods
        goToPage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.fetchItems();
            }
        },

        getVisiblePages() {
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            const pages = [];

            let start = Math.max(1, current - 2);
            let end = Math.min(last, current + 2);

            if (end - start < 4) {
                if (start === 1) {
                    end = Math.min(last, start + 4);
                } else {
                    start = Math.max(1, end - 4);
                }
            }

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            return pages;
        },

        // Modal methods
        openCreateModal() {
            this.resetForm();
            this.showModal = true;
            this.errors = {};
            this.imagePreview = null;
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
            this.errors = {};
            this.imagePreview = null;
        },

        edit(item) {
            this.form = {
                id: item.id,
                name: item.name || '',
                category_id: item.category?.id || '',
                model: item.model || '',
                manufacturer: item.manufacturer || '',
                total_quantity: item.total_quantity || 1,
                available_quantity: item.available_quantity || 1,
                status: item.status || 'active',
                condition_status: item.condition_status || 'excellent',
                purchase_date: item.purchase_date || '',
                purchase_price: item.purchase_price || '',
                location: item.location || '',
                notes: item.notes || '',
                image: null,
                image_url: item.image_url || '',
                remove_image: false,
            };
            this.showModal = true;
            this.errors = {};
            this.imagePreview = null;
        },

        resetForm() {
            this.form = {
                id: null,
                name: '',
                category_id: '',
                model: '',
                manufacturer: '',
                total_quantity: 1,
                available_quantity: 1,
                status: 'active',
                condition_status: 'excellent',
                purchase_date: '',
                purchase_price: '',
                location: '',
                notes: '',
                image: null,
                image_url: '',
                remove_image: false,
            };
        },

        // Form validation
        isFormInvalid() {
            return !this.form.name ||
                   !this.form.category_id ||
                   !this.form.status ||
                   !this.form.condition_status ||
                   this.form.total_quantity < 1 ||
                   this.form.available_quantity < 0 ||
                   this.form.available_quantity > this.form.total_quantity;
        },

        // Image handling
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.form.image = file;
                this.form.remove_image = false;

                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        removeImage() {
            this.form.image = null;
            this.form.remove_image = true;
            this.imagePreview = null;

            // Clear file input
            const fileInput = document.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.value = '';
            }
        },

        // CRUD operations
        async save() {
            if (this.isFormInvalid() || this.saving) return;

            this.saving = true;
            this.errors = {};

            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const formData = new FormData();

                // Debug: Log form data before processing
                console.log('Form data before processing:', this.form);

                // Required fields that must always be sent
                const requiredFields = ['name', 'category_id', 'total_quantity', 'available_quantity', 'status', 'condition_status'];

                // Add form fields
                Object.keys(this.form).forEach(key => {
                    // Skip special fields
                    if (key === 'id' || key === 'image' || key === 'image_url') {
                        console.log(`Skipped field: ${key} = ${this.form[key]} (reason: is ${key})`);
                        return;
                    }

                    // Always include required fields, even if empty
                    if (requiredFields.includes(key)) {
                        formData.append(key, this.form[key] || '');
                        console.log(`Added required field: ${key} = ${this.form[key] || ''}`);
                    }
                    // For optional fields, only include if they have values
                    else if (this.form[key] !== null && this.form[key] !== '') {
                        formData.append(key, this.form[key]);
                        console.log(`Added optional field: ${key} = ${this.form[key]}`);
                    } else {
                        console.log(`Skipped optional field: ${key} = ${this.form[key]} (reason: ${this.form[key] === null ? 'is null' : 'is empty'})`);
                    }
                });

                // Add image file if selected
                if (this.form.image) {
                    formData.append('image', this.form.image);
                    console.log('Added image file to FormData');
                }

                // Add remove image flag (always include for updates)
                if (this.form.id) {
                    formData.append('remove_image', this.form.remove_image ? '1' : '0');
                    console.log(`Added remove_image flag to FormData: ${this.form.remove_image ? '1' : '0'}`);
                }

                // Debug: Log final FormData contents
                console.log('Final FormData entries:');
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }

                const url = this.form.id
                    ? `/api/admin/equipment/${this.form.id}`
                    : '/api/admin/equipment';

                const method = this.form.id ? 'PUT' : 'POST';

                // For PUT requests via FormData, append _method
                if (method === 'PUT') {
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(url, {
                    method: this.form.id ? 'POST' : 'POST', // Always POST with _method for FormData
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showSuccess(this.form.id ? 'Alat berhasil diperbarui!' : 'Alat berhasil ditambahkan!');
                    this.closeModal();
                    await Promise.all([
                        this.fetchItems(),
                        this.fetchSummary()
                    ]);
                } else {
                    if (response.status === 422 && data.errors) {
                        this.errors = data.errors;
                        console.error('Validation errors:', data.errors);
                        console.error('Full response:', data);
                    } else {
                        console.error('Save failed:', data);
                        throw new Error(data.message || 'Gagal menyimpan alat.');
                    }
                }
            } catch (error) {
                console.error('Error saving equipment:', error);
                this.showError(error.message || 'Gagal menyimpan alat.');
            } finally {
                this.saving = false;
            }
        },

        confirmDelete(item) {
            this.deleteItem = item;
            this.showDeleteModal = true;
        },

        async deleteConfirmed() {
            if (!this.deleteItem || this.deleting) return;

            this.deleting = true;

            try {
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }

                const response = await fetch(`/api/admin/equipment/${this.deleteItem.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showSuccess('Alat berhasil dihapus!');
                    this.showDeleteModal = false;
                    this.deleteItem = null;
                    await Promise.all([
                        this.fetchItems(),
                        this.fetchSummary()
                    ]);
                } else {
                    throw new Error(data.message || 'Gagal menghapus alat.');
                }
            } catch (error) {
                console.error('Error deleting equipment:', error);
                this.showError(error.message || 'Gagal menghapus alat.');
            } finally {
                this.deleting = false;
            }
        },

        // Utility methods
        async refresh() {
            this.loading = true;
            try {
                await Promise.all([
                    this.fetchItems(),
                    this.fetchSummary()
                ]);
            } catch (error) {
                console.error('Error refreshing:', error);
                this.showError('Gagal memuat ulang data.');
            } finally {
                this.loading = false;
            }
        },

        showSuccess(message) {
            // Create and show success toast
            this.showToast(message, 'success');
        },

        showError(message) {
            // Create and show error toast
            this.showToast(message, 'error');
        },

        showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-md shadow-lg transition-all duration-300 transform translate-x-full`;

            if (type === 'success') {
                toast.className += ' bg-green-500 text-white';
            } else if (type === 'error') {
                toast.className += ' bg-red-500 text-white';
            } else {
                toast.className += ' bg-blue-500 text-white';
            }

            toast.innerHTML = `
                <div class="flex items-center gap-2">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(toast);

            // Show toast
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }, 5000);
        }
    }));
});
</script>
@endpush
@endsection
