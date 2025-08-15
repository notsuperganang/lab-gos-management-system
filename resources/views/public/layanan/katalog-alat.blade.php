<x-public.layouts.main>
    {{-- Mendefinisikan judul halaman untuk layout --}}
    <x-slot:title>
        Katalog Peralatan - Lab GOS USK
    </x-slot:title>

    <!-- Hero Section -->
    <section class="relative h-96 flex items-center justify-center">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
             style="background-image: url('/assets/images/hero-bg.jpeg');">
            <div class="absolute inset-0 bg-black bg-opacity-60"></div>
        </div>
        
        <!-- Content -->
        <div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <div x-data="{ animated: false }" 
                 x-scroll-animate.once="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                 class="transition-all duration-1200 ease-out">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
                    <span class="relative">
                        🔬 Katalog 
                        <span class="text-secondary">Peralatan</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Pilih peralatan laboratorium yang Anda butuhkan untuk penelitian
                </p>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-2 inline-block">
                    <p class="text-white flex items-center justify-center">
                        <i class="fas fa-tools mr-2 text-secondary"></i>
                        Laboratorium Gelombang, Optik & Spektroskopi
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tata Cara Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }" 
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="text-center mb-12 transition-all duration-1000 ease-out">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">
                    <span class="relative inline-block">
                        📋 Tata Cara Peminjaman
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                    </span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Ikuti langkah-langkah berikut untuk meminjam peralatan laboratorium
                </p>
            </div>
            
            <!-- Placeholder untuk diagram -->
            <div x-data="{ animated: false }" 
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
                 class="bg-white rounded-2xl shadow-lg p-8 transition-all duration-1000 ease-out">
                <div class="aspect-video bg-gray-100 rounded-xl flex items-center justify-center border-2 border-dashed border-gray-300">
                    <div class="text-center">
                        <i class="fas fa-image text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500 font-medium">Diagram Tata Cara Peminjaman</p>
                        <p class="text-gray-400 text-sm mt-2">Akan diisi dengan diagram horizontal dari Canva</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Filter & Equipment Section -->
            <div x-data="{
                // Modal state
                showDetailModal: false,
                
                // Loading state
                loading: true,
                error: null,
                
                // Filter states
                searchQuery: new URLSearchParams(window.location.search).get('q') || '',
                selectedCategory: new URLSearchParams(window.location.search).get('category_id') || 'semua',
                selectedStatus: 'semua',
                categories: [],
                statusOptions: ['semua', 'tersedia', 'tidak-tersedia'],
                
                // Pagination
                currentPage: parseInt(new URLSearchParams(window.location.search).get('page')) || 1,
                totalPages: 1,
                
                // Selection states
                selectedItems: [],
                
                // Equipment data
                equipments: [],
                
                // Initialization
                async init() {
                    // Wait for LabGOS to be available
                    let attempts = 0;
                    while (!window.LabGOS && attempts < 10) {
                        await new Promise(resolve => setTimeout(resolve, 100));
                        attempts++;
                    }
                    
                    if (!window.LabGOS) {
                        console.error('LabGOS API client not available');
                        return;
                    }
                    
                    await this.loadCategories();
                    await this.loadEquipments();
                },
                
                // Load categories from API
                async loadCategories() {
                    try {
                        const response = await window.LabGOS.getCategories();
                        if (response && response.success && response.data) {
                            this.categories = [
                                { id: 'semua', name: 'Semua', color_code: null },
                                ...response.data
                            ];
                        } else {
                            console.warn('Invalid categories response:', response);
                            this.categories = [{ id: 'semua', name: 'Semua', color_code: null }];
                        }
                    } catch (error) {
                        console.error('Failed to load categories:', error);
                        this.categories = [{ id: 'semua', name: 'Semua', color_code: null }];
                    }
                },
                
                // Load equipments from API
                async loadEquipments(updateUrl = false) {
                    try {
                        this.loading = true;
                        this.error = null;
                        
                        const params = {
                            page: this.currentPage
                        };
                        
                        if (this.selectedCategory !== 'semua') {
                            params.category_id = this.selectedCategory;
                        }
                        
                        if (this.searchQuery.trim()) {
                            params.search = this.searchQuery.trim();
                        }
                        
                        if (this.selectedStatus === 'tersedia') {
                            params.available_only = true;
                        } else if (this.selectedStatus === 'tidak-tersedia') {
                            params.available_only = false;
                        }
                        
                        const response = await window.LabGOS.getEquipment(params);
                        this.equipments = response.data;
                        this.totalPages = response.meta.pagination?.last_page || 1;
                        
                        if (updateUrl) {
                            this.updateURL();
                        }
                    } catch (error) {
                        console.error('Failed to load equipments:', error);
                        this.error = error.response?.data?.message || 'Gagal memuat data peralatan';
                        this.equipments = [];
                    } finally {
                        this.loading = false;
                    }
                },
                
                // Update URL with current filters
                updateURL() {
                    const params = new URLSearchParams();
                    
                    if (this.selectedCategory !== 'semua') {
                        params.set('category_id', this.selectedCategory);
                    }
                    
                    if (this.searchQuery.trim()) {
                        params.set('q', this.searchQuery.trim());
                    }
                    
                    if (this.currentPage > 1) {
                        params.set('page', this.currentPage);
                    }
                    
                    const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                    window.history.pushState({}, '', newUrl);
                },
                
                // Filter change handlers
                async onCategoryChange() {
                    this.currentPage = 1;
                    await this.loadEquipments(true);
                },
                
                async onSearchChange() {
                    this.currentPage = 1;
                    await this.loadEquipments(true);
                },
                
                async onStatusChange() {
                    this.currentPage = 1;
                    await this.loadEquipments(true);
                },
                
                // Pagination
                async goToPage(page) {
                    if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
                        this.currentPage = page;
                        await this.loadEquipments(true);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },
                
                // Equipment status helpers
                getEquipmentStatus(equipment) {
                    if (equipment.available_quantity > 0) {
                        return 'tersedia';
                    }
                    return equipment.condition_status === 'maintenance' ? 'maintenance' : 'dipinjam';
                },
                
                get filteredEquipments() {
                    return this.equipments;
                },
                
                addToSelection(equipment) {
                    const available = equipment.available_quantity || 0;
                    if (!this.isSelected(equipment.id) && available > 0) {
                        this.selectedItems.push({
                            id: equipment.id,
                            name: equipment.name,
                            category: equipment.category?.name || 'Tanpa Kategori',
                            available: available,
                            total: equipment.total_quantity || available,
                            specs: this.getKeySpecs(equipment),
                            quantity: 1
                        });
                    }
                },
                
                removeFromSelection(equipmentId) {
                    this.selectedItems = this.selectedItems.filter(item => item.id !== equipmentId);
                },
                
                isSelected(equipmentId) {
                    return this.selectedItems.some(item => item.id === equipmentId);
                },
                
                getStatusColor(status) {
                    const colors = {
                        'tersedia': 'text-green-600 bg-green-100',
                        'dipinjam': 'text-orange-600 bg-orange-100', 
                        'maintenance': 'text-red-600 bg-red-100',
                        'tidak-tersedia': 'text-red-600 bg-red-100'
                    };
                    return colors[status] || 'text-gray-600 bg-gray-100';
                },
                
                getCategoryBadgeStyle(category) {
                    if (!category || !category.color_code) {
                        return 'background-color: #f3f4f6; color: #374151;';
                    }
                    // Use inline style to avoid Tailwind conflicts
                    return `background-color: ${category.color_code}; color: #ffffff;`;
                },
                
                getKeySpecs(equipment) {
                    if (!equipment.specifications || typeof equipment.specifications !== 'object') {
                        return 'Tidak ada spesifikasi';
                    }
                    
                    const specs = equipment.specifications;
                    const specEntries = Object.entries(specs).filter(([key, value]) => 
                        value !== null && value !== undefined && value !== ''
                    );
                    
                    if (specEntries.length === 0) {
                        return 'Tidak ada spesifikasi';
                    }
                    
                    // Tier 1: Pattern-based priority detection
                    const prioritySpecs = this.getPrioritySpecs(specEntries);
                    if (prioritySpecs.length > 0) {
                        return prioritySpecs.slice(0, 2).map(([key, value]) => 
                            `${this.formatSpecKey(key)}: ${value}`
                        ).join(' • ');
                    }
                    
                    // Tier 2: Smart field selection (first 2 available, excluding metadata)
                    const filteredSpecs = this.filterMetadataFields(specEntries);
                    const selectedSpecs = filteredSpecs.slice(0, 2);
                    
                    return selectedSpecs.map(([key, value]) => 
                        `${this.formatSpecKey(key)}: ${value}`
                    ).join(' • ') || 'Tidak ada spesifikasi';
                },
                
                // Tier 1: Detect high-priority specification fields using patterns
                getPrioritySpecs(specEntries) {
                    const priorityPatterns = [
                        // Range and measurement fields (highest priority)
                        /.*range.*/i,
                        /.*measurement.*/i,
                        /.*span.*/i,
                        
                        // Precision and accuracy fields
                        /.*resolution.*/i,
                        /.*accuracy.*/i,
                        /.*precision.*/i,
                        
                        // Power and electrical specifications
                        /.*power.*/i,
                        /.*voltage.*/i,
                        /.*current.*/i,
                        /.*output.*/i,
                        
                        // Physical properties
                        /.*magnification.*/i,
                        /.*wavelength.*/i,
                        /.*frequency.*/i,
                        
                        // Performance specifications
                        /.*bandwidth.*/i,
                        /.*rate.*/i,
                        /.*speed.*/i,
                        /.*channels.*/i,
                        
                        // Optical specifications
                        /.*detector.*/i,
                        /.*illumination.*/i,
                        /.*beam.*/i,
                    ];
                    
                    const prioritySpecs = [];
                    
                    // Find specs matching priority patterns
                    for (const pattern of priorityPatterns) {
                        for (const [key, value] of specEntries) {
                            if (pattern.test(key) && !prioritySpecs.some(([existingKey]) => existingKey === key)) {
                                prioritySpecs.push([key, value]);
                                if (prioritySpecs.length >= 2) break;
                            }
                        }
                        if (prioritySpecs.length >= 2) break;
                    }
                    
                    return prioritySpecs;
                },
                
                // Tier 2: Filter out common metadata fields
                filterMetadataFields(specEntries) {
                    const metadataPatterns = [
                        /.*software.*/i,
                        /.*accessories.*/i,
                        /.*notes.*/i,
                        /.*description.*/i,
                        /.*manual.*/i,
                        /.*documentation.*/i,
                        /.*warranty.*/i,
                        /.*manufacturer.*/i,
                        /.*model.*/i,
                        /.*serial.*/i,
                    ];
                    
                    return specEntries.filter(([key]) => 
                        !metadataPatterns.some(pattern => pattern.test(key))
                    );
                },
                
                // Tier 3: Format specification keys to readable Indonesian labels
                formatSpecKey(key) {
                    // Indonesian translation mapping for common technical terms
                    const translations = {
                        'wavelength_range': 'Panjang Gelombang',
                        'frequency_range': 'Frekuensi',
                        'measuring_range': 'Range',
                        'amplitude_range': 'Amplitudo',
                        'wavelength': 'Panjang Gelombang',
                        'frequency': 'Frekuensi',
                        'resolution': 'Resolusi',
                        'accuracy': 'Akurasi',
                        'precision': 'Presisi',
                        'power': 'Daya',
                        'power_output': 'Output Daya',
                        'voltage': 'Tegangan',
                        'current': 'Arus',
                        'bandwidth': 'Bandwidth',
                        'sample_rate': 'Sample Rate',
                        'magnification': 'Perbesaran',
                        'objectives': 'Objektif',
                        'channels': 'Channel',
                        'detector': 'Detektor',
                        'illumination': 'Penerangan',
                        'beam_diameter': 'Diameter Beam',
                        'beam_splitter': 'Beam Splitter',
                        'optical_density': 'Densitas Optik',
                        'wavelength_protection': 'Proteksi Gelombang',
                        'operating_voltage': 'Tegangan Operasi',
                        'stability': 'Stabilitas',
                        'polarization': 'Polarisasi',
                        'material': 'Material',
                        'functions': 'Fungsi',
                        'battery': 'Baterai',
                        'eyepieces': 'Eyepiece',
                        'condenser': 'Kondensor',
                        'stage': 'Stage',
                        'light_source': 'Sumber Cahaya',
                        'interferometer': 'Interferometer',
                        'memory_depth': 'Kedalaman Memori',
                        'length': 'Panjang',
                        'scale': 'Skala'
                    };
                    
                    // Check for direct translation
                    if (translations[key]) {
                        return translations[key];
                    }
                    
                    // Dynamic formatting for unknown keys
                    return key
                        .split('_')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(' ');
                },
                
                // New methods for modal
                updateQuantity(itemId, newQuantity) {
                    const itemIndex = this.selectedItems.findIndex(item => item.id === itemId);
                    if (itemIndex !== -1) {
                        this.selectedItems[itemIndex].quantity = newQuantity;
                    }
                },
                
                proceedToForm() {
                    // Save selected equipment to sessionStorage for form page
                    try {
                        sessionStorage.setItem('selectedEquipments', JSON.stringify(this.selectedItems));
                    } catch (error) {
                        console.error('Failed to save selected equipments:', error);
                    }
                    window.location.href = '/layanan/peminjaman-alat/form';
                }
            }" x-init="init()">

                <!-- Selected Items Chart (Optional) -->
                <div x-show="selectedItems.length > 0" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mb-8">
                    <div class="bg-gradient-to-r from-primary to-blue-600 rounded-2xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold mb-2">Item Terpilih</h3>
                                <p class="text-blue-100">Total: <span x-text="selectedItems.length"></span> item</p>
                            </div>
                            <div class="flex space-x-2">
                                <button @click="showDetailModal = true" 
                                        class="bg-secondary text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-list mr-2"></i>Lihat Detail
                                </button>
                                <button @click="proceedToForm()" 
                                        class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg hover:bg-opacity-30 transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-arrow-right mr-2"></i>Lanjut
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Header -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="text-center mb-8 transition-all duration-1000 ease-out">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">
                        <span class="relative inline-block">
                            🔍 Cari Peralatan
                            <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-green-500 to-secondary rounded-full"></div>
                        </span>
                    </h2>
                </div>

                <!-- Filter Bar -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-12">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Alat</label>
                            <div class="relative">
                                <input type="text" 
                                       x-model="searchQuery"
                                       @keydown.enter="onSearchChange()"
                                       @input.debounce.500ms="onSearchChange()"
                                       placeholder="Nama alat atau spesifikasi..."
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <select x-model="selectedCategory" 
                                    @change="onCategoryChange()"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                <template x-for="category in categories" :key="category.id">
                                    <option :value="category.id" x-text="category.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select x-model="selectedStatus" 
                                    @change="onStatusChange()"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                <template x-for="status in statusOptions" :key="status">
                                    <option :value="status" x-text="status.charAt(0).toUpperCase() + status.slice(1).replace('-', ' ')"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div x-show="loading" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <template x-for="i in 6">
                        <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100 animate-pulse">
                            <div class="h-48 bg-gray-200"></div>
                            <div class="p-6">
                                <div class="h-6 bg-gray-200 rounded mb-2"></div>
                                <div class="h-4 bg-gray-200 rounded mb-4"></div>
                                <div class="h-4 bg-gray-200 rounded mb-4"></div>
                                <div class="h-10 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Error State -->
                <div x-show="error && !loading" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="text-center py-16">
                    <div class="text-6xl text-red-300 mb-4">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Terjadi Kesalahan</h3>
                    <p class="text-gray-500 mb-4" x-text="error"></p>
                    <button @click="loadEquipments()" 
                            class="bg-primary hover:bg-blue-800 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-refresh mr-2"></i>Coba Lagi
                    </button>
                </div>

                <!-- Equipment Grid -->
                <div x-show="!loading && !error" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <template x-for="(equipment, index) in filteredEquipments" :key="equipment.id">
                        <div x-data="{ animated: false }" 
                             x-scroll-animate="animated = true"
                             :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                             class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4 ease-out border border-gray-100"
                             :style="`transition-delay: ${index * 0.1}s`">
                            
                            <!-- Equipment Number Badge -->
                            <div class="absolute top-4 left-4 w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center font-bold text-sm z-10">
                                <span x-text="index + 1"></span>
                            </div>
                            
                            <!-- Equipment Image -->
                            <div class="relative h-48 overflow-hidden bg-gray-200">
                                <template x-if="equipment.image_url">
                                    <img :src="equipment.image_url" 
                                         :alt="equipment.name"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                </template>
                                <template x-if="!equipment.image_url">
                                    <div class="absolute inset-0 bg-gradient-to-br from-gray-300 to-gray-400">
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <i class="fas fa-microscope text-white text-4xl opacity-50"></i>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Status Badge -->
                                <div class="absolute top-4 right-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                          :class="getStatusColor(getEquipmentStatus(equipment))"
                                          x-text="getEquipmentStatus(equipment).charAt(0).toUpperCase() + getEquipmentStatus(equipment).slice(1)">
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Equipment Info -->
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-primary transition-colors duration-300" 
                                    x-text="equipment.name"></h3>
                                
                                <p class="text-gray-600 text-sm mb-4" x-text="getKeySpecs(equipment)"></p>
                                
                                <!-- Availability Info -->
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-box text-primary mr-2"></i>
                                        <span class="text-sm text-gray-600">
                                            Tersedia: <span class="font-semibold" x-text="equipment.available_quantity"></span>/<span x-text="equipment.total_quantity"></span>
                                        </span>
                                    </div>
                                    <div class="text-xs px-2 py-1 rounded-full text-white font-medium" 
                                         :style="getCategoryBadgeStyle(equipment.category)"
                                         x-text="equipment.category?.name || 'Tanpa Kategori'"></div>
                                </div>
                                
                                <!-- Progress Bar -->
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                                    <div class="bg-gradient-to-r from-primary to-blue-600 h-2 rounded-full transition-all duration-500"
                                         :style="`width: ${(equipment.available_quantity / equipment.total_quantity) * 100}%`"></div>
                                </div>
                                
                                <!-- Action Button -->
                                <button @click="addToSelection(equipment)"
                                        :disabled="equipment.available_quantity === 0 || isSelected(equipment.id)"
                                        :class="equipment.available_quantity === 0 ? 'bg-gray-300 cursor-not-allowed' : 
                                                isSelected(equipment.id) ? 'bg-green-500 text-white' :
                                                'bg-primary hover:bg-blue-800 text-white hover:scale-105'"
                                        class="w-full py-3 rounded-xl font-semibold transition-all duration-300 transform">
                                    <span x-show="equipment.available_quantity === 0">
                                        <i class="fas fa-ban mr-2"></i>Tidak Tersedia
                                    </span>
                                    <span x-show="equipment.available_quantity > 0 && !isSelected(equipment.id)">
                                        <i class="fas fa-plus mr-2"></i>Tambah ke Keranjang
                                    </span>
                                    <span x-show="isSelected(equipment.id)">
                                        <i class="fas fa-check mr-2"></i>Sudah Ditambahkan
                                    </span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- No Results -->
                <div x-show="!loading && !error && filteredEquipments.length === 0" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="text-center py-16">
                    <div class="text-6xl text-gray-300 mb-4">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada peralatan ditemukan</h3>
                    <p class="text-gray-500">Coba ubah filter atau kata kunci pencarian</p>
                </div>
                
                <!-- Pagination -->
                <div x-show="!loading && !error && totalPages > 1" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="flex justify-center items-center space-x-2 mt-12">
                    
                    <!-- Previous Button -->
                    <button @click="goToPage(currentPage - 1)"
                            :disabled="currentPage <= 1"
                            :class="currentPage <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary hover:text-white'"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <!-- Page Numbers -->
                    <template x-for="page in Array.from({length: Math.min(5, totalPages)}, (_, i) => {
                        let start = Math.max(1, currentPage - 2);
                        let end = Math.min(totalPages, start + 4);
                        start = Math.max(1, end - 4);
                        return start + i;
                    }).filter(p => p <= totalPages)" :key="page">
                        <button @click="goToPage(page)"
                                :class="page === currentPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-primary hover:text-white border border-gray-300'"
                                class="px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105 font-medium"
                                x-text="page">
                        </button>
                    </template>
                    
                    <!-- Next Button -->
                    <button @click="goToPage(currentPage + 1)"
                            :disabled="currentPage >= totalPages"
                            :class="currentPage >= totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary hover:text-white'"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    
                    <!-- Page Info -->
                    <div class="ml-4 text-sm text-gray-600">
                        Halaman <span class="font-semibold" x-text="currentPage"></span> dari <span class="font-semibold" x-text="totalPages"></span>
                    </div>
                </div>
                
                <!-- Selected Items Summary (Floating) -->
                <div x-show="selectedItems.length > 0" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="fixed bottom-6 right-6 bg-primary text-white p-4 rounded-2xl shadow-2xl z-50">
                    <div class="flex items-center space-x-4">
                        <div>
                            <p class="font-semibold">Item Terpilih</p>
                            <p class="text-sm text-blue-200" x-text="`${selectedItems.length} alat`"></p>
                        </div>
                        <button @click="showDetailModal = true" 
                                class="bg-secondary text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-arrow-right mr-2"></i>Detail
                        </button>
                    </div>
                </div>

                <!-- Detail Pinjaman Modal -->
                <div x-show="showDetailModal" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 overflow-y-auto"
                     style="display: none;"
                     @keydown.escape.window="showDetailModal = false">
                    
                    <!-- Background Blur Overlay -->
                    <div class="fixed inset-0 backdrop-blur-sm bg-black bg-opacity-50 transition-all duration-500"></div>
                    
                    <!-- Modal Container -->
                    <div class="min-h-screen px-4 text-center">
                        <!-- Centering trick -->
                        <span class="inline-block h-screen align-middle" aria-hidden="true">&#8203;</span>
                        
                        <!-- Modal Content -->
                        <div x-show="showDetailModal"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                             class="inline-block w-full max-w-4xl my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl overflow-hidden">
                            
                            <!-- Modal Header -->
                            <div class="bg-gradient-to-r from-primary to-blue-600 px-8 py-6 text-white relative overflow-hidden">
                                <!-- Background Pattern -->
                                <div class="absolute top-0 right-0 w-32 h-32 bg-secondary bg-opacity-20 rounded-full -translate-y-16 translate-x-16"></div>
                                                                
                                <div class="relative z-10 flex items-center justify-between">
                                    <div>
                                        <h2 class="text-2xl md:text-3xl font-bold mb-2 flex items-center">
                                            <i class="fas fa-shopping-cart mr-3 text-secondary"></i>
                                            Detail Peminjaman
                                        </h2>
                                        <p class="text-blue-200">
                                            Review alat yang akan dipinjam dan atur quantity
                                        </p>
                                    </div>
                                    <button @click="showDetailModal = false" 
                                            class="w-10 h-10 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full flex items-center justify-center transition-all duration-300 transform hover:scale-110 hover:rotate-90">
                                        <i class="fas fa-times text-white"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Modal Body -->
                            <div class="max-h-96 overflow-y-auto p-8">
                                <!-- Empty State -->
                                <div x-show="selectedItems.length === 0" class="text-center py-12">
                                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Alat Dipilih</h3>
                                    <p class="text-gray-500 mb-6">Kembali ke katalog untuk memilih peralatan yang dibutuhkan</p>
                                    <button @click="showDetailModal = false" 
                                            class="bg-primary hover:bg-blue-800 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Katalog
                                    </button>
                                </div>
                                
                                <!-- Items List -->
                                <div x-show="selectedItems.length > 0" class="space-y-4">
                                    <template x-for="(item, index) in selectedItems" :key="item.id">
                                        <div class="group bg-gray-50 hover:bg-gray-100 rounded-2xl p-6 transition-all duration-300 transform hover:scale-[1.02] border border-gray-200 hover:border-primary hover:shadow-lg">
                                            <div class="flex items-center space-x-8">
                                                <!-- Item Number -->
                                                <div class="flex-shrink-0">
                                                    <div class="w-12 h-12 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white font-bold">
                                                        <span x-text="index + 1"></span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Item Info -->
                                                <div class="flex-1">
                                                    <h4 class="text-lg font-bold text-gray-800 mb-1 group-hover:text-primary transition-colors duration-300" 
                                                        x-text="item.name"></h4>
                                                    <p class="text-gray-600 text-sm mb-2" x-text="item.specs"></p>
                                                    <div class="flex items-center space-x-4">
                                                        <span class="text-xs px-2 py-1 bg-gray-200 rounded-full text-gray-600 capitalize" 
                                                              x-text="item.category"></span>
                                                        <span class="text-xs text-green-600 font-medium">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            Tersedia: <span x-text="item.available"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Quantity Controls -->
                                                <div class="flex items-center space-x-3">
                                                    <button @click="updateQuantity(item.id, Math.max(1, item.quantity - 1))"
                                                            :disabled="item.quantity <= 1"
                                                            :class="item.quantity <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-500 hover:text-white'"
                                                            class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                                                        <i class="fas fa-minus text-xs"></i>
                                                    </button>
                                                    
                                                    <div class="w-16 text-center">
                                                        <span class="text-lg font-bold text-gray-800" x-text="item.quantity"></span>
                                                        <p class="text-xs text-gray-500">qty</p>
                                                    </div>
                                                    
                                                    <button @click="updateQuantity(item.id, Math.min(item.available, item.quantity + 1))"
                                                            :disabled="item.quantity >= item.available"
                                                            :class="item.quantity >= item.available ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-500 hover:text-white'"
                                                            class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                                                        <i class="fas fa-plus text-xs"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Remove Button -->
                                                <div class="flex-shrink-0">
                                                    <button @click="removeFromSelection(item.id)"
                                                            class="w-10 h-10 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all duration-300 transform hover:scale-110 group-hover:rotate-12">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Progress Bar -->
                                            <div class="mt-4 pt-4 border-t border-gray-200">
                                                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                                                    <span>Quantity yang dipilih</span>
                                                    <span x-text="`${item.quantity}/${item.available} tersedia`"></span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-gradient-to-r from-primary to-blue-600 h-2 rounded-full transition-all duration-500"
                                                         :style="`width: ${(item.quantity / item.available) * 100}%`"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Modal Footer -->
                            <div x-show="selectedItems.length > 0" class="bg-gray-50 px-8 py-6 border-t border-gray-200">
                                <!-- Summary -->
                                <div class="mb-6 p-4 bg-white rounded-xl border border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-1">Ringkasan Peminjaman</h4>
                                            <p class="text-gray-600 text-sm">Total item yang akan dipinjam</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-primary" x-text="selectedItems.length"></div>
                                            <div class="text-sm text-gray-500">
                                                <span x-text="selectedItems.reduce((sum, item) => sum + item.quantity, 0)"></span> unit
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <button @click="showDetailModal = false" 
                                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Kembali ke Katalog
                                    </button>
                                    
                                    <button @click="proceedToForm()" 
                                            class="flex-1 bg-gradient-to-r from-secondary to-yellow-500 hover:from-yellow-500 hover:to-secondary text-gray-800 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 flex items-center justify-center shadow-lg hover:shadow-xl">
                                        <i class="fas fa-arrow-right mr-2"></i>
                                        Lanjut ke Form Peminjaman
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-public.layouts.main>