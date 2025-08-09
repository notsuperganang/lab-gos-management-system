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
            
            <div x-data="trackingKunjungan()" class="space-y-8">
                
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
                                    <i class="fas fa-users mr-3 text-secondary"></i>
                                    Status Kunjungan
                                </h2>
                                <p class="text-blue-200">
                                    ID Kunjungan: <span class="font-bold text-secondary" x-text="visitId"></span>
                                </p>
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
                                                    <span x-text="step.timestamp || 'Menunggu...'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Kunjungan -->
                <div x-data="{ animated: false }" 
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
                                        <div class="text-sm text-gray-600 mb-1">Durasi Estimasi</div>
                                        <div class="font-semibold text-gray-800">2-3 jam</div>
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
                                 :class="currentStatus === 'approved' || currentStatus === 'ready' ? 'bg-green-100' : 'bg-gray-200'">
                                <i class="fas fa-download text-2xl"
                                   :class="currentStatus === 'approved' || currentStatus === 'ready' ? 'text-green-600' : 'text-gray-400'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Download Surat</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="currentStatus === 'approved' || currentStatus === 'ready'">Surat izin siap diunduh</span>
                                <span x-show="currentStatus !== 'approved' && currentStatus !== 'ready'">Menunggu persetujuan</span>
                            </p>
                            <button @click="downloadLetter()"
                                    :disabled="currentStatus !== 'approved' && currentStatus !== 'ready'"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105"
                                    :class="(currentStatus === 'approved' || currentStatus === 'ready') ? 
                                           'bg-green-500 hover:bg-green-600 text-white shadow-lg hover:shadow-xl' : 
                                           'bg-gray-300 text-gray-500 cursor-not-allowed'">
                                <span x-show="currentStatus === 'approved' || currentStatus === 'ready'">Download</span>
                                <span x-show="currentStatus !== 'approved' && currentStatus !== 'ready'">Belum Tersedia</span>
                            </button>
                        </div>

                        <!-- WhatsApp Konfirmasi -->
                        <div class="bg-green-50 rounded-2xl p-6 text-center hover:bg-green-100 transition-all duration-300">
                            <div class="w-16 h-16 bg-green-100 mx-auto mb-4 rounded-full flex items-center justify-center">
                                <i class="fab fa-whatsapp text-2xl text-green-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Konfirmasi WhatsApp</h4>
                            <p class="text-sm text-gray-600 mb-4">Hubungi admin untuk konfirmasi</p>
                            <button @click="sendWhatsAppConfirmation()"
                                    class="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                Hubungi Admin
                            </button>
                        </div>

                        <!-- Cancel Kunjungan -->
                        <div class="bg-red-50 rounded-2xl p-6 text-center hover:bg-red-100 transition-all duration-300">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                 :class="(currentStatus === 'approved' || currentStatus === 'ready' || currentStatus === 'completed') ? 'bg-gray-200' : 'bg-red-100'">
                                <i class="fas fa-times text-2xl"
                                   :class="(currentStatus === 'approved' || currentStatus === 'ready' || currentStatus === 'completed') ? 'text-gray-400' : 'text-red-600'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Batalkan Kunjungan</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="currentStatus === 'pending' || currentStatus === 'under_review'">Dapat dibatalkan sebelum disetujui</span>
                                <span x-show="currentStatus === 'approved' || currentStatus === 'ready' || currentStatus === 'completed'">Tidak dapat dibatalkan</span>
                            </p>
                            <button @click="cancelVisit()"
                                    :disabled="currentStatus === 'approved' || currentStatus === 'ready' || currentStatus === 'completed'"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105"
                                    :class="(currentStatus === 'approved' || currentStatus === 'ready' || currentStatus === 'completed') ? 
                                           'bg-gray-300 text-gray-500 cursor-not-allowed' : 
                                           'bg-red-500 hover:bg-red-600 text-white shadow-lg hover:shadow-xl'">
                                <span x-show="currentStatus === 'pending' || currentStatus === 'under_review'">Batalkan</span>
                                <span x-show="currentStatus === 'approved' || currentStatus === 'ready' || currentStatus === 'completed'">Tidak Tersedia</span>
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
                visitData: null,
                
                init() {
                    this.visitData = this.getVisitDataById('{{ $visitId ?? "KNJ-2025-001" }}');
                    this.setupStatusSteps();
                },
                
                getVisitDataById(visitId) {
                    // Dummy data based on visit ID
                    const dummyData = {
                        'KNJ-2025-001': {
                            id: 'KNJ-2025-001',
                            fullName: 'Dr. Ahmad Budiman',
                            email: 'ahmad.budiman@sman1.ac.id',
                            phone: '081234567890',
                            institution: 'SMAN 1 Banda Aceh',
                            purpose: 'study-visit',
                            visitDate: '15 Januari 2025',
                            visitTime: 'Pagi (08:00 - 11:00)',
                            participants: 25,
                            additionalNotes: 'Kunjungan dalam rangka pembelajaran mata pelajaran Fisika untuk kelas XII IPA',
                            status: 'approved',
                            submittedAt: '8 Januari 2025, 10:30'
                        },
                        'KNJ-2025-002': {
                            id: 'KNJ-2025-002',
                            fullName: 'Prof. Siti Rahmawati',
                            email: 'siti.rahmawati@univ.ac.id',
                            phone: '082345678901',
                            institution: 'Universitas Negeri Padang',
                            purpose: 'research',
                            visitDate: '20 Januari 2025',
                            visitTime: 'Siang (13:00 - 16:00)',
                            participants: 8,
                            additionalNotes: 'Penelitian kolaborasi tentang spektroskopi inframerah',
                            status: 'under_review',
                            submittedAt: '9 Januari 2025, 14:15'
                        },
                        'KNJ-2025-003': {
                            id: 'KNJ-2025-003',
                            fullName: 'Ir. Bambang Sutrisno',
                            email: 'bambang.s@smkn2.sch.id',
                            phone: '083456789012',
                            institution: 'SMK Negeri 2 Banda Aceh',
                            purpose: 'learning',
                            visitDate: '12 Januari 2025',
                            visitTime: 'Pagi (08:00 - 11:00)',
                            participants: 15,
                            additionalNotes: 'Pembelajaran praktik untuk siswa jurusan Teknik Elektronika',
                            status: 'ready',
                            submittedAt: '5 Januari 2025, 09:00'
                        }
                    };

                    return dummyData[visitId] || {
                        id: visitId,
                        fullName: 'Pengguna Demo',
                        email: 'demo@example.com',
                        phone: '081234567890',
                        institution: 'Instansi Demo',
                        purpose: 'study-visit',
                        visitDate: '20 Januari 2025',
                        visitTime: 'Pagi (08:00 - 11:00)',
                        participants: 10,
                        additionalNotes: 'Data demo untuk testing sistem',
                        status: 'pending',
                        submittedAt: '10 Januari 2025, 12:00'
                    };
                },

                setupStatusSteps() {
                    const baseSteps = [
                        {
                            title: 'Permohonan Diajukan',
                            description: 'Permohonan kunjungan telah berhasil dikirim dan sedang dalam antrian review.',
                            icon: 'fas fa-paper-plane',
                            status: 'completed',
                            timestamp: this.visitData.submittedAt
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
                        }
                    ];

                    // Update status based on current visit status
                    switch(this.visitData.status) {
                        case 'under_review':
                            baseSteps[1].status = 'current';
                            break;
                        case 'approved':
                            baseSteps[1].status = 'completed';
                            baseSteps[1].timestamp = '9 Januari 2025, 16:45';
                            baseSteps[2].status = 'current';
                            break;
                        case 'ready':
                            baseSteps[1].status = 'completed';
                            baseSteps[1].timestamp = '9 Januari 2025, 16:45';
                            baseSteps[2].status = 'completed';
                            baseSteps[2].timestamp = '10 Januari 2025, 10:20';
                            baseSteps[3].status = 'current';
                            break;
                        case 'completed':
                            baseSteps[1].status = 'completed';
                            baseSteps[1].timestamp = '9 Januari 2025, 16:45';
                            baseSteps[2].status = 'completed';
                            baseSteps[2].timestamp = '10 Januari 2025, 10:20';
                            baseSteps[3].status = 'completed';
                            baseSteps[3].timestamp = '12 Januari 2025, 11:30';
                            break;
                        default: // pending
                            baseSteps[1].status = 'current';
                    }

                    this.statusSteps = baseSteps;
                },

                // Computed properties
                get visitId() { return this.visitData?.id || 'KNJ-2025-001'; },
                get submittedDate() { return this.visitData?.submittedAt || ''; },
                get currentStatus() { return this.visitData?.status || 'pending'; },
                
                get visitorInfo() {
                    return {
                        fullName: this.visitData?.fullName || '',
                        institution: this.visitData?.institution || '',
                        email: this.visitData?.email || '',
                        phone: this.visitData?.phone || '',
                        purpose: this.visitData?.purpose || ''
                    };
                },
                
                get scheduleInfo() {
                    return {
                        visitDate: this.visitData?.visitDate || '',
                        visitTime: this.visitData?.visitTime || '',
                        participants: this.visitData?.participants || 0,
                        additionalNotes: this.visitData?.additionalNotes || ''
                    };
                },

                get visitAgenda() {
                    // Default agenda based on visit purpose
                    const agendas = {
                        'study-visit': [
                            { id: 1, activity: 'Presentasi Selamat Datang', description: 'Pengenalan laboratorium dan fasilitas', location: 'Ruang Meeting', duration: '30 menit' },
                            { id: 2, activity: 'Tur Laboratorium Optik', description: 'Kunjungan ke lab optik dan penjelasan peralatan', location: 'Lab Optik', duration: '45 menit' },
                            { id: 3, activity: 'Demonstrasi Spektroskopi', description: 'Demo penggunaan spektrometer', location: 'Lab Spektroskopi', duration: '60 menit' },
                            { id: 4, activity: 'Sesi Tanya Jawab', description: 'Diskusi dan tanya jawab dengan staff lab', location: 'Ruang Meeting', duration: '30 menit' }
                        ],
                        'research': [
                            { id: 1, activity: 'Briefing Penelitian', description: 'Diskusi rencana penelitian dan kebutuhan', location: 'Ruang Meeting', duration: '45 menit' },
                            { id: 2, activity: 'Setup Peralatan', description: 'Persiapan dan kalibrasi instrumen penelitian', location: 'Lab Spektroskopi', duration: '90 menit' },
                            { id: 3, activity: 'Kolaborasi Penelitian', description: 'Pelaksanaan penelitian bersama', location: 'Lab Spektroskopi', duration: '120 menit' }
                        ],
                        'learning': [
                            { id: 1, activity: 'Orientasi Lab', description: 'Pengenalan keselamatan dan prosedur lab', location: 'Ruang Meeting', duration: '30 menit' },
                            { id: 2, activity: 'Praktik Gelombang', description: 'Hands-on learning dengan peralatan gelombang', location: 'Lab Gelombang', duration: '75 menit' },
                            { id: 3, activity: 'Praktik Optik', description: 'Pembelajaran praktis sistem optik', location: 'Lab Optik', duration: '75 menit' },
                            { id: 4, activity: 'Evaluasi & Review', description: 'Evaluasi pembelajaran dan feedback', location: 'Ruang Meeting', duration: '30 menit' }
                        ]
                    };

                    return agendas[this.visitData?.purpose] || agendas['study-visit'];
                },

                // Status helper methods
                statusSteps: [],

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
                    const totalProgress = completedSteps + (currentStep >= 0 ? 0.5 : 0);
                    return (totalProgress / this.statusSteps.length) * 100;
                },
                
                getStepClass(status) {
                    switch(status) {
                        case 'completed':
                            return 'bg-green-500 text-white';
                        case 'current':
                            return 'bg-primary text-white animate-pulse';
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
                        case 'pending':
                            return 'Menunggu';
                        default:
                            return 'Menunggu';
                    }
                },
                
                // Actions
                downloadLetter() {
                    if (this.currentStatus !== 'approved' && this.currentStatus !== 'ready') {
                        alert('Surat izin hanya dapat diunduh setelah permohonan disetujui.');
                        return;
                    }
                    
                    // Simulate file download
                    alert('Surat izin kunjungan sedang diunduh...');
                },
                
                sendWhatsAppConfirmation() {
                    const message = `Halo Admin Lab GOS USK,

Saya ingin mengkonfirmasi kunjungan laboratorium dengan detail:
- ID Kunjungan: ${this.visitId}
- Tanggal Kunjungan: ${this.scheduleInfo.visitDate}
- Waktu: ${this.scheduleInfo.visitTime}
- Jumlah Peserta: ${this.scheduleInfo.participants} orang
- Instansi: ${this.visitorInfo.institution}

Mohon konfirmasi dan informasi lebih lanjut mengenai persiapan kunjungan.

Terima kasih.`;
                    
                    const phoneNumber = '6281234567890'; // Nomor admin lab
                    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
                    
                    window.open(whatsappUrl, '_blank');
                },
                
                async cancelVisit() {
                    if (this.currentStatus === 'approved' || this.currentStatus === 'ready' || this.currentStatus === 'completed') {
                        alert('Kunjungan yang sudah disetujui tidak dapat dibatalkan. Hubungi admin untuk pembatalan.');
                        return;
                    }
                    
                    const confirmation = confirm('Apakah Anda yakin ingin membatalkan permohonan kunjungan ini?');
                    
                    if (confirmation) {
                        try {
                            // Simulate API call
                            await new Promise(resolve => setTimeout(resolve, 1000));
                            
                            alert('Permohonan kunjungan berhasil dibatalkan.');
                            
                            // Redirect to home or visit form
                            window.location.href = '/layanan/kunjungan';
                            
                        } catch (error) {
                            console.error('Error canceling visit:', error);
                            alert('Terjadi kesalahan saat membatalkan permohonan. Silakan coba lagi.');
                        }
                    }
                }
            }
        }
    </script>

</x-public.layouts.main>