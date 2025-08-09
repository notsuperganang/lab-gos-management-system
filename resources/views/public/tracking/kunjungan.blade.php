<x-public.layouts.main>
    {{-- Mendefinisikan judul halaman untuk layout --}}
    <x-slot:title>
        Tracking Kunjungan Lab - Lab GOS USK
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
                        🔍 Tracking 
                        <span class="text-secondary">Kunjungan</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Masukkan ID kunjungan untuk melacak status permohonan Anda
                </p>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-2 inline-block">
                    <p class="text-white flex items-center justify-center">
                        <i class="fas fa-users mr-2 text-secondary"></i>
                        Pantau Status Kunjungan Real-time
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Tracking Form Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Tracking Form Container -->
            <div x-data="trackingForm()" class="space-y-8">
                
                <!-- Main Tracking Form -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="bg-white rounded-3xl shadow-2xl overflow-hidden transition-all duration-1000 ease-out">
                    
                    <!-- Form Header -->
                    <div class="bg-gradient-to-r from-primary to-blue-600 px-8 py-8 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-secondary bg-opacity-20 rounded-full -translate-y-16 translate-x-16"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white bg-opacity-10 rounded-full translate-y-12 -translate-x-12"></div>
                        
                        <div class="relative z-10 text-center">
                            <div class="w-20 h-20 bg-secondary rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                                <i class="fas fa-search text-gray-800 text-2xl"></i>
                            </div>
                            <h2 class="text-2xl md:text-3xl font-bold mb-2">
                                LACAK STATUS KUNJUNGAN
                            </h2>
                            <p class="text-blue-200">
                                Masukkan ID kunjungan yang Anda terima setelah mengajukan permohonan
                            </p>
                        </div>
                    </div>

                    <!-- Form Body -->
                    <div class="p-8">
                        <form @submit.prevent="searchTracking()" class="space-y-6">
                            
                            <!-- Tracking ID Input -->
                            <div class="text-center mb-8">
                                <label class="block text-lg font-semibold text-gray-800 mb-4">
                                    <i class="fas fa-key mr-2 text-primary"></i>
                                    ID Kunjungan
                                </label>
                                
                                <div class="relative max-w-md mx-auto">
                                    <input type="text" 
                                           x-model="trackingId"
                                           placeholder="Contoh: KNJ-2025-001"
                                           class="w-full px-6 py-4 text-center text-lg font-mono border-2 border-gray-300 rounded-2xl focus:ring-4 focus:ring-primary focus:ring-opacity-20 focus:border-primary transition-all duration-300 transform focus:scale-105"
                                           required
                                           pattern="^KNJ-\d{4}-\d{3}$"
                                           title="Format: KNJ-YYYY-XXX (contoh: KNJ-2025-001)">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                        <i class="fas fa-hashtag text-gray-400"></i>
                                    </div>
                                </div>
                                
                                <!-- Format Helper -->
                                <p class="text-sm text-gray-500 mt-2">
                                    Format: KNJ-TAHUN-NOMOR (contoh: KNJ-2025-001)
                                </p>
                            </div>

                            <!-- Search Button -->
                            <div class="text-center">
                                <button type="submit" 
                                        :disabled="!isValidFormat() || searching"
                                        :class="isValidFormat() && !searching ? 
                                               'bg-gradient-to-r from-primary to-blue-600 hover:from-blue-600 hover:to-primary text-white shadow-lg hover:shadow-xl transform hover:scale-105' : 
                                               'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                        class="px-12 py-4 rounded-2xl font-bold text-lg transition-all duration-300 flex items-center justify-center mx-auto">
                                    <span x-show="!searching" class="flex items-center">
                                        <i class="fas fa-search mr-3"></i>
                                        Lacak Status
                                    </span>
                                    <span x-show="searching" class="flex items-center">
                                        <i class="fas fa-spinner fa-spin mr-3"></i>
                                        Mencari...
                                    </span>
                                </button>
                            </div>

                            <!-- Error Message -->
                            <div x-show="errorMessage" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="bg-red-50 border border-red-200 rounded-2xl p-4 text-center">
                                <div class="flex items-center justify-center text-red-600">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span x-text="errorMessage"></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Information Cards -->
                <div class="grid md:grid-cols-2 gap-8">
                    
                    <!-- Cara Mendapatkan ID -->
                    <div x-data="{ animated: false }" 
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="bg-white rounded-2xl shadow-lg p-6 transition-all duration-1000 ease-out"
                         style="transition-delay: 0.2s;">
                        
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-info-circle text-primary text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Cara Mendapatkan ID</h3>
                        </div>
                        
                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-white text-xs font-bold">1</span>
                                </div>
                                <p>Setelah mengisi form kunjungan laboratorium</p>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-white text-xs font-bold">2</span>
                                </div>
                                <p>Sistem akan memberikan ID unik kunjungan</p>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-white text-xs font-bold">3</span>
                                </div>
                                <p>ID akan dikirim melalui email konfirmasi</p>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-white text-xs font-bold">4</span>
                                </div>
                                <p>Gunakan ID tersebut untuk tracking</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status yang Dapat Dilacak -->
                    <div x-data="{ animated: false }" 
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="bg-white rounded-2xl shadow-lg p-6 transition-all duration-1000 ease-out"
                         style="transition-delay: 0.4s;">
                        
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-tasks text-green-600 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Status Tracking</h3>
                        </div>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="text-gray-600">Permohonan Diajukan</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                <span class="text-gray-600">Sedang Review Admin</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-gray-600">Disetujui & Siap Kunjungan</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                <span class="text-gray-600">Kunjungan Selesai</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-gray-600">Ditolak</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Access Links -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="bg-gradient-to-r from-secondary to-yellow-500 rounded-2xl p-6 text-center transition-all duration-1000 ease-out"
                     style="transition-delay: 0.6s;">
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-rocket mr-2"></i>
                        Belum Punya ID Kunjungan?
                    </h3>
                    <p class="text-gray-700 mb-6">
                        Ajukan kunjungan laboratorium terlebih dahulu untuk mendapatkan ID tracking
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/layanan/kunjungan" 
                           class="bg-white hover:bg-gray-100 text-gray-800 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-users mr-2"></i>
                            Ajukan Kunjungan
                        </a>
                        
                        <a href="/" 
                           class="bg-primary hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-home mr-2"></i>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>

                <!-- Demo Section -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="bg-white rounded-2xl shadow-lg p-6 border-2 border-dashed border-gray-200 transition-all duration-1000 ease-out"
                     style="transition-delay: 0.8s;">
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-play-circle text-purple-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Coba Demo Tracking</h3>
                        <p class="text-gray-600 mb-4">
                            Gunakan ID demo untuk melihat bagaimana sistem tracking bekerja
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
                            <button @click="useDemo('KNJ-2025-001')" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 text-sm">
                                KNJ-2025-001
                                <br><span class="text-xs opacity-80">Disetujui</span>
                            </button>
                            <button @click="useDemo('KNJ-2025-002')" 
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 text-sm">
                                KNJ-2025-002
                                <br><span class="text-xs opacity-80">Sedang Review</span>
                            </button>
                            <button @click="useDemo('KNJ-2025-003')" 
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 text-sm">
                                KNJ-2025-003
                                <br><span class="text-xs opacity-80">Selesai</span>
                            </button>
                        </div>
                        
                        <p class="text-sm text-gray-500">
                            Klik salah satu ID di atas untuk melihat demo tracking
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function trackingForm() {
            return {
                trackingId: '',
                searching: false,
                errorMessage: '',
                
                isValidFormat() {
                    const pattern = /^KNJ-\d{4}-\d{3}$/;
                    return pattern.test(this.trackingId.trim());
                },
                
                async searchTracking() {
                    if (!this.isValidFormat()) {
                        this.errorMessage = 'Format ID tidak valid. Gunakan format: KNJ-YYYY-XXX';
                        return;
                    }
                    
                    this.searching = true;
                    this.errorMessage = '';
                    
                    try {
                        // Simulate API call to check if ID exists
                        await new Promise(resolve => setTimeout(resolve, 1500));
                        
                        // For demo purposes, accept specific valid IDs
                        const validIds = ['KNJ-2025-001', 'KNJ-2025-002', 'KNJ-2025-003'];
                        const inputId = this.trackingId.trim();
                        
                        if (validIds.includes(inputId) || inputId.match(/^KNJ-\d{4}-\d{3}$/)) {
                            // Redirect to confirmation page with tracking details
                            window.location.href = `/layanan/kunjungan/confirmation/${inputId}`;
                        } else {
                            this.errorMessage = 'ID kunjungan tidak ditemukan. Periksa kembali ID Anda atau hubungi admin.';
                        }
                        
                    } catch (error) {
                        console.error('Error searching tracking:', error);
                        this.errorMessage = 'Terjadi kesalahan saat mencari data. Silakan coba lagi.';
                    } finally {
                        this.searching = false;
                    }
                },
                
                useDemo(demoId) {
                    this.trackingId = demoId;
                    // Scroll to form
                    const form = document.querySelector('form');
                    if (form) {
                        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    // Auto submit after a short delay
                    setTimeout(() => {
                        this.searchTracking();
                    }, 500);
                }
            }
        }
    </script>

</x-public.layouts.main>