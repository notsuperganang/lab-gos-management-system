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
            
            <div x-data="trackingPeminjaman()" class="space-y-8">
                
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
                                <p class="text-blue-200">
                                    ID Permohonan: <span class="font-bold text-secondary" x-text="requestId"></span>
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
                                    <div class="bg-gray-50 rounded-xl p-4">
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
                                 :class="currentStatus === 'approved' ? 'bg-green-100' : 'bg-gray-200'">
                                <i class="fas fa-download text-2xl"
                                   :class="currentStatus === 'approved' ? 'text-green-600' : 'text-gray-400'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Download Surat</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="currentStatus === 'approved'">Surat izin siap diunduh</span>
                                <span x-show="currentStatus !== 'approved'">Menunggu persetujuan</span>
                            </p>
                            <button @click="downloadLetter()"
                                    :disabled="currentStatus !== 'approved'"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105"
                                    :class="currentStatus === 'approved' ? 
                                           'bg-green-500 hover:bg-green-600 text-white shadow-lg hover:shadow-xl' : 
                                           'bg-gray-300 text-gray-500 cursor-not-allowed'">
                                <span x-show="currentStatus === 'approved'">Download</span>
                                <span x-show="currentStatus !== 'approved'">Belum Tersedia</span>
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

                        <!-- Cancel Peminjaman -->
                        <div class="bg-red-50 rounded-2xl p-6 text-center hover:bg-red-100 transition-all duration-300">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                 :class="(currentStatus === 'approved' || currentStatus === 'completed') ? 'bg-gray-200' : 'bg-red-100'">
                                <i class="fas fa-times text-2xl"
                                   :class="(currentStatus === 'approved' || currentStatus === 'completed') ? 'text-gray-400' : 'text-red-600'"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">Batalkan Peminjaman</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                <span x-show="currentStatus === 'pending'">Dapat dibatalkan sebelum disetujui</span>
                                <span x-show="currentStatus === 'approved' || currentStatus === 'completed'">Tidak dapat dibatalkan</span>
                            </p>
                            <button @click="cancelRequest()"
                                    :disabled="currentStatus === 'approved' || currentStatus === 'completed'"
                                    class="w-full py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105"
                                    :class="(currentStatus === 'approved' || currentStatus === 'completed') ? 
                                           'bg-gray-300 text-gray-500 cursor-not-allowed' : 
                                           'bg-red-500 hover:bg-red-600 text-white shadow-lg hover:shadow-xl'">
                                <span x-show="currentStatus === 'pending'">Batalkan</span>
                                <span x-show="currentStatus === 'approved' || currentStatus === 'completed'">Tidak Tersedia</span>
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
                requestId: 'REQ-2025-001',
                submittedDate: '4 Agustus 2025, 14:30',
                currentStatus: 'pending', // pending, approved, rejected, completed
                
                // Status Steps
                statusSteps: [
                    {
                        title: 'Permohonan Diajukan',
                        description: 'Permohonan peminjaman telah berhasil dikirim dan sedang dalam antrian review.',
                        icon: 'fas fa-paper-plane',
                        status: 'completed',
                        timestamp: '4 Agustus 2025, 14:30'
                    },
                    {
                        title: 'Review Admin',
                        description: 'Tim admin sedang melakukan review terhadap permohonan peminjaman Anda.',
                        icon: 'fas fa-search',
                        status: 'current',
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
                    members: [
                        { name: 'Ahmad Fadli', nim: '1808107010001' },
                        { name: 'Siti Rahmah', nim: '1808107010002' }
                    ],
                    supervisor: {
                        name: 'Dr. Ir. Muhammad Syukri, M.Sc',
                        nip: '196801011993031002',
                        email: 'syukri@unsyiah.ac.id',
                        phone: '081234567890'
                    }
                },
                
                // Schedule Info
                scheduleInfo: {
                    borrowDate: '10 Agustus 2025',
                    returnDate: '12 Agustus 2025',
                    timeRange: '08:00 - 17:00 WIB',
                    purpose: 'Penelitian untuk tugas akhir tentang karakterisasi material semikonduktor menggunakan spektroskopi UV-Vis dan analisis optik menggunakan laser HeNe.'
                },
                
                // Equipment List
                equipmentList: [
                    { 
                        id: 1, 
                        name: 'Spektrometer UV-Vis', 
                        specs: 'Range: 190-1100 nm, Resolusi: 1.8 nm', 
                        quantity: 2, 
                        category: 'spektroskopi' 
                    },
                    { 
                        id: 2, 
                        name: 'Laser HeNe', 
                        specs: 'Wavelength: 632.8 nm, Power: 5 mW', 
                        quantity: 1, 
                        category: 'optik' 
                    }
                ],
                
                // Methods
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
                    if (this.currentStatus !== 'approved') {
                        alert('Surat izin hanya dapat diunduh setelah permohonan disetujui.');
                        return;
                    }
                    
                    // Simulate file download
                    const link = document.createElement('a');
                    link.href = '#'; // URL file akan diisi dari backend
                    link.download = `Surat_Izin_Peminjaman_${this.requestId}.pdf`;
                    link.click();
                    
                    alert('Surat izin peminjaman sedang diunduh...');
                },
                
                sendWhatsAppConfirmation() {
                    const message = `Halo Admin Lab GOS USK,

Saya ingin mengkonfirmasi peminjaman alat dengan detail:
- ID Permohonan: ${this.requestId}
- Tanggal Peminjaman: ${this.scheduleInfo.borrowDate}
- Waktu: ${this.scheduleInfo.timeRange}

Alat yang dipinjam:
${this.equipmentList.map((item, index) => `${index + 1}. ${item.name} (${item.quantity} unit)`).join('\n')}

Terima kasih.`;
                    
                    const phoneNumber = '6281234567890'; // Nomor admin lab
                    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
                    
                    window.open(whatsappUrl, '_blank');
                },
                
                async cancelRequest() {
                    if (this.currentStatus === 'approved' || this.currentStatus === 'completed') {
                        alert('Permohonan yang sudah disetujui tidak dapat dibatalkan.');
                        return;
                    }
                    
                    const confirmation = confirm('Apakah Anda yakin ingin membatalkan permohonan peminjaman ini?');
                    
                    if (confirmation) {
                        try {
                            // Simulate API call
                            await new Promise(resolve => setTimeout(resolve, 1000));
                            
                            alert('Permohonan peminjaman berhasil dibatalkan.');
                            
                            // Redirect to home or catalog
                            window.location.href = '/layanan/peminjaman-alat';
                            
                        } catch (error) {
                            console.error('Error canceling request:', error);
                            alert('Terjadi kesalahan saat membatalkan permohonan. Silakan coba lagi.');
                        }
                    }
                }
            }
        }
    </script>

</x-public.layouts.main>