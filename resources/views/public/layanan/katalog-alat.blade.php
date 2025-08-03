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
                // Filter states
                searchQuery: '',
                selectedCategory: 'semua',
                selectedStatus: 'semua',
                categories: ['semua', 'spektroskopi', 'optik', 'gelombang', 'elektronik'],
                statusOptions: ['semua', 'tersedia', 'dipinjam', 'maintenance'],
                
                // Selection states
                selectedItems: [],
                
                // Equipment data
                equipments: [
                    { id: 1, name: 'Spektrometer UV-Vis', category: 'spektroskopi', status: 'tersedia', available: 3, total: 5, specs: 'Range: 190-1100 nm, Resolusi: 1.8 nm' },
                    { id: 2, name: 'Laser HeNe', category: 'optik', status: 'tersedia', available: 2, total: 3, specs: 'Wavelength: 632.8 nm, Power: 5 mW' },
                    { id: 3, name: 'Function Generator', category: 'elektronik', status: 'tersedia', available: 4, total: 4, specs: 'Frequency: 1 μHz - 80 MHz' },
                    { id: 4, name: 'Mikroskop Optik', category: 'optik', status: 'dipinjam', available: 0, total: 2, specs: 'Magnification: 40x - 1000x' },
                    { id: 5, name: 'Osiloskop Digital', category: 'elektronik', status: 'tersedia', available: 1, total: 2, specs: 'Bandwidth: 100 MHz, 4 Channel' },
                    { id: 6, name: 'Interferometer Michelson', category: 'optik', status: 'maintenance', available: 0, total: 1, specs: 'Precision: λ/20, Mirror: Ø25mm' }
                ],
                
                // Methods
                get filteredEquipments() {
                    return this.equipments.filter(eq => {
                        const matchesSearch = eq.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                            eq.specs.toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchesCategory = this.selectedCategory === 'semua' || eq.category === this.selectedCategory;
                        const matchesStatus = this.selectedStatus === 'semua' || eq.status === this.selectedStatus;
                        return matchesSearch && matchesCategory && matchesStatus;
                    });
                },
                
                addToSelection(equipment) {
                    if (!this.isSelected(equipment.id) && equipment.available > 0) {
                        this.selectedItems.push({...equipment, quantity: 1});
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
                        'maintenance': 'text-red-600 bg-red-100'
                    };
                    return colors[status] || 'text-gray-600 bg-gray-100';
                }
            }">

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
                                <button class="bg-secondary text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition-colors duration-300">
                                    <i class="fas fa-list mr-2"></i>Lihat Detail
                                </button>
                                <button class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg hover:bg-opacity-30 transition-colors duration-300">
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
                                       placeholder="Nama alat atau spesifikasi..."
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <select x-model="selectedCategory" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                <template x-for="category in categories" :key="category">
                                    <option :value="category" x-text="category.charAt(0).toUpperCase() + category.slice(1)"></option>
                                </template>
                            </select>
                        </div>
                        
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select x-model="selectedStatus" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300">
                                <template x-for="status in statusOptions" :key="status">
                                    <option :value="status" x-text="status.charAt(0).toUpperCase() + status.slice(1)"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Filter Tags -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span x-show="selectedCategory !== 'semua'" 
                              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary text-white">
                            <span x-text="selectedCategory"></span>
                            <button @click="selectedCategory = 'semua'" class="ml-2 hover:text-gray-200">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </span>
                        <span x-show="selectedStatus !== 'semua'" 
                              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-secondary text-gray-800">
                            <span x-text="selectedStatus"></span>
                            <button @click="selectedStatus = 'semua'" class="ml-2 hover:text-gray-600">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </span>
                    </div>
                </div>

                <!-- Equipment Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
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
                                <div class="absolute inset-0 bg-gradient-to-br from-gray-300 to-gray-400"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <i class="fas fa-microscope text-white text-4xl opacity-50"></i>
                                </div>
                                
                                <!-- Status Badge -->
                                <div class="absolute top-4 right-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                          :class="getStatusColor(equipment.status)"
                                          x-text="equipment.status.charAt(0).toUpperCase() + equipment.status.slice(1)">
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Equipment Info -->
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-primary transition-colors duration-300" 
                                    x-text="equipment.name"></h3>
                                
                                <p class="text-gray-600 text-sm mb-4" x-text="equipment.specs"></p>
                                
                                <!-- Availability Info -->
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-box text-primary mr-2"></i>
                                        <span class="text-sm text-gray-600">
                                            Tersedia: <span class="font-semibold" x-text="equipment.available"></span>/<span x-text="equipment.total"></span>
                                        </span>
                                    </div>
                                    <div class="text-xs px-2 py-1 bg-gray-100 rounded-full text-gray-600 capitalize" 
                                         x-text="equipment.category"></div>
                                </div>
                                
                                <!-- Progress Bar -->
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                                    <div class="bg-gradient-to-r from-primary to-blue-600 h-2 rounded-full transition-all duration-500"
                                         :style="`width: ${(equipment.available / equipment.total) * 100}%`"></div>
                                </div>
                                
                                <!-- Action Button -->
                                <button @click="addToSelection(equipment)"
                                        :disabled="equipment.available === 0 || isSelected(equipment.id)"
                                        :class="equipment.available === 0 ? 'bg-gray-300 cursor-not-allowed' : 
                                                isSelected(equipment.id) ? 'bg-green-500 text-white' :
                                                'bg-primary hover:bg-blue-800 text-white hover:scale-105'"
                                        class="w-full py-3 rounded-xl font-semibold transition-all duration-300 transform">
                                    <span x-show="equipment.available === 0">
                                        <i class="fas fa-ban mr-2"></i>Tidak Tersedia
                                    </span>
                                    <span x-show="equipment.available > 0 && !isSelected(equipment.id)">
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
                <div x-show="filteredEquipments.length === 0" 
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
                        <button class="bg-secondary text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition-colors duration-300">
                            <i class="fas fa-arrow-right mr-2"></i>Lanjut
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-public.layouts.main>