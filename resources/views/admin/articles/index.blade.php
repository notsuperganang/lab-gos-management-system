@extends('admin.layouts.app')

@section('title', 'Kelola Artikel')

@section('content')
<div x-data="articlesData()" x-init="init()" class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Artikel</h1>
            <p class="text-sm text-gray-500">Buat, kelola, dan publikasikan artikel laboratorium.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-md shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                <span>Artikel Baru</span>
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
                    <input type="text" x-model.debounce.400ms="filters.search" @input="debouncedFetch()" placeholder="Cari judul / konten..." class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500" />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    </div>
                </div>
            </div>
            <div class="w-48">
                <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                <select x-model="filters.category" @change="fetchArticles()" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Semua</option>
                    <template x-for="(label, key) in meta.categories" :key="key">
                        <option :value="key" x-text="label"></option>
                    </template>
                </select>
            </div>
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select x-model="filters.is_published" @change="fetchArticles()" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Semua</option>
                    <option value="1">Dipublikasi</option>
                    <option value="0">Draft</option>
                </select>
            </div>
            <div class="w-48">
                <label class="block text-xs font-medium text-gray-500 mb-1">Penulis</label>
                <input type="text" x-model.debounce.400ms="filters.author" @input="debouncedFetch()" placeholder="Nama penulis" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500" />
            </div>
            <div class="flex items-center gap-2 mb-1">
                <button @click="resetFilters()" class="text-xs text-gray-600 hover:text-gray-900 underline">Reset</button>
            </div>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-500">
            <div>
                <span x-text="`Menampilkan ${pagination.from || 0}-${pagination.to || 0} dari ${pagination.total || 0} artikel`"></span>
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

    <!-- Articles Table -->
    <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-[11px] uppercase tracking-wider text-gray-600">
                        <th class="px-4 py-3 font-semibold">Judul</th>
                        <th class="px-4 py-3 font-semibold">Kategori</th>
                        <th class="px-4 py-3 font-semibold">Tags</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Publikasi</th>
                        <th class="px-4 py-3 font-semibold">Dilihat</th>
                        <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" x-show="articles.length" x-cloak>
                    <template x-for="article in articles" :key="article.id">
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-4 py-3 align-top">
                                <div class="flex items-start gap-3">
                                    <div class="w-14 h-14 flex-shrink-0 rounded-md bg-gray-100 overflow-hidden border">
                                        <template x-if="article.has_featured_image">
                                            <img :src="storageUrl(article.featured_image_path)" alt="" class="w-full h-full object-cover" loading="lazy" />
                                        </template>
                                        <template x-if="!article.has_featured_image">
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16c1.1 0 2 .9 2 2v8.5M4 6c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h12m0 0c0 1.1.9 2 2 2m-2-2a2 2 0 002-2" /></svg>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="space-y-1 min-w-[240px]">
                                        <div class="font-medium text-gray-900 line-clamp-2" x-text="article.title"></div>
                                        <div class="text-xs text-gray-500" x-text="`oleh ${article.author_name}`"></div>
                                        <div class="text-xs text-gray-400" x-text="article.created_at_human"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <x-articles.category-badge :category="'placeholder'" :label="'placeholder'" x-bind:class="''" x-show="false" />
                                <span class="inline-block px-2 py-1 rounded text-[11px] font-medium" :class="categoryBadgeClass(article.category)">
                                    <span class="capitalize" x-text="article.category"></span>
                                </span>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex flex-wrap gap-1 max-w-[160px]">
                                    <template x-for="tag in article.tags.slice(0,3)" :key="tag">
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-[11px] font-medium" x-text="tag"></span>
                                    </template>
                                    <span x-show="article.tag_count > 3" class="text-[11px] text-gray-500">+<span x-text="article.tag_count - 3"></span></span>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium" :class="article.is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'">
                                    <span x-text="article.is_published ? 'Dipublikasi' : 'Draft'"></span>
                                </span>
                            </td>
                            <td class="px-4 py-3 align-top text-xs text-gray-600 space-y-1">
                                <template x-if="article.is_published">
                                    <div>
                                        <div x-text="article.published_at_formatted || '-'" class="font-medium"></div>
                                        <div class="text-[11px] text-gray-400" x-text="article.publisher?.name || article.author_name"></div>
                                    </div>
                                </template>
                                <template x-if="!article.is_published">
                                    <span class="text-[11px] italic text-gray-400">Belum dipublikasikan</span>
                                </template>
                            </td>
                            <td class="px-4 py-3 align-top text-xs text-gray-600 font-medium" x-text="article.views_count"></td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex items-center justify-end gap-1">
                                    <button @click="openEditModal(article)" class="p-1.5 rounded hover:bg-primary-50 text-primary-600" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" /></svg>
                                    </button>
                                    <button @click="openDeleteModal(article)" class="p-1.5 rounded hover:bg-red-50 text-red-600" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m4 0H5" /></svg>
                                    </button>
                                    <button @click="openPreviewModal(article)" class="p-1.5 rounded hover:bg-gray-100 text-gray-600" title="Preview">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M4 6h8m-8 6h8m-8 6h8" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tbody x-show="!articles.length && !loading" class="text-sm" x-cloak>
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" /></svg>
                                <p class="font-medium">Belum ada artikel</p>
                                <p class="text-xs">Klik tombol "Artikel Baru" untuk menambahkan.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="border-t bg-gray-50 px-4 py-3 flex flex-col md:flex-row md:items-center justify-between gap-3">
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
        <div class="relative bg-white w-full max-w-4xl mx-auto my-10 rounded-lg shadow-lg border flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-5 py-3 border-b bg-gray-50/60">
                <h2 class="font-semibold text-gray-800" x-text="form.id ? 'Edit Artikel' : 'Artikel Baru'"></h2>
                <button @click="closeFormModal()" class="p-1 rounded hover:bg-gray-100 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form @submit.prevent="submitForm" class="overflow-y-auto px-5 divide-y divide-gray-100">
                <div class="py-5 grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-5">
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Judul <span class="text-red-500">*</span></label>
                            <input type="text" x-model="form.title" @input="syncSlugPreview()" required class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="Masukkan judul artikel..." />
                            <p class="text-[11px] text-gray-500">Slug: <span class="font-mono" x-text="slugPreview"></span></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Ringkasan</label>
                            <textarea x-model="form.excerpt" rows="2" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Ringkasan singkat artikel (opsional)..."></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Konten <span class="text-red-500">*</span></label>
                            <textarea x-model="form.content" rows="10" required class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 font-mono text-xs" placeholder="Tulis konten artikel menggunakan Markdown atau HTML..."></textarea>
                            <p class="text-[11px] text-gray-400">Gunakan format markdown sederhana atau HTML ringan.</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Tags</label>
                            <div class="flex flex-wrap items-center gap-2 p-2 border rounded-md min-h-[46px]" @click="$refs.tagInput.focus()">
                                <template x-for="(tag, idx) in form.tags" :key="tag + idx">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-100 text-gray-700 text-[11px]">
                                        <span x-text="tag"></span>
                                        <button type="button" @click="removeTag(idx)" class="hover:text-red-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </span>
                                </template>
                                <input x-ref="tagInput" x-model="tagInput" @keydown.enter.prevent="addTag()" @keydown.,.prevent="addTag()" @keydown.space.prevent="addTag()" @blur="addTag()" class="flex-1 min-w-[120px] text-xs focus:outline-none" placeholder="Tambah tag" />
                            </div>
                            <p class="text-[11px] text-gray-400">Tekan Enter atau koma untuk menambah tag. Maks 10 tag.</p>
                        </div>
                    </div>
                    <div class="space-y-5">
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Kategori <span class="text-red-500">*</span></label>
                            <select x-model="form.category" required class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm">
                                <option value="">-- pilih --</option>
                                <template x-for="(label, key) in meta.categories" :key="key">
                                    <option :value="key" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Gambar Utama</label>
                            <div class="space-y-2">
                                <div class="aspect-video w-full bg-gray-50 rounded border flex items-center justify-center overflow-hidden relative" :class="{'animate-pulse': imageUploading}">
                                    <template x-if="featuredImagePreview">
                                        <img :src="featuredImagePreview" alt="Preview" class="w-full h-full object-cover" />
                                    </template>
                                    <template x-if="!featuredImagePreview">
                                        <div class="text-gray-400 flex flex-col items-center text-xs">
                                            <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16c1.1 0 2 .9 2 2v8.5M4 6c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h12m0 0c0 1.1.9 2 2 2m-2-2a2 2 0 002-2" /></svg>
                                            <span class="text-[11px]">Belum ada gambar</span>
                                        </div>
                                    </template>
                                    <button type="button" @click="$refs.featuredImageInput.click()" class="absolute inset-0 opacity-0 focus:opacity-100 focus:outline-none" aria-label="Upload gambar"></button>
                                </div>
                                <input type="file" x-ref="featuredImageInput" class="hidden" accept="image/*" @change="handleFeaturedImageChange" />
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="$refs.featuredImageInput.click()" class="px-2 py-1 text-xs rounded border bg-white hover:bg-gray-50">Pilih Gambar</button>
                                    <button type="button" x-show="featuredImagePreview" @click="removeFeaturedImage()" class="px-2 py-1 text-xs rounded border bg-white hover:bg-red-50 text-red-600">Hapus</button>
                                </div>
                                <p class="text-[11px] text-gray-400">Format: jpg,png,gif,webp. Maks 5MB.</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Status</label>
                            <div class="flex items-center gap-3 text-xs">
                                <label class="inline-flex items-center gap-1">
                                    <input type="checkbox" x-model="form.is_published" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
                                    <span>Dipublikasi</span>
                                </label>
                                <label class="inline-flex items-center gap-1">
                                    <input type="checkbox" x-model="form.is_featured" :disabled="alreadyFeatured && !form.id" @change="handleFeaturedToggle" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 disabled:opacity-50" />
                                    <span>Unggulan</span>
                                </label>
                            </div>
                            <p class="text-[11px] text-gray-400" x-show="alreadyFeatured && !form.id">Sudah ada artikel unggulan aktif.</p>
                            <p class="text-[11px] text-emerald-600" x-show="form.is_featured">Artikel akan otomatis dipublikasikan.</p>
                        </div>
                        <div class="space-y-1" x-show="form.is_published">
                            <label class="text-xs font-medium text-gray-600">Tanggal Publikasi</label>
                            <input type="datetime-local" x-model="form.published_at" class="w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm" />
                            <p class="text-[11px] text-gray-400">Kosongkan untuk publikasi langsung saat disimpan.</p>
                        </div>
                        <div class="space-y-1" x-show="form.id">
                            <label class="text-xs font-medium text-gray-600">Statistik</label>
                            <div class="text-[11px] text-gray-500 space-y-0.5">
                                <p><span class="font-medium">Dibuat:</span> <span x-text="form.created_at_formatted"></span></p>
                                <p><span class="font-medium">Diupdate:</span> <span x-text="form.updated_at_formatted"></span></p>
                                <p><span class="font-medium">Views:</span> <span x-text="form.views_count"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-0 py-4 flex items-center justify-between gap-3 bg-gray-50/60 sticky bottom-0">
                    <div class="text-xs text-gray-500" x-text="form.id ? 'Perubahan akan disimpan.' : 'Artikel baru akan dibuat.'"></div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="closeFormModal()" class="px-3 py-2 text-xs rounded border bg-white hover:bg-gray-50">Batal</button>
                        <button type="submit" :disabled="submitting" class="px-4 py-2 text-xs rounded bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50 inline-flex items-center gap-2">
                            <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            <span x-text="form.id ? 'Simpan Perubahan' : 'Buat Artikel'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-40 flex items-center justify-center">
        <div @click="closeDeleteModal()" class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative bg-white w-full max-w-md mx-auto rounded-lg shadow-lg border">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h2 class="font-semibold text-gray-800 text-sm">Hapus Artikel</h2>
                <button @click="closeDeleteModal()" class="p-1 rounded hover:bg-gray-100 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-5 space-y-4 text-sm">
                <p>Yakin ingin menghapus artikel berikut?</p>
                <div class="p-3 rounded border bg-gray-50">
                    <p class="font-medium" x-text="selectedArticle?.title"></p>
                    <p class="text-xs text-gray-500" x-text="selectedArticle?.author_name"></p>
                </div>
                <p class="text-xs text-red-600">Tindakan ini tidak bisa dibatalkan.</p>
            </div>
            <div class="px-5 py-3 bg-gray-50 border-t flex items-center justify-end gap-2">
                <button @click="closeDeleteModal()" class="px-3 py-2 text-xs rounded border bg-white hover:bg-gray-50">Batal</button>
                <button @click="confirmDelete()" :disabled="deleting" class="px-4 py-2 text-xs rounded bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 inline-flex items-center gap-2">
                    <svg x-show="deleting" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    <span>Hapus</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div x-show="showPreviewModal" x-cloak class="fixed inset-0 z-40 flex items-start md:items-center justify-center overflow-y-auto">
        <div @click="closePreviewModal()" class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative bg-white w-full max-w-3xl mx-auto my-10 rounded-lg shadow-lg border flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-5 py-3 border-b bg-gray-50/60">
                <h2 class="font-semibold text-gray-800 text-sm">Preview Artikel</h2>
                <button @click="closePreviewModal()" class="p-1 rounded hover:bg-gray-100 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6 space-y-6 text-sm">
                <div class="space-y-3">
                    <h3 class="text-xl font-semibold" x-text="selectedArticle?.title"></h3>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                        <span x-text="selectedArticle?.author_name"></span>
                        <span x-text="selectedArticle?.published_at_formatted || 'Draft'" class="inline-flex items-center gap-1"></span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100" x-text="selectedArticle?.category"></span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100" x-text="`${selectedArticle?.reading_time || 1} min read`"></span>
                    </div>
                </div>
                <template x-if="selectedArticle?.has_featured_image">
                    <div>
                        <img :src="storageUrl(selectedArticle.featured_image_path)" alt="" class="w-full rounded-md border" />
                    </div>
                </template>
                <div class="prose max-w-none prose-sm" x-html="selectedArticle?.content"></div>
            </div>
            <div class="px-5 py-3 bg-gray-50 border-t flex items-center justify-end">
                <button @click="closePreviewModal()" class="px-3 py-2 text-xs rounded border bg-white hover:bg-gray-50">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="fixed top-4 right-4 z-50 space-y-2" x-cloak>
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" x-transition class="px-4 py-2 rounded-md shadow border text-xs flex items-center gap-2" :class="toast.type === 'error' ? 'bg-red-600 text-white border-red-700' : 'bg-gray-900 text-white border-gray-800'">
                <span x-text="toast.message"></span>
                <button @click="dismissToast(toast.id)" class="text-white/70 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
