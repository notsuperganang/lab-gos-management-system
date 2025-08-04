<!-- Header/Navbar with Scroll Transition -->
<nav class="fixed w-full top-0 z-50 transition-all duration-700"
    x-data="{ 
            mobileOpen: false, 
            scrolled: false,
            layananDropdown: false,
            trackingDropdown: false,
            init() {
                window.addEventListener('scroll', () => {
                    this.scrolled = window.scrollY > 50;
                });
            }
         }"
    :class="scrolled ? 'bg-white shadow-lg' : 'bg-transparent'"
    @click.away="layananDropdown = false; trackingDropdown = false">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <!-- Logo with Smooth Transition -->
            <div class="flex items-center relative">
                <!-- Logo Putih (untuk navbar transparan) -->
                <img src="/assets/images/logo-fisika-putih.png"
                    alt="Logo Fisika FMIPA USK"
                    class="h-12 w-auto transition-all duration-700 transform hover:scale-105"
                    :class="scrolled ? 'opacity-0 scale-95' : 'opacity-100 scale-100'">
                <!-- Logo Hitam (untuk navbar putih) -->
                <img src="/assets/images/logo-fisika-hitam.png"
                    alt="Logo Fisika FMIPA USK"
                    class="absolute top-0 left-0 h-12 w-auto transition-all duration-700 transform hover:scale-105"
                    :class="scrolled ? 'opacity-100 scale-100' : 'opacity-0 scale-95'">
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex space-x-8">
                <a href="/"
                    class="font-medium border-b-2 hover:border-secondary transition-all duration-500 transform hover:-translate-y-1 {{ request()->is('/') ? 'border-secondary' : 'border-transparent' }}"
                    :class="scrolled 
           ? '{{ request()->is('/') ? 'text-primary' : 'text-gray-800 hover:text-primary' }}'
           : '{{ request()->is('/') ? 'text-secondary' : 'text-white hover:text-secondary' }}'">
                    Beranda
                </a>

                <a href="/staff"
                    class="font-medium border-b-2 hover:border-secondary transition-all duration-500 transform hover:-translate-y-1 {{ request()->is('staff*') ? 'border-secondary' : 'border-transparent' }}"
                    :class="scrolled 
           ? '{{ request()->is('staff*') ? 'text-primary' : 'text-gray-800 hover:text-primary' }}'
           : '{{ request()->is('staff*') ? 'text-secondary' : 'text-white hover:text-secondary' }}'">
                    Staff
                </a>

                <a href="/artikel"
                    class="font-medium border-b-2 hover:border-secondary transition-all duration-500 transform hover:-translate-y-1 {{ request()->is('artikel*') ? 'border-secondary' : 'border-transparent' }}"
                    :class="scrolled 
           ? '{{ request()->is('artikel*') ? 'text-primary' : 'text-gray-800 hover:text-primary' }}'
           : '{{ request()->is('artikel*') ? 'text-secondary' : 'text-white hover:text-secondary' }}'">
                    Artikel
                </a>

                <!-- Layanan Dropdown -->
                <div class="relative"
                     @mouseenter="layananDropdown = true"
                     @mouseleave="layananDropdown = false">
                    <button class="font-medium border-b-2 hover:border-secondary transition-all duration-500 transform hover:-translate-y-1 flex items-center {{ request()->is('layanan*') ? 'border-secondary' : 'border-transparent' }}"
                            :class="scrolled 
                       ? '{{ request()->is('layanan*') ? 'text-primary' : 'text-gray-800 hover:text-primary' }}'
                       : '{{ request()->is('layanan*') ? 'text-secondary' : 'text-white hover:text-secondary' }}'">
                        Layanan
                        <i class="fas fa-chevron-down ml-1 text-xs transition-transform duration-300"
                           :class="layananDropdown ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="layananDropdown"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="absolute top-full left-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50"
                         style="display: none;">
                        
                        <!-- Dropdown Header -->
                        <div class="bg-gradient-to-r from-primary to-blue-600 px-6 py-4">
                            <h3 class="text-white font-bold text-lg flex items-center">
                                <i class="fas fa-cogs mr-2 text-secondary"></i>
                                Layanan Laboratorium
                            </h3>
                            <p class="text-blue-100 text-sm mt-1">Pilih layanan yang Anda butuhkan</p>
                        </div>
                        
                        <!-- Dropdown Items -->
                        <div class="py-2">
                            <a href="/layanan/peminjaman-alat" 
                               class="group flex items-center px-6 py-4 hover:bg-gray-50 transition-all duration-300 transform hover:scale-105">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-handshake text-white text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 group-hover:text-primary transition-colors duration-300">
                                        Peminjaman Alat
                                    </h4>
                                    <p class="text-gray-600 text-sm">Pinjam peralatan laboratorium</p>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-primary group-hover:translate-x-1 transition-all duration-300"></i>
                            </a>
                            
                            <a href="/layanan/kunjungan" 
                               class="group flex items-center px-6 py-4 hover:bg-gray-50 transition-all duration-300 transform hover:scale-105">
                                <div class="w-12 h-12 bg-gradient-to-br from-secondary to-yellow-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-users text-white text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 group-hover:text-secondary transition-colors duration-300">
                                        Kunjungan Lab
                                    </h4>
                                    <p class="text-gray-600 text-sm">Ajukan kunjungan laboratorium</p>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-secondary group-hover:translate-x-1 transition-all duration-300"></i>
                            </a>
                            
                            <a href="/layanan/pengujian" 
                               class="group flex items-center px-6 py-4 hover:bg-gray-50 transition-all duration-300 transform hover:scale-105">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-600 to-green-700 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-vial text-white text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 group-hover:text-green-600 transition-colors duration-300">
                                        Pengujian Sampel
                                    </h4>
                                    <p class="text-gray-600 text-sm">Layanan analisis dan pengujian</p>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-green-600 group-hover:translate-x-1 transition-all duration-300"></i>
                            </a>
                        </div>
                        
                        <!-- Dropdown Footer -->
                        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                            <p class="text-gray-600 text-xs text-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                Semua layanan memerlukan persetujuan admin
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tracking Dropdown -->
                <div class="relative"
                     @mouseenter="trackingDropdown = true"
                     @mouseleave="trackingDropdown = false">
                    <button class="font-medium border-b-2 hover:border-secondary transition-all duration-500 transform hover:-translate-y-1 flex items-center {{ request()->is('tracking*') ? 'border-secondary' : 'border-transparent' }}"
                            :class="scrolled 
                       ? '{{ request()->is('tracking*') ? 'text-primary' : 'text-gray-800 hover:text-primary' }}'
                       : '{{ request()->is('tracking*') ? 'text-secondary' : 'text-white hover:text-secondary' }}'">
                        Tracking
                        <i class="fas fa-chevron-down ml-1 text-xs transition-transform duration-300"
                           :class="trackingDropdown ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- Tracking Dropdown Menu -->
                    <div x-show="trackingDropdown"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="absolute top-full left-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50"
                         style="display: none;">
                        
                        <!-- Dropdown Header -->
                        <div class="bg-gradient-to-r from-primary to-blue-600 px-6 py-4">
                            <h3 class="text-white font-bold text-lg flex items-center">
                                <i class="fas fa-search mr-2 text-secondary"></i>
                                Tracking Status
                            </h3>
                            <p class="text-white text-sm mt-1">Lacak status permohonan Anda</p>
                        </div>
                        
                        <!-- Tracking Dropdown Items -->
                        <div class="py-2">
                            <a href="/tracking/peminjaman-alat" 
                               class="group flex items-center px-6 py-4 hover:bg-gray-50 transition-all duration-300 transform hover:scale-105">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-clipboard-check text-white text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 group-hover:text-primary transition-colors duration-300">
                                        Tracking Peminjaman
                                    </h4>
                                    <p class="text-gray-600 text-sm">Status peminjaman alat</p>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-primary group-hover:translate-x-1 transition-all duration-300"></i>
                            </a>
                            
                            <a href="/tracking/kunjungan" 
                               class="group flex items-center px-6 py-4 hover:bg-gray-50 transition-all duration-300 transform hover:scale-105">
                                <div class="w-12 h-12 bg-gradient-to-br from-secondary to-yellow-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-map-marker-alt text-white text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 group-hover:text-secondary transition-colors duration-300">
                                        Tracking Kunjungan
                                    </h4>
                                    <p class="text-gray-600 text-sm">Status kunjungan lab</p>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-secondary group-hover:translate-x-1 transition-all duration-300"></i>
                            </a>
                            
                            <a href="/tracking/pengujian" 
                               class="group flex items-center px-6 py-4 hover:bg-gray-50 transition-all duration-300 transform hover:scale-105">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-600 to-green-700 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-microscope text-white text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 group-hover:text-green-600 transition-colors duration-300">
                                        Tracking Pengujian
                                    </h4>
                                    <p class="text-gray-600 text-sm">Status pengujian sampel</p>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-green-600 group-hover:translate-x-1 transition-all duration-300"></i>
                            </a>
                        </div>
                        
                        <!-- Tracking Dropdown Footer -->
                        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                            <p class="text-gray-600 text-xs text-center">
                                <i class="fas fa-key mr-1"></i>
                                Masukkan ID tracking untuk melihat status
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <button @click="mobileOpen = !mobileOpen" class="md:hidden transition-colors duration-500"
                :class="scrolled ? 'text-gray-800' : 'text-white'">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <div x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden pb-4 backdrop-blur-md bg-white bg-opacity-95 rounded-lg mx-4 mb-4">
            <div class="flex flex-col space-y-3 p-4">
                <a href="/" class="text-gray-800 hover:text-primary transition-colors duration-300 font-medium">Beranda</a>
                <a href="/staff" class="text-gray-800 hover:text-primary transition-colors duration-300 font-medium">Staff</a>
                <a href="/artikel" class="text-gray-800 hover:text-primary transition-colors duration-300 font-medium">Artikel</a>
                
                <!-- Mobile Layanan Section -->
                <div class="border-t border-gray-200 pt-3">
                    <div class="text-gray-600 font-semibold text-sm mb-2 flex items-center">
                        <i class="fas fa-cogs mr-2"></i>
                        Layanan
                    </div>
                    <div class="ml-4 space-y-2">
                        <a href="/layanan/peminjaman-alat" class="flex items-center text-gray-700 hover:text-primary transition-colors duration-300">
                            <i class="fas fa-handshake mr-2 text-primary"></i>
                            Peminjaman Alat
                        </a>
                        <a href="/layanan/kunjungan" class="flex items-center text-gray-700 hover:text-secondary transition-colors duration-300">
                            <i class="fas fa-users mr-2 text-secondary"></i>
                            Kunjungan Lab
                        </a>
                        <a href="/layanan/pengujian" class="flex items-center text-gray-700 hover:text-green-600 transition-colors duration-300">
                            <i class="fas fa-vial mr-2 text-green-600"></i>
                            Pengujian Sampel
                        </a>
                    </div>
                </div>
                
                <!-- Mobile Tracking Section -->
                <div class="border-t border-gray-200 pt-3">
                    <div class="text-gray-600 font-semibold text-sm mb-2 flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Tracking
                    </div>
                    <div class="ml-4 space-y-2">
                        <a href="/tracking/peminjaman-alat" class="flex items-center text-gray-700 hover:text-primary transition-colors duration-300">
                            <i class="fas fa-clipboard-check mr-2 text-primary"></i>
                            Tracking Peminjaman
                        </a>
                        <a href="/tracking/kunjungan" class="flex items-center text-gray-700 hover:text-secondary transition-colors duration-300">
                            <i class="fas fa-map-marker-alt mr-2 text-secondary"></i>
                            Tracking Kunjungan
                        </a>
                        <a href="/tracking/pengujian" class="flex items-center text-gray-700 hover:text-green-600 transition-colors duration-300">
                            <i class="fas fa-microscope mr-2 text-green-600"></i>
                            Tracking Pengujian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>