<x-public.layouts.main>
    {{-- Mendefinisikan judul halaman untuk layout --}}
    <x-slot:title>
        Kunjungan Laboratorium - Lab GOS USK
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
                        🏛️ Kunjungan
                        <span class="text-secondary">Laboratorium</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Ajukan kunjungan untuk melihat fasilitas dan kegiatan laboratorium kami
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

    <!-- Informasi Kunjungan -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }"
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="text-center mb-12 transition-all duration-1000 ease-out">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">
                    <span class="relative inline-block">
                        ℹ️ Informasi Kunjungan
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                    </span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Pelajari ketentuan dan prosedur kunjungan laboratorium
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <!-- Kapasitas -->
                <div x-data="{ animated: false }"
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="bg-white rounded-2xl shadow-lg p-6 text-center transition-all duration-1000 ease-out hover:shadow-xl transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Kapasitas Maksimal</h3>
                    <p class="text-gray-600 text-sm mb-3">Jumlah pengunjung per sesi</p>
                    <div class="text-3xl font-bold text-primary">25</div>
                    <p class="text-gray-500 text-xs">orang per kunjungan</p>
                </div>

                <!-- Waktu Kunjungan -->
                <div x-data="{ animated: false }"
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="bg-white rounded-2xl shadow-lg p-6 text-center transition-all duration-1000 ease-out hover:shadow-xl transform hover:-translate-y-2"
                     style="transition-delay: 0.1s;">
                    <div class="w-16 h-16 bg-secondary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-secondary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Waktu Operasional</h3>
                    <p class="text-gray-600 text-sm mb-3">Senin - Jumat</p>
                    <div class="text-lg font-bold text-secondary">08:00 - 16:00</div>
                    <p class="text-gray-500 text-xs">WIB</p>
                </div>

                <!-- Durasi -->
                <div x-data="{ animated: false }"
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="bg-white rounded-2xl shadow-lg p-6 text-center transition-all duration-1000 ease-out hover:shadow-xl transform hover:-translate-y-2"
                     style="transition-delay: 0.2s;">
                    <div class="w-16 h-16 bg-green-600 bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hourglass-half text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Durasi Kunjungan</h3>
                    <p class="text-gray-600 text-sm mb-3">Pilihan durasi fleksibel</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-center space-x-4">
                            <div class="bg-green-100 rounded-lg px-3 py-1">
                                <span class="text-sm font-semibold text-green-700">1 Jam</span>
                            </div>
                            <div class="bg-green-100 rounded-lg px-3 py-1">
                                <span class="text-sm font-semibold text-green-700">2 Jam</span>
                            </div>
                            <div class="bg-green-100 rounded-lg px-3 py-1">
                                <span class="text-sm font-semibold text-green-700">3 Jam</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">Sesuai kebutuhan kunjungan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tata Cara Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }" 
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="text-center mb-12 transition-all duration-1000 ease-out">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">
                    <span class="relative inline-block">
                        📋 Tata Cara Kunjungan
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                    </span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Ikuti langkah-langkah berikut untuk mengajukan kunjungan laboratorium
                </p>
            </div>
            
            <!-- Timeline Tata Cara Kunjungan -->
            <div x-data="{ animated: false }" 
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
                 class="bg-gray-50 rounded-2xl shadow-lg p-8 transition-all duration-1000 ease-out">
                
                <!-- Timeline Container -->
                <div class="relative">
                    <!-- Timeline Steps -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
                        
                        <!-- Progress Line (Horizontal) - positioned relative to the circles -->
                        <div class="hidden md:block absolute top-12 left-0 right-0 h-1 bg-gray-200 rounded-full" 
                             style="left: calc(25% / 2 + 3rem); right: calc(25% / 2 + 3rem);"></div>
                        <div class="hidden md:block absolute top-12 h-1 bg-gradient-to-r from-primary to-secondary rounded-full transition-all duration-3000 ease-out" 
                             style="left: calc(25% / 2 + 3rem); right: calc(25% / 2 + 3rem);"></div>

                        <!-- Step 1: Isi Form Permohonan -->
                        <div class="text-center group">
                            <div class="relative mx-auto mb-4 w-24 h-24">
                                <div class="w-24 h-24 bg-gradient-to-br from-primary to-blue-600 rounded-full flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-all duration-300 relative z-10">
                                    <i class="fas fa-edit text-2xl"></i>
                                </div>
                                <div class="absolute -top-1 -right-1 w-8 h-8 bg-secondary text-gray-800 rounded-full flex items-center justify-center font-bold text-sm z-20">
                                    1
                                </div>
                            </div>
                            <h3 class="font-bold text-gray-800 mb-2 group-hover:text-primary transition-colors duration-300">
                                Isi Form Permohonan
                            </h3>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Lengkapi form dengan data kontak, tujuan kunjungan, jadwal, dan upload surat resmi instansi
                            </p>
                        </div>

                        <!-- Step 2: Menunggu Persetujuan -->
                        <div class="text-center group">
                            <div class="relative mx-auto mb-4 w-24 h-24">
                                <div class="w-24 h-24 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-full flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-all duration-300 relative z-10">
                                    <i class="fas fa-clock text-2xl"></i>
                                </div>
                                <div class="absolute -top-1 -right-1 w-8 h-8 bg-secondary text-gray-800 rounded-full flex items-center justify-center font-bold text-sm z-20">
                                    2
                                </div>
                            </div>
                            <h3 class="font-bold text-gray-800 mb-2 group-hover:text-yellow-600 transition-colors duration-300">
                                Menunggu Persetujuan
                            </h3>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Admin laboratorium akan review permohonan dan jadwal yang diminta serta memberikan persetujuan
                            </p>
                        </div>

                        <!-- Step 3: Konfirmasi Kehadiran -->
                        <div class="text-center group">
                            <div class="relative mx-auto mb-4 w-24 h-24">
                                <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-all duration-300 relative z-10">
                                    <i class="fas fa-phone text-2xl"></i>
                                </div>
                                <div class="absolute -top-1 -right-1 w-8 h-8 bg-secondary text-gray-800 rounded-full flex items-center justify-center font-bold text-sm z-20">
                                    3
                                </div>
                            </div>
                            <h3 class="font-bold text-gray-800 mb-2 group-hover:text-green-600 transition-colors duration-300">
                                Konfirmasi Kehadiran
                            </h3>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Konfirmasi kedatangan H-1 melalui WhatsApp dan siapkan peserta sesuai protokol laboratorium
                            </p>
                        </div>

                        <!-- Step 4: Pelaksanaan Kunjungan -->
                        <div class="text-center group">
                            <div class="relative mx-auto mb-4 w-24 h-24">
                                <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-all duration-300 relative z-10">
                                    <i class="fas fa-users text-2xl"></i>
                                </div>
                                <div class="absolute -top-1 -right-1 w-8 h-8 bg-secondary text-gray-800 rounded-full flex items-center justify-center font-bold text-sm z-20">
                                    4
                                </div>
                            </div>
                            <h3 class="font-bold text-gray-800 mb-2 group-hover:text-purple-600 transition-colors duration-300">
                                Pelaksanaan Kunjungan
                            </h3>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Kunjungan dilaksanakan sesuai jadwal dengan didampingi staff laboratorium dan mengisi buku tamu
                            </p>
                        </div>

                    </div>

                    <!-- Status Information Cards -->
                    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <!-- Status Information -->
                        <div class="bg-gradient-to-r from-blue-50 to-primary/10 rounded-xl p-6 border border-blue-100">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white mr-3">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <h4 class="font-bold text-gray-800">Status Tracking</h4>
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Pantau status permohonan kunjungan Anda secara real-time melalui halaman tracking dengan ID yang diberikan.
                            </p>
                        </div>

                        <!-- Required Documents -->
                        <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-xl p-6 border border-red-200">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center text-white mr-3">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <h4 class="font-bold text-gray-800">Surat Resmi</h4>
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Wajib melampirkan surat permohonan resmi dari instansi dengan kop surat dan tanda tangan pimpinan.
                            </p>
                        </div>

                        <!-- Attendance Confirmation -->
                        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center text-white mr-3">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <h4 class="font-bold text-gray-800">Konfirmasi H-1</h4>
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Hubungi admin melalui WhatsApp dari halaman tracking untuk konfirmasi kehadiran H-1 sebelum kunjungan.
                            </p>
                        </div>

                    </div>

                    <!-- Action Button -->
                    <div class="mt-10 text-center">
                        <a href="#form-kunjungan" 
                           class="inline-flex items-center bg-gradient-to-r from-primary to-blue-600 hover:from-blue-600 hover:to-primary text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-arrow-down mr-3"></i>
                            Mulai Isi Form Kunjungan
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Form Kunjungan -->
    <section id="form-kunjungan" class="py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div x-data="formKunjungan()" class="bg-white rounded-3xl shadow-2xl overflow-hidden">

                <!-- Form Header -->
                <div class="bg-gradient-to-r from-primary to-blue-600 px-8 py-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-secondary bg-opacity-20 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white bg-opacity-10 rounded-full translate-y-12 -translate-x-12"></div>

                    <div class="relative z-10 text-center">
                        <div class="w-20 h-20 bg-secondary rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                            <i class="fas fa-users text-gray-800 text-2xl"></i>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-bold mb-2">
                            FORM KUNJUNGAN LABORATORIUM
                        </h2>
                        <p class="text-blue-200">
                            Laboratorium Gelombang, Optik dan Spektroskopi<br>
                            Departemen Fisika FMIPA Universitas Syiah Kuala
                        </p>
                    </div>
                </div>

                <!-- Form Body -->
                <form @submit.prevent="submitForm" class="p-8 space-y-8">

                    <!-- Section 1: Contact Information -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out">

                        <div class="bg-gray-50 rounded-2xl p-6 border-l-4 border-primary">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-user mr-3 text-primary"></i>
                                Data Kontak & Penanggung Jawab
                            </h3>

                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Full Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           x-model="formData.fullName"
                                           placeholder="Nama lengkap penanggung jawab"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 bg-white"
                                           required>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email"
                                           x-model="formData.email"
                                           placeholder="email@domain.com"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 bg-white"
                                           required>
                                </div>

                                <!-- Phone Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nomor Telepon/WhatsApp <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel"
                                           x-model="formData.phone"
                                           placeholder="08xxxxxxxxx"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 bg-white"
                                           required>
                                </div>

                                <!-- Institution -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Instansi/Organisasi <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           x-model="formData.institution"
                                           placeholder="Nama instansi atau organisasi"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 bg-white"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Visit Information -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out"
                         style="transition-delay: 0.2s;">

                        <div class="bg-gray-50 rounded-2xl p-6 border-l-4 border-secondary">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-calendar mr-3 text-secondary"></i>
                                Informasi Kunjungan
                            </h3>

                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Visit Purpose -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tujuan Kunjungan <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="formData.purpose"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300 bg-white"
                                            required>
                                        <option value="">Pilih tujuan kunjungan</option>
                                        <option value="study-visit">Kunjungan Studi</option>
                                        <option value="research">Penelitian</option>
                                        <option value="learning">Pembelajaran</option>
                                        <option value="internship">Magang</option>
                                        <option value="others">Lainnya</option>
                                    </select>
                                </div>

                                <!-- Visit Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Kunjungan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date"
                                           x-model="formData.visitDate"
                                           @change="validateAndUpdateDate($event)"
                                           :min="getMinDate()"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300 bg-white"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">Minimal H+3 dari hari ini (Senin - Jumat)</p>
                                    <div x-show="dateError" class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg">
                                        <p class="text-red-600 text-sm flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span x-text="dateError"></span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Visit Duration -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Durasi Kunjungan <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="formData.visitDuration"
                                            @change="updateAvailableSlots()"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300 bg-white"
                                            required>
                                        <option value="">Pilih durasi kunjungan</option>
                                        <option value="1">1 Jam</option>
                                        <option value="2">2 Jam</option>
                                        <option value="3">3 Jam</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Pilih berapa lama kunjungan akan berlangsung</p>
                                </div>

                                <!-- Available Start Time -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Waktu Mulai <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="formData.startTime"
                                            :disabled="!formData.visitDuration || !formData.visitDate || loadingSlots"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300 bg-white disabled:bg-gray-100 disabled:cursor-not-allowed"
                                            required>
                                        <option value="" x-show="!formData.visitDuration">Pilih durasi terlebih dahulu</option>
                                        <option value="" x-show="formData.visitDuration && loadingSlots">Memuat waktu tersedia...</option>
                                        <option value="" x-show="formData.visitDuration && !loadingSlots && availableSlots.length === 0">Tidak ada waktu tersedia</option>
                                        <template x-for="slot in availableSlots" :key="slot.start">
                                            <option :value="slot.start" x-text="slot.display"></option>
                                        </template>
                                    </select>
                                    
                                    <!-- Calculated End Time Display -->
                                    <div x-show="formData.startTime && formData.visitDuration" 
                                         class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg">
                                        <p class="text-sm text-blue-700">
                                            <i class="fas fa-clock mr-1"></i>
                                            Waktu selesai: <span class="font-semibold" x-text="calculateEndTime()"></span>
                                        </p>
                                    </div>
                                    
                                    <p class="text-xs text-gray-500 mt-1">Waktu yang tersedia berdasarkan durasi dan jadwal lab</p>
                                </div>

                                <!-- Number of Participants -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Jumlah Peserta <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number"
                                           x-model.number="formData.participants"
                                           min="1"
                                           max="25"
                                           placeholder="Maksimal 25 orang"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300 bg-white"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">Maksimal 25 orang per kunjungan</p>
                                </div>
                            </div>

                            <!-- Additional Notes -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan Tambahan <span class="text-gray-500">(Opsional)</span>
                                </label>
                                <textarea x-model="formData.additionalNotes"
                                          rows="4"
                                          placeholder="Informasi tambahan tentang kunjungan, kebutuhan khusus, atau agenda yang ingin disampaikan..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300 bg-white resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Document Upload -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out"
                         style="transition-delay: 0.4s;">

                        <div class="bg-gray-50 rounded-2xl p-6 border-l-4 border-green-500">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-upload mr-3 text-green-500"></i>
                                Upload Dokumen Resmi
                            </h3>

                            <!-- File Upload Area -->
                            <div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Surat Permohonan Resmi dari Instansi <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-sm text-gray-600 mb-4">
                                        Upload surat resmi dari instansi yang meminta izin kunjungan laboratorium
                                    </p>
                                </div>

                                <!-- Drag & Drop Area -->
                                <div @dragover.prevent @dragenter.prevent @drop.prevent="handleFileDrop($event)"
                                     @click="$refs.fileInput.click()"
                                     :class="isDragging ? 'border-primary bg-primary bg-opacity-5' : 'border-gray-300 hover:border-primary hover:bg-gray-50'"
                                     class="border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-all duration-300">
                                    
                                    <div x-show="!uploadedFile">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-lg font-medium text-gray-700 mb-2">
                                            Klik untuk pilih file atau drag & drop di sini
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Format yang diterima: PDF, DOC, DOCX (Maksimal 5MB)
                                        </p>
                                    </div>

                                    <!-- File Preview -->
                                    <div x-show="uploadedFile" class="flex items-center justify-center space-x-4">
                                        <div class="bg-green-100 p-3 rounded-lg">
                                            <i class="fas fa-file-alt text-green-600 text-2xl"></i>
                                        </div>
                                        <div class="text-left">
                                            <p class="font-medium text-gray-800" x-text="uploadedFile?.name"></p>
                                            <p class="text-sm text-gray-500" x-text="formatFileSize(uploadedFile?.size)"></p>
                                        </div>
                                        <button type="button" @click.stop="removeFile()" 
                                                class="text-red-500 hover:text-red-700 transition-colors duration-200">
                                            <i class="fas fa-times-circle text-xl"></i>
                                        </button>
                                    </div>

                                    <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" 
                                           accept=".pdf,.doc,.docx" class="hidden" required>
                                </div>

                                <!-- File Validation Error -->
                                <div x-show="fileError" x-transition
                                     class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <p class="text-red-600 text-sm flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <span x-text="fileError"></span>
                                    </p>
                                </div>

                                <!-- Upload Requirements -->
                                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-start space-x-3">
                                        <i class="fas fa-info-circle text-blue-500 text-lg mt-0.5"></i>
                                        <div>
                                            <h4 class="font-medium text-blue-800 mb-2">Persyaratan Dokumen:</h4>
                                            <ul class="text-sm text-blue-700 space-y-1">
                                                <li>• Surat harus menggunakan kop resmi instansi</li>
                                                <li>• Mencantumkan tujuan dan agenda kunjungan</li>
                                                <li>• Ditandatangani oleh pimpinan instansi</li>
                                                <li>• Format file: PDF, DOC, atau DOCX</li>
                                                <li>• Ukuran maksimal: 5MB</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Agreement -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out"
                         style="transition-delay: 0.6s;">

                        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-1"></i>
                                <div class="flex-1">
                                    <h4 class="font-bold text-yellow-800 mb-3">Ketentuan Kunjungan Laboratorium</h4>
                                    <ul class="text-yellow-700 text-sm leading-relaxed space-y-2 mb-4">
                                        <li>• Kunjungan harus didampingi oleh staff laboratorium yang bertugas</li>
                                        <li>• Wajib mematuhi semua protokol keselamatan laboratorium</li>
                                        <li>• Dilarang keras menyentuh atau mengoperasikan peralatan tanpa izin</li>
                                        <li>• Konfirmasi kehadiran H-1 sebelum jadwal kunjungan melalui WhatsApp</li>
                                        <li>• Pembatalan kunjungan maksimal H-2 sebelum jadwal yang ditentukan</li>
                                        <li>• Segala kerusakan akibat kelalaian menjadi tanggung jawab instansi pengunjung</li>
                                        <li>• Wajib mengisi buku tamu dan daftar hadir peserta kunjungan</li>
                                    </ul>
                                    <label class="flex items-start cursor-pointer">
                                        <input type="checkbox"
                                               x-model="formData.agreement"
                                               class="w-5 h-5 text-secondary border-gray-300 rounded focus:ring-secondary focus:ring-2 mt-1"
                                               required>
                                        <span class="ml-3 text-sm font-medium text-yellow-800">
                                            Saya menyetujui dan akan memastikan semua peserta kunjungan mematuhi 
                                            ketentuan dan peraturan laboratorium yang telah ditetapkan
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out flex flex-col sm:flex-row gap-4 pt-8"
                         style="transition-delay: 0.6s;">

                        <button type="button"
                                @click="window.history.back()"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-4 px-6 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </button>

                        <button type="submit"
                                :disabled="!isFormValid()"
                                :class="isFormValid() ? 'bg-gradient-to-r from-secondary to-yellow-500 hover:from-yellow-500 hover:to-secondary text-gray-800' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                class="flex-1 py-4 px-6 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 flex items-center justify-center shadow-lg hover:shadow-xl">
                            <i class="fas fa-paper-plane mr-2"></i>
                            <span x-show="!submitting">Kirim Permohonan</span>
                            <span x-show="submitting" class="flex items-center">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Mengirim...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        function formKunjungan() {
            return {
                submitting: false,

                formData: {
                    fullName: '',
                    email: '',
                    phone: '',
                    institution: '',
                    purpose: '',
                    visitDate: '',
                    visitDuration: '',
                    startTime: '',
                    participants: 1,
                    additionalNotes: '',
                    agreement: false
                },

                // File upload properties
                uploadedFile: null,
                isDragging: false,
                fileError: '',

                // Time slot management
                availableSlots: [],
                loadingSlots: false,
                slotsError: '',
                dateError: '',

                getMinDate() {
                    const today = new Date();
                    today.setDate(today.getDate() + 3); // H+3
                    return today.toISOString().split('T')[0];
                },

                // Validate date and check for weekends
                validateAndUpdateDate(event) {
                    const selectedDate = event.target.value;
                    this.dateError = '';
                    
                    if (!selectedDate) {
                        this.updateAvailableSlots();
                        return;
                    }
                    
                    const date = new Date(selectedDate);
                    const dayOfWeek = date.getDay(); // 0 = Sunday, 6 = Saturday
                    
                    if (dayOfWeek === 0 || dayOfWeek === 6) {
                        this.dateError = 'Kunjungan laboratorium hanya tersedia pada hari kerja (Senin - Jumat)';
                        this.formData.visitDate = '';
                        this.availableSlots = [];
                        return;
                    }
                    
                    // If valid, update available slots
                    this.updateAvailableSlots();
                },

                isFormValid() {
                    const requiredFields = [
                        'fullName', 'email', 'phone', 'institution',
                        'purpose', 'visitDate', 'visitDuration', 'startTime'
                    ];

                    const fieldsValid = requiredFields.every(field =>
                        this.formData[field] && this.formData[field].toString().trim() !== ''
                    );

                    return fieldsValid &&
                           this.formData.participants >= 1 &&
                           this.formData.participants <= 50 &&
                           this.formData.agreement &&
                           !this.dateError; // Also check for date validation errors
                },

                // Calculate end time based on start time and duration
                calculateEndTime() {
                    if (!this.formData.startTime || !this.formData.visitDuration) {
                        return '';
                    }

                    const [hours, minutes] = this.formData.startTime.split(':').map(Number);
                    const durationHours = parseInt(this.formData.visitDuration);
                    
                    const endHour = hours + durationHours;
                    const endTime = `${endHour.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
                    
                    return `${endTime} WIB`;
                },

                // Generate base time slots based on duration
                generateBaseSlots(duration) {
                    const slots = [];
                    const durationHours = parseInt(duration);
                    
                    // Operating hours: 08:00-16:00, lunch break: 12:00-13:00
                    const startHour = 8;
                    const endHour = 16;
                    const lunchStart = 12;
                    const lunchEnd = 13;
                    
                    for (let hour = startHour; hour <= endHour - durationHours; hour++) {
                        const endTimeHour = hour + durationHours;
                        
                        // Skip if slot would overlap with lunch break
                        if (hour < lunchEnd && endTimeHour > lunchStart) {
                            continue;
                        }
                        
                        // Skip if slot would end after operating hours
                        if (endTimeHour > endHour) {
                            continue;
                        }
                        
                        const startTime = `${hour.toString().padStart(2, '0')}:00`;
                        const endTime = `${endTimeHour.toString().padStart(2, '0')}:00`;
                        
                        slots.push({
                            start: startTime,
                            end: endTime,
                            display: `${startTime} - ${endTime} WIB (${durationHours} jam)`
                        });
                    }
                    
                    return slots;
                },

                // Update available slots when duration or date changes
                async updateAvailableSlots() {
                    if (!this.formData.visitDuration || !this.formData.visitDate) {
                        this.availableSlots = [];
                        return;
                    }

                    this.loadingSlots = true;
                    this.slotsError = '';
                    this.formData.startTime = ''; // Reset start time selection

                    try {
                        // Wait for LabGOS to be available
                        if (!window.LabGOS) {
                            // Fallback to generated slots if API not available
                            console.warn('LabGOS API not available, using generated slots');
                            this.availableSlots = this.generateBaseSlots(this.formData.visitDuration);
                            return;
                        }

                        // Try to get available slots from API
                        try {
                            const response = await window.LabGOS.getAvailableTimeSlots(
                                this.formData.visitDate, 
                                this.formData.visitDuration
                            );
                            
                            if (response.success && response.data.available_slots) {
                                this.availableSlots = response.data.available_slots;
                            } else {
                                // Fallback to generated slots
                                this.availableSlots = this.generateBaseSlots(this.formData.visitDuration);
                            }
                        } catch (apiError) {
                            console.warn('API call failed, using generated slots:', apiError);
                            // Fallback to generated slots
                            this.availableSlots = this.generateBaseSlots(this.formData.visitDuration);
                        }

                    } catch (error) {
                        console.error('Error updating available slots:', error);
                        this.slotsError = 'Gagal memuat waktu tersedia. Silakan coba lagi.';
                        this.availableSlots = [];
                    } finally {
                        this.loadingSlots = false;
                    }
                },

                async submitForm() {
                    if (!this.isFormValid()) return;

                    this.submitting = true;

                    try {
                        // Wait for LabGOS to be available
                        let attempts = 0;
                        while (!window.LabGOS && attempts < 10) {
                            await new Promise(resolve => setTimeout(resolve, 100));
                            attempts++;
                        }

                        if (!window.LabGOS) {
                            throw new Error('Sistem tidak dapat memuat. Silakan refresh halaman.');
                        }

                        // Calculate end time
                        const [hours, minutes] = this.formData.startTime.split(':').map(Number);
                        const durationHours = parseInt(this.formData.visitDuration);
                        const endHour = hours + durationHours;
                        const endTime = `${endHour.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

                        // Prepare FormData for file upload support
                        const formData = new FormData();
                        formData.append('visitor_name', this.formData.fullName);
                        formData.append('visitor_email', this.formData.email);
                        formData.append('visitor_phone', this.formData.phone);
                        formData.append('institution', this.formData.institution);
                        formData.append('group_size', this.formData.participants);
                        formData.append('visit_date', this.formData.visitDate);
                        formData.append('start_time', this.formData.startTime);
                        formData.append('end_time', endTime);
                        formData.append('visit_purpose', this.formData.purpose);
                        formData.append('purpose_description', this.formData.additionalNotes || '');
                        formData.append('special_requirements', this.formData.additionalNotes || '');

                        // Add uploaded file if present
                        if (this.uploadedFile) {
                            formData.append('request_letter', this.uploadedFile);
                            console.log('Including uploaded file:', this.uploadedFile.name);
                        }

                        console.log('Submitting visit request with FormData');

                        // Submit to API using direct fetch (since LabGOS client doesn't handle FormData)
                        const response = await fetch('/api/requests/visit', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                // Don't set Content-Type header - let browser set it with boundary for FormData
                            }
                        });

                        const responseData = await response.json();

                        if (responseData.success) {
                            // Store tracking data for next page
                            sessionStorage.setItem('visitTrackingData', JSON.stringify({
                                requestId: responseData.data.request_id,
                                submittedAt: responseData.data.submitted_at,
                                status: responseData.data.status
                            }));

                            // Show success message
                            alert(`✅ Permohonan kunjungan berhasil dikirim!\n\nID Kunjungan: ${responseData.data.request_id}\n\nAnda akan diarahkan ke halaman tracking.`);

                            // Redirect to tracking page
                            window.location.href = `/layanan/kunjungan/confirmation/${responseData.data.request_id}`;
                        } else {
                            throw new Error(responseData.message || 'Gagal mengirim permohonan');
                        }

                    } catch (error) {
                        console.error('Error submitting form:', error);
                        
                        let errorMessage = 'Terjadi kesalahan saat mengirim permohonan.';
                        
                        if (error.response && error.response.data) {
                            if (error.response.data.errors) {
                                // Validation errors
                                const errors = Object.values(error.response.data.errors).flat();
                                errorMessage = errors.join('\n');
                            } else if (error.response.data.message) {
                                errorMessage = error.response.data.message;
                            }
                        } else if (error.message) {
                            errorMessage = error.message;
                        }

                        alert('❌ ' + errorMessage + '\n\nSilakan periksa kembali data yang diisi dan coba lagi.');
                    } finally {
                        this.submitting = false;
                    }
                },

                // File upload methods
                handleFileDrop(event) {
                    this.isDragging = false;
                    const files = event.dataTransfer.files;
                    if (files.length > 0) {
                        this.validateAndSetFile(files[0]);
                    }
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.validateAndSetFile(file);
                    }
                },

                validateAndSetFile(file) {
                    this.fileError = '';

                    // Check file type
                    const allowedTypes = ['.pdf', '.doc', '.docx'];
                    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                    
                    if (!allowedTypes.includes(fileExtension)) {
                        this.fileError = 'Format file tidak didukung. Gunakan format PDF, DOC, atau DOCX.';
                        return;
                    }

                    // Check file size (5MB = 5 * 1024 * 1024 bytes)
                    if (file.size > 5 * 1024 * 1024) {
                        this.fileError = 'Ukuran file terlalu besar. Maksimal 5MB.';
                        return;
                    }

                    this.uploadedFile = file;
                },

                removeFile() {
                    this.uploadedFile = null;
                    this.fileError = '';
                    // Reset file input
                    this.$refs.fileInput.value = '';
                },

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }
            }
        }

    </script>

</x-public.layouts.main>
