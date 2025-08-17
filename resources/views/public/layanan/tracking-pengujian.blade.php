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
                    Pantau status permohonan pengujian sampel laboratorium Anda
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

            <div x-data="trackingPengujian()" x-init="init()" class="space-y-8">

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
                        <h3 class="text-lg font-bold text-red-800 mb-2">Gagal Memuat Data</h3>
                        <p class="text-red-600 mb-4" x-text="error"></p>
                        <a href="/layanan/pengujian"
                           class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Ajukan Pengujian Baru
                        </a>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div x-show="!loading && !error"
                     x-data="{ animated: false }"
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="bg-white rounded-3xl shadow-2xl overflow-hidden transition-all duration-1000 ease-out">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-primary to-blue-600 px-8 py-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-secondary bg-opacity-20 rounded-full -translate-y-16 translate-x-16"></div>
                        <div class="relative z-10 flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl md:text-3xl font-bold mb-2 flex items-center">
                                    <i class="fas fa-flask mr-3 text-secondary"></i>
                                    Status Pengujian
                                </h2>
                                <p class="text-blue-200 mb-2">
                                    ID Pengujian: <span class="font-bold text-secondary" x-text="testingId"></span>
                                </p>
                                <!-- Status Badge -->
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                     :class="{
                                        'bg-yellow-100 text-yellow-800': currentStatus === 'pending',
                                        'bg-blue-100 text-blue-800': currentStatus === 'approved',
                                        'bg-purple-100 text-purple-800': currentStatus === 'in_progress',
                                        'bg-green-100 text-green-800': currentStatus === 'completed',
                                        'bg-red-100 text-red-800': currentStatus === 'rejected' || currentStatus === 'cancelled'
                                     }">
                                    <span x-show="currentStatus === 'pending'">⏳ Menunggu Review & Quote</span>
                                    <span x-show="currentStatus === 'approved'">✅ Disetujui, Siap Testing</span>
                                    <span x-show="currentStatus === 'in_progress'">🔬 Testing Berlangsung</span>
                                    <span x-show="currentStatus === 'completed'">✅ Testing Selesai</span>
                                    <span x-show="currentStatus === 'rejected'">❌ Ditolak</span>
                                    <span x-show="currentStatus === 'cancelled'">🚫 Dibatalkan</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-blue-200 mb-1">Diajukan pada</div>
                                <div class="font-bold text-secondary" x-text="submittedDate"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="p-8">
                        <div class="relative">
                            <!-- Progress Line -->
                            <div class="absolute left-8 top-0 bottom-0 w-1 bg-gray-200"></div>
                            <div class="absolute left-8 top-0 w-1 bg-gradient-to-b from-primary to-blue-600 transition-all duration-1000 ease-out"
                                 :style="`height: ${getProgressHeight()}%`"></div>

                            <!-- Timeline Items -->
                            <div class="space-y-8">
                                <template x-for="(step, index) in statusSteps" :key="index">
                                    <div class="relative flex items-center">
                                        <!-- Status Icon -->
                                        <div class="flex-shrink-0 w-16 h-16 rounded-full flex items-center justify-center border-4 border-white shadow-lg transition-all duration-500"
                                             :class="getStepClass(step.status)">
                                            <i :class="step.icon" class="text-xl"></i>
                                        </div>

                                        <!-- Status Content -->
                                        <div class="ml-6 flex-1">
                                            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                                                 :class="step.status === 'current' ? 'ring-2 ring-primary ring-opacity-50' : ''">
                                                <div class="flex items-center justify-between mb-2">
                                                    <h3 class="text-lg font-bold text-gray-800" x-text="step.title"></h3>
                                                    <span class="text-xs px-3 py-1 rounded-full font-semibold"
                                                          :class="getStatusBadgeClass(step.status)"
                                                          x-text="getStatusText(step.status)"></span>
                                                </div>
                                                <p class="text-gray-600 text-sm mb-3" x-text="step.description"></p>
                                                <div class="flex items-center text-xs text-gray-500">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <span x-text="getStepTimestamp(step)"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Cancelled Status Message -->
                        <div x-show="currentStatus === 'cancelled'" class="mt-8 p-6 bg-red-50 border border-red-200 rounded-xl">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-ban text-red-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-red-800">Permohonan Dibatalkan</h3>
                                    <p class="text-red-600 text-sm">Permohonan pengujian telah dibatalkan</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-red-100">
                                <p class="text-gray-700 text-sm">
                                    <strong>Informasi:</strong> Permohonan pengujian dengan ID <span x-text="testingId" class="font-mono font-semibold"></span>
                                    telah dibatalkan. Jika Anda masih memerlukan layanan pengujian, silakan ajukan permohonan baru melalui halaman layanan.
                                </p>
                                <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                    <a href="/layanan/pengujian"
                                       class="inline-flex items-center justify-center px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Ajukan Permohonan Baru
                                    </a>
                                    <a href="/"
                                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-home mr-2"></i>
                                        Kembali ke Beranda
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Pengujian -->
                <div x-show="!loading && !error"
                     x-data="{ animated: false }"
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="bg-white rounded-3xl shadow-2xl overflow-hidden transition-all duration-1000 ease-out"
                     style="transition-delay: 0.2s;">

                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-8 py-6 text-white">
                        <h2 class="text-2xl font-bold mb-2 flex items-center">
                            <i class="fas fa-info-circle mr-3"></i>
                            Detail Pengujian
                        </h2>
                        <p class="text-purple-100">Informasi lengkap permohonan pengujian sampel Anda</p>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                            <!-- Info Klien -->
                            <div class="space-y-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-user mr-3 text-purple-600"></i>
                                    Informasi Klien
                                </h3>

                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="text-sm text-gray-600 mb-1">Nama Klien</div>
                                        <div class="font-semibold text-gray-800" x-text="clientInfo.name"></div>
                                    </div>

                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="text-sm text-gray-600 mb-1">Organisasi</div>
                                        <div class="font-semibold text-gray-800" x-text="clientInfo.organization"></div>
                                    </div>

                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="text-sm text-gray-600 mb-1">Email</div>
                                        <div class="font-semibold text-gray-800" x-text="clientInfo.email"></div>
                                    </div>

                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="text-sm text-gray-600 mb-1">No. Telepon</div>
                                        <div class="font-semibold text-gray-800" x-text="clientInfo.phone"></div>
                                    </div>

                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="text-sm text-gray-600 mb-1">Alamat</div>
                                        <div class="font-semibold text-gray-800" x-text="clientInfo.address"></div>
                                    </div>
                                </div>

                                <!-- Jenis Testing -->
                                <div class="bg-purple-50 rounded-xl p-4">
                                    <h4 class="font-semibold text-purple-800 mb-2">Jenis Testing</h4>
                                    <div class="text-sm text-purple-700" x-text="testingInfo.type_label"></div>
                                    <div x-show="testingInfo.urgent_request" class="mt-2">
                                        <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Urgent Request
                                        </span>
                                    </div>
                                </div>

                                <!-- Cost Information -->
                                <div x-show="costInfo.cost" class="bg-green-50 rounded-xl p-4">
                                    <h4 class="font-semibold text-green-800 mb-2">Biaya Testing</h4>
                                    <div class="text-lg font-bold text-green-700" x-text="formatCurrency(costInfo.cost)"></div>
                                </div>
                            </div>

                            <!-- Detail Sampel & Jadwal -->
                            <div class="space-y-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-vial mr-3 text-teal-600"></i>
                                    Informasi Sampel & Jadwal
                                </h3>

                                <!-- Sample Information -->
                                <div class="bg-teal-50 rounded-xl p-4 border border-teal-200">
                                    <h4 class="font-semibold text-teal-800 mb-3">Detail Sampel</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-teal-600">Nama Sampel:</span>
                                            <span class="text-sm font-medium text-teal-800" x-text="sampleInfo.name"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-teal-600">Kuantitas:</span>
                                            <span class="text-sm font-medium text-teal-800" x-text="sampleInfo.quantity"></span>
                                        </div>
                                        <div x-show="sampleInfo.description" class="mt-3">
                                            <span class="text-sm text-teal-600">Deskripsi:</span>
                                            <p class="text-sm text-teal-800 mt-1" x-text="sampleInfo.description"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Schedule Information -->
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="text-sm text-gray-600 mb-1">Jadwal Pengantaran Sampel</div>
                                        <div class="font-semibold text-gray-800" x-text="scheduleInfo.sampleDelivery || 'Belum ditentukan'"></div>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="text-sm text-gray-600 mb-1">Estimasi Durasi</div>
                                        <div class="font-semibold text-gray-800" x-text="getEstimatedDurationText()"></div>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="text-sm text-gray-600 mb-1">Estimasi Selesai</div>
                                        <div class="font-semibold text-gray-800" x-text="scheduleInfo.estimatedCompletion || 'Belum ditentukan'"></div>
                                    </div>
                                    <div x-show="scheduleInfo.actualCompletion" class="bg-green-50 rounded-xl p-4 border border-green-200">
                                        <div class="text-sm text-green-600 mb-1">Tanggal Selesai Aktual</div>
                                        <div class="font-semibold text-green-800" x-text="scheduleInfo.actualCompletion"></div>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-600">Progress Testing</span>
                                        <span class="text-sm font-bold text-gray-800" x-text="progressPercentage + '%'"></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all duration-500"
                                             :style="`width: ${progressPercentage}%`"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testing Parameters -->
                        <div class="mt-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-cogs mr-3 text-purple-600"></i>
                                Parameter Testing
                            </h3>

                            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg">
                                <div x-show="!testingInfo.parameters || Object.keys(testingInfo.parameters).length === 0" class="text-center text-gray-500 py-4">
                                    Parameter testing belum ditentukan
                                </div>
                                <div x-show="testingInfo.parameters && Object.keys(testingInfo.parameters).length > 0" class="space-y-3">
                                    <template x-for="[key, value] in Object.entries(testingInfo.parameters || {})" :key="key">
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                            <span class="text-sm font-medium text-gray-600" x-text="formatParameterKey(key)"></span>
                                            <span class="text-sm text-gray-800" x-text="value"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Results Section -->
                        <div x-show="currentStatus === 'completed' && (resultsInfo.summary || (resultsInfo.files && resultsInfo.files.length > 0))" class="mt-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-clipboard-list mr-3 text-green-600"></i>
                                Hasil Testing
                            </h3>

                            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-lg">
                                <!-- Result Summary -->
                                <div x-show="resultsInfo.summary" class="mb-6">
                                    <h4 class="font-semibold text-gray-800 mb-2">Ringkasan Hasil</h4>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-sm text-gray-700" x-text="resultsInfo.summary"></p>
                                    </div>
                                </div>

                                <!-- Result Files -->
                                <div x-show="resultsInfo.files && resultsInfo.files.length > 0">
                                    <h4 class="font-semibold text-gray-800 mb-3">File Hasil</h4>
                                    <div class="space-y-2">
                                        <template x-for="(file, index) in resultsInfo.files || []" :key="index">
                                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                                <div class="flex items-center">
                                                    <i class="fas fa-file-pdf text-green-600 mr-3"></i>
                                                    <span class="text-sm font-medium text-green-800" x-text="file.name || `Hasil_${index + 1}.pdf`"></span>
                                                </div>
                                                <button @click="downloadResultFile(file)"
                                                        class="text-green-600 hover:text-green-800 transition-colors">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div x-show="!loading && !error"
                     x-data="{ animated: false }"
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="bg-white rounded-3xl shadow-2xl p-8 transition-all duration-1000 ease-out"
                     style="transition-delay: 0.4s;">

                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2 flex items-center justify-center">
                            <i class="fas fa-cogs mr-3 text-purple-600"></i>
                            Aksi Tersedia
                        </h3>
                        <p class="text-gray-600">Pilih aksi yang ingin Anda lakukan</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                        <!-- Download Hasil Pengujian -->
                        <div class="bg-gray-50 rounded-2xl p-6 text-center hover:bg-gray-100 transition-all duration-300">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                 :class="currentStatus === 'completed' ? 'bg-green-100' :
                                         currentStatus === 'cancelled' ? 'bg-gray-200' : 'bg-gray-200'">
                                <i class="fas fa-download text-2xl"
                                   :class="currentStatus === 'completed' ? 'text-green-600' :
                                           currentStatus === 'cancelled' ? 'text-gray-400' : 'text-gray-400'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Download Hasil Pengujian</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="currentStatus === 'completed'">Hasil pengujian siap diunduh</span>
                                <span x-show="currentStatus === 'pending'">Menunggu review & quote</span>
                                <span x-show="currentStatus === 'approved'">Testing disetujui, belum dimulai</span>
                                <span x-show="currentStatus === 'in_progress'">Testing sedang berlangsung</span>
                                <span x-show="currentStatus === 'cancelled'" class="text-red-600 font-semibold">Tidak tersedia - permohonan dibatalkan</span>
                                <span x-show="currentStatus === 'rejected'" class="text-red-600 font-semibold">Tidak tersedia - permohonan ditolak</span>
                            </p>
                            <button @click="downloadResults()"
                                    :disabled="currentStatus !== 'completed'"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105"
                                    :class="currentStatus === 'completed' ?
                                           'bg-green-500 hover:bg-green-600 text-white shadow-lg hover:shadow-xl' :
                                           'bg-gray-300 text-gray-500 cursor-not-allowed'">
                                <span x-show="currentStatus === 'completed'">Download Hasil</span>
                                <span x-show="currentStatus === 'pending'">Belum Tersedia</span>
                                <span x-show="currentStatus === 'approved'">Belum Tersedia</span>
                                <span x-show="currentStatus === 'in_progress'">Belum Tersedia</span>
                                <span x-show="currentStatus === 'cancelled'">Tidak Tersedia</span>
                                <span x-show="currentStatus === 'rejected'">Tidak Tersedia</span>
                            </button>
                        </div>

                        <!-- WhatsApp Konfirmasi -->
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

                        <!-- Cancel Pengujian -->
                        <div class="bg-red-50 rounded-2xl p-6 text-center hover:bg-red-100 transition-all duration-300">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                 :class="['completed', 'cancelled', 'rejected'].includes(currentStatus) ? 'bg-gray-200' : 'bg-red-100'">
                                <i class="fas fa-times text-2xl"
                                   :class="['completed', 'cancelled', 'rejected'].includes(currentStatus) ? 'text-gray-400' : 'text-red-600'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Batalkan Pengujian</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="currentStatus === 'pending'">Dapat dibatalkan sebelum disetujui</span>
                                <span x-show="currentStatus === 'approved'">Dapat dibatalkan sebelum testing dimulai</span>
                                <span x-show="currentStatus === 'in_progress'">Dapat dibatalkan dengan konsultasi admin</span>
                                <span x-show="currentStatus === 'completed'">Tidak dapat dibatalkan - testing selesai</span>
                                <span x-show="currentStatus === 'cancelled'" class="text-red-600 font-semibold">Sudah dibatalkan</span>
                                <span x-show="currentStatus === 'rejected'" class="text-red-600 font-semibold">Sudah ditolak</span>
                            </p>
                            <button @click="cancelTesting()"
                                    :disabled="['completed', 'cancelled', 'rejected'].includes(currentStatus)"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105"
                                    :class="['completed', 'cancelled', 'rejected'].includes(currentStatus) ?
                                           'bg-gray-300 text-gray-500 cursor-not-allowed' :
                                           'bg-red-500 hover:bg-red-600 text-white shadow-lg hover:shadow-xl'">
                                <span x-show="['pending', 'approved', 'in_progress'].includes(currentStatus)">Batalkan</span>
                                <span x-show="currentStatus === 'completed'">Tidak Tersedia</span>
                                <span x-show="currentStatus === 'cancelled'">Sudah Dibatalkan</span>
                                <span x-show="currentStatus === 'rejected'">Sudah Ditolak</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Back to Home -->
                <div class="text-center">
                    <a href="/"
                       class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-primary to-blue-600 hover:from-blue-600 hover:to-primary text-white rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Beranda
                    </a>
                </div>

            </div>
        </div>
    </section>

    <script>
        function trackingPengujian() {
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
                                const data = JSON.parse(storedData);
                                await this.loadTestingData(data.requestId);
                            } catch (error) {
                                console.error('Failed to parse stored testing tracking data:', error);
                                this.error = 'Gagal memuat data tracking';
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
                            this.siteSettings = response.data;
                            this.extractAdminPhone();
                        }
                    } catch (error) {
                        console.error('Failed to load site settings:', error);
                    }
                },

                // Extract admin phone from site settings
                extractAdminPhone() {
                    // Use WhatsApp admin phone directly since it's the most accurate
                    const whatsappAdminPhones = this.siteSettings?.whatsapp_admin_phones;

                    if (whatsappAdminPhones && whatsappAdminPhones.length > 0) {
                        // Use first WhatsApp admin phone
                        let adminPhone = whatsappAdminPhones[0];
                        // Format phone number for WhatsApp (remove +, spaces, dashes)
                        this.adminPhone = adminPhone.replace(/[\s\-\+]/g, '');
                        // Ensure it starts with country code
                        if (!this.adminPhone.startsWith('62') && this.adminPhone.startsWith('08')) {
                            this.adminPhone = '62' + this.adminPhone.substring(1);
                        }
                        return;
                    }

                    // Fallback to site settings if WhatsApp admin phones not available
                    if (!this.siteSettings) return;
                    const siteSettings = this.siteSettings.site_settings || {};

                    // Try to get from technical_contact first, then lab_head
                    let adminPhone = null;

                    if (siteSettings.technical_contact) {
                        try {
                            const technicalContact = JSON.parse(siteSettings.technical_contact);
                            adminPhone = technicalContact.phone;
                        } catch (e) {
                            console.error('Failed to parse technical_contact:', e);
                        }
                    }

                    if (!adminPhone && siteSettings.lab_head) {
                        try {
                            const labHead = JSON.parse(siteSettings.lab_head);
                            adminPhone = labHead.phone;
                        } catch (e) {
                            console.error('Failed to parse lab_head:', e);
                        }
                    }

                    // Format phone number for WhatsApp (remove +, spaces, dashes)
                    if (adminPhone) {
                        this.adminPhone = adminPhone.replace(/[\s\-\+]/g, '');
                        // Ensure it starts with country code
                        if (!this.adminPhone.startsWith('62') && this.adminPhone.startsWith('08')) {
                            this.adminPhone = '62' + this.adminPhone.substring(1);
                        }
                    }
                },

                // Load testing data from API
                async loadTestingData(testingId) {
                    try {
                        const response = await window.LabGOS.getTestingRequest(testingId);
                        if (response.success) {
                            this.testingData = response.data;
                            this.setupStatusSteps();
                        } else {
                            this.error = response.message || 'Gagal memuat data pengujian';
                        }
                    } catch (error) {
                        console.error('Failed to load testing data:', error);
                        this.error = 'Terjadi kesalahan saat memuat data pengujian';
                    }
                },

                // Computed properties for data access
                get testingId() {
                    return this.testingData?.request_id || '';
                },

                get submittedDate() {
                    if (!this.testingData?.submitted_at) return '';

                    // Fix time display issue - Laravel returns UTC time, convert to Jakarta time
                    const submittedAtUTC = this.testingData.submitted_at.replace(' ', 'T') + 'Z';
                    const submittedDate = new Date(submittedAtUTC);

                    // Format in Indonesian locale with Jakarta timezone
                    const dateString = submittedDate.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                        timeZone: 'Asia/Jakarta'
                    });

                    const timeString = submittedDate.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                        timeZone: 'Asia/Jakarta'
                    });

                    return `${dateString} pukul ${timeString}`;
                },

                get currentStatus() {
                    return this.testingData?.status || 'pending';
                },

                get progressPercentage() {
                    return this.testingData?.progress_percentage || 0;
                },

                get clientInfo() {
                    if (!this.testingData) return {};
                    return {
                        name: this.testingData.client?.name || '',
                        organization: this.testingData.client?.organization || '',
                        email: this.testingData.client?.email || '',
                        phone: this.testingData.client?.phone || '',
                        address: this.testingData.client?.address || ''
                    };
                },

                get sampleInfo() {
                    if (!this.testingData) return {};
                    return {
                        name: this.testingData.sample?.name || '',
                        description: this.testingData.sample?.description || '',
                        quantity: this.testingData.sample?.quantity || ''
                    };
                },

                get testingInfo() {
                    if (!this.testingData) return {};
                    return {
                        type: this.testingData.testing?.type || '',
                        type_label: this.testingData.testing?.type_label || '',
                        parameters: this.testingData.testing?.parameters || {},
                        urgent_request: this.testingData.testing?.urgent_request || false
                    };
                },

                get scheduleInfo() {
                    if (!this.testingData) return {};

                    const formatDate = (dateStr) => {
                        if (!dateStr) return '';
                        return new Date(dateStr).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                    };

                    return {
                        sampleDelivery: formatDate(this.testingData.schedule?.sample_delivery_schedule),
                        estimatedCompletion: formatDate(this.testingData.schedule?.estimated_completion_date),
                        actualCompletion: formatDate(this.testingData.schedule?.completion_date),
                        estimatedDuration: this.testingData.schedule?.estimated_duration || 0
                    };
                },

                get costInfo() {
                    if (!this.testingData) return {};
                    return {
                        cost: this.testingData.cost?.cost || 0
                    };
                },

                get resultsInfo() {
                    if (!this.testingData) return {};
                    return {
                        summary: this.testingData.results?.summary || '',
                        files: this.testingData.results?.files || []
                    };
                },

                // Helper methods
                formatCurrency(amount) {
                    if (!amount) return 'Belum ditentukan';
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    }).format(amount);
                },

                getEstimatedDurationText() {
                    const duration = this.scheduleInfo.estimatedDuration;
                    if (!duration) return 'Belum ditentukan';

                    if (duration === 1) return '1 hari';
                    return `${duration} hari`;
                },

                formatParameterKey(key) {
                    const keyMap = {
                        'wavenumber_range': 'Rentang Wavenumber',
                        'sample_preparation': 'Preparasi Sampel',
                        'wavelength_range': 'Rentang Wavelength',
                        'solvent_used': 'Pelarut yang Digunakan',
                        'magnification': 'Perbesaran',
                        'illumination_type': 'Jenis Penerangan',
                        'custom_parameters': 'Parameter Khusus'
                    };
                    return keyMap[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                },

                setupStatusSteps() {
                    if (!this.testingData) return;

                    // Reset to base steps first for testing workflow
                    const baseSteps = [
                        {
                            title: 'Permohonan Dikirim',
                            description: 'Permohonan telah dikirim dan menunggu persetujuan admin. Mohon tunggu konfirmasi dari kami sebelum mengirimkan sampel.',
                            icon: 'fas fa-paper-plane',
                            status: 'completed',
                            timestamp: this.submittedDate
                        },
                        {
                            title: 'Menunggu Sampel',
                            description: 'Permohonan telah disetujui. Silakan antar sampel ke Jurusan Fisika sesuai jadwal yang ditentukan.',
                            icon: 'fas fa-check-circle',
                            status: 'pending',
                            timestamp: null
                        },
                        {
                            title: 'Sampel Diterima',
                            description: 'Sampel telah diterima. Tim kami sedang meninjau sampel dan menyiapkan proses pengujian.',
                            icon: 'fas fa-box',
                            status: 'pending',
                            timestamp: null
                        },
                        {
                            title: 'Pengujian Berlangsung',
                            description: 'Proses pengujian sampel sedang berlangsung di laboratorium.',
                            icon: 'fas fa-flask',
                            status: 'pending',
                            timestamp: null
                        },
                        {
                            title: 'Hasil Siap',
                            description: 'Hasil pengujian telah tersedia. Anda dapat melihat atau mengunduh laporannya.',
                            icon: 'fas fa-clipboard-check',
                            status: 'pending',
                            timestamp: null
                        }
                    ];

                    // Update status based on current testing status
                    switch(this.currentStatus) {
                        case 'pending':
                        case 'under_review':
                            // Step 1: Permohonan Dikirim (current)
                            baseSteps[1].status = 'current';
                            break;
                        case 'approved':
                            // Step 2: Menunggu Sampel (current)
                            baseSteps[1].status = 'completed';
                            baseSteps[2].status = 'current';
                            break;
                        case 'sample_received':
                            // Step 3: Sampel Diterima (current)
                            baseSteps[1].status = 'completed';
                            baseSteps[2].status = 'completed';
                            baseSteps[3].status = 'current';
                            break;
                        case 'testing':
                        case 'in_progress':
                            // Step 4: Pengujian Berlangsung (current)
                            baseSteps[1].status = 'completed';
                            baseSteps[2].status = 'completed';
                            baseSteps[3].status = 'completed';
                            baseSteps[4].status = 'current';
                            break;
                        case 'completed':
                            // Step 5: Hasil Siap (completed)
                            baseSteps[1].status = 'completed';
                            baseSteps[2].status = 'completed';
                            baseSteps[3].status = 'completed';
                            baseSteps[4].status = 'completed';
                            break;
                        case 'rejected':
                            // For rejected, show rejection at review stage (Step 1)
                            baseSteps[1].status = 'rejected';
                            baseSteps[1].title = 'Permohonan Ditolak';
                            baseSteps[1].description = 'Permohonan pengujian ditolak oleh admin. Silakan ajukan permohonan baru.';
                            baseSteps[1].icon = 'fas fa-times-circle';
                            baseSteps[1].timestamp = this.formatTimestamp(this.testingData.reviewed_at) || 'Ditolak pada ' + this.submittedDate;
                            // Mark remaining steps as skipped
                            for (let i = 2; i < baseSteps.length; i++) {
                                baseSteps[i].status = 'skipped';
                                baseSteps[i].description = 'Tahap ini tidak akan dilaksanakan karena permohonan ditolak.';
                                baseSteps[i].icon = 'fas fa-ban';
                            }
                            break;
                        case 'cancelled':
                            // For cancelled, show cancellation at review stage (Step 1)
                            baseSteps[1].status = 'cancelled';
                            baseSteps[1].title = 'Permohonan Dibatalkan';
                            baseSteps[1].description = 'Permohonan pengujian telah dibatalkan oleh pemohon.';
                            baseSteps[1].icon = 'fas fa-ban';
                            baseSteps[1].timestamp = this.formatTimestamp(this.testingData.reviewed_at) || 'Dibatalkan pada ' + this.submittedDate;
                            // Mark remaining steps as skipped
                            for (let i = 2; i < baseSteps.length; i++) {
                                baseSteps[i].status = 'skipped';
                                baseSteps[i].description = 'Tahap ini tidak akan dilaksanakan karena permohonan dibatalkan.';
                                baseSteps[i].icon = 'fas fa-ban';
                            }
                            break;
                        default:
                            baseSteps[1].status = 'current';
                    }

                    this.statusSteps = baseSteps;
                },

                // Status helper methods

                formatTimestamp(timestamp) {
                    if (!timestamp) return null;

                    // Fix time display issue - Laravel returns UTC time, convert to Jakarta time
                    const timestampUTC = timestamp.replace(' ', 'T') + 'Z';
                    const date = new Date(timestampUTC);

                    // Format in Indonesian locale with Jakarta timezone
                    const dateString = date.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                        timeZone: 'Asia/Jakarta'
                    });

                    const timeString = date.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                        timeZone: 'Asia/Jakarta'
                    });

                    return `${dateString} pukul ${timeString}`;
                },

                getPurposeText(purpose) {
                    const purposes = {
                        'study-visit': 'Kunjungan Studi',
                        'research': 'Penelitian',
                        'learning': 'Pembelajaran',
                        'internship': 'Magang',
                        'others': 'Lainnya'
                    };
                    return purposes[purpose] || purpose;
                },

                getProgressHeight() {
                    const completedSteps = this.statusSteps.filter(step => step.status === 'completed').length;
                    const currentStep = this.statusSteps.findIndex(step => step.status === 'current');
                    const rejectedStep = this.statusSteps.findIndex(step => step.status === 'rejected');
                    const cancelledStep = this.statusSteps.findIndex(step => step.status === 'cancelled');

                    // If there's a rejected or cancelled step, progress goes to that step
                    if (rejectedStep >= 0 || cancelledStep >= 0) {
                        const finalStep = Math.max(rejectedStep, cancelledStep);
                        return ((finalStep + 1) / this.statusSteps.length) * 100;
                    }

                    // Normal progress calculation
                    const totalProgress = completedSteps + (currentStep >= 0 ? 0.5 : 0);
                    return (totalProgress / this.statusSteps.length) * 100;
                },

                getStepClass(status) {
                    switch(status) {
                        case 'completed':
                            return 'bg-green-500 text-white';
                        case 'current':
                            return 'bg-primary text-white animate-pulse';
                        case 'rejected':
                            return 'bg-red-500 text-white';
                        case 'cancelled':
                            return 'bg-orange-500 text-white';
                        case 'skipped':
                            return 'bg-gray-400 text-gray-300';
                        case 'pending':
                            return 'bg-gray-300 text-gray-500';
                        default:
                            return 'bg-gray-300 text-gray-500';
                    }
                },

                getStatusBadgeClass(status) {
                    switch(status) {
                        case 'completed':
                            return 'bg-green-100 text-green-800';
                        case 'current':
                            return 'bg-blue-100 text-blue-800';
                        case 'rejected':
                            return 'bg-red-100 text-red-800';
                        case 'cancelled':
                            return 'bg-orange-100 text-orange-800';
                        case 'skipped':
                            return 'bg-gray-100 text-gray-500';
                        case 'pending':
                            return 'bg-gray-100 text-gray-600';
                        default:
                            return 'bg-gray-100 text-gray-600';
                    }
                },

                getStatusText(status) {
                    switch(status) {
                        case 'completed':
                            return 'Selesai';
                        case 'current':
                            return 'Sedang Proses';
                        case 'rejected':
                            return 'Ditolak';
                        case 'cancelled':
                            return 'Dibatalkan';
                        case 'skipped':
                            return 'Tidak Berlaku';
                        case 'pending':
                            return 'Menunggu';
                        default:
                            return 'Menunggu';
                    }
                },

                getStepTimestamp(step) {
                    switch(step.status) {
                        case 'completed':
                        case 'current':
                        case 'rejected':
                        case 'cancelled':
                            return step.timestamp || this.submittedDate;
                        case 'skipped':
                            return 'Tidak akan dilaksanakan';
                        case 'pending':
                        default:
                            return 'Menunggu...';
                    }
                },

                // Actions
                downloadResults() {
                    if (this.currentStatus !== 'completed') {
                        alert('Hasil pengujian hanya dapat diunduh setelah testing selesai.');
                        return;
                    }

                    // Check if results are available
                    if (!this.resultsInfo.files || this.resultsInfo.files.length === 0) {
                        alert('File hasil pengujian belum tersedia. Hubungi admin untuk informasi lebih lanjut.');
                        return;
                    }

                    // Download the main result file
                    const mainFile = this.resultsInfo.files[0];
                    this.downloadResultFile(mainFile);
                },

                downloadResultFile(file) {
                    if (!file || !file.url) {
                        alert('File tidak tersedia atau rusak.');
                        return;
                    }

                    // Create download link
                    const link = document.createElement('a');
                    link.href = file.url;
                    link.download = file.name || `Hasil_Pengujian_${this.testingId}.pdf`;
                    link.click();

                    alert('File hasil pengujian sedang diunduh...');
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

Saya bermaksud untuk melakukan konsultasi terkait permohonan pengujian sampel laboratorium dengan rincian sebagai berikut:

=== DETAIL PERMOHONAN PENGUJIAN ===
- ID Pengujian: ${this.testingId}
- Nama Klien: ${this.clientInfo.name}
- Organisasi: ${this.clientInfo.organization}
- Email: ${this.clientInfo.email}
- Telepon: ${this.clientInfo.phone}
- Jenis Testing: ${this.testingInfo.type_label}
- Nama Sampel: ${this.sampleInfo.name}
- Deskripsi Sampel: ${this.sampleInfo.description}
- Kuantitas Sampel: ${this.sampleInfo.quantity}
- Status Permohonan: ${this.currentStatus === 'pending' ? 'Menunggu Review & Quote' :
                         this.currentStatus === 'approved' ? 'Disetujui, Siap Testing' :
                         this.currentStatus === 'in_progress' ? 'Testing Berlangsung' :
                         this.currentStatus === 'completed' ? 'Testing Selesai' :
                         this.currentStatus === 'rejected' ? 'Ditolak' :
                         this.currentStatus === 'cancelled' ? 'Dibatalkan' : this.currentStatus}
${this.testingInfo.urgent_request ? '- URGENT REQUEST: Ya' : ''}

Link Tracking (Klik untuk membuka):
${trackingUrl}

Mohon kiranya Bapak/Ibu dapat memberikan informasi lebih lanjut mengenai:
1. Status dan progress pengujian sampel
2. Estimasi waktu penyelesaian testing
3. Prosedur pengambilan hasil pengujian
4. Biaya testing dan metode pembayaran
5. Informasi lain yang perlu diketahui

Demikian permohonan ini saya sampaikan. Atas perhatian dan kerjasamanya, saya ucapkan terima kasih.

Hormat saya,
${this.clientInfo.name}
${this.clientInfo.organization}

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

                async cancelTesting() {
                    if (['completed', 'cancelled', 'rejected'].includes(this.currentStatus)) {
                        alert('Pengujian dengan status ini tidak dapat dibatalkan. Hubungi admin untuk informasi lebih lanjut.');
                        return;
                    }

                    const confirmation = confirm('Apakah Anda yakin ingin membatalkan permohonan pengujian ini?\n\nPermohonan yang sudah dibatalkan tidak dapat dikembalikan.');

                    if (confirmation) {
                        try {
                            // Show loading state
                            const originalText = event.target.innerHTML;
                            event.target.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Membatalkan...';
                            event.target.disabled = true;

                            // Call cancel API
                            const response = await window.LabGOS.cancelTestingRequest(this.testingId);

                            if (response.success) {
                                // Show success message
                                alert('✅ Permohonan pengujian berhasil dibatalkan.\n\nHalaman tracking akan ditutup dan Anda akan diarahkan ke halaman pengujian.');

                                // Clear tracking data from session storage
                                sessionStorage.removeItem('testingTrackingData');

                                // Redirect to testing request page
                                window.location.href = '/layanan/pengujian';

                            } else {
                                // Show error message from API
                                alert('❌ ' + (response.message || 'Gagal membatalkan permohonan.'));

                                // Restore button
                                event.target.innerHTML = originalText;
                                event.target.disabled = false;
                            }

                        } catch (error) {
                            console.error('Error canceling testing:', error);

                            // Show user-friendly error message
                            let errorMessage = 'Terjadi kesalahan saat membatalkan permohonan.';
                            if (error.response && error.response.data && error.response.data.message) {
                                errorMessage = error.response.data.message;
                            }

                            alert('❌ ' + errorMessage + '\n\nSilakan coba lagi atau hubungi admin jika masalah berlanjut.');

                            // Restore button
                            event.target.innerHTML = originalText;
                            event.target.disabled = false;
                        }
                    }
                },

            }
        }
    </script>

</x-public.layouts.main>
