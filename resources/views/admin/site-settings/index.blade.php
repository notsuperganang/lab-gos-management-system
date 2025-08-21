@extends('admin.layouts.app')

@section('title', 'Konfigurasi Website')

@section('content')
<div x-data="siteSettingsManager()" class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="mr-3 h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Konfigurasi Website
                </h1>
                <p class="text-gray-600 mt-1">Kelola pengaturan dan konfigurasi website laboratorium</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Save All Button -->
                <button @click="saveAllSettings()" 
                        :disabled="loading || !hasChanges" 
                        :class="hasChanges ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan Semua'"></span>
                </button>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="mt-4 grid grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="text-sm text-blue-600 font-medium">Total Pengaturan</div>
                <div class="text-2xl font-bold text-blue-900" x-text="Object.keys(settings).length"></div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="text-sm text-green-600 font-medium">Aktif</div>
                <div class="text-2xl font-bold text-green-900" x-text="Object.values(settings).filter(s => s.is_active).length"></div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="text-sm text-yellow-600 font-medium">Perubahan</div>
                <div class="text-2xl font-bold text-yellow-900" x-text="Object.keys(changedSettings).length"></div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="initialLoading" class="flex justify-center items-center py-12">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-4 text-gray-600">Memuat pengaturan...</p>
        </div>
    </div>

    <!-- Settings Content -->
    <div x-show="!initialLoading" class="space-y-6">
        

        <!-- Settings Form -->
        <form @submit.prevent="saveAllSettings()">
            <div class="space-y-6">
                
                <!-- Basic Information Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ collapsed: false }">
                    <div class="p-6 border-b border-gray-200">
                        <button type="button" @click="collapsed = !collapsed" class="w-full flex items-center justify-between text-left">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m5 0v-4a1 1 0 011-1h2a1 1 0 011 1v4M7 7h10M7 11h4m6 0h4"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar</h3>
                            </div>
                            <svg :class="{ 'rotate-180': !collapsed }" class="h-5 w-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                    <div x-show="!collapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Text Fields -->
                            <template x-for="key in getBasicInfoSettings()" :key="key">
                                <div class="space-y-2">
                                    <label :for="key" class="text-sm font-medium text-gray-700" x-text="settings[key]?.title || key"></label>
                                    <input :id="key" 
                                           :type="getInputType(settings[key]?.type)"
                                           x-model="settings[key].content"
                                           @input="markAsChanged(key)"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           :placeholder="'Masukkan ' + (settings[key]?.title || key).toLowerCase()">
                                    <div class="flex items-center mt-2">
                                        <input :id="key + '_active'" 
                                               type="checkbox" 
                                               x-model="settings[key].is_active"
                                               @change="markAsChanged(key)"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label :for="key + '_active'" class="ml-2 text-sm text-gray-600">Aktif</label>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ collapsed: false }">
                    <div class="p-6 border-b border-gray-200">
                        <button type="button" @click="collapsed = !collapsed" class="w-full flex items-center justify-between text-left">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Kontak</h3>
                            </div>
                            <svg :class="{ 'rotate-180': !collapsed }" class="h-5 w-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                    <div x-show="!collapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <template x-for="key in getContactSettings()" :key="key">
                                <div class="space-y-2">
                                    <label :for="key" class="text-sm font-medium text-gray-700" x-text="settings[key]?.title || key"></label>
                                    <textarea x-show="key === 'address'" 
                                             :id="key"
                                             x-model="settings[key].content"
                                             @input="markAsChanged(key)"
                                             rows="4"
                                             class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                             :placeholder="'Masukkan ' + (settings[key]?.title || key).toLowerCase()"></textarea>
                                    <input x-show="key !== 'address'" 
                                           :id="key" 
                                           :type="getInputType(settings[key]?.type)"
                                           x-model="settings[key].content"
                                           @input="markAsChanged(key)"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           :placeholder="'Masukkan ' + (settings[key]?.title || key).toLowerCase()">
                                    <div class="flex items-center mt-2">
                                        <input :id="key + '_active'" 
                                               type="checkbox" 
                                               x-model="settings[key].is_active"
                                               @change="markAsChanged(key)"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label :for="key + '_active'" class="ml-2 text-sm text-gray-600">Aktif</label>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Content Sections -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ collapsed: false }">
                    <div class="p-6 border-b border-gray-200">
                        <button type="button" @click="collapsed = !collapsed" class="w-full flex items-center justify-between text-left">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Konten & Visi Misi</h3>
                            </div>
                            <svg :class="{ 'rotate-180': !collapsed }" class="h-5 w-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                    <div x-show="!collapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div class="p-6 space-y-6">
                            <template x-for="key in getContentSettings()" :key="key">
                                <div class="space-y-2" x-data="{ previewMode: false }">
                                    <div class="flex items-center justify-between">
                                        <label :for="key" class="text-sm font-medium text-gray-700" x-text="settings[key]?.title || key"></label>
                                        <!-- Preview Toggle for Rich Text -->
                                        <div x-show="settings[key]?.type === 'rich_text'" class="flex items-center space-x-2">
                                            <button type="button" 
                                                    @click="previewMode = false"
                                                    :class="!previewMode ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                                    class="px-3 py-1 text-xs rounded-md transition-colors duration-200">
                                                Edit
                                            </button>
                                            <button type="button" 
                                                    @click="previewMode = true"
                                                    :class="previewMode ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                                    class="px-3 py-1 text-xs rounded-md transition-colors duration-200">
                                                Preview
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Edit Mode (Textarea) -->
                                    <textarea x-show="settings[key]?.type !== 'rich_text' || !previewMode"
                                             :id="key"
                                             x-model="settings[key].content"
                                             @input="markAsChanged(key)"
                                             rows="6"
                                             class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                             :placeholder="'Masukkan ' + (settings[key]?.title || key).toLowerCase()"></textarea>
                                    
                                    <!-- Preview Mode (Rendered HTML) -->
                                    <div x-show="settings[key]?.type === 'rich_text' && previewMode"
                                         class="w-full min-h-[150px] px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                        <div class="prose prose-sm max-w-none" x-html="settings[key]?.content || '<p class=&quot;text-gray-500 italic&quot;>No content to preview</p>'"></div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mt-2">
                                        <div class="flex items-center">
                                            <input :id="key + '_active'" 
                                                   type="checkbox" 
                                                   x-model="settings[key].is_active"
                                                   @change="markAsChanged(key)"
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label :for="key + '_active'" class="ml-2 text-sm text-gray-600">Aktif</label>
                                        </div>
                                        <span class="text-xs text-gray-400" x-text="'Tipe: ' + (settings[key]?.type || 'text')"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- JSON Settings Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ collapsed: false }">
                    <div class="p-6 border-b border-gray-200">
                        <button type="button" @click="collapsed = !collapsed" class="w-full flex items-center justify-between text-left">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Pengaturan Kompleks (JSON)</h3>
                            </div>
                            <svg :class="{ 'rotate-180': !collapsed }" class="h-5 w-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                    <div x-show="!collapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div class="p-6 space-y-6">
                            <template x-for="key in getJsonSettings()" :key="key">
                                <div class="space-y-2">
                                    <label :for="key" class="text-sm font-medium text-gray-700" x-text="settings[key]?.title || key"></label>
                                    <div class="border border-gray-300 rounded-md">
                                        <textarea :id="key"
                                                 x-model="settings[key].content"
                                                 @input="markAsChanged(key); validateJson(key)"
                                                 rows="8"
                                                 class="w-full px-3 py-2 border-0 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                                                 :placeholder="'JSON untuk ' + (settings[key]?.title || key).toLowerCase()"></textarea>
                                        <div class="border-t border-gray-200 px-3 py-2 bg-gray-50 rounded-b-md flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <span x-show="jsonErrors[key]" class="text-red-600 text-xs" x-text="jsonErrors[key]"></span>
                                                <span x-show="!jsonErrors[key] && settings[key]?.content" class="text-green-600 text-xs">✓ JSON valid</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <button type="button" @click="formatJson(key)" class="text-xs text-blue-600 hover:text-blue-800">Format</button>
                                                <button type="button" @click="previewJson(key)" class="text-xs text-green-600 hover:text-green-800">Preview</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center mt-2">
                                        <input :id="key + '_active'" 
                                               type="checkbox" 
                                               x-model="settings[key].is_active"
                                               @change="markAsChanged(key)"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label :for="key + '_active'" class="ml-2 text-sm text-gray-600">Aktif</label>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- All Other Settings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ collapsed: true }">
                    <div class="p-6 border-b border-gray-200">
                        <button type="button" @click="collapsed = !collapsed" class="w-full flex items-center justify-between text-left">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Pengaturan Lainnya</h3>
                                <span class="ml-2 text-sm text-gray-500" x-text="'(' + getOtherSettings().length + ' item)'"></span>
                            </div>
                            <svg :class="{ 'rotate-180': !collapsed }" class="h-5 w-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                    <div x-show="!collapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <template x-for="key in getOtherSettings()" :key="key">
                                <div class="space-y-2">
                                    <label :for="key" class="text-sm font-medium text-gray-700" x-text="settings[key]?.title || key"></label>
                                    <textarea x-show="['textarea', 'rich_text'].includes(settings[key]?.type)" 
                                             :id="key"
                                             x-model="settings[key].content"
                                             @input="markAsChanged(key)"
                                             rows="4"
                                             class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                             :placeholder="'Masukkan ' + (settings[key]?.title || key).toLowerCase()"></textarea>
                                    <input x-show="!['textarea', 'rich_text'].includes(settings[key]?.type)" 
                                           :id="key" 
                                           :type="getInputType(settings[key]?.type)"
                                           x-model="settings[key].content"
                                           @input="markAsChanged(key)"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           :placeholder="'Masukkan ' + (settings[key]?.title || key).toLowerCase()">
                                    <div class="flex items-center justify-between mt-2">
                                        <div class="flex items-center">
                                            <input :id="key + '_active'" 
                                                   type="checkbox" 
                                                   x-model="settings[key].is_active"
                                                   @change="markAsChanged(key)"
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label :for="key + '_active'" class="ml-2 text-sm text-gray-600">Aktif</label>
                                        </div>
                                        <span class="text-xs text-gray-400" x-text="'Tipe: ' + (settings[key]?.type || 'text')"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Form Actions -->
            <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span x-show="Object.keys(changedSettings).length > 0">
                            <span x-text="Object.keys(changedSettings).length"></span> pengaturan telah diubah
                        </span>
                        <span x-show="Object.keys(changedSettings).length === 0">
                            Tidak ada perubahan
                        </span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button" 
                                @click="resetChanges()" 
                                :disabled="Object.keys(changedSettings).length === 0"
                                :class="Object.keys(changedSettings).length > 0 ? 'text-red-600 hover:text-red-800' : 'text-gray-400 cursor-not-allowed'"
                                class="text-sm font-medium transition-colors duration-200">
                            Reset Perubahan
                        </button>
                        <button type="submit" 
                                :disabled="loading || !hasChanges" 
                                :class="hasChanges ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="loading ? 'Menyimpan...' : 'Simpan Pengaturan'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JSON Preview Modal (Outside Component) -->
