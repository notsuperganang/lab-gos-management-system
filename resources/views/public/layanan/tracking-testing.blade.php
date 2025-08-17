<x-public.layouts.main>
    {{-- Mendefinisikan judul halaman untuk layout --}}
    <x-slot:title>
        Tracking Pengujian - Lab GOS USK
    </x-slot:title>

    <!-- Hero Section -->
    <section class="relative h-80 flex items-center justify-center pt-20">
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
                        🧪 Tracking
                        <span class="text-secondary">Pengujian</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Pantau status permohonan pengujian sampel Anda
                </p>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-2 inline-block">
                    <p class="text-white flex items-center justify-center">
                        <i class="fas fa-search mr-2 text-secondary"></i>
                        Status Pengujian Real-time
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div x-data="trackingTesting()" x-init="init()" class="space-y-8">

                <!-- Loading State -->
                <div x-show="loading" class="text-center py-20">
                    <div class="inline-flex items-center px-6 py-3 bg-white rounded-xl shadow-lg">
                        <i class="fas fa-spinner fa-spin text-primary text-xl mr-3"></i>
                        <span class="text-gray-700 font-semibold">Memuat data pengujian...</span>
                    </div>
                </div>

                <!-- Error State -->
                <div x-show="error && !loading" class="text-center py-20">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-8 max-w-md mx-auto">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-red-800 mb-2">Terjadi Kesalahan</h3>
                        <p class="text-red-600 mb-4" x-text="error"></p>
                        <button @click="init()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-300">
                            <i class="fas fa-redo mr-2"></i>
                            Coba Lagi
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <div x-show="!loading && !error" class="space-y-8">

                    <!-- Request Overview Card -->
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-primary to-blue-800 p-8 text-white">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h2 class="text-2xl font-bold mb-2">Status Pengujian</h2>
                                    <p class="text-blue-100">ID Pengujian: <span class="font-semibold" x-text="testingId"></span></p>
                                </div>
                                <div class="text-right">
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                         :class="{
                                            'bg-yellow-100 text-yellow-800': currentStatus === 'pending',
                                            'bg-green-100 text-green-800': currentStatus === 'approved',
                                            'bg-blue-100 text-blue-800': currentStatus === 'in_progress',
                                            'bg-green-100 text-green-800': currentStatus === 'completed',
                                            'bg-red-100 text-red-800': currentStatus === 'rejected' || currentStatus === 'cancelled'
                                         }">
                                        <span x-show="currentStatus === 'pending'">⏳ Menunggu Persetujuan</span>
                                        <span x-show="currentStatus === 'approved'">✅ Disetujui & Dijadwalkan</span>
                                        <span x-show="currentStatus === 'in_progress'">🔬 Sedang Diuji</span>
                                        <span x-show="currentStatus === 'completed'">✅ Selesai</span>
                                        <span x-show="currentStatus === 'rejected'">❌ Ditolak</span>
                                        <span x-show="currentStatus === 'cancelled'">🚫 Dibatalkan</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-blue-200 mb-1">Diajukan pada</div>
                                    <div class="font-bold text-secondary" x-text="submittedDate"></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-blue-200 mb-1">Progress</div>
                                    <div class="flex items-center">
                                        <div class="w-32 bg-blue-700 rounded-full h-2 mr-3">
                                            <div class="bg-secondary h-2 rounded-full transition-all duration-500"
                                                 :style="`width: ${progressPercentage}%`"></div>
                                        </div>
                                        <span class="text-sm font-semibold" x-text="`${progressPercentage}%`"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testing Details -->
                        <div class="p-8">
                            <div class="grid md:grid-cols-2 gap-8">
                                <!-- Client Information -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                        <i class="fas fa-user text-primary mr-2"></i>
                                        Informasi Klien
                                    </h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Nama:</span>
                                            <span class="font-semibold text-gray-800" x-text="clientInfo.name"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Organisasi:</span>
                                            <span class="font-semibold text-gray-800" x-text="clientInfo.organization"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Email:</span>
                                            <span class="font-semibold text-gray-800" x-text="clientInfo.email"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Telepon:</span>
                                            <span class="font-semibold text-gray-800" x-text="clientInfo.phone"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sample Information -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                        <i class="fas fa-flask text-primary mr-2"></i>
                                        Informasi Sampel
                                    </h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Nama Sampel:</span>
                                            <span class="font-semibold text-gray-800" x-text="sampleInfo.name"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Jumlah:</span>
                                            <span class="font-semibold text-gray-800" x-text="sampleInfo.quantity"></span>
                                        </div>
                                        <div class="col-span-2">
                                            <span class="text-gray-600">Deskripsi:</span>
                                            <p class="font-semibold text-gray-800 mt-1" x-text="sampleInfo.description"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Testing Information -->
                            <div class="mt-8">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                    <i class="fas fa-microscope text-primary mr-2"></i>
                                    Informasi Pengujian
                                </h3>
                                <div class="grid md:grid-cols-2 gap-8">
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Jenis Pengujian:</span>
                                            <span class="font-semibold text-gray-800" x-text="testingInfo.type_label"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Jadwal Pengantaran Sampel:</span>
                                            <span class="font-semibold text-gray-800" x-text="testingInfo.sample_delivery_schedule"></span>
                                        </div>
                                        <div class="flex justify-between" x-show="testingInfo.urgent_request">
                                            <span class="text-gray-600">Status:</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Mendesak
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex justify-between" x-show="testingInfo.estimated_duration">
                                            <span class="text-gray-600">Estimasi Lama Waktu Pengujian:</span>
                                            <span class="font-semibold text-gray-800" x-text="`${testingInfo.estimated_duration} hari kerja`"></span>
                                        </div>
                                        <div class="flex justify-between" x-show="testingInfo.cost_estimate">
                                            <span class="text-gray-600">Estimasi Biaya:</span>
                                            <span class="font-semibold text-gray-800" x-text="`Rp ${Number(testingInfo.cost_estimate).toLocaleString('id-ID')}`"></span>
                                        </div>
                                        <div class="flex justify-between" x-show="testingInfo.final_cost">
                                            <span class="text-gray-600">Biaya Final:</span>
                                            <span class="font-semibold text-gray-800" x-text="`Rp ${Number(testingInfo.final_cost).toLocaleString('id-ID')}`"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Testing Parameters -->
                                <div x-show="testingInfo.parameters && Object.keys(testingInfo.parameters).length > 0" class="mt-6">
                                    <h4 class="font-semibold text-gray-800 mb-3">Parameter Pengujian:</h4>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <template x-for="[key, value] in Object.entries(testingInfo.parameters || {})" :key="key">
                                            <div class="flex justify-between mb-2">
                                                <span class="text-gray-600 capitalize" x-text="key.replace('_', ' ')"></span>
                                                <span class="font-semibold text-gray-800" x-text="value"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Timeline -->
                        <div class="p-8 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-800 mb-6">
                                <i class="fas fa-timeline text-primary mr-2"></i>
                                Timeline Pengujian
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- Submitted -->
                                <div class="flex items-center" :class="currentStatus !== 'pending' ? 'text-green-600' : 'text-blue-600'">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-4"
                                         :class="currentStatus !== 'pending' ? 'bg-green-100' : 'bg-blue-100'">
                                        <i class="fas fa-paper-plane text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold">Pengajuan Diterima</div>
                                        <div class="text-sm text-gray-600" x-text="`${submittedDate} - Pengajuan berhasil dikirim`"></div>
                                    </div>
                                </div>

                                <!-- Approved -->
                                <div class="flex items-center" :class="['approved', 'in_progress', 'completed'].includes(currentStatus) ? 'text-green-600' : 'text-gray-400'">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-4"
                                         :class="['approved', 'in_progress', 'completed'].includes(currentStatus) ? 'bg-green-100' : 'bg-gray-100'">
                                        <i class="fas fa-check text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold">Persetujuan & Penjadwalan</div>
                                        <div class="text-sm text-gray-600" x-text="['approved', 'in_progress', 'completed'].includes(currentStatus) ? 'Pengajuan disetujui dan dijadwalkan' : 'Menunggu persetujuan admin'"></div>
                                    </div>
                                </div>

                                <!-- In Progress -->
                                <div class="flex items-center" :class="['in_progress', 'completed'].includes(currentStatus) ? 'text-green-600' : 'text-gray-400'">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-4"
                                         :class="['in_progress', 'completed'].includes(currentStatus) ? 'bg-green-100' : 'bg-gray-100'">
                                        <i class="fas fa-microscope text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold">Pengujian Berlangsung</div>
                                        <div class="text-sm text-gray-600" x-text="['in_progress', 'completed'].includes(currentStatus) ? 'Sampel sedang/telah diuji' : 'Belum dimulai'"></div>
                                    </div>
                                </div>

                                <!-- Completed -->
                                <div class="flex items-center" :class="currentStatus === 'completed' ? 'text-green-600' : 'text-gray-400'">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-4"
                                         :class="currentStatus === 'completed' ? 'bg-green-100' : 'bg-gray-100'">
                                        <i class="fas fa-flag-checkered text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold">Selesai & Hasil Tersedia</div>
                                        <div class="text-sm text-gray-600" x-text="currentStatus === 'completed' ? 'Pengujian selesai, hasil dapat diunduh' : 'Belum selesai'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Cards -->
                    <div class="grid md:grid-cols-3 gap-6">
                        
                        <!-- Download Results -->
                        <div class="bg-gray-50 rounded-2xl p-6 text-center hover:bg-gray-100 transition-all duration-300" x-show="currentStatus === 'completed'">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center bg-green-100">
                                <i class="fas fa-download text-2xl text-green-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Download Hasil</h4>
                            <p class="text-sm text-gray-600 mb-4">Hasil pengujian siap diunduh</p>
                            <button @click="downloadResults()"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 bg-green-500 hover:bg-green-600 text-white shadow-lg hover:shadow-xl">
                                Download
                            </button>
                        </div>

                        <!-- Chat WhatsApp Admin -->
                        <div class="bg-green-50 rounded-2xl p-6 text-center hover:bg-green-100 transition-all duration-300">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                 :class="currentStatus === 'cancelled' ? 'bg-gray-200' : 'bg-green-100'">
                                <i class="fab fa-whatsapp text-2xl"
                                   :class="currentStatus === 'cancelled' ? 'text-gray-400' : 'text-green-600'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Chat WhatsApp Admin</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="currentStatus !== 'cancelled'">Chat langsung dengan admin lab</span>
                                <span x-show="currentStatus === 'cancelled'" class="text-red-600 font-semibold">Tidak tersedia - permohonan dibatalkan</span>
                            </p>
                            <button @click="openWhatsAppChat()"
                                    :disabled="currentStatus === 'cancelled' || !canSendMessage"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105"
                                    :class="currentStatus === 'cancelled' ? 
                                           'bg-gray-300 text-gray-500 cursor-not-allowed' :
                                           !canSendMessage ? 
                                           'bg-orange-400 text-white cursor-not-allowed' :
                                           'bg-green-500 hover:bg-green-600 text-white shadow-lg hover:shadow-xl'">
                                <span x-show="currentStatus === 'cancelled'">Tidak Tersedia</span>
                                <span x-show="currentStatus !== 'cancelled' && canSendMessage">
                                    <i class="fab fa-whatsapp mr-2"></i>Chat Admin
                                </span>
                                <span x-show="currentStatus !== 'cancelled' && !canSendMessage" x-text="cooldownText"></span>
                            </button>
                        </div>

                        <!-- Cancel Testing -->
                        <div class="bg-red-50 rounded-2xl p-6 text-center hover:bg-red-100 transition-all duration-300">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                 :class="['in_progress', 'completed', 'cancelled'].includes(currentStatus) ? 'bg-gray-200' : 'bg-red-100'">
                                <i class="fas fa-times text-2xl"
                                   :class="['in_progress', 'completed', 'cancelled'].includes(currentStatus) ? 'text-gray-400' : 'text-red-600'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Batalkan Pengujian</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="['pending', 'approved'].includes(currentStatus)">Dapat dibatalkan sebelum pengujian dimulai</span>
                                <span x-show="['in_progress', 'completed'].includes(currentStatus)">Tidak dapat dibatalkan</span>
                                <span x-show="currentStatus === 'cancelled'" class="text-red-600 font-semibold">Sudah dibatalkan</span>
                            </p>
                            <button @click="cancelTesting()"
                                    :disabled="['in_progress', 'completed', 'cancelled'].includes(currentStatus)"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105"
                                    :class="['in_progress', 'completed', 'cancelled'].includes(currentStatus) ?
                                           'bg-gray-300 text-gray-500 cursor-not-allowed' :
                                           'bg-red-500 hover:bg-red-600 text-white shadow-lg hover:shadow-xl'">
                                <span x-show="['pending', 'approved'].includes(currentStatus)">Batalkan</span>
                                <span x-show="['in_progress', 'completed'].includes(currentStatus)">Tidak Tersedia</span>
                                <span x-show="currentStatus === 'cancelled'">Sudah Dibatalkan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Cancelled Status Message -->
                    <div x-show="currentStatus === 'cancelled'" class="mt-8 p-6 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-red-800">Pengujian Dibatalkan</h3>
                                <p class="text-red-600">Permohonan pengujian telah dibatalkan dan tidak akan diproses lebih lanjut.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <script>
        function trackingTesting() {
            return {
                // Data management
                testingData: null,
                loading: true,
                error: null,
                siteSettings: null,
                adminPhone: null,

                // Cooldown system
                lastMessageTime: 0,
                cooldownDuration: 300000, // 5 minutes in milliseconds
                remainingTime: 0,
                cooldownTimer: null,

                // Initialize component
                async init() {
                    // Wait for LabGOS to be available
                    let attempts = 0;
                    while (!window.LabGOS && attempts < 10) {
                        await new Promise(resolve => setTimeout(resolve, 100));
                        attempts++;
                    }

                    if (!window.LabGOS) {
                        console.error('LabGOS API client not available');
                        this.error = 'Sistem tidak dapat memuat data. Silakan refresh halaman.';
                        this.loading = false;
                        return;
                    }

                    // Load site settings for admin contact
                    await this.loadSiteSettings();

                    // Get testing ID from URL
                    const urlPath = window.location.pathname;
                    const testingIdMatch = urlPath.match(/\/confirmation\/([^\/]+)$/);

                    if (testingIdMatch) {
                        const testingId = testingIdMatch[1];
                        await this.loadTestingData(testingId);
                    } else {
                        // Try to get from sessionStorage (from previous page)
                        const storedData = sessionStorage.getItem('testingTrackingData');
                        if (storedData) {
                            try {
                                const parsed = JSON.parse(storedData);
                                if (parsed.testingId) {
                                    await this.loadTestingData(parsed.testingId);
                                } else {
                                    this.error = 'ID pengujian tidak ditemukan';
                                }
                            } catch (e) {
                                this.error = 'Data tracking tidak valid';
                            }
                        } else {
                            this.error = 'ID pengujian tidak ditemukan';
                        }
                    }

                    // Initialize cooldown system
                    this.initializeCooldown();

                    this.loading = false;
                },

                // Load site settings for admin contact info
                async loadSiteSettings() {
                    try {
                        const response = await window.LabGOS.getSiteSettings();
                        if (response.success) {
                            this.siteSettings = response.data.site_settings;
                            this.adminPhone = response.data.whatsapp_admin_phones?.[0] || null;
                        }
                    } catch (error) {
                        console.error('Failed to load site settings:', error);
                    }
                },

                async loadTestingData(testingId) {
                    try {
                        const response = await window.LabGOS.getTestingRequest(testingId);
                        
                        if (response.success) {
                            this.testingData = response.data;
                        } else {
                            this.error = response.message || 'Pengujian tidak ditemukan';
                        }
                    } catch (error) {
                        console.error('Error loading testing data:', error);
                        this.error = 'Gagal memuat data pengujian';
                    }
                },

                // Cooldown system methods
                initializeCooldown() {
                    const stored = localStorage.getItem(`whatsapp_cooldown_${this.testingId}`);
                    if (stored) {
                        this.lastMessageTime = parseInt(stored);
                        this.updateCooldownStatus();
                    }
                },

                get canSendMessage() {
                    const now = Date.now();
                    return (now - this.lastMessageTime) >= this.cooldownDuration;
                },

                get cooldownText() {
                    if (this.canSendMessage) return 'Chat Admin';
                    const remaining = Math.ceil(this.remainingTime / 1000);
                    const minutes = Math.floor(remaining / 60);
                    const seconds = remaining % 60;
                    return `Tunggu ${minutes}:${seconds.toString().padStart(2, '0')}`;
                },

                updateCooldownStatus() {
                    const now = Date.now();
                    this.remainingTime = Math.max(0, this.cooldownDuration - (now - this.lastMessageTime));
                    
                    if (this.remainingTime > 0 && !this.cooldownTimer) {
                        this.cooldownTimer = setInterval(() => {
                            this.updateCooldownStatus();
                        }, 1000);
                    } else if (this.remainingTime === 0 && this.cooldownTimer) {
                        clearInterval(this.cooldownTimer);
                        this.cooldownTimer = null;
                    }
                },

                startCooldown() {
                    this.lastMessageTime = Date.now();
                    localStorage.setItem(`whatsapp_cooldown_${this.testingId}`, this.lastMessageTime.toString());
                    this.updateCooldownStatus();
                },

                openWhatsAppChat() {
                    // Check cooldown
                    if (!this.canSendMessage) {
                        alert(`Harap tunggu ${this.cooldownText.replace('Tunggu ', '')} sebelum mengirim pesan lagi.`);
                        return;
                    }
                    
                    // Generate tracking URL
                    const trackingUrl = `${window.location.origin}/layanan/testing/confirmation/${this.testingId}`;
                    
                    const message = `Kepada Yang Terhormat,
Admin Laboratorium Gelombang, Optik dan Spektroskopi (GOS)
Departemen Fisika FMIPA Universitas Syiah Kuala

Dengan hormat,

Saya bermaksud untuk melakukan konsultasi terkait permohonan pengujian sampel dengan rincian sebagai berikut:

=== DETAIL PERMOHONAN PENGUJIAN ===
- ID Pengujian: ${this.testingId}
- Nama Klien: ${this.clientInfo.name}
- Organisasi: ${this.clientInfo.organization}
- Nama Sampel: ${this.sampleInfo.name}
- Jenis Pengujian: ${this.testingInfo.type_label}
- Jadwal Pengantaran Sampel: ${this.testingInfo.sample_delivery_schedule}
- Status Permohonan: ${this.currentStatus === 'pending' ? 'Menunggu Persetujuan' : 
                         this.currentStatus === 'approved' ? 'Disetujui & Dijadwalkan' : 
                         this.currentStatus === 'in_progress' ? 'Sedang Diuji' :
                         this.currentStatus === 'completed' ? 'Selesai' :
                         this.currentStatus === 'rejected' ? 'Ditolak' :
                         this.currentStatus === 'cancelled' ? 'Dibatalkan' : this.currentStatus}

Link Tracking (Klik untuk membuka):
${trackingUrl}

Mohon kiranya Bapak/Ibu dapat memberikan informasi lebih lanjut mengenai:
1. Progress pengujian saat ini
2. Estimasi waktu penyelesaian
3. Informasi biaya (jika belum ditetapkan)
4. Prosedur pengambilan hasil
5. Informasi teknis lainnya yang perlu diketahui

Demikian permohonan ini saya sampaikan. Atas perhatian dan kerjasamanya, saya ucapkan terima kasih.

Hormat saya,
${this.clientInfo.name}
${this.clientInfo.organization}
${this.clientInfo.email}

---
Laboratorium GOS - Departemen Fisika FMIPA USK
Email: labgos@usu.ac.id
Jam Operasional: Senin-Jumat, 08:00-16:00 WIB`;

                    // Get admin phone from environment or use fallback
                    const phoneNumber = '{{ str_replace("+", "", env("WHATSAPP_LAB_PHONE", "6285338573726")) }}';
                    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;

                    // Start cooldown after successful message
                    this.startCooldown();
                    
                    window.open(whatsappUrl, '_blank');
                },

                async downloadResults() {
                    if (this.currentStatus !== 'completed') {
                        alert('Hasil pengujian belum tersedia');
                        return;
                    }

                    try {
                        // Create download link (placeholder implementation)
                        const link = document.createElement('a');
                        link.href = `/api/testing/${this.testingId}/results`;
                        link.download = `Hasil_Pengujian_${this.testingId}.pdf`;
                        link.click();

                        alert('Hasil pengujian sedang diunduh...');
                    } catch (error) {
                        alert('Gagal mengunduh hasil pengujian');
                    }
                },

                async cancelTesting() {
                    // Check if testing can be canceled
                    if (['in_progress', 'completed', 'cancelled'].includes(this.currentStatus)) {
                        alert('Pengujian yang sudah dimulai atau selesai tidak dapat dibatalkan. Hubungi admin untuk pembatalan.');
                        return;
                    }

                    if (!confirm('Apakah Anda yakin ingin membatalkan pengujian ini?')) {
                        return;
                    }

                    try {
                        const response = await window.LabGOS.cancelTestingRequest(this.testingId);
                        
                        if (response.success) {
                            alert('Pengujian berhasil dibatalkan');
                            // Reload data to reflect the change
                            await this.loadTestingData(this.testingId);
                        } else {
                            alert('Gagal membatalkan pengujian: ' + response.message);
                        }
                    } catch (error) {
                        console.error('Error canceling testing:', error);
                        alert('Terjadi kesalahan saat membatalkan pengujian');
                    }
                },

                // Computed properties
                get testingId() {
                    return this.testingData?.request_id || '';
                },

                get currentStatus() {
                    return this.testingData?.status || 'pending';
                },

                get submittedDate() {
                    if (!this.testingData?.submitted_at) return '';
                    return new Date(this.testingData.submitted_at).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },

                get progressPercentage() {
                    const statusProgress = {
                        'pending': 10,
                        'approved': 30,
                        'in_progress': 70,
                        'completed': 100,
                        'rejected': 0,
                        'cancelled': 0
                    };
                    return statusProgress[this.currentStatus] || 0;
                },

                get clientInfo() {
                    return this.testingData?.client || {};
                },

                get sampleInfo() {
                    return this.testingData?.sample || {};
                },

                get testingInfo() {
                    return this.testingData?.testing || {};
                }
            }
        }
    </script>
</x-public.layouts.main>