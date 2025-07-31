<!-- resources/views/public/staff.blade.php -->

<x-public.layouts.main>
    {{-- Mendefinisikan judul halaman untuk layout --}}
    <x-slot:title>
        Staff Laboratorium - Lab GOS USK
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
                        Staff 
                        <span class="text-secondary">Laboratorium</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Tim profesional yang berdedikasi untuk kemajuan ilmu pengetahuan
                </p>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-2 inline-block">
                    <p class="text-white flex items-center justify-center">
                        <i class="fas fa-users mr-2 text-secondary"></i>
                        Laboratorium Gelombang, Optik & Spektroskopi
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Staff Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Filter Section -->
            <div x-data="{ 
                activeFilter: 'semua',
                staffData: [
                    { name: 'Dr. Ahmad Reza Firmansyah, M.Si', position: 'Kepala Laboratorium', category: 'dosen', specialization: 'Spektroskopi Molekular', image: '/assets/images/staff-1.jpg' },
                    { name: 'Dr. Siti Nurhaliza, M.Sc', position: 'Dosen Senior', category: 'dosen', specialization: 'Optik Quantum', image: '/assets/images/staff-2.jpg' },
                    { name: 'Dr. Muhammad Iqbal, Ph.D', position: 'Peneliti Utama', category: 'dosen', specialization: 'Fisika Gelombang', image: '/assets/images/staff-3.jpg' },
                    { name: 'Prof. Dr. Fatimah Al-Zahra, M.Si', position: 'Guru Besar', category: 'dosen', specialization: 'Spektroskopi Laser', image: '/assets/images/staff-4.jpg' },
                    { name: 'Rizky Pratama, S.Si', position: 'Laboran Senior', category: 'laboran', specialization: 'Maintenance Peralatan', image: '/assets/images/staff-5.jpg' },
                    { name: 'Devi Anggraini, S.Si', position: 'Laboran', category: 'laboran', specialization: 'Preparasi Sampel', image: '/assets/images/staff-6.jpg' },
                    { name: 'Andri Setiawan, A.Md', position: 'Teknisi Elektronik', category: 'teknisi', specialization: 'Sistem Elektronik', image: '/assets/images/staff-7.jpg' },
                    { name: 'Lina Marlina, A.Md', position: 'Teknisi Optik', category: 'teknisi', specialization: 'Kalibrasi Optik', image: '/assets/images/staff-8.jpg' },
                    { name: 'Bambang Hermawan, S.T', position: 'Teknisi Senior', category: 'teknisi', specialization: 'Maintenance Lab', image: '/assets/images/staff-9.jpg' }
                ],
                get filteredStaff() {
                    if (this.activeFilter === 'semua') {
                        return this.staffData;
                    }
                    return this.staffData.filter(staff => staff.category === this.activeFilter);
                },
                setFilter(filter) {
                    this.activeFilter = filter;
                }
            }">
                
                <!-- Filter Buttons -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="flex justify-center mb-12 transition-all duration-1000 ease-out">
                    <div class="bg-white rounded-2xl p-2 shadow-lg inline-flex space-x-2">
                        <button @click="setFilter('semua')" 
                                :class="activeFilter === 'semua' ? 'bg-primary text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-users mr-2"></i>
                            Semua
                        </button>
                        <button @click="setFilter('dosen')" 
                                :class="activeFilter === 'dosen' ? 'bg-secondary text-gray-800 shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            Dosen
                        </button>
                        <button @click="setFilter('laboran')" 
                                :class="activeFilter === 'laboran' ? 'bg-green-500 text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-flask mr-2"></i>
                            Laboran
                        </button>
                        <button @click="setFilter('teknisi')" 
                                :class="activeFilter === 'teknisi' ? 'bg-purple-500 text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-tools mr-2"></i>
                            Teknisi
                        </button>
                    </div>
                </div>

                <!-- Staff Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <template x-for="(staff, index) in filteredStaff" :key="staff.name">
                        <div x-data="{ animated: false }" 
                             x-scroll-animate="animated = true"
                             :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                             class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4 hover:rotate-1 ease-out"
                             :style="`transition-delay: ${index * 0.1}s`">
                            
                            <!-- Staff Photo -->
                            <div class="relative h-64 overflow-hidden">
                                <!-- Gradient Background as placeholder -->
                                <div class="absolute inset-0 bg-gradient-to-br"
                                     :class="{
                                         'from-primary to-blue-600': staff.category === 'dosen',
                                         'from-green-500 to-green-700': staff.category === 'laboran',
                                         'from-purple-500 to-purple-700': staff.category === 'teknisi'
                                     }"></div>
                                <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                                
                                <!-- Avatar Icon -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-24 h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                                        <i class="fas fa-user text-white text-3xl"></i>
                                    </div>
                                </div>
                                
                                <!-- Category Badge -->
                                <div class="absolute top-4 right-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                          :class="{
                                              'bg-secondary text-gray-800': staff.category === 'dosen',
                                              'bg-green-400 text-white': staff.category === 'laboran',
                                              'bg-purple-400 text-white': staff.category === 'teknisi'
                                          }"
                                          x-text="staff.category.charAt(0).toUpperCase() + staff.category.slice(1)">
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Staff Info -->
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-primary transition-colors duration-300" 
                                    x-text="staff.name"></h3>
                                <p class="text-gray-600 font-semibold mb-2" x-text="staff.position"></p>
                                <p class="text-sm text-gray-500 mb-4" x-text="staff.specialization"></p>
                                
                                <!-- Contact Info -->
                                <div class="space-y-2">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-envelope mr-2 text-primary"></i>
                                        <span x-text="staff.name.toLowerCase().replace(/[^a-z]/g, '') + '@unsyiah.ac.id'"></span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-phone mr-2 text-primary"></i>
                                        <span>+62 651-7552922</span>
                                    </div>
                                </div>
                                
                                <!-- Social Links -->
                                <div class="flex space-x-3 mt-4 pt-4 border-t border-gray-100">
                                    <a href="#" class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 hover:bg-blue-200 transition-colors duration-300 transform hover:scale-110">
                                        <i class="fab fa-linkedin text-sm"></i>
                                    </a>
                                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors duration-300 transform hover:scale-110">
                                        <i class="fas fa-envelope text-sm"></i>
                                    </a>
                                    <a href="#" class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 hover:bg-green-200 transition-colors duration-300 transform hover:scale-110">
                                        <i class="fab fa-whatsapp text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredStaff.length === 0" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="text-center py-16">
                    <div class="text-6xl text-gray-300 mb-4">
                        <i class="fas fa-user-slash"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada staff ditemukan</h3>
                    <p class="text-gray-500">Coba pilih kategori lain atau reset filter</p>
                </div>

                <!-- Stats Section -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="mt-16 bg-white rounded-2xl p-8 shadow-lg transition-all duration-1000 ease-out">
                    <h3 class="text-2xl font-bold text<!-- resources/views/public/staff.blade.php -->