function articlesData() {
    return {
        loading: false,
        submitting: false,
        deleting: false,
        showFormModal: false,
        showDeleteModal: false,
        showPreviewModal: false,
        articles: [],
        selectedArticle: null,
        form: defaultForm(),
        tagInput: '',
        featuredImageFile: null,
        featuredImagePreview: null,
        imageUploading: false,
        slugPreview: '',
        pagination: { current_page: 1, per_page: 15 },
    meta: { categories: {} },
    alreadyFeatured: false,
        filters: { search: '', category: '', is_published: '', author: '' },
        toasts: [],
        token: localStorage.getItem('admin_token'),
        init() { this.fetchArticles(); },
        storageUrl(path) { return path ? `/storage/${path}` : ''; },
        categoryBadgeClass(cat) {
            const map = { research: 'bg-blue-100 text-blue-700', news: 'bg-green-100 text-green-700', announcement: 'bg-purple-100 text-purple-700', publication: 'bg-orange-100 text-orange-700' };
            return map[cat] || 'bg-gray-100 text-gray-700';
        },
        pageNumbers() {
            if (!this.pagination.last_page) return [1];
            const total = this.pagination.last_page; const current = this.pagination.current_page; const delta = 2; const pages = [];
            for (let i = Math.max(1, current - delta); i <= Math.min(total, current + delta); i++) pages.push(i);
            if (!pages.includes(1)) pages.unshift(1);
            if (!pages.includes(total)) pages.push(total);
            return [...new Set(pages)];
        },
        changePage(page) { if (page < 1 || page === this.pagination.current_page || page > this.pagination.last_page) return; this.pagination.current_page = page; this.fetchArticles(); },
        changePerPage() { this.pagination.current_page = 1; this.fetchArticles(); },
        resetFilters() { this.filters = { search: '', category: '', is_published: '', author: '' }; this.fetchArticles(); },
        refresh() { this.fetchArticles(); },
        debouncedFetch: debounce(function() { this.pagination.current_page = 1; this.fetchArticles(); }, 400),
        fetchArticles() {
            if (!this.token) { this.toast('Token admin tidak ditemukan', 'error'); return; }
            this.loading = true;
            const params = new URLSearchParams({
                page: this.pagination.current_page,
                per_page: this.pagination.per_page || 15,
            });
            Object.entries(this.filters).forEach(([k,v]) => { if (v !== '' && v !== null) params.append(k, v); });
            fetch(`/api/admin/content/articles?${params.toString()}`, { headers: { 'Authorization': `Bearer ${this.token}` } })
                .then(r => r.json())
                .then(json => {
                    if (!json.success) throw new Error(json.message || 'Gagal memuat artikel');
                    this.articles = json.data;
                    this.meta.categories = json.meta?.categories || {};
                    this.pagination = json.pagination || this.pagination;
                    this.alreadyFeatured = this.articles.some(a => a.is_featured);
                })
                .catch(e => this.toast(e.message, 'error'))
                .finally(() => this.loading = false);
        },
        handleFeaturedToggle() {
            if (this.form.is_featured && !this.form.is_published) {
                this.form.is_published = true;
                if (!this.form.published_at) {
                    const now = new Date();
                    this.form.published_at = now.toISOString().slice(0,16);
                }
            }
        },
    openCreateModal() { this.form = defaultForm(); this.slugPreview=''; this.featuredImageFile=null; this.featuredImagePreview=null; this.showFormModal = true; if (this.alreadyFeatured) { this.form.is_featured=false; } },
        openEditModal(article) { this.form = JSON.parse(JSON.stringify(article)); this.slugPreview=article.slug; this.showFormModal = true; this.featuredImagePreview = article.has_featured_image ? this.storageUrl(article.featured_image_path) : null; },
        closeFormModal() { if (this.submitting) return; this.showFormModal=false; },
        openDeleteModal(article) { this.selectedArticle = article; this.showDeleteModal = true; },
        closeDeleteModal() { if (this.deleting) return; this.showDeleteModal=false; this.selectedArticle=null; },
        openPreviewModal(article) { this.selectedArticle = article; this.showPreviewModal = true; },
        closePreviewModal() { this.showPreviewModal=false; this.selectedArticle=null; },
        syncSlugPreview() { if (!this.form.id) { this.slugPreview = slugify(this.form.title || ''); } },
        addTag() { const val = (this.tagInput || '').trim(); if (!val) return; if (this.form.tags.length >= 10) { this.toast('Maksimal 10 tag', 'error'); this.tagInput=''; return; } if (!this.form.tags.includes(val)) this.form.tags.push(val); this.tagInput=''; },
        removeTag(i) { this.form.tags.splice(i,1); },
        handleFeaturedImageChange(e) { const file = e.target.files[0]; if (!file) return; this.featuredImageFile = file; const reader = new FileReader(); reader.onload = ev => { this.featuredImagePreview = ev.target.result; }; reader.readAsDataURL(file); },
        removeFeaturedImage() { this.featuredImageFile=null; this.featuredImagePreview=null; if (this.form.id) { this.form.remove_featured_image = true; } },
        submitForm() {
            if (!this.token) { this.toast('Token admin tidak ditemukan', 'error'); return; }
            this.submitting = true;
            const isEdit = !!this.form.id;
            const url = isEdit ? `/api/admin/content/articles/${this.form.id}` : '/api/admin/content/articles';
            const method = isEdit ? 'POST' : 'POST'; // Using POST with method override for PUT when editing if needed
            const formData = new FormData();
            formData.append('title', this.form.title || '');
            formData.append('excerpt', this.form.excerpt || '');
            formData.append('content', this.form.content || '');
            formData.append('category', this.form.category || '');
            this.form.tags.forEach((t,i)=> formData.append(`tags[${i}]`, t));
            formData.append('is_published', this.form.is_published ? '1':'0');
            formData.append('is_featured', this.form.is_featured ? '1':'0');
            if (this.form.published_at) formData.append('published_at', this.form.published_at);
            if (this.form.remove_featured_image) formData.append('remove_featured_image', '1');
            if (this.featuredImageFile) formData.append('featured_image', this.featuredImageFile);
            if (isEdit) formData.append('_method','PUT');
            fetch(url, { method, headers: { 'Authorization': `Bearer ${this.token}` }, body: formData })
                .then(r=>r.json())
                .then(json=> {
                    if (!json.success) throw new Error(json.message || 'Gagal menyimpan artikel');
                    this.toast(isEdit ? 'Artikel berhasil diperbarui' : 'Artikel berhasil dibuat');
                    this.showFormModal = false;
                    this.fetchArticles();
                })
                .catch(e=> this.toast(e.message,'error'))
                .finally(()=> this.submitting=false);
        },
        confirmDelete() {
            if (!this.selectedArticle) return; this.deleting=true;
            fetch(`/api/admin/content/articles/${this.selectedArticle.id}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${this.token}` }})
                .then(r=>r.json())
                .then(json=> { if (!json.success) throw new Error(json.message || 'Gagal menghapus artikel'); this.toast('Artikel berhasil dihapus'); this.showDeleteModal=false; this.fetchArticles(); })
                .catch(e=> this.toast(e.message,'error'))
                .finally(()=> this.deleting=false);
        },
        toast(message, type='info') { const id=Date.now()+Math.random(); this.toasts.push({id,message,type}); setTimeout(()=> this.dismissToast(id), 4500); },
        dismissToast(id) { this.toasts = this.toasts.filter(t=>t.id!==id); }
    };
}
function defaultForm() { return { id:null,title:'',excerpt:'',content:'',category:'',tags:[],is_published:false,is_featured:false,published_at:'',views_count:0 }; }
function slugify(str){ return (str||'').toString().toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu,'').replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)+/g,'').substring(0,80); }
function debounce(fn,delay){ let t; return function(){ clearTimeout(t); t=setTimeout(()=>fn.apply(this,arguments),delay); }; }
</script>
@endpush
