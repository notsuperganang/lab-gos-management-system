<x-public.layouts.main>
    {{-- Mendefinisikan judul halaman untuk layout --}}
    <x-slot:title>
        Form Peminjaman Alat - Lab GOS USK
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
                        📝 Form
                        <span class="text-secondary">Peminjaman Alat</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Lengkapi formulir untuk mengajukan peminjaman peralatan laboratorium
                </p>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-2 inline-block">
                    <p class="text-white flex items-center justify-center">
                        <i class="fas fa-file-alt mr-2 text-secondary"></i>
                        Surat Izin Pemakaian Alat
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Form Container -->
            <div x-data="formPeminjaman()" x-init="init()" class="bg-white rounded-3xl shadow-2xl overflow-hidden">

                <!-- Form Header -->
                <div class="bg-gradient-to-r from-primary to-blue-600 px-8 py-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-secondary bg-opacity-20 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white bg-opacity-10 rounded-full translate-y-12 -translate-x-12"></div>

                    <div class="relative z-10 text-center">
                        <div class="w-20 h-20 bg-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-list text-gray-800 text-2xl"></i>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-bold mb-2">
                            FORM SURAT IZIN PEMAKAIAN ALAT
                        </h2>
                        <p class="text-blue-200">
                            Laboratorium Gelombang, Optik dan Spektroskopi<br>
                            Departemen Fisika FMIPA Universitas Syiah Kuala
                        </p>
                    </div>
                </div>

                <!-- Form Body -->
                <form @submit.prevent="submitForm" class="p-8 space-y-8">

                    <!-- Section 1: Identitas Peminjam -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out">

                        <div class="bg-gray-50 rounded-2xl p-6 mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-user mr-3 text-primary"></i>
                                Identitas Peminjam
                            </h3>
                            <p class="text-gray-600 mb-6">
                                Saya yang bertanda tangan di bawah ini sebagai <strong>Dosen Pembimbing/Pimpinan Instansi</strong> dari mahasiswa/staf/peneliti:
                            </p>

                            <!-- Dynamic Member List -->
                            <div class="space-y-4">
                                <template x-for="(member, index) in members" :key="index">
                                    <div class="bg-white rounded-xl p-4 border border-gray-200 hover:border-primary transition-colors duration-300">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-bold">
                                                    <span x-text="index + 1"></span>
                                                </div>
                                            </div>
                                            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                                    <input type="text"
                                                           x-model="member.name"
                                                           placeholder="Masukkan nama lengkap"
                                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                                                           required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                                                    <input type="text"
                                                           x-model="member.nim"
                                                           placeholder="Masukkan NIM"
                                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                                                           required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                                                    <input type="text"
                                                           x-model="member.study_program"
                                                           placeholder="Contoh: Fisika, Matematika"
                                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                                                           required>
                                                </div>
                                            </div>
                                            <button type="button"
                                                    x-show="members.length > 1"
                                                    @click="removeMember(index)"
                                                    class="flex-shrink-0 w-10 h-10 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded-full flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Add Member Button -->
                                <button type="button"
                                        @click="addMember()"
                                        class="w-full bg-gray-100 hover:bg-primary hover:text-white border-2 border-dashed border-gray-300 hover:border-primary rounded-xl py-4 transition-all duration-300 transform hover:scale-[1.02]">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah Anggota
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Daftar Alat -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out"
                         style="transition-delay: 0.2s;">

                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-tools mr-3 text-secondary"></i>
                            Alat yang Dipinjam
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Mohon diberikan izin kepada mahasiswa/staf/peneliti tersebut agar dapat memakai peralatan sebagai berikut:
                        </p>

                        <!-- Equipment Table -->
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
                                        <template x-for="(item, index) in selectedEquipments" :key="item.id">
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

                                        <!-- Empty state -->
                                        <tr x-show="selectedEquipments.length === 0">
                                            <td colspan="4" class="px-6 py-12 text-center">
                                                <div class="text-gray-400">
                                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                                    <p class="text-lg font-medium">Belum ada alat dipilih</p>
                                                    <p class="text-sm">Kembali ke katalog untuk memilih peralatan</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Equipment Summary -->
                        <div x-show="selectedEquipments.length > 0" class="mt-4 bg-blue-50 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-blue-800 font-medium">Total Peralatan:</span>
                                <div class="flex items-center space-x-4">
                                    <span class="text-blue-600" x-text="selectedEquipments.length + ' jenis alat'"></span>
                                    <span class="text-blue-600" x-text="getTotalQuantity() + ' unit'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Detail Penggunaan -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out"
                         style="transition-delay: 0.4s;">

                        <div class="bg-gray-50 rounded-2xl p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-info-circle mr-3 text-green-600"></i>
                                Detail Penggunaan
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tujuan Penelitian -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tujuan Penelitian/Praktikum <span class="text-red-500">*</span>
                                    </label>
                                    <textarea x-model="formData.purpose"
                                              rows="4"
                                              placeholder="Jelaskan tujuan penggunaan peralatan laboratorium..."
                                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                                              required></textarea>
                                </div>

                                <!-- Tanggal Peminjaman -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Peminjaman <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date"
                                           x-model="formData.borrowDate"
                                           min="{{ now()->addDay()->toDateString() }}"
                                           @change="validateAndUpdateBorrowDate($event)"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                                           required>
                                    <p x-show="isWeekend(formData.borrowDate)" class="text-red-500 text-xs mt-1">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Tanggal peminjaman tidak boleh jatuh pada hari Sabtu atau Minggu
                                    </p>
                                </div>

                                <!-- Tanggal Pengembalian -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Pengembalian <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date"
                                           x-model="formData.returnDate"
                                           x-bind:min="getReturnDateMin()"
                                           @change="validateAndUpdateReturnDate($event)"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                                           required>
                                    <p x-show="isWeekend(formData.returnDate)" class="text-red-500 text-xs mt-1">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Tanggal pengembalian tidak boleh jatuh pada hari Sabtu atau Minggu
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Informasi Pembimbing -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out"
                         style="transition-delay: 0.6s;">

                        <div class="bg-gray-50 rounded-2xl p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-user-tie mr-3 text-purple-600"></i>
                                Informasi Pembimbing/Penanggung Jawab
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Dosen Pembimbing/Pimpinan Instansi <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           x-model="formData.supervisorName"
                                           placeholder="Nama lengkap pembimbing"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        NIP <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           x-model="formData.supervisorNip"
                                           placeholder="NIP"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Pembimbing <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email"
                                           x-model="formData.supervisorEmail"
                                           placeholder="email@unsyiah.ac.id"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        No. Telepon Pembimbing <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel"
                                           x-model="formData.supervisorPhone"
                                           placeholder="08xxxxxxxxxx"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pernyataan -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out"
                         style="transition-delay: 0.8s;">

                        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-1"></i>
                                <div class="flex-1">
                                    <h4 class="font-bold text-yellow-800 mb-3">Pernyataan Tanggung Jawab</h4>
                                    <p class="text-yellow-700 text-sm leading-relaxed mb-4">
                                        Segala sesuatu yang menyebabkan kerusakan akan menjadi tanggung jawab mahasiswa yang bersangkutan.
                                        Setiap kerusakan alat-alat/instrumen yang diakibatkan kelalaian, peralatan tersebut harus diperbaiki/diganti
                                        segera oleh dosen/mahasiswa secara pribadi atau kelompok atau bersama-sama tergantung kesepakatan.
                                    </p>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox"
                                               x-model="formData.agreement"
                                               class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary focus:ring-2"
                                               required>
                                        <span class="ml-3 text-sm font-medium text-yellow-800">
                                            Saya menyetujui dan bertanggung jawab atas penggunaan alat laboratorium
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
                         style="transition-delay: 1s;">

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
        function formPeminjaman() {
            return {
                submitting: false,
                members: [
                    { name: '', nim: '', study_program: '' }
                ],
                selectedEquipments: [],

                // Initialize with selected equipment from localStorage or sessionStorage
                init() {
                    this.loadSelectedEquipments();
                },

                loadSelectedEquipments() {
                    try {
                        const stored = sessionStorage.getItem('selectedEquipments');
                        if (stored) {
                            this.selectedEquipments = JSON.parse(stored);
                        }
                    } catch (error) {
                        console.error('Failed to load selected equipments:', error);
                        this.selectedEquipments = [];
                    }
                },
                formData: {
                    purpose: '',
                    borrowDate: '',
                    returnDate: '',
                    supervisorName: '',
                    supervisorNip: '',
                    supervisorEmail: '',
                    supervisorPhone: '',
                    agreement: false
                },

                addMember() {
                    this.members.push({ name: '', nim: '', study_program: '' });
                },

                removeMember(index) {
                    if (this.members.length > 1) {
                        this.members.splice(index, 1);
                    }
                },

                getTotalQuantity() {
                    return this.selectedEquipments.reduce((total, item) => total + item.quantity, 0);
                },

                // Date validation functions
                getReturnDateMin() {
                    return this.formData.borrowDate || '{{ now()->addDay()->toDateString() }}';
                },

                updateReturnDateMin() {
                    // If return date is before the new borrow date, update it
                    if (this.formData.returnDate && this.formData.borrowDate && this.formData.returnDate < this.formData.borrowDate) {
                        this.formData.returnDate = this.formData.borrowDate;
                    }
                },

                isWeekend(dateString) {
                    if (!dateString) return false;
                    const date = new Date(dateString);
                    const day = date.getDay();
                    return day === 0 || day === 6; // Sunday = 0, Saturday = 6
                },

                validateAndUpdateBorrowDate() {
                    if (this.formData.borrowDate && this.isWeekend(this.formData.borrowDate)) {
                        // Clear the date if it's a weekend
                        this.formData.borrowDate = '';
                        // Show error message briefly
                        setTimeout(() => {
                            const errorElement = document.getElementById('borrow-date-weekend-error');
                            if (errorElement) {
                                errorElement.style.display = 'block';
                                setTimeout(() => {
                                    errorElement.style.display = 'none';
                                }, 3000);
                            }
                        }, 100);
                    } else {
                        // Hide error message if date is valid
                        const errorElement = document.getElementById('borrow-date-weekend-error');
                        if (errorElement) {
                            errorElement.style.display = 'none';
                        }
                        // Update return date minimum
                        this.updateReturnDateMin();
                    }
                },

                validateAndUpdateReturnDate() {
                    if (this.formData.returnDate && this.isWeekend(this.formData.returnDate)) {
                        // Clear the date if it's a weekend
                        this.formData.returnDate = '';
                        // Show error message briefly
                        setTimeout(() => {
                            const errorElement = document.getElementById('return-date-weekend-error');
                            if (errorElement) {
                                errorElement.style.display = 'block';
                                setTimeout(() => {
                                    errorElement.style.display = 'none';
                                }, 3000);
                            }
                        }, 100);
                    } else {
                        // Hide error message if date is valid
                        const errorElement = document.getElementById('return-date-weekend-error');
                        if (errorElement) {
                            errorElement.style.display = 'none';
                        }
                    }
                },

                isFormValid() {
                    // Check if all required fields are filled
                    const membersValid = this.members.every(member => member.name && member.nim && member.study_program);
                    const formDataValid = Object.entries(this.formData).every(([key, value]) => {
                        if (key === 'agreement') return value === true;
                        return value && value.toString().trim() !== '';
                    });
                    const equipmentsValid = this.selectedEquipments.length > 0;
                    const datesNotWeekend = !this.isWeekend(this.formData.borrowDate) && !this.isWeekend(this.formData.returnDate);

                    return membersValid && formDataValid && equipmentsValid && datesNotWeekend;
                },

                async submitForm() {
                    if (!this.isFormValid()) return;

                    this.submitting = true;

                    try {
                        // Prepare equipment items for API
                        const equipmentItems = this.selectedEquipments.map(item => ({
                            equipment_id: item.id,
                            quantity_requested: item.quantity,
                            notes: null
                        }));

                        // Prepare API payload
                        const payload = {
                            members: this.members,
                            supervisor_name: this.formData.supervisorName,
                            supervisor_nip: this.formData.supervisorNip,
                            supervisor_email: this.formData.supervisorEmail,
                            supervisor_phone: this.formData.supervisorPhone,
                            purpose: this.formData.purpose,
                            borrow_date: this.formData.borrowDate,
                            return_date: this.formData.returnDate,
                            equipment_items: equipmentItems
                        };

                        // Submit via API
                        const response = await window.LabGOS.submitBorrowRequest(payload);

                        if (response.success) {
                            // Clear selected equipments from storage
                            sessionStorage.removeItem('selectedEquipments');

                            // Show success message with Indonesian text
                            alert(`Permohonan peminjaman berhasil dikirim!\n\nNomor Tracking: ${response.data.request_id}\n\nAnda akan diarahkan ke halaman tracking.`);

                            // Redirect to tracking page
                            window.location.href = `/layanan/tracking-peminjaman?rid=${response.data.request_id}`;
                        } else {
                            throw new Error(response.message || 'Gagal mengirim permohonan');
                        }

                    } catch (error) {
                        console.error('Error submitting form:', error);

                        let errorMessage = 'Terjadi kesalahan saat mengirim permohonan.';

                        if (error.response && error.response.data) {
                            if (error.response.data.message) {
                                errorMessage = error.response.data.message;
                            } else if (error.response.data.errors) {
                                // Handle validation errors
                                const errors = Object.values(error.response.data.errors).flat();
                                errorMessage = errors.join('\n');
                            }
                        } else if (error.message) {
                            errorMessage = error.message;
                        }

                        alert(errorMessage + '\n\nSilakan coba lagi.');
                    } finally {
                        this.submitting = false;
                    }
                }
            }
        }
    </script>

</x-public.layouts.main>
