<x-public.layouts.main>
    {{-- Mendefinisikan judul halaman untuk layout --}}
    <x-slot:title>
        Tracking Peminjaman - Lab GOS USK
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
                        📋 Tracking
                        <span class="text-secondary">Peminjaman</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Pantau status permohonan peminjaman peralatan laboratorium Anda
                </p>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-2 inline-block">
                    <p class="text-white flex items-center justify-center">
                        <i class="fas fa-search mr-2 text-secondary"></i>
                        Status Permohonan Real-time
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div x-data="trackingPeminjaman()" x-init="init()" class="space-y-8">

                <!-- Status Timeline -->
                <div x-data="{ animated: false }"
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="bg-white rounded-3xl shadow-2xl overflow-hidden transition-all duration-1000 ease-out">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-primary to-blue-600 px-8 py-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-secondary bg-opacity-20 rounded-full -translate-y-16 translate-x-16"></div>
                        <div class="relative z-10 flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl md:text-3xl font-bold mb-2 flex items-center">
                                    <i class="fas fa-clipboard-check mr-3 text-secondary"></i>
                                    Status Peminjaman
                                </h2>
                                <p class="text-blue-200 mb-2">
                                    ID Permohonan: <span class="font-bold text-secondary" x-text="requestId"></span>
                                </p>
                                <!-- Status Badge -->
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                     :class="{
                                        'bg-yellow-100 text-yellow-800': currentStatus === 'pending',
                                        'bg-green-100 text-green-800': currentStatus === 'approved',
                                        'bg-blue-100 text-blue-800': currentStatus === 'active',
                                        'bg-gray-100 text-gray-800': currentStatus === 'completed',
                                        'bg-red-100 text-red-800': currentStatus === 'rejected' || currentStatus === 'cancelled'
                                     }">
                                    <span x-show="currentStatus === 'pending'">⏳ Menunggu Persetujuan</span>
                                    <span x-show="currentStatus === 'approved'">✅ Permohonan Disetujui</span>
                                    <span x-show="currentStatus === 'active'">⏳ Masa Peminjaman Berlangsung</span>
                                    <span x-show="currentStatus === 'completed'">🎉 Peminjaman Selesai</span>
                                    <span x-show="currentStatus === 'rejected'">❌ Ditolak</span>
                                    <span x-show="currentStatus === 'cancelled'">🚫 Dibatalkan</span>
                                </div>

                                <!-- Status Message -->
                                <div class="mt-3 text-blue-100 text-sm">
                                    <div x-show="currentStatus === 'approved'" class="bg-blue-500 bg-opacity-20 rounded-lg p-3">
                                        <div class="font-medium mb-1">Langkah Selanjutnya:</div>
                                        <div>Silakan unduh <strong>Surat Izin Pemakaian Alat</strong>, tanda tangani sesuai ketentuan, lalu bawa surat ini ke <strong>Jurusan</strong> pada <span x-text="formatSafeDate(scheduleInfo?.borrowDate, 'tanggal yang telah ditentukan')"></span> untuk pengambilan alat.</div>
                                    </div>
                                    <div x-show="currentStatus === 'active'" class="bg-blue-500 bg-opacity-20 rounded-lg p-3">
                                        <div class="font-medium mb-1">Periode Peminjaman:</div>
                                        <div x-text="getDateRangeText()"></div>
                                        <div class="mt-2 text-xs">Mohon menjaga kondisi alat dan mengikuti tata tertib laboratorium.</div>
                                    </div>
                                    <div x-show="currentStatus === 'completed'" class="bg-gray-500 bg-opacity-20 rounded-lg p-3">
                                        <div>Terima kasih. Peminjaman telah selesai dan diarsipkan.</div>
                                    </div>
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
                                    <p class="text-red-600 text-sm">Permohonan peminjaman telah dibatalkan</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-red-100">
                                <p class="text-gray-700 text-sm">
                                    <strong>Informasi:</strong> Permohonan peminjaman dengan ID <span x-text="requestId" class="font-mono font-semibold"></span>
                                    telah dibatalkan. Jika Anda memerlukan alat laboratorium, silakan ajukan permohonan baru melalui halaman utama.
                                </p>
                                <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                    <a href="/peminjaman"
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

                <!-- Detail Peminjaman -->
                <div x-data="{ animated: false }"
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="bg-white rounded-3xl shadow-2xl overflow-hidden transition-all duration-1000 ease-out"
                     style="transition-delay: 0.2s;">

                    <div class="bg-gradient-to-r from-secondary to-yellow-500 px-8 py-6 text-gray-800">
                        <h2 class="text-2xl font-bold mb-2 flex items-center">
                            <i class="fas fa-info-circle mr-3"></i>
                            Detail Peminjaman
                        </h2>
                        <p class="text-gray-700">Informasi lengkap permohonan peminjaman Anda</p>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                            <!-- Info Peminjam -->
                            <div class="space-y-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-users mr-3 text-primary"></i>
                                    Peminjam
                                </h3>

                                <div class="space-y-4">
                                    <template x-for="(member, index) in borrowerInfo.members" :key="index">
                                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-bold">
                                                    <span x-text="index + 1"></span>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-800" x-text="member.name"></div>
                                                    <div class="text-sm text-gray-600" x-text="member.nim"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Pembimbing -->
                                <div class="bg-blue-50 rounded-xl p-4">
                                    <h4 class="font-semibold text-blue-800 mb-2">Dosen Pembimbing</h4>
                                    <div class="text-sm text-blue-700">
                                        <div x-text="borrowerInfo.supervisor.name"></div>
                                        <div x-text="borrowerInfo.supervisor.nip"></div>
                                        <div x-text="borrowerInfo.supervisor.email"></div>
                                        <div x-text="borrowerInfo.supervisor.phone"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Waktu -->
                            <div class="space-y-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-calendar-alt mr-3 text-green-600"></i>
                                    Jadwal Penggunaan
                                </h3>

                                <div class="grid grid-cols-1 gap-4">
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="text-sm text-gray-600 mb-1">Tanggal Peminjaman</div>
                                        <div class="font-semibold text-gray-800" x-text="scheduleInfo.borrowDate"></div>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="text-sm text-gray-600 mb-1">Tanggal Pengembalian</div>
                                        <div class="font-semibold text-gray-800" x-text="scheduleInfo.returnDate"></div>
                                    </div>
                                    <div x-show="scheduleInfo.timeRange" class="bg-gray-50 rounded-xl p-4">
                                        <div class="text-sm text-gray-600 mb-1">Waktu Penggunaan</div>
                                        <div class="font-semibold text-gray-800" x-text="scheduleInfo.timeRange"></div>
                                    </div>
                                </div>

                                <!-- Tujuan -->
                                <div class="bg-green-50 rounded-xl p-4">
                                    <h4 class="font-semibold text-green-800 mb-2">Tujuan Penelitian</h4>
                                    <p class="text-sm text-green-700" x-text="scheduleInfo.purpose"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Alat -->
                        <div class="mt-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-tools mr-3 text-secondary"></i>
                                Alat yang Dipinjam
                            </h3>

                            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-lg">
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gradient-to-r from-primary to-blue-600 text-white">
                                            <tr>
                                                <th class="px-6 py-4 text-left text-sm font-semibold">No.</th>
                                                <th class="px-6 py-4 text-left text-sm font-semibold">Nama Alat</th>
                                                <th class="px-6 py-4 text-left text-sm font-semibold">Spesifikasi</th>
                                                <th class="px-6 py-4 text-center text-sm font-semibold">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <template x-for="(item, index) in equipmentList" :key="item.id">
                                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                    <td class="px-6 py-4 text-sm">
                                                        <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center font-bold text-xs">
                                                            <span x-text="index + 1"></span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm font-medium text-gray-900" x-text="item.name"></div>
                                                        <div class="text-xs text-gray-500 capitalize" x-text="item.category"></div>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-700" x-text="item.specs"></td>
                                                    <td class="px-6 py-4 text-center">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                                                              x-text="item.quantity + ' unit'"></span>
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
                <div x-data="{ animated: false }"
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
                                 :class="['approved', 'active'].includes(currentStatus) ? 'bg-green-100' : 'bg-gray-200'">
                                <i class="fas fa-download text-2xl"
                                   :class="['approved', 'active'].includes(currentStatus) ? 'text-green-600' : 'text-gray-400'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Download Surat</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="['approved', 'active'].includes(currentStatus)">Surat izin siap diunduh</span>
                                <span x-show="currentStatus === 'pending'">Menunggu persetujuan</span>
                                <span x-show="currentStatus === 'completed'" class="text-gray-600 font-semibold">Sudah selesai - tidak tersedia</span>
                                <span x-show="currentStatus === 'cancelled'" class="text-red-600 font-semibold">Tidak tersedia - permohonan dibatalkan</span>
                                <span x-show="currentStatus === 'rejected'">Tidak tersedia - ditolak</span>
                            </p>
                            <button @click="downloadLetter()"
                                    :disabled="!['approved', 'active'].includes(currentStatus)"
                                    :title="currentStatus === 'completed' ? 'Sudah selesai' : ''"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform"
                                    :class="['approved', 'active'].includes(currentStatus) ?
                                           'bg-green-500 hover:bg-green-600 text-white shadow-lg hover:shadow-xl hover:scale-105 cursor-pointer' :
                                           'bg-gray-300 text-gray-500 cursor-not-allowed'">
                                <span x-show="['approved', 'active'].includes(currentStatus)">
                                    <i class="fas fa-download mr-2"></i>Download Surat
                                </span>
                                <span x-show="currentStatus === 'pending'">Belum Tersedia</span>
                                <span x-show="currentStatus === 'completed'">Sudah Selesai</span>
                                <span x-show="currentStatus === 'cancelled'">Tidak Tersedia</span>
                                <span x-show="currentStatus === 'rejected'">Ditolak</span>
                            </button>
                        </div>

                        <!-- WhatsApp Konfirmasi -->
                        <div class="bg-green-50 rounded-2xl p-6 text-center hover:bg-green-100 transition-all duration-300">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                 :class="['cancelled', 'completed'].includes(currentStatus) ? 'bg-gray-200' : 'bg-green-100'">
                                <i class="fab fa-whatsapp text-2xl"
                                   :class="['cancelled', 'completed'].includes(currentStatus) ? 'text-gray-400' : 'text-green-600'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Chat WhatsApp Admin</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="!['cancelled', 'completed'].includes(currentStatus)">Chat langsung dengan admin lab</span>
                                <span x-show="currentStatus === 'completed'" class="text-gray-600 font-semibold">Sudah selesai - tidak tersedia</span>
                                <span x-show="currentStatus === 'cancelled'" class="text-red-600 font-semibold">Tidak tersedia - permohonan dibatalkan</span>
                            </p>
                            <button @click="openWhatsAppChat()"
                                    :disabled="['cancelled', 'completed'].includes(currentStatus) || !canSendMessage"
                                    :title="currentStatus === 'completed' ? 'Sudah selesai' : ''"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform"
                                    :class="['cancelled', 'completed'].includes(currentStatus) ?
                                           'bg-gray-300 text-gray-500 cursor-not-allowed' :
                                           !canSendMessage ?
                                           'bg-orange-400 text-white cursor-not-allowed' :
                                           'bg-green-500 hover:bg-green-600 text-white shadow-lg hover:shadow-xl hover:scale-105'">
                                <span x-show="currentStatus === 'cancelled'">Tidak Tersedia</span>
                                <span x-show="currentStatus === 'completed'">Sudah Selesai</span>
                                <span x-show="!['cancelled', 'completed'].includes(currentStatus) && canSendMessage">
                                    <i class="fab fa-whatsapp mr-2"></i>Chat Admin
                                </span>
                                <span x-show="!['cancelled', 'completed'].includes(currentStatus) && !canSendMessage" x-text="cooldownText"></span>
                            </button>
                        </div>

                        <!-- Cancel Peminjaman -->
                        <div class="bg-red-50 rounded-2xl p-6 text-center hover:bg-red-100 transition-all duration-300">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                 :class="['approved', 'active', 'completed', 'cancelled'].includes(currentStatus) ? 'bg-gray-200' : 'bg-red-100'">
                                <i class="fas fa-times text-2xl"
                                   :class="['approved', 'active', 'completed', 'cancelled'].includes(currentStatus) ? 'text-gray-400' : 'text-red-600'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Batalkan Peminjaman</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="currentStatus === 'pending'">Dapat dibatalkan sebelum disetujui</span>
                                <span x-show="currentStatus === 'approved'">Tidak dapat dibatalkan - sudah disetujui</span>
                                <span x-show="currentStatus === 'active'">Tidak dapat dibatalkan - sedang berlangsung</span>
                                <span x-show="currentStatus === 'completed'" class="text-gray-600 font-semibold">Sudah selesai</span>
                                <span x-show="currentStatus === 'cancelled'" class="text-red-600 font-semibold">Sudah dibatalkan</span>
                            </p>
                            <button @click="cancelRequest()"
                                    :disabled="['approved', 'active', 'completed', 'cancelled'].includes(currentStatus)"
                                    :title="currentStatus === 'completed' ? 'Sudah selesai' : ''"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform"
                                    :class="['approved', 'active', 'completed', 'cancelled'].includes(currentStatus) ?
                                           'bg-gray-300 text-gray-500 cursor-not-allowed' :
                                           'bg-red-500 hover:bg-red-600 text-white shadow-lg hover:shadow-xl hover:scale-105'">
                                <span x-show="currentStatus === 'pending'">Batalkan</span>
                                <span x-show="currentStatus === 'approved'">Tidak Tersedia</span>
                                <span x-show="currentStatus === 'active'">Tidak Tersedia</span>
                                <span x-show="currentStatus === 'completed'">Sudah Selesai</span>
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
        function trackingPeminjaman() {
            return {
                // Request Info
                requestId: '',
                borrowRequestId: null, // Numeric ID for API calls
                submittedDate: '',
                currentStatus: 'pending', // pending, approved, rejected, completed
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

                    const urlParams = new URLSearchParams(window.location.search);
                    const requestId = urlParams.get('rid');

                    if (requestId) {
                        this.requestId = requestId;
                        await this.loadTrackingData(requestId);
                    } else {
                        // Try to get from sessionStorage (from previous page)
                        const storedData = sessionStorage.getItem('trackingData');
                        if (storedData) {
                            try {
                                const data = JSON.parse(storedData);
                                this.populateTrackingData(data);
                            } catch (error) {
                                console.error('Failed to parse stored tracking data:', error);
                                this.error = 'Gagal memuat data tracking';
                            }
                        } else {
                            this.error = 'Request ID tidak ditemukan';
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

                // Load tracking data from API
                async loadTrackingData(requestId) {
                    try {
                        const response = await window.LabGOS.trackBorrow(requestId);
                        if (response.success) {
                            this.populateTrackingData(response.data);
                        } else {
                            this.error = response.message || 'Gagal memuat data tracking';
                        }
                    } catch (error) {
                        console.error('Failed to load tracking data:', error);
                        this.error = 'Terjadi kesalahan saat memuat data tracking';
                    }
                },

                // Populate data from API response
                populateTrackingData(data) {
                    this.requestId = data.request_id;
                    this.currentStatus = data.status;
                    this.borrowRequestId = data.id || null; // Store numeric ID for API calls

                    // Fix time display issue - Laravel returns UTC time, convert to Jakarta time
                    // API returns: "2025-08-11 07:41:42" (UTC format from Laravel)
                    const submittedAtUTC = data.submitted_at.replace(' ', 'T') + 'Z'; // Convert to ISO format with Z for UTC
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

                    this.submittedDate = `${dateString} pukul ${timeString}`;

                    // Update schedule info
                    this.scheduleInfo.borrowDate = new Date(data.borrow_date).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    this.scheduleInfo.returnDate = new Date(data.return_date).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    this.scheduleInfo.purpose = data.purpose;

                    // Handle times conditionally
                    if (data.start_time && data.end_time) {
                        this.scheduleInfo.timeRange = `${data.start_time.substring(0,5)} - ${data.end_time.substring(0,5)} WIB`;
                    } else {
                        this.scheduleInfo.timeRange = null;
                    }

                    // Update borrower info
                    this.borrowerInfo.members = data.members || [];
                    this.borrowerInfo.supervisor = {
                        name: data.supervisor?.name || '',
                        nip: data.supervisor?.nip || '',
                        email: data.supervisor?.email || '',
                        phone: data.supervisor?.phone || ''
                    };

                    // Update equipment list with proper specs formatting
                    if (data.equipment_items && Array.isArray(data.equipment_items)) {
                        console.log('Processing equipment_items:', data.equipment_items);
                        this.equipmentList = data.equipment_items.map((item, index) => ({
                            id: item.id || item.equipment?.id || index, // Ensure unique ID for template key
                            name: item.equipment?.name || 'Equipment name not available',
                            category: item.equipment?.category?.name || 'Unknown',
                            specs: this.getKeySpecs(item.equipment),
                            quantity: item.quantity_requested || item.quantity_approved || 0
                        }));
                        console.log('Processed equipmentList:', this.equipmentList);
                    } else {
                        console.warn('No equipment_items found or invalid format:', data.equipment_items);
                        this.equipmentList = [];
                    }

                    // Update status steps based on current status
                    this.updateStatusSteps();
                },

                // Reset status steps to original state
                resetStatusSteps() {
                    // Reset all steps to their original values
                    this.statusSteps[0] = {
                        title: 'Permohonan Diajukan',
                        description: 'Permohonan peminjaman telah berhasil dikirim dan sedang dalam antrian review.',
                        icon: 'fas fa-paper-plane',
                        status: 'pending',
                        timestamp: null
                    };
                    this.statusSteps[1] = {
                        title: 'Review Admin',
                        description: 'Tim admin sedang melakukan review terhadap permohonan peminjaman Anda.',
                        icon: 'fas fa-search',
                        status: 'pending',
                        timestamp: null
                    };
                    this.statusSteps[2] = {
                        title: 'Persetujuan',
                        description: 'Menunggu persetujuan dari kepala laboratorium untuk peminjaman alat.',
                        icon: 'fas fa-check-circle',
                        status: 'pending',
                        timestamp: null
                    };
                    this.statusSteps[3] = {
                        title: 'Siap Digunakan',
                        description: 'Alat telah disetujui dan siap untuk digunakan sesuai jadwal yang ditentukan.',
                        icon: 'fas fa-tools',
                        status: 'pending',
                        timestamp: null
                    };
                },

                // Update status steps based on current request status
                updateStatusSteps() {
                    // Reset all steps to their original state first
                    this.resetStatusSteps();

                    // Update first step (always completed when request exists)
                    this.statusSteps[0].status = 'completed';
                    this.statusSteps[0].timestamp = this.submittedDate;

                    // Update status based on current request status
                    switch (this.currentStatus) {
                        case 'pending':
                            this.statusSteps[1].status = 'current';
                            break;
                        case 'approved':
                            this.statusSteps[1].status = 'completed';
                            this.statusSteps[2].status = 'completed';
                            this.statusSteps[2].title = 'Permohonan Disetujui';
                            this.statusSteps[2].description = 'Permohonan peminjaman telah disetujui! Silakan unduh surat izin, tanda tangani, dan bawa ke Jurusan pada tanggal peminjaman.';
                            this.statusSteps[2].icon = 'fas fa-check-circle';
                            this.statusSteps[3].status = 'current';
                            this.statusSteps[3].title = 'Siap untuk Pengambilan';
                            this.statusSteps[3].description = 'Bawa surat izin yang telah ditandatangani ke Jurusan pada tanggal peminjaman untuk mengambil peralatan.';
                            this.statusSteps[3].icon = 'fas fa-hand-holding';
                            break;
                        case 'active':
                            this.statusSteps[1].status = 'completed';
                            this.statusSteps[2].status = 'completed';
                            this.statusSteps[2].title = 'Permohonan Disetujui';
                            this.statusSteps[2].description = 'Permohonan peminjaman telah disetujui dan surat izin telah diunduh.';
                            this.statusSteps[2].icon = 'fas fa-check-circle';
                            this.statusSteps[3].status = 'completed';
                            this.statusSteps[3].title = 'Masa Peminjaman Berlangsung';
                            this.statusSteps[3].description = `Peralatan sedang digunakan dalam periode ${this.getDateRangeText()}. Mohon menjaga kondisi alat dan mengikuti tata tertib laboratorium.`;
                            this.statusSteps[3].icon = 'fas fa-play-circle';
                            break;
                        case 'completed':
                            this.statusSteps[1].status = 'completed';
                            this.statusSteps[2].status = 'completed';
                            this.statusSteps[2].title = 'Permohonan Disetujui';
                            this.statusSteps[2].description = 'Permohonan peminjaman telah disetujui dan surat izin telah diunduh.';
                            this.statusSteps[2].icon = 'fas fa-check-circle';
                            this.statusSteps[3].status = 'completed';
                            this.statusSteps[3].title = 'Peminjaman Selesai';
                            this.statusSteps[3].description = 'Peralatan telah dikembalikan dengan baik dan peminjaman selesai. Terima kasih atas partisipasinya.';
                            this.statusSteps[3].icon = 'fas fa-check-double';
                            break;
                        case 'rejected':
                            // For rejected, show rejection at review stage
                            this.statusSteps[1].status = 'rejected';
                            this.statusSteps[1].title = 'Permohonan Ditolak';
                            this.statusSteps[1].description = 'Permohonan peminjaman ditolak oleh admin. Silakan ajukan permohonan baru.';
                            this.statusSteps[1].icon = 'fas fa-times-circle';
                            this.statusSteps[1].timestamp = 'Ditolak pada ' + this.submittedDate;
                            // Mark remaining steps as skipped and update descriptions
                            this.statusSteps[2].status = 'skipped';
                            this.statusSteps[2].title = 'Persetujuan';
                            this.statusSteps[2].description = 'Tahap ini tidak akan dilaksanakan karena permohonan ditolak.';
                            this.statusSteps[2].icon = 'fas fa-ban';
                            this.statusSteps[2].timestamp = null;
                            this.statusSteps[3].status = 'skipped';
                            this.statusSteps[3].title = 'Siap Digunakan';
                            this.statusSteps[3].description = 'Tahap ini tidak akan dilaksanakan karena permohonan ditolak.';
                            this.statusSteps[3].icon = 'fas fa-ban';
                            this.statusSteps[3].timestamp = null;
                            break;
                        case 'cancelled':
                            // For cancelled, show cancellation at review stage
                            this.statusSteps[1].status = 'cancelled';
                            this.statusSteps[1].title = 'Permohonan Dibatalkan';
                            this.statusSteps[1].description = 'Permohonan peminjaman telah dibatalkan oleh pemohon.';
                            this.statusSteps[1].icon = 'fas fa-ban';
                            this.statusSteps[1].timestamp = 'Dibatalkan pada ' + this.submittedDate;
                            // Mark remaining steps as skipped and update descriptions
                            this.statusSteps[2].status = 'skipped';
                            this.statusSteps[2].title = 'Persetujuan';
                            this.statusSteps[2].description = 'Tahap ini tidak akan dilaksanakan karena permohonan dibatalkan.';
                            this.statusSteps[2].icon = 'fas fa-ban';
                            this.statusSteps[2].timestamp = null;
                            this.statusSteps[3].status = 'skipped';
                            this.statusSteps[3].title = 'Siap Digunakan';
                            this.statusSteps[3].description = 'Tahap ini tidak akan dilaksanakan karena permohonan dibatalkan.';
                            this.statusSteps[3].icon = 'fas fa-ban';
                            this.statusSteps[3].timestamp = null;
                            break;
                    }
                },

                // Status Steps
                statusSteps: [
                    {
                        title: 'Permohonan Diajukan',
                        description: 'Permohonan peminjaman telah berhasil dikirim dan sedang dalam antrian review.',
                        icon: 'fas fa-paper-plane',
                        status: 'pending',
                        timestamp: null
                    },
                    {
                        title: 'Review Admin',
                        description: 'Tim admin sedang melakukan review terhadap permohonan peminjaman Anda.',
                        icon: 'fas fa-search',
                        status: 'pending',
                        timestamp: null
                    },
                    {
                        title: 'Persetujuan',
                        description: 'Menunggu persetujuan dari kepala laboratorium untuk peminjaman alat.',
                        icon: 'fas fa-check-circle',
                        status: 'pending',
                        timestamp: null
                    },
                    {
                        title: 'Siap Digunakan',
                        description: 'Alat telah disetujui dan siap untuk digunakan sesuai jadwal yang ditentukan.',
                        icon: 'fas fa-tools',
                        status: 'pending',
                        timestamp: null
                    }
                ],

                // Borrower Info
                borrowerInfo: {
                    members: [],
                    supervisor: {
                        name: '',
                        nip: '',
                        email: '',
                        phone: ''
                    }
                },

                // Schedule Info
                scheduleInfo: {
                    borrowDate: '',
                    returnDate: '',
                    timeRange: null, // Will be set to null if no times available
                    purpose: ''
                },

                // Equipment List
                equipmentList: [],

                // Methods
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
                    if (!['approved', 'active'].includes(this.currentStatus)) {
                        alert('Surat izin hanya dapat diunduh setelah permohonan disetujui.');
                        return;
                    }

                    try {
                        // Use direct storage URL since this is a public tracking page
                        const storageUrl = `/storage/letters/${this.requestId}.pdf`;

                        // Test if the file exists by making a HEAD request
                        const testResponse = await fetch(storageUrl, { method: 'HEAD' });

                        if (testResponse.ok) {
                            // File exists, open it
                            window.open(storageUrl, '_blank');
                            alert('✅ Surat izin peminjaman berhasil dibuka dalam tab baru.');
                        } else {
                            // File doesn't exist or not accessible
                            throw new Error('Surat belum tersedia');
                        }

                    } catch (error) {
                        console.error('Letter download error:', error);

                        // Show user-friendly error
                        if (error.message.includes('Surat belum tersedia')) {
                            alert('📋 Surat izin belum tersedia. Surat akan dibuat secara otomatis setelah permohonan disetujui oleh admin.');
                        } else {
                            alert('❌ Gagal mengakses surat izin. Silakan hubungi admin laboratorium atau coba lagi nanti.');
                        }
                    }
                },

                // Cooldown system methods
                initializeCooldown() {
                    const stored = localStorage.getItem(`whatsapp_cooldown_${this.requestId}`);
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
                    localStorage.setItem(`whatsapp_cooldown_${this.requestId}`, this.lastMessageTime.toString());
                    this.updateCooldownStatus();
                },

                openWhatsAppChat() {
                    // Check cooldown
                    if (!this.canSendMessage) {
                        alert(`Harap tunggu ${this.cooldownText.replace('Tunggu ', '')} sebelum mengirim pesan lagi.`);
                        return;
                    }
                    // Generate tracking URL
                    const trackingUrl = `${window.location.origin}/layanan/peminjaman-alat/tracking/${this.requestId}`;

                    // Format equipment list professionally
                    const equipmentListFormatted = this.equipmentList.map((item, index) =>
                        `${index + 1}. ${item.name} - ${item.quantity} unit`
                    ).join('\n   ');

                    const message = `Kepada Yang Terhormat,
Admin Laboratorium Gelombang, Optik dan Spektroskopi (GOS)
Departemen Fisika FMIPA Universitas Syiah Kuala

Dengan hormat,

Saya bermaksud untuk melakukan konsultasi terkait permohonan peminjaman peralatan laboratorium dengan rincian sebagai berikut:

=== DETAIL PERMOHONAN PEMINJAMAN ===
- ID Permohonan: ${this.requestId}
- Pembimbing: ${this.borrowerInfo.supervisor?.name || 'Tidak disebutkan'}
- Institusi: ${this.borrowerInfo.supervisor?.nip ? 'Universitas Syiah Kuala' : 'Tidak disebutkan'}
- Tanggal Peminjaman: ${this.scheduleInfo.borrowDate}
- Tanggal Pengembalian: ${this.scheduleInfo.returnDate}
- Waktu: ${this.scheduleInfo.timeRange || 'Sesuai jam operasional laboratorium'}
- Status Permohonan: ${this.currentStatus === 'pending' ? 'Menunggu Persetujuan' :
                         this.currentStatus === 'approved' ? 'Disetujui' :
                         this.currentStatus === 'active' ? 'Sedang Berlangsung' :
                         this.currentStatus === 'completed' ? 'Selesai' :
                         this.currentStatus === 'rejected' ? 'Ditolak' :
                         this.currentStatus === 'cancelled' ? 'Dibatalkan' : this.currentStatus}

=== DAFTAR PERALATAN ===
   ${equipmentListFormatted}

Link Tracking (Klik untuk membuka):
${trackingUrl}

Mohon kiranya Bapak/Ibu dapat memberikan informasi lebih lanjut mengenai:
1. Prosedur pengambilan dan pengembalian peralatan
2. Persyaratan keamanan dan keselamatan kerja
3. Pelatihan penggunaan peralatan (jika diperlukan)
4. Informasi teknis dan panduan operasional
5. Jadwal yang tersedia untuk koordinasi

Demikian permohonan ini saya sampaikan. Atas perhatian dan kerjasamanya, saya ucapkan terima kasih.

Hormat saya,
${this.borrowerInfo.supervisor?.name || 'Pemohon'}
${this.borrowerInfo.supervisor?.nip ? `NIP: ${this.borrowerInfo.supervisor.nip}` : ''}
${this.borrowerInfo.supervisor?.email || ''}

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

                async cancelRequest() {
                    // Check if request can be canceled
                    if (['approved', 'active', 'completed', 'cancelled'].includes(this.currentStatus)) {
                        alert('Permohonan yang sudah disetujui, sedang berlangsung, atau sudah dibatalkan tidak dapat dibatalkan lagi.');
                        return;
                    }

                    const confirmation = confirm('Apakah Anda yakin ingin membatalkan permohonan peminjaman ini?\n\nPermohonan yang sudah dibatalkan tidak dapat dikembalikan.');

                    if (confirmation) {
                        try {
                            // Show loading state
                            const originalText = event.target.innerHTML;
                            event.target.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Membatalkan...';
                            event.target.disabled = true;

                            // Call cancel API
                            const response = await window.LabGOS.cancelBorrow(this.requestId);

                            if (response.success) {
                                // Show success message
                                alert('✅ Permohonan peminjaman berhasil dibatalkan.\n\nHalaman tracking akan ditutup dan Anda akan diarahkan ke halaman peminjaman.');

                                // Clear tracking data from session storage
                                sessionStorage.removeItem('trackingData');

                                // Redirect to borrow request page
                                window.location.href = '/layanan/peminjaman-alat';

                            } else {
                                // Show error message from API
                                alert('❌ ' + (response.message || 'Gagal membatalkan permohonan.'));

                                // Restore button
                                event.target.innerHTML = originalText;
                                event.target.disabled = false;
                            }

                        } catch (error) {
                            console.error('Error canceling request:', error);

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

                // Equipment specification helper methods (from katalog-alat)
                getKeySpecs(equipment) {
                    if (!equipment || !equipment.specifications) {
                        return 'Tidak ada spesifikasi';
                    }

                    let specs = equipment.specifications;

                    // Handle case where specifications is a JSON string
                    if (typeof specs === 'string') {
                        try {
                            specs = JSON.parse(specs);
                        } catch (e) {
                            return 'Tidak ada spesifikasi';
                        }
                    }

                    if (typeof specs !== 'object') {
                        return 'Tidak ada spesifikasi';
                    }

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

                // Detect high-priority specification fields using patterns
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

                // Filter out common metadata fields
                filterMetadataFields(specEntries) {
                    const metadataPatterns = [
                        /.*software.*/i,
                        /.*accessories.*/i,
                        /.*notes.*/i,
                        /.*description.*/i,
                        /.*manual.*/i,
                        /.*documentation.*/i,
                        /.*warranty.*/i,
                        /.*support.*/i,
                        /.*version.*/i,
                        /.*installation.*/i,
                        /.*maintenance.*/i,
                        /.*calibration.*/i,
                        /.*service.*/i,
                        /.*contact.*/i,
                        /.*website.*/i,
                        /.*url.*/i,
                        /.*link.*/i,
                        /.*file.*/i,
                        /.*pdf.*/i,
                        /.*document.*/i,
                        /.*guide.*/i,
                        /.*help.*/i,
                        /.*tutorial.*/i,
                        /.*example.*/i,
                        /.*sample.*/i,
                        /.*demo.*/i,
                        /.*test.*/i
                    ];

                    return specEntries.filter(([key]) =>
                        !metadataPatterns.some(pattern => pattern.test(key))
                    );
                },

                // Format specification key for display
                formatSpecKey(key) {
                    return key
                        .replace(/[_-]/g, ' ')
                        .replace(/([A-Z])/g, ' $1')
                        .replace(/\b\w/g, char => char.toUpperCase())
                        .trim();
                },

                // Safe date formatter with fallback
                formatSafeDate(dateString, fallback = 'tanggal tidak tersedia') {
                    if (!dateString || dateString === 'N/A') return fallback;
                    try {
                        // If it's already formatted, return as is
                        if (dateString.includes(' ')) return dateString;

                        // Try to format the date
                        const date = new Date(dateString);
                        if (isNaN(date.getTime())) return fallback;

                        return date.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                    } catch (error) {
                        console.warn('Date formatting error:', error);
                        return fallback;
                    }
                },

                // Get date range safely
                getDateRangeText() {
                    const startDate = this.formatSafeDate(this.scheduleInfo?.borrowDate, 'tanggal mulai');
                    const endDate = this.formatSafeDate(this.scheduleInfo?.returnDate, 'tanggal selesai');
                    return `${startDate} s.d. ${endDate}`;
                }
            }
        }
    </script>

</x-public.layouts.main>