<x-public.layouts.main>
    {{-- Mendefinisikan judul halaman untuk layout --}}
    <x-slot:title>
        Staff Laboratorium - Lab GOS USK
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
                        Staff 
                        <span class="text-secondary">Laboratorium</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Tim profesional yang berdedikasi untuk kemajuan ilmu pengetahuan
                </p>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-2 inline-block">
                    <p class="text-white flex items-center justify-center">
                        <i class="fas fa-users mr-2 text-secondary"></i>
                        Laboratorium Gelombang, Optik & Spektroskopi
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Staff Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Filter Section -->
            <div x-data="{ 
                activeFilter: 'semua',
                staffData: [
                    { name: 'Dr. Ahmad Reza Firmansyah, M.Si', position: 'Kepala Laboratorium', category: 'dosen', specialization: 'Spektroskopi Molekular', image: '/assets/images/staff-1.jpg' },
                    { name: 'Dr. Siti Nurhaliza, M.Sc', position: 'Dosen Senior', category: 'dosen', specialization: 'Optik Quantum', image: '/assets/images/staff-2.jpg' },
                    { name: 'Dr. Muhammad Iqbal, Ph.D', position: 'Peneliti Utama', category: 'dosen', specialization: 'Fisika Gelombang', image: '/assets/images/staff-3.jpg' },
                    { name: 'Prof. Dr. Fatimah Al-Zahra, M.Si', position: 'Guru Besar', category: 'dosen', specialization: 'Spektroskopi Laser', image: '/assets/images/staff-4.jpg' },
                    { name: 'Rizky Pratama, S.Si', position: 'Laboran Senior', category: 'laboran', specialization: 'Maintenance Peralatan', image: '/assets/images/staff-5.jpg' },
                    { name: 'Devi Anggraini, S.Si', position: 'Laboran', category: 'laboran', specialization: 'Preparasi Sampel', image: '/assets/images/staff-6.jpg' },
                    { name: 'Andri Setiawan, A.Md', position: 'Teknisi Elektronik', category: 'teknisi', specialization: 'Sistem Elektronik', image: '/assets/images/staff-7.jpg' },
                    { name: 'Lina Marlina, A.Md', position: 'Teknisi Optik', category: 'teknisi', specialization: 'Kalibrasi Optik', image: '/assets/images/staff-8.jpg' },
                    { name: 'Bambang Hermawan, S.T', position: 'Teknisi Senior', category: 'teknisi', specialization: 'Maintenance Lab', image: '/assets/images/staff-9.jpg' }
                ],
                get filteredStaff() {
                    if (this.activeFilter === 'semua') {
                        return this.staffData;
                    }
                    return this.staffData.filter(staff => staff.category === this.activeFilter);
                },
                setFilter(filter) {
                    this.activeFilter = filter;
                }
            }">
                
                <!-- Filter Buttons -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="flex justify-center mb-12 transition-all duration-1000 ease-out">
                    <div class="bg-white rounded-2xl p-2 shadow-lg inline-flex space-x-2">
                        <button @click="setFilter('semua')" 
                                :class="activeFilter === 'semua' ? 'bg-primary text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-users mr-2"></i>
                            Semua
                        </button>
                        <button @click="setFilter('dosen')" 
                                :class="activeFilter === 'dosen' ? 'bg-secondary text-gray-800 shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            Dosen
                        </button>
                        <button @click="setFilter('laboran')" 
                                :class="activeFilter === 'laboran' ? 'bg-green-500 text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-flask mr-2"></i>
                            Laboran
                        </button>
                        <button @click="setFilter('teknisi')" 
                                :class="activeFilter === 'teknisi' ? 'bg-purple-500 text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-tools mr-2"></i>
                            Teknisi
                        </button>
                    </div>
                </div>

                <!-- Staff Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <template x-for="(staff, index) in filteredStaff" :key="staff.name">
                        <div x-data="{ animated: false }" 
                             x-scroll-animate="animated = true"
                             :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                             class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4 hover:rotate-1 ease-out"
                             :style="`transition-delay: ${index * 0.1}s`">
                            
                            <!-- Staff Photo -->
                            <div class="relative h-64 overflow-hidden">
                                <!-- Gradient Background as placeholder -->
                                <div class="absolute inset-0 bg-gradient-to-br"
                                     :class="{
                                         'from-primary to-blue-600': staff.category === 'dosen',
                                         'from-green-500 to-green-700': staff.category === 'laboran',
                                         'from-purple-500 to-purple-700': staff.category === 'teknisi'
                                     }"></div>
                                <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                                
                                <!-- Avatar Icon -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-24 h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                                        <i class="fas fa-user text-white text-3xl"></i>
                                    </div>
                                </div>
                                
                                <!-- Category Badge -->
                                <div class="absolute top-4 right-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                          :class="{
                                              'bg-secondary text-gray-800': staff.category === 'dosen',
                                              'bg-green-400 text-white': staff.category === 'laboran',
                                              'bg-purple-400 text-white': staff.category === 'teknisi'
                                          }"
                                          x-text="staff.category.charAt(0).toUpperCase() + staff.category.slice(1)">
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Staff Info -->
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-primary transition-colors duration-300" 
                                    x-text="staff.name"></h3>
                                <p class="text-gray-600 font-semibold mb-2" x-text="staff.position"></p>
                                <p class="text-sm text-gray-500 mb-4" x-text="staff.specialization"></p>
                                
                                <!-- Contact Info -->
                                <div class="space-y-2">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-envelope mr-2 text-primary"></i>
                                        <span x-text="staff.name.toLowerCase().replace(/[^a-z]/g, '') + '@unsyiah.ac.id'"></span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-phone mr-2 text-primary"></i>
                                        <span>+62 651-7552922</span>
                                    </div>
                                </div>
                                
                                <!-- Social Links -->
                                <div class="flex space-x-3 mt-4 pt-4 border-t border-gray-100">
                                    <a href="#" class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 hover:bg-blue-200 transition-colors duration-300 transform hover:scale-110">
                                        <i class="fab fa-linkedin text-sm"></i>
                                    </a>
                                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors duration-300 transform hover:scale-110">
                                        <i class="fas fa-envelope text-sm"></i>
                                    </a>
                                    <a href="#" class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 hover:bg-green-200 transition-colors duration-300 transform hover:scale-110">
                                        <i class="fab fa-whatsapp text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredStaff.length === 0" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="text-center py-16">
                    <div class="text-6xl text-gray-300 mb-4">
                        <i class="fas fa-user-slash"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada staff ditemukan</h3>
                    <p class="text-gray-500">Coba pilih kategori lain atau reset filter</p>
                </div>

                <!-- Stats Section -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="mt-16 bg-white rounded-2xl p-8 shadow-lg transition-all duration-1000 ease-out">
                    <h3 class="text-2xl font-bold text-center text-gray-800 mb-8">
                        <span class="relative inline-block">
                            📊 Statistik Tim
                            <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                        </span>
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                        <div class="p-4">
                            <div class="text-3xl font-bold text-primary mb-2" x-text="staffData.length"></div>
                            <div class="text-gray-600">Total Staff</div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-secondary mb-2" x-text="staffData.filter(s => s.category === 'dosen').length"></div>
                            <div class="text-gray-600">Dosen</div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-green-500 mb-2" x-text="staffData.filter(s => s.category === 'laboran').length"></div>
                            <div class="text-gray-600">Laboran</div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-purple-500 mb-2" x-text="staffData.filter(s => s.category === 'teknisi').length"></div>
                            <div class="text-gray-600">Teknisi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
</x-public.layouts.main>-center text-gray-800 mb-8">
                        <span class="relative inline-block">
                            📊 Statistik Tim
                            <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                        </span>
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                        <div class="p-4">
                            <div class="text-3xl font-bold text-primary mb-2" x-text="staffData.length"></div>
                            <div class="text-gray-600">Total Staff</div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-secondary mb-2" x-text="staffData.filter(s => s.category === 'dosen').length"></div>
                            <div class="text-gray-600">Dosen</div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-green-500 mb-2" x-text="staffData.filter(s => s.category === 'laboran').length"></div>
                            <div class="text-gray-600">Laboran</div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-purple-500 mb-2" x-text="staffData.filter(s => s.category === 'teknisi').length"></div>
                            <div class="text-gray-600">Teknisi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
</x-public.layouts.main>