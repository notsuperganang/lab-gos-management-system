@extends('admin.layouts.app')

@section('title', 'Kelola Staff')
@section('page-title', 'Kelola Staff')

@section('breadcrumbs')
    <li class="flex items-center">
        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"/>
        </svg>
        <span class="ml-4 text-sm font-medium text-gray-500">Profil & Publikasi</span>
    </li>
    <li class="flex items-center">
        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"/>
        </svg>
        <span class="ml-4 text-sm font-medium text-gray-500">Kelola Staff</span>
    </li>
@endsection

@section('page-actions')
    <div x-data>
        <button @click="$dispatch('open-create-modal')"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Staff
        </button>
    </div>
@endsection

@section('content')
<div x-data="staffData()" x-init="init()" @open-create-modal.window="openCreateModal()" class="space-y-6">

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input x-model="filters.search"
                           @input.debounce.300ms="fetchStaff()"
                           type="text"
                           id="search"
                           placeholder="Cari nama, posisi, email..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Staff Type Filter -->
            <div>
                <label for="staff_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Staff</label>
                <select x-model="filters.staff_type"
                        @change="fetchStaff()"
                        id="staff_type"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="dosen">Dosen</option>
                    <option value="laboran">Laboran</option>
                    <option value="teknisi">Teknisi</option>
                    <option value="kepala_laboratorium">Kepala Lab</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select x-model="filters.status"
                        @change="fetchStaff()"
                        id="status"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Non-aktif</option>
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Per Halaman</label>
                <select x-model="pagination.per_page"
                        @change="fetchStaff()"
                        id="per_page"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Daftar Staff</h3>
                <div class="text-sm text-gray-500">
                    <span x-show="!loading" x-text="`${pagination.total || 0} total staff`"></span>
                    <span x-show="loading">Loading...</span>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="p-8 text-center">
            <div class="inline-flex items-center space-x-2">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-600">Memuat data staff...</span>
            </div>
        </div>

        <!-- Table Content -->
        <div x-show="!loading" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button @click="sortBy('name')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Nama</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                </svg>
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipe Staff
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Posisi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button @click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Dibuat</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                </svg>
                            </button>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="staff in staffList" :key="staff.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                <img x-show="staff.photo_url"
                                    :src="staff.photo_url"
                                    :alt="staff.name"
                                    x-on:error="$event.target.src='/assets/images/placeholder.svg'"
                                    class="h-10 w-10 rounded-full object-cover">
                                        <div x-show="!staff.photo_url"
                                             class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="staff.name"></div>
                                        <div class="text-sm text-gray-500" x-text="staff.specialization || 'Tidak ada spesialisasi'"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="getStaffTypeBadgeClass(staff.staff_type)"
                                      x-text="getStaffTypeLabel(staff.staff_type)">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="staff.position || '-'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="staff.email || '-'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="staff.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                      x-text="staff.is_active ? 'Aktif' : 'Non-aktif'">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span x-text="formatDate(staff.created_at)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="viewStaff(staff)"
                                            class="text-blue-600 hover:text-blue-900 p-1"
                                            title="Lihat Detail">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button @click="editStaff(staff)"
                                            class="text-indigo-600 hover:text-indigo-900 p-1"
                                            title="Edit">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button @click="deleteStaff(staff)"
                                            class="text-red-600 hover:text-red-900 p-1"
                                            title="Hapus">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Empty State -->
            <div x-show="!loading && staffList.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada staff</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan staff pertama.</p>
                <div class="mt-6">
                    <button @click="openCreateModal()"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Staff
                    </button>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div x-show="!loading && pagination.total > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan
                    <span x-text="pagination.from || 0"></span> -
                    <span x-text="pagination.to || 0"></span> dari
                    <span x-text="pagination.total || 0"></span> staff
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="previousPage()"
                            :disabled="pagination.current_page <= 1"
                            :class="pagination.current_page <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white">
                        Previous
                    </button>

                    <template x-for="page in getPageNumbers()" :key="page">
                        <button @click="goToPage(page)"
                                :class="page === pagination.current_page ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                class="px-3 py-1 text-sm border border-gray-300 rounded-md"
                                x-text="page">
                        </button>
                    </template>

                    <button @click="nextPage()"
                            :disabled="pagination.current_page >= pagination.last_page"
                            :class="pagination.current_page >= pagination.last_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         @click.away="closeModal()">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" x-text="isEditing ? 'Edit Staff' : 'Tambah Staff Baru'"></h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form @submit.prevent="submitForm()" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input x-model="form.name"
                               type="text"
                               id="name"
                               required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <div x-show="errors.name" class="mt-1 text-sm text-red-600" x-text="errors.name"></div>
                    </div>

                    <!-- Staff Type -->
                    <div>
                        <label for="staff_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipe Staff <span class="text-red-500">*</span>
                        </label>
                        <select x-model="form.staff_type"
                                id="staff_type"
                                required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Tipe Staff</option>
                                                    <option value="dosen">Dosen</option>
                                                    <option value="laboran">Laboran</option>
                                                    <option value="teknisi">Teknisi</option>
                                                    <option value="kepala_laboratorium" :disabled="kepalaExists && !isEditing" x-bind:title="kepalaExists ? 'Sudah ada Kepala Laboratorium' : ''">Kepala Laboratorium</option>
                        </select>
                        <div x-show="errors.staff_type" class="mt-1 text-sm text-red-600" x-text="errors.staff_type"></div>
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Posisi/Jabatan</label>
                        <input x-model="form.position"
                               type="text"
                               id="position"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <div x-show="errors.position" class="mt-1 text-sm text-red-600" x-text="errors.position"></div>
                    </div>

                    <!-- Specialization -->
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi</label>
                        <input x-model="form.specialization"
                               type="text"
                               id="specialization"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <div x-show="errors.specialization" class="mt-1 text-sm text-red-600" x-text="errors.specialization"></div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input x-model="form.email"
                               type="email"
                               id="email"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <div x-show="errors.email" class="mt-1 text-sm text-red-600" x-text="errors.email"></div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input x-model="form.phone"
                               type="tel"
                               id="phone"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <div x-show="errors.phone" class="mt-1 text-sm text-red-600" x-text="errors.phone"></div>
                    </div>

                    <!-- Education -->
                    <div class="md:col-span-2">
                        <label for="education" class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                        <textarea x-model="form.education"
                                  id="education"
                                  rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        <div x-show="errors.education" class="mt-1 text-sm text-red-600" x-text="errors.education"></div>
                    </div>

                    <!-- Bio -->
                    <div class="md:col-span-2">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio/Profil Singkat</label>
                        <textarea x-model="form.bio"
                                  id="bio"
                                  rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        <div x-show="errors.bio" class="mt-1 text-sm text-red-600" x-text="errors.bio"></div>
                    </div>

                    <!-- Research Interests -->
                    <div class="md:col-span-2">
                        <label for="research_interests" class="block text-sm font-medium text-gray-700 mb-1">Minat Penelitian</label>
                        <textarea x-model="form.research_interests"
                                  id="research_interests"
                                  rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        <div x-show="errors.research_interests" class="mt-1 text-sm text-red-600" x-text="errors.research_interests"></div>
                    </div>

                    <!-- Photo Upload -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <div x-show="!form.photo_preview" class="mx-auto h-12 w-12 text-gray-400">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <img x-show="form.photo_preview"
                                     :src="form.photo_preview"
                                     class="mx-auto h-20 w-20 rounded-full object-cover">
                                <div class="flex text-sm text-gray-600">
                                    <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a file</span>
                                        <input @change="handlePhotoUpload($event)"
                                               id="photo"
                                               name="photo"
                                               type="file"
                                               accept="image/*"
                                               class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        <div x-show="errors.photo" class="mt-1 text-sm text-red-600" x-text="errors.photo"></div>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil</label>
                        <input x-model.number="form.sort_order"
                               type="number"
                               id="sort_order"
                               min="0"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <div x-show="errors.sort_order" class="mt-1 text-sm text-red-600" x-text="errors.sort_order"></div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="form.is_active"
                                id="is_active"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="1">Aktif</option>
                            <option value="0">Non-aktif</option>
                        </select>
                        <div x-show="errors.is_active" class="mt-1 text-sm text-red-600" x-text="errors.is_active"></div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button @click="closeModal()"
                            type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="submitting"
                            :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span x-show="!submitting" x-text="isEditing ? 'Update Staff' : 'Tambah Staff'"></span>
                        <span x-show="submitting">Processing...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Modal -->
    <div x-show="showViewModal"
         x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         @click.away="closeViewModal()">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Detail Staff</h3>
                <button @click="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div x-show="viewingStaff" class="space-y-6">
                <!-- Profile Section -->
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                    <img x-show="viewingStaff?.photo_url"
                        :src="viewingStaff?.photo_url"
                        :alt="viewingStaff?.name"
                        x-on:error="$event.target.src='/assets/images/placeholder.svg'"
                        class="h-20 w-20 rounded-full object-cover">
                        <div x-show="!viewingStaff?.photo_url"
                             class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                            <svg class="h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xl font-semibold text-gray-900" x-text="viewingStaff?.name"></h4>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="getStaffTypeBadgeClass(viewingStaff?.staff_type)"
                                  x-text="getStaffTypeLabel(viewingStaff?.staff_type)">
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1" x-text="viewingStaff?.position || 'Tidak ada posisi'"></p>
                        <p class="text-sm text-blue-600" x-text="viewingStaff?.specialization || 'Tidak ada spesialisasi'"></p>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Kontak</h5>
                        <div class="space-y-2">
                            <div x-show="viewingStaff?.email">
                                <span class="text-sm text-gray-600">Email:</span>
                                <span class="text-sm text-gray-900 ml-2" x-text="viewingStaff?.email"></span>
                            </div>
                            <div x-show="viewingStaff?.phone">
                                <span class="text-sm text-gray-600">Telepon:</span>
                                <span class="text-sm text-gray-900 ml-2" x-text="viewingStaff?.phone"></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Status</h5>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                              :class="viewingStaff?.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                              x-text="viewingStaff?.is_active ? 'Aktif' : 'Non-aktif'">
                        </span>
                    </div>
                </div>

                <!-- Education -->
                <div x-show="viewingStaff?.education">
                    <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Pendidikan</h5>
                    <p class="text-sm text-gray-900 whitespace-pre-wrap" x-text="viewingStaff?.education"></p>
                </div>

                <!-- Bio -->
                <div x-show="viewingStaff?.bio">
                    <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Bio/Profil</h5>
                    <p class="text-sm text-gray-900 whitespace-pre-wrap" x-text="viewingStaff?.bio"></p>
                </div>

                <!-- Research Interests -->
                <div x-show="viewingStaff?.research_interests">
                    <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Minat Penelitian</h5>
                    <p class="text-sm text-gray-900 whitespace-pre-wrap" x-text="viewingStaff?.research_interests"></p>
                </div>

                <!-- Metadata -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                        <div>
                            <span>Dibuat:</span>
                            <span class="ml-2" x-text="formatDate(viewingStaff?.created_at)"></span>
                        </div>
                        <div>
                            <span>Diupdate:</span>
                            <span class="ml-2" x-text="formatDate(viewingStaff?.updated_at)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal"
         x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-5">Hapus Staff</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus staff
                        <span class="font-medium" x-text="deletingStaff?.name"></span>?
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="flex items-center justify-center space-x-4 px-4 py-3">
                    <button @click="showDeleteModal = false"
                            class="px-4 py-2 bg-white text-gray-500 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50">
                        Batal
                    </button>
                    <button @click="confirmDelete()"
                            :disabled="deleting"
                            :class="deleting ? 'opacity-50 cursor-not-allowed' : ''"
                            class="px-4 py-2 bg-red-600 text-white border border-transparent rounded-md text-sm font-medium hover:bg-red-700">
                        <span x-show="!deleting">Hapus</span>
                        <span x-show="deleting">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.staffData = () => ({
    // Data properties
    staffList: [],
    loading: false,
    submitting: false,
    deleting: false,

    // Modal states
    showModal: false,
    showViewModal: false,
    showDeleteModal: false,
    isEditing: false,

    // Current items being viewed/edited/deleted
    editingStaff: null,
    viewingStaff: null,
    deletingStaff: null,

    // Filters and search
    filters: {
        search: '',
        staff_type: '',
        status: '',
    },

    // Sorting
    sort: {
        field: 'created_at',
        direction: 'desc'
    },

    // Pagination
    pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 25,
        total: 0,
        from: 0,
        to: 0
    },

    // Form data
    form: {
        name: '',
        staff_type: '',
        position: '',
        specialization: '',
        education: '',
        email: '',
        phone: '',
        bio: '',
        research_interests: '',
        sort_order: 0,
        is_active: '1',
        photo: null,
        photo_preview: null
    },

    // Form errors
    errors: {},

    // Flag indicating a Kepala Laboratorium already exists
    get kepalaExists() {
        return this.staffList?.some(s => s.staff_type === 'kepala_laboratorium');
    },

    // API token
    apiToken: localStorage.getItem('admin_token'),

    // Initialize component
    async init() {
        console.log('Staff management initializing...');

        if (!this.apiToken) {
            console.error('No admin token found, redirecting to login');
            window.location.href = '/admin/login';
            return;
        }

        await this.fetchStaff();
    },

    // Fetch staff data from API
    async fetchStaff() {
        this.loading = true;
        try {
            const params = new URLSearchParams({
                page: this.pagination.current_page,
                per_page: this.pagination.per_page,
                sort_field: this.sort.field,
                sort_direction: this.sort.direction
            });

            // Add filters with proper API parameter names
            if (this.filters.search) {
                params.append('search', this.filters.search);
            }
            if (this.filters.staff_type) {
                params.append('staff_type', this.filters.staff_type);
            }
            if (this.filters.status !== '') {
                params.append('is_active', this.filters.status);
            }

            const response = await fetch(`/api/admin/content/staff?${params}`, {
                headers: {
                    'Authorization': `Bearer ${this.apiToken}`,
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
                this.staffList = data.data;
                this.pagination = data.meta?.pagination || this.pagination;
                console.log('Staff data loaded:', this.staffList.length, 'items');
            } else {
                throw new Error(data.message || 'Failed to fetch staff');
            }
        } catch (error) {
            console.error('Failed to fetch staff:', error);
            this.showNotification('Error loading staff data', 'error');
        } finally {
            this.loading = false;
        }
    },

    // Sorting
    async sortBy(field) {
        if (this.sort.field === field) {
            this.sort.direction = this.sort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            this.sort.field = field;
            this.sort.direction = 'asc';
        }
        this.pagination.current_page = 1;
        await this.fetchStaff();
    },

    // Pagination methods
    async goToPage(page) {
        this.pagination.current_page = page;
        await this.fetchStaff();
    },

    async previousPage() {
        if (this.pagination.current_page > 1) {
            this.pagination.current_page--;
            await this.fetchStaff();
        }
    },

    async nextPage() {
        if (this.pagination.current_page < this.pagination.last_page) {
            this.pagination.current_page++;
            await this.fetchStaff();
        }
    },

    getPageNumbers() {
        const pages = [];
        const current = this.pagination.current_page;
        const last = this.pagination.last_page;

        let start = Math.max(1, current - 2);
        let end = Math.min(last, current + 2);

        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        return pages;
    },

    // Modal management
    openCreateModal() {
        this.resetForm();
        this.isEditing = false;
        this.showModal = true;
    },

    closeModal() {
        this.showModal = false;
        this.resetForm();
        this.isEditing = false;
        this.editingStaff = null;
    },

    closeViewModal() {
        this.showViewModal = false;
        this.viewingStaff = null;
    },

    resetForm() {
        this.form = {
            name: '',
            staff_type: '',
            position: '',
            specialization: '',
            education: '',
            email: '',
            phone: '',
            bio: '',
            research_interests: '',
            sort_order: 0,
            is_active: '1',
            photo: null,
            photo_preview: null
        };
        this.errors = {};
    },

    // CRUD operations
    async viewStaff(staff) {
        try {
            const response = await fetch(`/api/admin/content/staff/${staff.id}`, {
                headers: {
                    'Authorization': `Bearer ${this.apiToken}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to fetch staff details');

            const data = await response.json();
            if (data.success) {
                this.viewingStaff = data.data;
                this.showViewModal = true;
            }
        } catch (error) {
            console.error('Error viewing staff:', error);
            this.showNotification('Error loading staff details', 'error');
        }
    },

    editStaff(staff) {
        this.editingStaff = staff;
        this.isEditing = true;

        // Populate form with staff data
        this.form = {
            name: staff.name || '',
            staff_type: staff.staff_type || '',
            position: staff.position || '',
            specialization: staff.specialization || '',
            education: staff.education || '',
            email: staff.email || '',
            phone: staff.phone || '',
            bio: staff.bio || '',
            research_interests: staff.research_interests || '',
            sort_order: staff.sort_order || 0,
            is_active: staff.is_active ? '1' : '0',
            photo: null,
            photo_preview: staff.photo_url || null
        };

        this.showModal = true;
    },

    deleteStaff(staff) {
        this.deletingStaff = staff;
        this.showDeleteModal = true;
    },

    async confirmDelete() {
        if (!this.deletingStaff) return;

        this.deleting = true;
        try {
            const response = await fetch(`/api/admin/content/staff/${this.deletingStaff.id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${this.apiToken}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to delete staff');

            const data = await response.json();
            if (data.success) {
                this.showNotification('Staff berhasil dihapus', 'success');
                await this.fetchStaff();
                this.showDeleteModal = false;
                this.deletingStaff = null;
            }
        } catch (error) {
            console.error('Error deleting staff:', error);
            this.showNotification('Error deleting staff', 'error');
        } finally {
            this.deleting = false;
        }
    },

    // Form submission
    async submitForm() {
        this.submitting = true;
        this.errors = {};

        try {
            const formData = new FormData();

            // Append text fields
            Object.keys(this.form).forEach(key => {
                if (key !== 'photo' && key !== 'photo_preview' && this.form[key] !== null) {
                    formData.append(key, this.form[key]);
                }
            });

            // Append photo if selected
            if (this.form.photo) {
                formData.append('photo', this.form.photo);
            }

            const url = this.isEditing
                ? `/api/admin/content/staff/${this.editingStaff.id}`
                : '/api/admin/content/staff';

            // For PUT requests with FormData, we need to use POST with _method
            if (this.isEditing) {
                formData.append('_method', 'PUT');
            }

            const response = await fetch(url, {
                method: 'POST', // Always use POST for FormData
                headers: {
                    'Authorization': `Bearer ${this.apiToken}`,
                    'Accept': 'application/json'
                    // Don't set Content-Type header - let browser set it with boundary for FormData
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(
                    this.isEditing ? 'Staff berhasil diupdate' : 'Staff berhasil ditambahkan',
                    'success'
                );
                await this.fetchStaff();
                this.closeModal();
            } else {
                if (data.errors) {
                    this.errors = data.errors;
                } else {
                    throw new Error(data.message || 'Validation failed');
                }
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            this.showNotification('Error saving staff data', 'error');
        } finally {
            this.submitting = false;
        }
    },

    // Photo upload handling
    handlePhotoUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            this.showNotification('File size must be less than 2MB', 'error');
            return;
        }

        // Validate file type
        if (!file.type.startsWith('image/')) {
            this.showNotification('Please select an image file', 'error');
            return;
        }

        this.form.photo = file;

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            this.form.photo_preview = e.target.result;
        };
        reader.readAsDataURL(file);
    },

    // Utility methods
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
            return 'Invalid date';
        }
    },

    getStaffTypeLabel(staffType) {
        const labels = {
            'dosen': 'Dosen',
            'laboran': 'Laboran',
            'teknisi': 'Teknisi',
            'kepala_laboratorium': 'Kepala Lab'
        };
        return labels[staffType] || staffType;
    },

    getStaffTypeBadgeClass(staffType) {
        const classes = {
            'dosen': 'bg-blue-100 text-blue-800',
            'laboran': 'bg-green-100 text-green-800',
            'teknisi': 'bg-yellow-100 text-yellow-800',
            'kepala_laboratorium': 'bg-purple-100 text-purple-800'
        };
        return classes[staffType] || 'bg-gray-100 text-gray-800';
    },

    showNotification(message, type = 'info') {
        // Create better notification system
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 max-w-md transform transition-all duration-300 ease-in-out ${
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'success' ? 'bg-green-500 text-white' :
            'bg-blue-500 text-white'
        }`;

        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${type === 'error' ?
                        '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>' :
                        type === 'success' ?
                        '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' :
                        '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
                    }
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()"
                            class="inline-flex text-white hover:text-gray-200 focus:outline-none">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }
});
</script>
@endpush
