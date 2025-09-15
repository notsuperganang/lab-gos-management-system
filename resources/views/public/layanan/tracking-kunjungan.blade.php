<x-public.layouts.main>
    {{-- Mendefinisikan judul halaman untuk layout --}}
    <x-slot:title>
        Tracking Kunjungan - Lab GOS USK
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
                        🏛️ Tracking
                        <span class="text-secondary">Kunjungan</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Pantau status permohonan kunjungan laboratorium Anda
                </p>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-2 inline-block">
                    <p class="text-white flex items-center justify-center">
                        <i class="fas fa-search mr-2 text-secondary"></i>
                        Status Kunjungan Real-time
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div x-data="trackingKunjungan()" x-init="init()" class="space-y-8">

                <!-- Loading State -->
                <div x-show="loading" class="text-center py-20">
                    <div class="inline-flex items-center px-6 py-3 bg-white rounded-xl shadow-lg">
                        <i class="fas fa-spinner fa-spin text-primary text-xl mr-3"></i>
                        <span class="text-gray-700 font-semibold">Memuat data kunjungan...</span>
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
                        <a href="/tracking/kunjungan"
                           class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Cari Kunjungan Lain
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
                                    <i class="fas fa-users mr-3 text-secondary"></i>
                                    Status Kunjungan
                                </h2>
                                <p class="text-blue-200 mb-2">
                                    ID Kunjungan: <span class="font-bold text-secondary" x-text="visitId"></span>
                                </p>
                                <!-- Status Badge -->
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                     :class="{
                                        'bg-yellow-100 text-yellow-800': currentStatus === 'pending' || currentStatus === 'under_review',
                                        'bg-green-100 text-green-800': currentStatus === 'approved' || currentStatus === 'ready',
                                        'bg-blue-100 text-blue-800': currentStatus === 'completed',
                                        'bg-red-100 text-red-800': currentStatus === 'rejected' || currentStatus === 'cancelled'
                                     }">
                                    <span x-show="currentStatus === 'pending'">⏳ Menunggu Review</span>
                                    <span x-show="currentStatus === 'under_review'">🔍 Sedang Direview</span>
                                    <span x-show="currentStatus === 'approved'">✅ Disetujui</span>
                                    <span x-show="currentStatus === 'ready'">🏛️ Siap Dikunjungi</span>
                                    <span x-show="currentStatus === 'completed'">✅ Selesai</span>
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
                                                <p class="text-gray-600 text-sm mb-3" x-html="step.description"></p>
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
                                    <p class="text-red-600 text-sm">Permohonan kunjungan telah dibatalkan</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-red-100">
                                <p class="text-gray-700 text-sm">
                                    <strong>Informasi:</strong> Permohonan kunjungan dengan ID <span x-text="visitId" class="font-mono font-semibold"></span>
                                    telah dibatalkan. Jika Anda masih memerlukan kunjungan laboratorium, silakan ajukan permohonan baru melalui halaman utama.
                                </p>
                                <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                    <a href="/layanan/kunjungan"
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

                <!-- Detail Kunjungan -->
                <div x-show="!loading && !error"
                     x-data="{ animated: false }"
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="bg-white rounded-3xl shadow-2xl overflow-hidden transition-all duration-1000 ease-out"
                     style="transition-delay: 0.2s;">

                    <div class="bg-gradient-to-r from-secondary to-yellow-500 px-8 py-6 text-gray-800">
                        <h2 class="text-2xl font-bold mb-2 flex items-center">
                            <i class="fas fa-info-circle mr-3"></i>
                            Detail Kunjungan
                        </h2>
                        <p class="text-gray-700">Informasi lengkap permohonan kunjungan Anda</p>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                            <!-- Info Pengunjung -->
                            <div class="space-y-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-user mr-3 text-primary"></i>
                                    Informasi Pengunjung
                                </h3>

                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="text-sm text-gray-600 mb-1">Nama Penanggung Jawab</div>
                                        <div class="font-semibold text-gray-800" x-text="visitorInfo.fullName"></div>
                                    </div>

                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="text-sm text-gray-600 mb-1">Instansi/Organisasi</div>
                                        <div class="font-semibold text-gray-800" x-text="visitorInfo.institution"></div>
                                    </div>

                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="text-sm text-gray-600 mb-1">Email</div>
                                        <div class="font-semibold text-gray-800" x-text="visitorInfo.email"></div>
                                    </div>

                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="text-sm text-gray-600 mb-1">No. Telepon</div>
                                        <div class="font-semibold text-gray-800" x-text="visitorInfo.phone"></div>
                                    </div>
                                </div>

                                <!-- Tujuan Kunjungan -->
                                <div class="bg-blue-50 rounded-xl p-4">
                                    <h4 class="font-semibold text-blue-800 mb-2">Tujuan Kunjungan</h4>
                                    <div class="text-sm text-blue-700" x-text="getPurposeText(visitorInfo.purpose)"></div>
                                </div>

                                <!-- Surat Permohonan -->
                                <div class="bg-purple-50 rounded-xl p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-semibold text-purple-800 mb-1 flex items-center">
                                                <i class="fas fa-file-pdf mr-2"></i>
                                                Surat Permohonan
                                            </h4>
                                            <div class="text-sm text-purple-700" x-text="getLetterDisplayText()"></div>
                                        </div>
                                        <button x-show="hasRequestLetter()"
                                                @click="previewRequestLetter()"
                                                class="w-8 h-8 bg-purple-100 hover:bg-purple-200 text-purple-600 hover:text-purple-700 rounded-full flex items-center justify-center transition-all duration-200 transform hover:scale-110 group"
                                                title="Lihat surat permohonan yang telah dikirim">
                                            <i class="fas fa-eye text-sm group-hover:animate-pulse"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Jadwal -->
                            <div class="space-y-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-calendar-alt mr-3 text-green-600"></i>
                                    Jadwal Kunjungan
                                </h3>

                                <div class="grid grid-cols-1 gap-4">
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="text-sm text-gray-600 mb-1">Tanggal Kunjungan</div>
                                        <div class="font-semibold text-gray-800" x-text="scheduleInfo.visitDate"></div>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="text-sm text-gray-600 mb-1">Waktu Kunjungan</div>
                                        <div class="font-semibold text-gray-800" x-text="scheduleInfo.visitTime"></div>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="text-sm text-gray-600 mb-1">Jumlah Peserta</div>
                                        <div class="font-semibold text-gray-800">
                                            <span x-text="scheduleInfo.participants"></span> orang
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="text-sm text-gray-600 mb-1">Durasi</div>
                                        <div class="font-semibold text-gray-800" x-text="getVisitDurationText()"></div>
                                    </div>
                                </div>

                                <!-- Catatan -->
                                <div class="bg-green-50 rounded-xl p-4" x-show="scheduleInfo.additionalNotes">
                                    <h4 class="font-semibold text-green-800 mb-2">Catatan Tambahan</h4>
                                    <p class="text-sm text-green-700" x-text="scheduleInfo.additionalNotes"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Agenda Kunjungan -->
                        <div class="mt-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-list-check mr-3 text-secondary"></i>
                                Agenda Kunjungan
                            </h3>

                            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-lg">
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gradient-to-r from-primary to-blue-600 text-white">
                                            <tr>
                                                <th class="px-6 py-4 text-left text-sm font-semibold">No.</th>
                                                <th class="px-6 py-4 text-left text-sm font-semibold">Aktivitas</th>
                                                <th class="px-6 py-4 text-left text-sm font-semibold">Lokasi</th>
                                                <th class="px-6 py-4 text-center text-sm font-semibold">Durasi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <template x-for="(agenda, index) in visitAgenda" :key="agenda.id">
                                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                    <td class="px-6 py-4 text-sm">
                                                        <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center font-bold text-xs">
                                                            <span x-text="index + 1"></span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm font-medium text-gray-900" x-text="agenda.activity"></div>
                                                        <div class="text-xs text-gray-500" x-text="agenda.description"></div>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-700" x-text="agenda.location"></td>
                                                    <td class="px-6 py-4 text-center">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                                                              x-text="agenda.duration"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
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

                        <!-- Download Surat -->
                        <div class="bg-gray-50 rounded-2xl p-6 text-center hover:bg-gray-100 transition-all duration-300">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                 :class="(currentStatus === 'approved' || currentStatus === 'ready') ? 'bg-green-100' :
                                         currentStatus === 'cancelled' ? 'bg-gray-200' : 'bg-gray-200'">
                                <i class="fas fa-download text-2xl"
                                   :class="(currentStatus === 'approved' || currentStatus === 'ready') ? 'text-green-600' :
                                           currentStatus === 'cancelled' ? 'text-gray-400' : 'text-gray-400'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Download Surat</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="currentStatus === 'approved' || currentStatus === 'ready'">Surat izin siap diunduh</span>
                                <span x-show="currentStatus === 'pending' || currentStatus === 'under_review'">Menunggu persetujuan</span>
                                <span x-show="currentStatus === 'cancelled'" class="text-red-600 font-semibold">Tidak tersedia - permohonan dibatalkan</span>
                                <span x-show="['completed', 'rejected'].includes(currentStatus)">Tidak tersedia</span>
                            </p>
                            <button @click="downloadLetter()"
                                    :disabled="currentStatus !== 'approved' && currentStatus !== 'ready'"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105"
                                    :class="(currentStatus === 'approved' || currentStatus === 'ready') ?
                                           'bg-green-500 hover:bg-green-600 text-white shadow-lg hover:shadow-xl' :
                                           'bg-gray-300 text-gray-500 cursor-not-allowed'">
                                <span x-show="currentStatus === 'approved' || currentStatus === 'ready'">Download</span>
                                <span x-show="currentStatus === 'pending' || currentStatus === 'under_review'">Belum Tersedia</span>
                                <span x-show="currentStatus === 'cancelled'">Tidak Tersedia</span>
                                <span x-show="['completed', 'rejected'].includes(currentStatus)">Tidak Tersedia</span>
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

                        <!-- Cancel Kunjungan -->
                        <div class="bg-red-50 rounded-2xl p-6 text-center hover:bg-red-100 transition-all duration-300">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                 :class="['approved', 'ready', 'completed', 'cancelled'].includes(currentStatus) ? 'bg-gray-200' : 'bg-red-100'">
                                <i class="fas fa-times text-2xl"
                                   :class="['approved', 'ready', 'completed', 'cancelled'].includes(currentStatus) ? 'text-gray-400' : 'text-red-600'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Batalkan Kunjungan</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="currentStatus === 'pending' || currentStatus === 'under_review'">Dapat dibatalkan sebelum disetujui</span>
                                <span x-show="['approved', 'ready', 'completed'].includes(currentStatus)">Tidak dapat dibatalkan</span>
                                <span x-show="currentStatus === 'cancelled'" class="text-red-600 font-semibold">Sudah dibatalkan</span>
                            </p>
                            <button @click="cancelVisit()"
                                    :disabled="['approved', 'ready', 'completed', 'cancelled'].includes(currentStatus)"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105"
                                    :class="['approved', 'ready', 'completed', 'cancelled'].includes(currentStatus) ?
                                           'bg-gray-300 text-gray-500 cursor-not-allowed' :
                                           'bg-red-500 hover:bg-red-600 text-white shadow-lg hover:shadow-xl'">
                                <span x-show="currentStatus === 'pending' || currentStatus === 'under_review'">Batalkan</span>
                                <span x-show="['approved', 'ready', 'completed'].includes(currentStatus)">Tidak Tersedia</span>
                                <span x-show="currentStatus === 'cancelled'">Sudah Dibatalkan</span>
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
        function trackingKunjungan() {
            return {
                // Data management
                visitData: null,
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

                    // Get visit ID from URL
                    const urlPath = window.location.pathname;
                    const visitIdMatch = urlPath.match(/\/confirmation\/([^\/]+)$/);

                    if (visitIdMatch) {
                        const visitId = visitIdMatch[1];
                        await this.loadVisitData(visitId);
                    } else {
                        // Try to get from sessionStorage (from previous page)
                        const storedData = sessionStorage.getItem('visitTrackingData');
                        if (storedData) {
                            try {
                                const data = JSON.parse(storedData);
                                await this.loadVisitData(data.requestId);
                            } catch (error) {
                                console.error('Failed to parse stored visit tracking data:', error);
                                this.error = 'Gagal memuat data tracking';
                            }
                        } else {
                            this.error = 'ID kunjungan tidak ditemukan';
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

                    // Get phone from site settings
                    if (!this.siteSettings) {
                        console.warn('No site settings available for phone extraction');
                        return;
                    }
                    const siteSettings = this.siteSettings.site_settings || {};

                    console.log('=== PHONE EXTRACTION DEBUG ===');
                    console.log('Site settings keys:', Object.keys(siteSettings));
                    console.log('Phone field value:', siteSettings.phone);
                    console.log('WhatsApp admin phone:', siteSettings.whatsapp_admin_phone);

                    // Try to get phone in this priority order:
                    // 1. Direct phone key from site settings
                    // 2. WhatsApp admin phone if available
                    // 3. Technical contact phone
                    // 4. Lab head phone
                    let adminPhone = null;

                    // Check phone field (with content property for site settings structure)
                    if (siteSettings.phone && siteSettings.phone.content) {
                        adminPhone = siteSettings.phone.content;
                        console.log('Found phone in phone.content:', adminPhone);
                    } else if (siteSettings.phone && typeof siteSettings.phone === 'string') {
                        adminPhone = siteSettings.phone;
                        console.log('Found phone as string:', adminPhone);
                    }

                    // Fallback to whatsapp_admin_phone
                    if (!adminPhone && siteSettings.whatsapp_admin_phone) {
                        adminPhone = siteSettings.whatsapp_admin_phone.content || siteSettings.whatsapp_admin_phone;
                        console.log('Using WhatsApp admin phone fallback:', adminPhone);
                    }

                    if (!adminPhone && siteSettings.technical_contact) {
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
                        console.log('Raw admin phone before formatting:', adminPhone);
                        this.adminPhone = adminPhone.replace(/[\s\-\+]/g, '');

                        // Ensure it starts with country code
                        if (!this.adminPhone.startsWith('62') && this.adminPhone.startsWith('08')) {
                            console.log('Converting 08 to 62 format');
                            this.adminPhone = '62' + this.adminPhone.substring(1);
                        }

                        console.log('Final formatted admin phone:', this.adminPhone);
                    } else {
                        console.warn('No admin phone found in site settings');
                    }
                    console.log('=== PHONE EXTRACTION DEBUG END ===');
                },

                // Load visit data from API
                async loadVisitData(visitId) {
                    try {
                        const response = await window.LabGOS.trackVisit(visitId);
                        if (response.success) {
                            this.visitData = response.data;
                            this.setupStatusSteps();
                        } else {
                            this.error = response.message || 'Gagal memuat data kunjungan';
                        }
                    } catch (error) {
                        console.error('Failed to load visit data:', error);
                        this.error = 'Terjadi kesalahan saat memuat data kunjungan';
                    }
                },

                // Computed properties for data access
                get visitId() {
                    return this.visitData?.request_id || '';
                },

                get submittedDate() {
                    if (!this.visitData?.submitted_at) return '';

                    // Fix time display issue - Laravel returns UTC time, convert to Jakarta time
                    const submittedAtUTC = this.visitData.submitted_at.replace(' ', 'T') + 'Z';
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
                    return this.visitData?.status || 'pending';
                },

                get visitorInfo() {
                    if (!this.visitData) return {};
                    return {
                        fullName: this.visitData.applicant?.visitor_name || '',
                        institution: this.visitData.applicant?.institution || '',
                        email: this.visitData.applicant?.visitor_email || '',
                        phone: this.visitData.applicant?.visitor_phone || '',
                        purpose: this.visitData.visit_purpose || ''
                    };
                },

                get scheduleInfo() {
                    if (!this.visitData) return {};

                    const visitDate = this.visitData.visit_date ?
                        new Date(this.visitData.visit_date).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        }) : '';

                    // Use the new visit_time structure from API response
                    let visitTime = '';
                    if (this.visitData.visit_time && this.visitData.visit_time.display) {
                        visitTime = this.visitData.visit_time.display;
                    } else if (this.visitData.start_time && this.visitData.end_time) {
                        // Fallback to individual time fields
                        const startTime = this.visitData.start_time.substring(0,5);
                        const endTime = this.visitData.end_time.substring(0,5);
                        visitTime = `${startTime} - ${endTime} WIB`;
                    }

                    return {
                        visitDate: visitDate,
                        visitTime: visitTime,
                        participants: this.visitData.group_size || 0,
                        additionalNotes: this.visitData.purpose_description || this.visitData.special_requirements || ''
                    };
                },

                setupStatusSteps() {
                    if (!this.visitData) return;

                    // Reset to base steps first
                    const baseSteps = [
                        {
                            title: 'Permohonan Diajukan',
                            description: 'Permohonan kunjungan telah berhasil dikirim dan sedang dalam antrian review.',
                            icon: 'fas fa-paper-plane',
                            status: 'completed',
                            timestamp: this.submittedDate
                        },
                        {
                            title: 'Review Admin',
                            description: 'Tim admin sedang melakukan review terhadap permohonan kunjungan Anda.',
                            icon: 'fas fa-search',
                            status: 'pending',
                            timestamp: null
                        },
                        {
                            title: 'Persetujuan',
                            description: 'Menunggu persetujuan dari kepala laboratorium untuk kunjungan.',
                            icon: 'fas fa-check-circle',
                            status: 'pending',
                            timestamp: null
                        },
                        {
                            title: 'Siap Dikunjungi',
                            description: 'Kunjungan telah disetujui dan siap dilaksanakan sesuai jadwal yang ditentukan.',
                            icon: 'fas fa-building',
                            status: 'pending',
                            timestamp: null
                        },
                        {
                            title: 'Selesai',
                            description: 'Kunjungan laboratorium telah berhasil dilaksanakan.',
                            icon: 'fas fa-flag-checkered',
                            status: 'pending',
                            timestamp: null
                        }
                    ];

                    // Update status based on current visit status
                    switch(this.currentStatus) {
                        case 'pending':
                            baseSteps[1].status = 'current';
                            break;
                        case 'under_review':
                            baseSteps[1].status = 'current';
                            break;
                        case 'approved':
                            baseSteps[1].status = 'completed';
                            baseSteps[2].status = 'completed';
                            baseSteps[3].status = 'current';
                            // Add detailed instructions for approved status
                            baseSteps[3].description = `
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-3">
                                    <div class="flex items-start space-x-3 mb-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <span class="text-green-600 font-bold text-sm">📋</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-green-800 mb-2">Kunjungan telah disetujui! Silakan lakukan langkah berikut:</h4>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-3 ml-11">
                                        <div class="flex items-start space-x-3">
                                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-semibold">1</span>
                                            <span class="text-gray-700"><strong>📥 Download surat izin</strong> kunjungan dengan tombol "Download Surat" di bawah</span>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-semibold">2</span>
                                            <span class="text-gray-700"><strong>✍️ Tanda tangani</strong> surat izin tersebut dengan tinta biru/hitam</span>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-semibold">3</span>
                                            <span class="text-gray-700"><strong>🏛️ Datang ke laboratorium</strong> pada tanggal <strong>${this.scheduleInfo.visitDate}</strong> pukul <strong>${this.scheduleInfo.visitTime}</strong></span>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-semibold">4</span>
                                            <span class="text-gray-700"><strong>📄 Bawa surat izin</strong> yang sudah ditandatangani beserta identitas diri</span>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-semibold">5</span>
                                            <span class="text-gray-700"><strong>📞 Hubungi admin lab</strong> jika ada pertanyaan atau kendala</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                        <div class="flex items-start space-x-2">
                                            <span class="text-amber-500 text-lg">⚠️</span>
                                            <span class="text-amber-800 text-sm font-medium">Penting: Kunjungan hanya dapat dilaksanakan dengan membawa surat izin yang sudah ditandatangani.</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                            break;
                        case 'ready':
                            baseSteps[1].status = 'completed';
                            baseSteps[2].status = 'completed';
                            baseSteps[3].status = 'current';
                            // Add detailed instructions for ready status
                            baseSteps[3].description = `
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-3">
                                    <div class="flex items-start space-x-3 mb-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-bold text-sm">🎯</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-blue-800 mb-2">Kunjungan siap dilaksanakan! Silakan lakukan langkah berikut:</h4>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-3 ml-11">
                                        <div class="flex items-start space-x-3">
                                            <span class="flex-shrink-0 w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-semibold">1</span>
                                            <span class="text-gray-700"><strong>📥 Download surat izin</strong> kunjungan dengan tombol "Download Surat" di bawah</span>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <span class="flex-shrink-0 w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-semibold">2</span>
                                            <span class="text-gray-700"><strong>✍️ Pastikan surat izin</strong> sudah ditandatangani dengan tinta biru/hitam</span>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <span class="flex-shrink-0 w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-semibold">3</span>
                                            <span class="text-gray-700"><strong>🏛️ Datang ke laboratorium</strong> pada tanggal <strong>${this.scheduleInfo.visitDate}</strong> pukul <strong>${this.scheduleInfo.visitTime}</strong></span>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <span class="flex-shrink-0 w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-semibold">4</span>
                                            <span class="text-gray-700"><strong>📄 Bawa surat izin</strong> yang sudah ditandatangani beserta identitas diri</span>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <span class="flex-shrink-0 w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-semibold">5</span>
                                            <span class="text-gray-700"><strong>📞 Hubungi admin lab</strong> jika ada pertanyaan atau kendala</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                        <div class="flex items-start space-x-2">
                                            <span class="text-amber-500 text-lg">⚠️</span>
                                            <span class="text-amber-800 text-sm font-medium">Penting: Kunjungan hanya dapat dilaksanakan dengan membawa surat izin yang sudah ditandatangani.</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                            break;
                        case 'completed':
                            baseSteps[1].status = 'completed';
                            baseSteps[2].status = 'completed';
                            baseSteps[3].status = 'completed';
                            baseSteps[4].status = 'completed';
                            baseSteps[4].timestamp = this.formatTimestamp(this.visitData.reviewed_at) || 'Selesai';
                            baseSteps[4].description = '✅ Kunjungan laboratorium telah berhasil dilaksanakan. Terima kasih atas kunjungan Anda ke Laboratorium GOS!';
                            break;
                        case 'rejected':
                            // For rejected, show rejection at review stage
                            baseSteps[1].status = 'rejected';
                            baseSteps[1].title = 'Permohonan Ditolak';
                            baseSteps[1].description = 'Permohonan kunjungan ditolak oleh admin. Silakan ajukan permohonan baru.';
                            baseSteps[1].icon = 'fas fa-times-circle';
                            baseSteps[1].timestamp = this.formatTimestamp(this.visitData.reviewed_at) || 'Ditolak pada ' + this.submittedDate;
                            // Mark remaining steps as skipped and update descriptions
                            baseSteps[2].status = 'skipped';
                            baseSteps[2].title = 'Persetujuan';
                            baseSteps[2].description = 'Tahap ini tidak akan dilaksanakan karena permohonan ditolak.';
                            baseSteps[2].icon = 'fas fa-ban';
                            baseSteps[2].timestamp = null;
                            baseSteps[3].status = 'skipped';
                            baseSteps[3].title = 'Siap Dikunjungi';
                            baseSteps[3].description = 'Tahap ini tidak akan dilaksanakan karena permohonan ditolak.';
                            baseSteps[3].icon = 'fas fa-ban';
                            baseSteps[3].timestamp = null;
                            baseSteps[4].status = 'skipped';
                            baseSteps[4].title = 'Selesai';
                            baseSteps[4].description = 'Tahap ini tidak akan dilaksanakan karena permohonan ditolak.';
                            baseSteps[4].icon = 'fas fa-ban';
                            baseSteps[4].timestamp = null;
                            break;
                        case 'cancelled':
                            // For cancelled, show cancellation at review stage
                            baseSteps[1].status = 'cancelled';
                            baseSteps[1].title = 'Permohonan Dibatalkan';
                            baseSteps[1].description = 'Permohonan kunjungan telah dibatalkan oleh pemohon.';
                            baseSteps[1].icon = 'fas fa-ban';
                            baseSteps[1].timestamp = this.formatTimestamp(this.visitData.reviewed_at) || 'Dibatalkan pada ' + this.submittedDate;
                            // Mark remaining steps as skipped and update descriptions
                            baseSteps[2].status = 'skipped';
                            baseSteps[2].title = 'Persetujuan';
                            baseSteps[2].description = 'Tahap ini tidak akan dilaksanakan karena permohonan dibatalkan.';
                            baseSteps[2].icon = 'fas fa-ban';
                            baseSteps[2].timestamp = null;
                            baseSteps[3].status = 'skipped';
                            baseSteps[3].title = 'Siap Dikunjungi';
                            baseSteps[3].description = 'Tahap ini tidak akan dilaksanakan karena permohonan dibatalkan.';
                            baseSteps[3].icon = 'fas fa-ban';
                            baseSteps[3].timestamp = null;
                            baseSteps[4].status = 'skipped';
                            baseSteps[4].title = 'Selesai';
                            baseSteps[4].description = 'Tahap ini tidak akan dilaksanakan karena permohonan dibatalkan.';
                            baseSteps[4].icon = 'fas fa-ban';
                            baseSteps[4].timestamp = null;
                            break;
                        default:
                            baseSteps[1].status = 'current';
                    }

                    this.statusSteps = baseSteps;
                },

                // Status Steps
                statusSteps: [],

                get visitAgenda() {
                    // Calculate total visit duration in minutes from visit time
                    const totalDuration = this.getVisitDurationInMinutes();

                    // Generate dynamic agenda based on visit purpose and duration
                    return this.generateDynamicAgenda(this.visitData?.visit_purpose || 'study-visit', totalDuration);
                },

                // Calculate visit duration in minutes from visit_time
                getVisitDurationInMinutes() {
                    if (!this.visitData?.visit_time) return 120; // Default 2 hours

                    // If visit_time has start_time and end_time
                    if (this.visitData.visit_time.start_time && this.visitData.visit_time.end_time) {
                        const [startHour, startMin] = this.visitData.visit_time.start_time.split(':').map(Number);
                        const [endHour, endMin] = this.visitData.visit_time.end_time.split(':').map(Number);

                        const startMinutes = startHour * 60 + startMin;
                        const endMinutes = endHour * 60 + endMin;

                        return endMinutes - startMinutes;
                    }

                    // Parse from display string if available (e.g., "08:00 - 10:00 WIB")
                    if (this.visitData.visit_time.display) {
                        const timeMatch = this.visitData.visit_time.display.match(/(\d{2}):(\d{2})\s*-\s*(\d{2}):(\d{2})/);
                        if (timeMatch) {
                            const [, startHour, startMin, endHour, endMin] = timeMatch.map(Number);
                            const startMinutes = startHour * 60 + startMin;
                            const endMinutes = endHour * 60 + endMin;
                            return endMinutes - startMinutes;
                        }
                    }

                    return 120; // Default 2 hours
                },

                // Generate dynamic agenda based on purpose and available time
                generateDynamicAgenda(purpose, totalMinutes) {
                    const agendaTemplates = {
                        'study-visit': [
                            { activity: 'Presentasi Selamat Datang', description: 'Pengenalan laboratorium dan fasilitas', location: 'Ruang Meeting', weight: 0.2 },
                            { activity: 'Tur Laboratorium Optik', description: 'Kunjungan ke lab optik dan penjelasan peralatan', location: 'Lab Optik', weight: 0.3 },
                            { activity: 'Demonstrasi Spektroskopi', description: 'Demo penggunaan spektrometer', location: 'Lab Spektroskopi', weight: 0.3 },
                            { activity: 'Sesi Tanya Jawab', description: 'Diskusi dan tanya jawab dengan staff lab', location: 'Ruang Meeting', weight: 0.2 }
                        ],
                        'research': [
                            { activity: 'Briefing Penelitian', description: 'Diskusi rencana penelitian dan kebutuhan', location: 'Ruang Meeting', weight: 0.2 },
                            { activity: 'Setup Peralatan', description: 'Persiapan dan kalibrasi instrumen penelitian', location: 'Lab Spektroskopi', weight: 0.3 },
                            { activity: 'Kolaborasi Penelitian', description: 'Pelaksanaan penelitian bersama', location: 'Lab Spektroskopi', weight: 0.5 }
                        ],
                        'learning': [
                            { activity: 'Orientasi Lab', description: 'Pengenalan keselamatan dan prosedur lab', location: 'Ruang Meeting', weight: 0.15 },
                            { activity: 'Praktik Gelombang', description: 'Hands-on learning dengan peralatan gelombang', location: 'Lab Gelombang', weight: 0.35 },
                            { activity: 'Praktik Optik', description: 'Pembelajaran praktis sistem optik', location: 'Lab Optik', weight: 0.35 },
                            { activity: 'Evaluasi & Review', description: 'Evaluasi pembelajaran dan feedback', location: 'Ruang Meeting', weight: 0.15 }
                        ]
                    };

                    const template = agendaTemplates[purpose] || agendaTemplates['study-visit'];

                    // Calculate duration for each activity based on weight and total time
                    return template.map((item, index) => {
                        const duration = Math.round(totalMinutes * item.weight);
                        const hours = Math.floor(duration / 60);
                        const minutes = duration % 60;

                        let durationText;
                        if (hours > 0 && minutes > 0) {
                            durationText = `${hours} jam ${minutes} menit`;
                        } else if (hours > 0) {
                            durationText = `${hours} jam`;
                        } else {
                            durationText = `${minutes} menit`;
                        }

                        return {
                            id: index + 1,
                            activity: item.activity,
                            description: item.description,
                            location: item.location,
                            duration: durationText
                        };
                    });
                },

                // Get formatted visit duration text
                getVisitDurationText() {
                    const totalMinutes = this.getVisitDurationInMinutes();
                    const hours = Math.floor(totalMinutes / 60);
                    const minutes = totalMinutes % 60;

                    if (hours > 0 && minutes > 0) {
                        return `${hours} jam ${minutes} menit`;
                    } else if (hours > 0) {
                        return `${hours} jam`;
                    } else {
                        return `${minutes} menit`;
                    }
                },

                // Status helper methods
                statusSteps: [],

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
                async downloadLetter() {
                    if (this.currentStatus !== 'approved' && this.currentStatus !== 'ready') {
                        alert('Surat izin hanya dapat diunduh setelah permohonan disetujui.');
                        return;
                    }

                    try {
                        // Create direct download URL to the API endpoint
                        const downloadUrl = `/api/tracking/visit/${this.visitId}/letter`;

                        // Create download link
                        const link = document.createElement('a');
                        link.href = downloadUrl;
                        link.download = `Surat_Izin_Kunjungan_${this.visitId}.pdf`;
                        link.target = '_blank';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        // Show success message
                        alert('Surat izin kunjungan sedang diunduh...');
                    } catch (error) {
                        console.error('Failed to download letter:', error);
                        alert('Gagal mengunduh surat izin. Silakan coba lagi atau hubungi admin.');
                    }
                },

                // Cooldown system methods
                initializeCooldown() {
                    const stored = localStorage.getItem(`whatsapp_cooldown_${this.visitId}`);
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
                    localStorage.setItem(`whatsapp_cooldown_${this.visitId}`, this.lastMessageTime.toString());
                    this.updateCooldownStatus();
                },

                openWhatsAppChat() {
                    // Check cooldown
                    if (!this.canSendMessage) {
                        alert(`Harap tunggu ${this.cooldownText.replace('Tunggu ', '')} sebelum mengirim pesan lagi.`);
                        return;
                    }

                    // Check if admin phone is available
                    if (!this.adminPhone) {
                        alert('Nomor WhatsApp admin tidak tersedia. Silakan hubungi admin melalui email atau telepon.');
                        return;
                    }

                    // Generate tracking URL
                    const trackingUrl = `${window.location.origin}/layanan/kunjungan/confirmation/${this.visitId}`;

                    const message = `Kepada Yang Terhormat,
Admin Laboratorium Gelombang, Optik dan Spektroskopi (GOS)
Departemen Fisika FMIPA Universitas Syiah Kuala

Dengan hormat,

Saya bermaksud untuk melakukan konsultasi terkait permohonan kunjungan laboratorium dengan rincian sebagai berikut:

=== DETAIL PERMOHONAN KUNJUNGAN ===
- ID Kunjungan: ${this.visitId}
- Nama Pengunjung: ${this.visitorInfo.fullName}
- Instansi/Institusi: ${this.visitorInfo.institution}
- Tanggal Kunjungan: ${this.scheduleInfo.visitDate}
- Waktu Kunjungan: ${this.scheduleInfo.visitTime}
- Jumlah Peserta: ${this.scheduleInfo.participants} orang
- Status Permohonan: ${this.currentStatus === 'pending' ? 'Menunggu Persetujuan' :
                         this.currentStatus === 'approved' ? 'Disetujui' :
                         this.currentStatus === 'ready' ? 'Siap Dikunjungi' :
                         this.currentStatus === 'completed' ? 'Selesai' :
                         this.currentStatus === 'rejected' ? 'Ditolak' :
                         this.currentStatus === 'cancelled' ? 'Dibatalkan' : this.currentStatus}

Link Tracking (Klik untuk membuka):
${trackingUrl}

Mohon kiranya Bapak/Ibu dapat memberikan informasi lebih lanjut mengenai:
1. Persiapan yang diperlukan sebelum kunjungan
2. Fasilitas laboratorium yang dapat diakses
3. Prosedur dan tata tertib selama kunjungan
4. Informasi lain yang perlu diketahui

Demikian permohonan ini saya sampaikan. Atas perhatian dan kerjasamanya, saya ucapkan terima kasih.

Hormat saya,
${this.visitorInfo.fullName}
${this.visitorInfo.institution}

---
Laboratorium GOS - Departemen Fisika FMIPA USK
Email: labgos@usk.ac.id
Jam Operasional: Senin-Jumat, 08:00-16:00 WIB`;

                    // Use the dynamically retrieved phone number
                    const whatsappUrl = `https://wa.me/${this.adminPhone}?text=${encodeURIComponent(message)}`;

                    // Start cooldown after successful message
                    this.startCooldown();

                    window.open(whatsappUrl, '_blank');
                },

                async cancelVisit() {
                    if (['approved', 'ready', 'completed'].includes(this.currentStatus)) {
                        alert('Kunjungan yang sudah disetujui tidak dapat dibatalkan. Hubungi admin untuk pembatalan.');
                        return;
                    }

                    const confirmation = confirm('Apakah Anda yakin ingin membatalkan permohonan kunjungan ini?\n\nPermohonan yang sudah dibatalkan tidak dapat dikembalikan.');

                    if (confirmation) {
                        try {
                            // Show loading state
                            const originalText = event.target.innerHTML;
                            event.target.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Membatalkan...';
                            event.target.disabled = true;

                            // Call cancel API
                            const response = await window.LabGOS.cancelVisit(this.visitId);

                            if (response.success) {
                                // Show success message
                                alert('✅ Permohonan kunjungan berhasil dibatalkan.\n\nHalaman tracking akan ditutup dan Anda akan diarahkan ke halaman kunjungan.');

                                // Clear tracking data from session storage
                                sessionStorage.removeItem('visitTrackingData');

                                // Redirect to visit request page
                                window.location.href = '/layanan/kunjungan';

                            } else {
                                // Show error message from API
                                alert('❌ ' + (response.message || 'Gagal membatalkan permohonan.'));

                                // Restore button
                                event.target.innerHTML = originalText;
                                event.target.disabled = false;
                            }

                        } catch (error) {
                            console.error('Error canceling visit:', error);

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

                // Letter preview functionality
                hasRequestLetter() {
                    return this.visitData?.request_letter_url !== null && this.visitData?.request_letter_url !== undefined;
                },

                getLetterDisplayText() {
                    return this.hasRequestLetter() ?
                        'Surat permohonan telah diunggah' :
                        'Tidak ada surat permohonan yang diunggah';
                },

                previewRequestLetter() {
                    if (this.hasRequestLetter()) {
                        // Open the letter file in a new tab
                        window.open(this.visitData.request_letter_url, '_blank');
                    } else {
                        alert('Tidak ada surat permohonan yang tersedia untuk dilihat.');
                    }
                }
            }
        }
    </script>

</x-public.layouts.main>
