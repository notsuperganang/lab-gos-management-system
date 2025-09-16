@extends('admin.layouts.app')

@section('title', 'Kelola Galeri')

@section('content')
<div x-data="galleryData()" x-init="init()" class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Galeri</h1>
            <p class="text-sm text-gray-500">Kelola item galeri dan atur tampilan beranda.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-md shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                <span>Item Baru</span>
            </button>
            <button @click="refresh()" class="p-2 rounded-md border text-gray-600 hover:bg-gray-50" :disabled="loading">
                <svg :class="{'animate-spin': loading}" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </button>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-lg border space-y-4 shadow-sm">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Pencarian</label>
                <div class="relative">
                    <input type="text" x-model.debounce.400ms="filters.search" @input="debouncedFetch()" placeholder="Cari judul / deskripsi..." class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500" />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    </div>
                </div>
            </div>
            <div class="w-48">
                <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                <select x-model="filters.category" @change="fetchItems()" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Semua</option>
                    <template x-for="(label, key) in meta.categories" :key="key">
                        <option :value="key" x-text="label"></option>
                    </template>
                </select>
            </div>
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select x-model="filters.is_active" @change="fetchItems()" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Semua</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">Urutan</label>
                <select x-model="filters.sort" @change="fetchItems()" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="created_at">Terbaru Dulu</option>
                    <option value="sort_order">Urutan Tampilan</option>
                </select>
            </div>
            <div class="flex items-center gap-2 mb-1">
                <button @click="resetFilters()" class="text-xs text-gray-600 hover:text-gray-900 underline">Reset</button>
            </div>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-500">
            <div>
                <span x-text="`Menampilkan ${pagination.from || 0}-${pagination.to || 0} dari ${pagination.total || 0} item`"></span>
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

    <!-- Featured Slots Panel -->
    <div class="bg-white p-6 rounded-lg border shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Galeri Beranda</h2>
                <p class="text-sm text-gray-500">Pilih 4 item galeri untuk ditampilkan di halaman beranda</p>
            </div>
            <div class="flex gap-2">
                <button @click="resetFeaturedSlots()" class="px-3 py-1 text-xs rounded border bg-white hover:bg-gray-50" :disabled="featuredSaving">Reset</button>
                <button @click="saveFeaturedSlots()" class="px-4 py-2 text-xs rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50" :disabled="featuredSaving">
                    <span x-show="!featuredSaving">Simpan</span>
                    <span x-show="featuredSaving" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        <span>Menyimpan...</span>
                    </span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <template x-for="slot in [1,2,3,4]" :key="slot">
                <div class="border rounded-lg p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700" x-text="`Posisi ${slot}`"></span>
                        <span class="text-xs text-gray-500">#<span x-text="slot"></span></span>
                    </div>

                    <div class="space-y-2">
                        <select x-model="featuredSlots[slot]" @change="validateSlotSelection()" class="w-full text-xs rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih item --</option>
                            <template x-for="gallery in availableGalleries" :key="gallery.id">
                                <option :value="gallery.id" x-text="gallery.title" :disabled="isSlotTaken(gallery.id, slot)"></option>
                            </template>
                        </select>

                        <div x-show="featuredSlots[slot]" class="relative">
                            <div class="aspect-video bg-gray-100 rounded border overflow-hidden">
                                <template x-if="getGalleryForSlot(slot)">
                                    <img :src="getGalleryForSlot(slot)?.image_url" :alt="getGalleryForSlot(slot)?.alt_text" class="w-full h-full object-cover" loading="lazy" x-on:error="$el.src = '/assets/images/placeholder.svg'" />
                                </template>
                                <template x-if="!getGalleryForSlot(slot) && featuredSlots[slot]">
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                                        <span>Preview tidak tersedia</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="slotErrors.length > 0" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Kesalahan Validasi</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            <template x-for="error in slotErrors" :key="error">
                                <li x-text="error"></li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <h3 class="text-sm font-medium text-gray-800">Daftar Galeri</h3>
        </div>

        <div class="p-6">
            <div x-show="items.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <template x-for="item in items" :key="item.id">
                    <div class="group border rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200 bg-white">
                        <div class="aspect-video bg-gray-100 overflow-hidden relative">
                            <img :src="item.image_url" :alt="item.alt_text" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy" x-on:error="$el.src = '/assets/images/placeholder.svg'" />
                            <div class="absolute top-2 left-2 flex gap-1">
                                <span class="px-2 py-1 text-xs rounded-full" :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    <span x-text="item.is_active ? 'Aktif' : 'Tidak Aktif'"></span>
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800" x-text="item.category_label"></span>
                            </div>
                            <div class="absolute top-2 right-2 text-xs bg-black bg-opacity-75 text-white px-2 py-1 rounded">
                                #<span x-text="item.sort_order"></span>
                            </div>
                        </div>

                        <div class="p-4 space-y-3">
                            <div>
                                <h4 class="font-medium text-gray-900 line-clamp-1" x-text="item.title"></h4>
                                <p class="text-sm text-gray-500 line-clamp-2 mt-1" x-text="item.description || 'Tidak ada deskripsi'"></p>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="text-xs text-gray-400" x-text="item.created_at_human"></div>
                                <div class="flex items-center gap-1">
                                    <button @click="moveUp(item)" :disabled="item.sort_order <= 0" class="p-1 rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed" title="Pindah ke atas">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                    </button>
                                    <button @click="moveDown(item)" class="p-1 rounded hover:bg-gray-100" title="Pindah ke bawah">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <button @click="openEditModal(item)" class="p-1 rounded hover:bg-primary-50 text-primary-600" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" /></svg>
                                    </button>
                                    <button @click="openDeleteModal(item)" class="p-1 rounded hover:bg-red-50 text-red-600" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m4 0H5" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="!items.length && !loading" class="text-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <p class="font-medium">Belum ada item galeri</p>
                    <p class="text-sm text-gray-500">Klik tombol "Item Baru" untuk menambahkan.</p>
                </div>
            </div>

            <!-- Loading State -->
            <div x-show="loading" class="flex justify-center py-16">
                <svg class="w-8 h-8 animate-spin text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </div>
        </div>

        <!-- Pagination -->
        <div x-show="items.length" class="border-t bg-gray-50 px-6 py-3 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="text-xs text-gray-500" x-text="`Halaman ${pagination.current_page || 1} dari ${pagination.last_page || 1}`"></div>
            <div class="flex items-center gap-1">
                <button @click="changePage(pagination.current_page - 1)" :disabled="!pagination.prev_page_url || loading" class="px-2 py-1 text-xs rounded border bg-white disabled:opacity-50">Prev</button>
                <template x-for="page in pageNumbers()" :key="page">
                    <button @click="changePage(page)" :class="{'bg-primary-600 text-white border-primary-600': page === pagination.current_page}" class="px-3 py-1 text-xs rounded border bg-white hover:bg-gray-100" x-text="page"></button>
                </template>
                <button @click="changePage(pagination.current_page + 1)" :disabled="!pagination.next_page_url || loading" class="px-2 py-1 text-xs rounded border bg-white disabled:opacity-50">Next</button>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showFormModal" x-cloak class="fixed inset-0 z-40 flex items-start md:items-center justify-center overflow-y-auto">
        <div @click="closeFormModal()" class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative bg-white w-full max-w-2xl mx-auto my-10 rounded-lg shadow-lg border flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-5 py-3 border-b bg-gray-50/60">
                <h2 class="font-semibold text-gray-800" x-text="form.id ? 'Edit Item Galeri' : 'Item Galeri Baru'"></h2>
                <button @click="closeFormModal()" class="p-1 rounded hover:bg-gray-100 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form @submit.prevent="submitForm" class="overflow-y-auto px-5 py-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-600">Judul <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.title" required class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="Masukkan judul item..." />
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-600">Kategori <span class="text-red-500">*</span></label>
                        <select x-model="form.category" required class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <option value="">-- Pilih kategori --</option>
                            <template x-for="(label, key) in meta.categories" :key="key">
                                <option :value="key" x-text="label"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-gray-600">Deskripsi</label>
                    <textarea x-model="form.description" rows="3" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="Deskripsi item (opsional)..."></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-gray-600">Alt Text</label>
                    <input type="text" x-model="form.alt_text" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="Alt text untuk gambar (opsional)..." />
                    <p class="text-[11px] text-gray-400">Kosongkan untuk menggunakan judul sebagai alt text.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-600 flex items-center gap-2">
                            Urutan Tampilan
                            <div class="relative group">
                                <svg class="w-4 h-4 text-gray-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-64 p-2 bg-gray-900 text-white text-xs rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                    <div class="mb-1 font-medium">🚀 Otomatis Pintar!</div>
                                    <div>• Kosongkan untuk urutan otomatis</div>
                                    <div>• Isi angka jika ingin posisi tertentu</div>
                                    <div>• Sistem akan cegah duplikasi</div>
                                </div>
                            </div>
                        </label>
                        <input type="number" x-model.number="form.sort_order" min="0" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500" :placeholder="form.id ? 'Biarkan kosong untuk otomatis' : 'Kosong = urutan otomatis'" />
                        <div class="space-y-1">
                            <p class="text-[11px] text-gray-500">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <strong>Otomatis:</strong> Sistem akan atur urutan jika kosong atau konflik
                                </span>
                            </p>
                            <p class="text-[11px] text-gray-400">Angka lebih kecil ditampilkan lebih dulu. Manual: 1, 2, 3... atau biarkan kosong.</p>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-600">Status</label>
                        <div class="flex items-center gap-1 text-sm">
                            <input type="checkbox" x-model="form.is_active" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
                            <span>Aktif</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-medium text-gray-600">Gambar <span x-show="!form.id" class="text-red-500">*</span></label>
                    <div class="space-y-3">
                        <div class="aspect-video w-full bg-gray-50 rounded border flex items-center justify-center overflow-hidden relative" :class="{'animate-pulse': imageUploading}">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover" x-on:error="$el.src = '/assets/images/placeholder.svg'" />
                            </template>
                            <template x-if="!imagePreview">
                                <div class="text-gray-400 flex flex-col items-center text-sm">
                                    <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    <span>Preview gambar</span>
                                </div>
                            </template>
                            <div x-show="imageUploading" class="absolute inset-0 bg-black/30 flex items-center justify-center">
                                <svg class="w-6 h-6 animate-spin text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="file" @change="handleImageChange" accept="image/*" class="text-xs file:mr-3 file:py-1.5 file:px-3 file:border-0 file:text-xs file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 file:rounded-md border border-gray-300 rounded-md" />
                            <button type="button" @click="removeImage()" x-show="imagePreview || form.id" class="text-xs text-red-600 hover:text-red-700">
                                Hapus
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-400">Format: JPG, PNG, GIF, WEBP. Maksimal 5MB.</p>
                    </div>
                </div>
            </form>

            <div class="border-t px-5 py-3 flex flex-col-reverse md:flex-row md:justify-end gap-2">
                <button type="button" @click="closeFormModal()" :disabled="submitting" class="px-4 py-2 text-sm border rounded-md bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
                    Batal
                </button>
                <button @click="submitForm()" :disabled="isFormInvalid() || submitting" 
                    :class="{
                        'bg-primary-600 hover:bg-primary-700 text-white': !isFormInvalid() && !submitting,
                        'bg-gray-300 text-gray-500 cursor-not-allowed': isFormInvalid() || submitting
                    }"
                    class="px-4 py-2 text-sm rounded-md disabled:opacity-50 flex items-center gap-2 transition-all duration-200 min-w-[100px] justify-center">
                    
                    <!-- Loading spinner -->
                    <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    
                    <!-- Success icon for updates -->
                    <svg x-show="!submitting && form.id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    
                    <!-- Plus icon for new items -->  
                    <svg x-show="!submitting && !form.id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    
                    <span x-show="submitting" x-text="form.id ? 'Memperbarui...' : 'Menyimpan...'"></span>
                    <span x-show="!submitting" x-text="form.id ? 'Perbarui Item' : 'Buat Item Baru'"></span>
                </button>
                
                <!-- Validation helper text -->
                <div x-show="isFormInvalid() && !submitting" x-transition class="text-xs text-red-500 mt-1">
                    <div x-show="!form.title">• Judul wajib diisi</div>
                    <div x-show="!form.category">• Kategori wajib dipilih</div>
                    <div x-show="!form.id && !imagePreview">• Gambar wajib dipilih untuk item baru</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
        <div @click="closeDeleteModal()" class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative bg-white w-full max-w-md mx-auto my-10 rounded-lg shadow-lg border">
            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m4 0H5" /></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Konfirmasi Penghapusan</h3>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                <div class="bg-gray-50 p-3 rounded-md mb-4">
                    <p class="text-sm text-gray-700">Yakin ingin menghapus item galeri berikut?</p>
                    <p class="font-medium text-gray-900 mt-1" x-text="selectedItem?.title"></p>
                </div>
                <div class="flex flex-col-reverse md:flex-row md:justify-end gap-2">
                    <button @click="closeDeleteModal()" :disabled="deleting" class="px-4 py-2 text-sm border rounded-md bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50">
                        Batal
                    </button>
                    <button @click="confirmDelete()" :disabled="deleting" class="px-4 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50 flex items-center gap-2">
                        <svg x-show="deleting" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="fixed bottom-4 right-4 z-50 space-y-2">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="bg-white border rounded-lg shadow-lg p-4 max-w-sm flex items-center gap-3 animate-slide-in">
                <div class="flex-shrink-0">
                    <svg x-show="toast.type === 'success'" class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    <svg x-show="toast.type === 'error'" class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    <svg x-show="toast.type === 'info'" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900" x-text="toast.message"></p>
                </div>
                <button @click="dismissToast(toast.id)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </template>
    </div>
</div>
@endsection

@push('styles')
<style>
[x-cloak] { display: none !important; }
.animate-slide-in {
    animation: slideIn 0.3s ease-out forwards;
}
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>
@endpush

@push('scripts')
<script>
// Define gallery data function before Alpine.js initializes
document.addEventListener('alpine:init', () => {
    // Image fallback helper
    function handleImageError(imgElement) {
        if (imgElement.src !== '/assets/images/placeholder.svg') {
            imgElement.src = '/assets/images/placeholder.svg';
        }
    }

    // Make handleImageError globally available
    window.handleImageError = handleImageError;

    // Register the galleryData component with Alpine.js
    Alpine.data('galleryData', () => ({
        loading: false,
        submitting: false,
        deleting: false,
        reordering: false,
        featuredSaving: false,
        showFormModal: false,
        showDeleteModal: false,
        items: [],
        selectedItem: null,
        form: {
            id: null,
            title: '',
            description: '',
            alt_text: '',
            category: '',
            sort_order: 0,
            is_active: true
        },
        imageFile: null,
        imagePreview: null,
        imageUploading: false,
        pagination: { current_page: 1, per_page: 15 },
        meta: { categories: {} },
        filters: { search: '', category: '', is_active: '', sort: 'created_at' },
        toasts: [],

        // Featured slots
        featuredSlots: { '1': null, '2': null, '3': null, '4': null },
        availableGalleries: [],
        slotErrors: [],

        async init() {
            // Try to wait for AdminAPI briefly, but don't block on it
            await this.waitForAdminAPI();

            // Load data regardless of AdminAPI availability (fallback will handle it)
            await this.fetchItems();
            await this.loadFeaturedSlots();
        },
        
        async waitForAdminAPI() {
            return new Promise((resolve) => {
                if (typeof window.AdminAPI !== 'undefined') {
                    resolve();
                } else {
                    let attempts = 0;
                    const maxAttempts = 20; // 1 second max wait

                    const checkInterval = setInterval(() => {
                        attempts++;
                        if (typeof window.AdminAPI !== 'undefined') {
                            clearInterval(checkInterval);
                            resolve();
                        } else if (attempts >= maxAttempts) {
                            clearInterval(checkInterval);
                            resolve();
                        }
                    }, 50);
                }
            });
        },

        pageNumbers() {
            if (!this.pagination.last_page) return [1];
            const total = this.pagination.last_page;
            const current = this.pagination.current_page;
            const delta = 2;
            const pages = [];
            for (let i = Math.max(1, current - delta); i <= Math.min(total, current + delta); i++) pages.push(i);
            if (!pages.includes(1)) pages.unshift(1);
            if (!pages.includes(total)) pages.push(total);
            return [...new Set(pages)];
        },

        changePage(page) {
            if (page < 1 || page === this.pagination.current_page || page > this.pagination.last_page) return;
            this.pagination.current_page = page;
            this.fetchItems();
        },
        changePerPage() {
            this.pagination.current_page = 1;
            this.fetchItems();
        },

        resetFilters() {
            this.filters = { search: '', category: '', is_active: '', sort: 'created_at' };
            this.fetchItems();
        },

        refresh() { this.fetchItems(); },

        debouncedFetch: debounce(function() { this.pagination.current_page = 1; this.fetchItems(); }, 400),

        async fetchItems() {
            try {
                this.loading = true;
                
                // Use direct fetch for consistency (avoid AdminAPI auth issues)
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }
                
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page || 15,
                });
                
                // Add non-empty filters
                Object.entries(this.filters).forEach(([k,v]) => { 
                    if (v !== '' && v !== null) params.append(k, v); 
                });

                const res = await fetch(`/api/admin/content/gallery?${params.toString()}`, {
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Accept': 'application/json' 
                    }
                });
                
                if (!res.ok) {
                    const errorText = await res.text();
                    console.error('Gallery fetch failed:', res.status, errorText);
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }
                
                const response = await res.json();
                
                if (!response.success) {
                    throw new Error(response.message || 'Gagal memuat data galeri');
                }
                
                this.items = response.data;
                this.meta.categories = response.meta?.categories || {};
                this.pagination = response.meta?.pagination || this.pagination;
                
            } catch (error) {
                console.error('Fetch items error:', error);
                
                // Handle authentication errors
                if (!this.handleAuthError(error)) {
                    this.toast(error.message || 'Gagal memuat data galeri', 'error');
                }
            } finally {
                this.loading = false;
            }
        },

        async loadFeaturedSlots() {
            try {
                // Use direct fetch for consistency
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                if (!token) {
                    console.warn('No admin token found for featured slots');
                    return;
                }
                
                const res = await fetch('/api/admin/content/gallery/featured-slots', {
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Accept': 'application/json' 
                    }
                });
                
                if (!res.ok) {
                    console.error('Featured slots load failed:', res.status, res.statusText);
                    this.setDefaultSlots();
                    return;
                }
                
                const response = await res.json();
                
                if (response.success) {
                    // Handle both array and object formats from API
                    let slots = response.data.slots || { '1': null, '2': null, '3': null, '4': null };
                    
                    // Convert array to object if needed and ensure string keys
                    if (Array.isArray(slots)) {
                        const slotsObj = { '1': null, '2': null, '3': null, '4': null };
                        slots.forEach((value, index) => {
                            if (index >= 1 && index <= 4) {
                                slotsObj[String(index)] = value;
                            }
                        });
                        this.featuredSlots = slotsObj;
                    } else {
                        // Ensure string keys
                        const normalizedSlots = { '1': null, '2': null, '3': null, '4': null };
                        for (let i = 1; i <= 4; i++) {
                            const key = String(i);
                            if (slots.hasOwnProperty(key) || slots.hasOwnProperty(i)) {
                                normalizedSlots[key] = slots[key] || slots[i];
                            }
                        }
                        this.featuredSlots = normalizedSlots;
                    }
                    
                    this.availableGalleries = response.data.available_galleries || [];
                } else {
                    this.setDefaultSlots();
                }
                
            } catch (error) {
                console.error('Failed to load featured slots:', error);
                this.setDefaultSlots();
            }
        },
        
        setDefaultSlots() {
            this.featuredSlots = { '1': null, '2': null, '3': null, '4': null };
            this.availableGalleries = [];
        },
        
        getGalleryForSlot(slot) {
            const galleryId = this.featuredSlots[slot];
            if (!galleryId) return null;
            
            const gallery = this.availableGalleries.find(g => String(g.id) === String(galleryId));
            
            // Debug logging
            if (galleryId && !gallery) {
                console.warn(`Gallery not found for slot ${slot}, ID: ${galleryId}`, {
                    availableGalleries: this.availableGalleries.map(g => ({ id: g.id, title: g.title }))
                });
            }
            
            return gallery || null;
        },

        validateSlotSelection() {
            this.slotErrors = [];
            const selectedIds = Object.values(this.featuredSlots).filter(id => id !== null);
            const uniqueIds = [...new Set(selectedIds)];

            if (selectedIds.length !== uniqueIds.length) {
                this.slotErrors.push('Setiap item galeri hanya dapat dipilih untuk satu posisi');
            }
        },

        isSlotTaken(galleryId, currentSlot) {
            return Object.entries(this.featuredSlots).some(([slot, id]) =>
                String(id) === String(galleryId) && String(slot) !== String(currentSlot)
            );
        },

        async saveFeaturedSlots() {
            this.validateSlotSelection();
            if (this.slotErrors.length > 0) return;

            try {
                this.featuredSaving = true;
                
                let response;
                
                // Use direct fetch for featured slots to avoid AdminAPI auth issues
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }
                
                
                // Prepare data for API - convert object keys to individual fields
                const apiData = {
                    '1': this.featuredSlots['1'] || null,
                    '2': this.featuredSlots['2'] || null,  
                    '3': this.featuredSlots['3'] || null,
                    '4': this.featuredSlots['4'] || null
                };
                
                
                const res = await fetch('/api/admin/content/gallery/featured-slots', {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(apiData)
                });
                
                if (!res.ok) {
                    const errorText = await res.text();
                    console.error('Featured slots save failed:', res.status, errorText);
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }
                
                response = await res.json();
                
                if (!response.success) {
                    throw new Error(response.message || 'Gagal menyimpan slot unggulan');
                }
                
                this.toast('Slot unggulan berhasil disimpan');
                
            } catch (error) {
                console.error('Save featured slots error:', error);
                
                // Handle authentication errors
                if (!this.handleAuthError(error)) {
                    this.toast(error.message || 'Gagal menyimpan slot unggulan', 'error');
                }
            } finally {
                this.featuredSaving = false;
            }
        },

        resetFeaturedSlots() {
            this.featuredSlots = { 1: null, 2: null, 3: null, 4: null };
            this.slotErrors = [];
        },

        moveUp(item) {
            if (item.sort_order <= 0) return;
            this.reorderItem(item, item.sort_order - 1);
        },

        moveDown(item) {
            this.reorderItem(item, item.sort_order + 1);
        },

        async reorderItem(item, newOrder) {
            try {
                this.reordering = true;

                // Use direct fetch for consistency (avoid AdminAPI auth issues)
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }
                

                const res = await fetch('/api/admin/content/gallery/reorder', {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: item.id,
                        sort_order: newOrder
                    })
                });
                
                if (!res.ok) {
                    const errorText = await res.text();
                    console.error('Gallery reorder failed:', res.status, errorText);
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }
                
                const response = await res.json();

                if (!response.success) {
                    throw new Error(response.message || 'Gagal mengubah urutan');
                }

                this.toast('Urutan berhasil diubah');
                await this.fetchItems(); // Refresh list
                
            } catch (error) {
                console.error('Reorder error:', error);
                
                // Handle authentication errors
                if (!this.handleAuthError(error)) {
                    this.toast(error.message || 'Gagal mengubah urutan', 'error');
                }
            } finally {
                this.reordering = false;
            }
        },

        openCreateModal() {
            this.form = {
                id: null,
                title: '',
                description: '',
                alt_text: '',
                category: '',
                sort_order: null, // Changed to null to trigger auto-assignment
                is_active: true
            };
            this.imageFile = null;
            this.imagePreview = null;
            this.showFormModal = true;
        },

        openEditModal(item) {
            this.form = JSON.parse(JSON.stringify(item));
            this.imagePreview = item.image_url;
            this.showFormModal = true;
        },

        closeFormModal() {
            if (this.submitting) return;
            this.showFormModal = false;
        },

        openDeleteModal(item) {
            this.selectedItem = item;
            this.showDeleteModal = true;
        },

        closeDeleteModal() {
            if (this.deleting) return;
            this.showDeleteModal = false;
            this.selectedItem = null;
        },

        handleImageChange(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                this.toast('Format gambar tidak valid. Gunakan jpg, png, gif, atau webp.', 'error');
                return;
            }

            if (file.size > 5 * 1024 * 1024) { // 5MB
                this.toast('Ukuran gambar terlalu besar. Maksimal 5MB.', 'error');
                return;
            }

            this.imageFile = file;
            const reader = new FileReader();
            reader.onload = ev => { this.imagePreview = ev.target.result; };
            reader.readAsDataURL(file);
        },

        removeImage() {
            this.imageFile = null;
            this.imagePreview = null;
            if (this.form.id) {
                this.form.remove_image = true;
            }
        },

        isFormInvalid() {
            // Check required fields
            if (!this.form.title || !this.form.category) {
                return true;
            }
            
            // For new items, image is required
            if (!this.form.id && !this.imagePreview) {
                return true;
            }
            
            return false;
        },

        async submitForm() {
            try {
                this.submitting = true;
                const isEdit = !!this.form.id;

                const formData = new FormData();
                formData.append('title', this.form.title || '');
                formData.append('description', this.form.description || '');
                formData.append('alt_text', this.form.alt_text || '');
                formData.append('category', this.form.category || '');
                // Only append sort_order if it has a value, let backend auto-handle if null/empty
                if (this.form.sort_order !== null && this.form.sort_order !== undefined && this.form.sort_order !== '') {
                    formData.append('sort_order', this.form.sort_order);
                }
                formData.append('is_active', this.form.is_active ? '1' : '0');

                if (this.form.remove_image) formData.append('remove_image', '1');
                if (this.imageFile) formData.append('image', this.imageFile);

                // Use direct fetch for consistency with featured slots (avoid AdminAPI auth issues)
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }
                
                
                const url = isEdit ? `/api/admin/content/gallery/${this.form.id}` : '/api/admin/content/gallery';
                
                if (isEdit) {
                    formData.append('_method', 'PUT');
                }
                
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                if (!res.ok) {
                    const errorText = await res.text();
                    console.error('Gallery form submit failed:', res.status, errorText);
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }
                
                const response = await res.json();

                if (!response.success) {
                    throw new Error(response.message || 'Gagal menyimpan item galeri');
                }

                // Check if sort_order was auto-assigned by comparing with form
                const wasAutoAssigned = response.data && response.data.sort_order && 
                    (!this.form.sort_order || this.form.sort_order !== response.data.sort_order);
                
                let message = isEdit ? 'Item galeri berhasil diperbarui' : 'Item galeri berhasil dibuat';
                
                if (wasAutoAssigned) {
                    message += ` (Urutan otomatis: #${response.data.sort_order})`;
                }
                
                this.toast(message, 'success');
                this.showFormModal = false;
                
                // For new items, ensure we're on "Terbaru Dulu" sort to see the new item at the top
                if (!isEdit) {
                    if (this.filters.sort !== 'created_at') {
                        this.filters.sort = 'created_at';
                        this.pagination.current_page = 1;
                    }
                }
                
                // Refresh data to show the new/updated item
                await this.fetchItems();
                await this.loadFeaturedSlots(); // Refresh available galleries
                
            } catch (error) {
                console.error('Submit form error:', error);
                
                // Handle authentication errors
                if (!this.handleAuthError(error)) {
                    this.toast(error.message || 'Gagal menyimpan item galeri', 'error');
                }
            } finally {
                this.submitting = false;
            }
        },

        async confirmDelete() {
            if (!this.selectedItem) return;
            
            try {
                this.deleting = true;
                
                // Use direct fetch for consistency (avoid AdminAPI auth issues)
                const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
                
                if (!token) {
                    throw new Error('Token tidak ditemukan. Silakan login ulang.');
                }
                
                
                const res = await fetch(`/api/admin/content/gallery/${this.selectedItem.id}`, {
                    method: 'DELETE',
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Accept': 'application/json' 
                    }
                });
                
                if (!res.ok) {
                    const errorText = await res.text();
                    console.error('Gallery delete failed:', res.status, errorText);
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }
                
                const response = await res.json();
                
                if (!response.success) {
                    throw new Error(response.message || 'Gagal menghapus item galeri');
                }

                this.toast('Item galeri berhasil dihapus');
                this.showDeleteModal = false;
                
                // Refresh data
                await this.fetchItems();
                await this.loadFeaturedSlots(); // Refresh available galleries
                
            } catch (error) {
                console.error('Delete error:', error);
                
                // Handle authentication errors
                if (!this.handleAuthError(error)) {
                    this.toast(error.message || 'Gagal menghapus item galeri', 'error');
                }
            } finally {
                this.deleting = false;
            }
        },

        toast(message, type = 'info') {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message, type });
            setTimeout(() => this.dismissToast(id), 4500);
        },

        dismissToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },
        
        handleAuthError(error) {
            // Check if this is an authentication error
            const isAuthError = (error.status === 401 || error.status === 403) ||
                (error.message && (
                    error.message.includes('Authentication') || 
                    error.message.includes('Unauthorized') ||
                    error.message.includes('403') ||
                    error.message.includes('401')
                ));
                
            if (isAuthError) {
                console.error('Authentication error detected:', error);
                
                // Clear invalid token
                localStorage.removeItem('admin_token');
                sessionStorage.removeItem('admin_token');
                
                this.toast('Sesi telah berakhir. Silakan login ulang.', 'error');
                
                // Redirect to login after a delay
                setTimeout(() => {
                    window.location.href = '/admin/login';
                }, 2000);
                
                return true; // Indicates auth error was handled
            }
            
            return false; // Not an auth error
        }
    }));

    // Helper function for default form values
    function defaultForm() {
        return {
            id: null,
            title: '',
            description: '',
            alt_text: '',
            category: '',
            sort_order: 0,
            is_active: true
        };
    }

    // Debounce utility function
    function debounce(fn, delay) {
        let t;
        return function() {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, arguments), delay);
        };
    }

    // Make utility functions globally available
    window.defaultForm = defaultForm;
    window.debounce = debounce;
});
</script>
@endpush