<div x-data="jsonPreviewModal()" 
     x-show="visible" 
     @preview-json.window="showPreview($event.detail)"
     @close-preview.window="visible = false"
     x-transition:enter="transition ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="visible = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="'Preview: ' + (previewKey || '')"></h3>
                    <button type="button" @click="visible = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <pre class="bg-gray-50 p-4 rounded-md text-sm font-mono" x-text="previewContent"></pre>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" @click="visible = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
console.log('Site settings script loading...');
console.log('AdminAPI at script load time:', typeof window.AdminAPI);

function siteSettingsManager() {
    console.log('siteSettingsManager function called, AdminAPI:', typeof window.AdminAPI);
    
    // Check for admin authentication token
    const adminToken = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
    if (!adminToken) {
        console.warn('No admin token found, redirecting to login');
        window.location.href = '/admin/login';
        return {};
    }
    
    return {
        // State
        settings: {},
        originalSettings: {},
        changedSettings: {},
        loading: false,
        initialLoading: true,
        jsonErrors: {},

        // Computed
        get hasChanges() {
            return Object.keys(this.changedSettings).length > 0;
        },

        // Initialize
        async init() {
            console.log('Initializing site settings manager...');
            console.log('AdminAPI availability check:', typeof window.AdminAPI);
            console.log('Window object keys:', Object.keys(window).filter(k => k.includes('Admin') || k.includes('API')));
            
            // Wait for AdminAPI to be available
            if (typeof window.AdminAPI === 'undefined') {
                console.log('AdminAPI not available, waiting...');
                const maxWait = 5000; // 5 seconds max wait
                const startTime = Date.now();
                
                while (typeof window.AdminAPI === 'undefined' && (Date.now() - startTime) < maxWait) {
                    await new Promise(resolve => setTimeout(resolve, 100)); // Wait 100ms
                    console.log('Still waiting for AdminAPI...', Date.now() - startTime, 'ms');
                }
                
                if (typeof window.AdminAPI === 'undefined') {
                    console.error('AdminAPI failed to load after 5 seconds');
                    this.$dispatch('toast', { 
                        message: 'System initialization failed. Please refresh the page.', 
                        type: 'error' 
                    });
                    return;
                }
            }
            
            console.log('AdminAPI available:', typeof window.AdminAPI);
            console.log('AdminAPI methods:', Object.keys(window.AdminAPI));
            
            // Check authentication
            const token = localStorage.getItem('admin_token');
            if (!token) {
                console.warn('No auth token found, redirecting to login...');
                window.location.href = '/admin/login';
                return;
            }
            
            await this.loadSettings();
            this.initialLoading = false;
        },

        // Load settings from API
        async loadSettings() {
            try {
                console.log('loadSettings: AdminAPI check:', typeof window.AdminAPI);
                if (typeof AdminAPI === 'undefined') {
                    throw new Error('AdminAPI is not defined in loadSettings');
                }
                const response = await AdminAPI.getSiteSettings();
                if (response.success && response.data && response.data.settings) {
                    // Process settings to handle different field types properly
                    this.settings = this.processSettingsForDisplay(response.data.settings);
                    this.originalSettings = JSON.parse(JSON.stringify(this.settings));
                    console.log('Settings loaded:', Object.keys(this.settings).length, 'items');
                } else {
                    console.error('Invalid response format:', response);
                    this.$dispatch('toast', { 
                        message: 'Gagal memuat pengaturan: Format response tidak valid', 
                        type: 'error' 
                    });
                }
            } catch (error) {
                console.error('Error loading settings:', error);
                // Check if it's an authentication error
                if (error.status === 401) {
                    console.warn('Authentication failed, redirecting to login...');
                    localStorage.removeItem('admin_token');
                    window.location.href = '/admin/login';
                    return;
                }
                
                this.$dispatch('toast', { 
                    message: 'Gagal memuat pengaturan: ' + (error.message || 'Terjadi kesalahan'), 
                    type: 'error' 
                });
            }
        },

        // Process settings for proper display based on field type
        processSettingsForDisplay(rawSettings) {
            const processedSettings = {};
            
            for (const [key, setting] of Object.entries(rawSettings)) {
                processedSettings[key] = { ...setting };
                
                if (setting.type === 'json') {
                    try {
                        // If content is a string, try to parse it first to validate
                        let jsonContent = setting.content;
                        if (typeof jsonContent === 'string') {
                            // Already a string, try to parse to validate and reformat
                            const parsed = JSON.parse(jsonContent);
                            jsonContent = JSON.stringify(parsed, null, 2);
                        } else if (typeof jsonContent === 'object' && jsonContent !== null) {
                            // It's an object, stringify it for textarea display
                            jsonContent = JSON.stringify(jsonContent, null, 2);
                        }
                        processedSettings[key].content = jsonContent;
                    } catch (error) {
                        console.warn(`Invalid JSON in setting ${key}:`, error);
                        // Keep original content if JSON parsing fails
                        processedSettings[key].content = setting.content;
                    }
                }
            }
            
            return processedSettings;
        },

        // Mark setting as changed
        markAsChanged(key) {
            const original = this.originalSettings[key];
            const current = this.settings[key];
            
            if (JSON.stringify(original) !== JSON.stringify(current)) {
                this.changedSettings[key] = current;
            } else {
                delete this.changedSettings[key];
            }
        },

        // Reset changes
        resetChanges() {
            this.settings = JSON.parse(JSON.stringify(this.originalSettings));
            this.changedSettings = {};
            this.jsonErrors = {};
        },

        // Save all settings
        async saveAllSettings() {
            if (this.loading || !this.hasChanges) return;

            // Check for JSON validation errors
            const hasErrors = Object.keys(this.jsonErrors).some(key => this.jsonErrors[key]);
            if (hasErrors) {
                this.$dispatch('toast', { 
                    message: 'Tidak dapat menyimpan: Ada kesalahan format JSON', 
                    type: 'error' 
                });
                return;
            }

            this.loading = true;
            try {
                // Convert settings to the format expected by the API
                const settingsArray = Object.keys(this.changedSettings).map(key => ({
                    key: key,
                    title: this.settings[key].title,
                    content: this.settings[key].content,
                    type: this.settings[key].type,
                    is_active: this.settings[key].is_active
                }));

                const response = await AdminAPI.updateSiteSettings(settingsArray);
                
                if (response.success) {
                    this.originalSettings = JSON.parse(JSON.stringify(this.settings));
                    this.changedSettings = {};
                    this.$dispatch('toast', { 
                        message: 'Pengaturan berhasil disimpan', 
                        type: 'success' 
                    });
                } else {
                    throw new Error(response.message || 'Gagal menyimpan pengaturan');
                }
            } catch (error) {
                console.error('Error saving settings:', error);
                // Check if it's an authentication error
                if (error.status === 401) {
                    console.warn('Authentication failed, redirecting to login...');
                    localStorage.removeItem('admin_token');
                    window.location.href = '/admin/login';
                    return;
                }
                
                this.$dispatch('toast', { 
                    message: 'Gagal menyimpan pengaturan: ' + (error.message || 'Terjadi kesalahan'), 
                    type: 'error' 
                });
            } finally {
                this.loading = false;
            }
        },

        // Utility functions
        getInputType(type) {
            switch(type) {
                case 'boolean': return 'checkbox';
                case 'number': return 'number';
                case 'email': return 'email';
                case 'url': return 'url';
                default: return 'text';
            }
        },


        // Category functions
        getBasicInfoSettings() {
            return Object.keys(this.settings).filter(key => 
                ['lab_name', 'lab_acronym', 'institution_name', 'department_name', 'faculty_name'].includes(key)
            );
        },

        getContactSettings() {
            return Object.keys(this.settings).filter(key => 
                ['address', 'phone', 'email', 'website'].includes(key)
            );
        },

        getContentSettings() {
            return Object.keys(this.settings).filter(key => 
                ['vision', 'mission', 'about', 'footer_text', 'safety_policy', 'equipment_usage_policy'].includes(key) &&
                this.settings[key]?.type !== 'json'
            );
        },

        getJsonSettings() {
            return Object.keys(this.settings).filter(key => 
                this.settings[key]?.type === 'json'
            );
        },

        getOtherSettings() {
            const categorized = [
                ...this.getBasicInfoSettings(),
                ...this.getContactSettings(), 
                ...this.getContentSettings(),
                ...this.getJsonSettings()
            ];
            return Object.keys(this.settings).filter(key => !categorized.includes(key));
        },

        // JSON handling
        validateJson(key) {
            const content = this.settings[key]?.content;
            if (!content) {
                delete this.jsonErrors[key];
                return;
            }

            try {
                JSON.parse(content);
                delete this.jsonErrors[key];
            } catch (error) {
                this.jsonErrors[key] = 'Format JSON tidak valid: ' + error.message;
            }
        },

        formatJson(key) {
            const content = this.settings[key]?.content;
            if (!content) return;

            try {
                const parsed = JSON.parse(content);
                this.settings[key].content = JSON.stringify(parsed, null, 2);
                this.markAsChanged(key);
                delete this.jsonErrors[key];
            } catch (error) {
                this.jsonErrors[key] = 'Tidak dapat memformat: ' + error.message;
            }
        },

        previewJson(key) {
            const content = this.settings[key]?.content;
            if (!content) return;

            try {
                const parsed = JSON.parse(content);
                const previewData = {
                    content: JSON.stringify(parsed, null, 2),
                    key: this.settings[key]?.title || key
                };
                this.$dispatch('preview-json', previewData);
            } catch (error) {
                this.$dispatch('toast', { 
                    message: 'Tidak dapat menampilkan preview: ' + error.message, 
                    type: 'error' 
                });
            }
        }
    };
}

// JSON Preview Modal Component
function jsonPreviewModal() {
    return {
        visible: false,
        previewContent: '',
        previewKey: '',
        
        showPreview(data) {
            this.previewContent = data.content;
            this.previewKey = data.key;
            this.visible = true;
        }
    };
}

console.log('JSON preview modal component registered');
</script>
@endpush