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
                    <p class="text-gray-600 text-sm mb-3">Estimasi waktu</p>
                    <div class="text-3xl font-bold text-green-600">2-3</div>
                    <p class="text-gray-500 text-xs">jam</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Kunjungan -->
    <section class="py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div x-data="formKunjungan()" class="bg-white rounded-3xl shadow-2xl overflow-hidden">

                <!-- Form Header -->
                <div class="bg-gradient-to-r from-secondary to-yellow-500 px-8 py-8 text-gray-800 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white bg-opacity-20 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-primary bg-opacity-20 rounded-full translate-y-12 -translate-x-12"></div>

                    <div class="relative z-10 text-center">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-alt text-gray-800 text-2xl"></i>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-bold mb-2">
                            FORM PENGAJUAN KUNJUNGAN
                        </h2>
                        <p class="text-gray-700">
                            Laboratorium Gelombang, Optik dan Spektroskopi<br>
                            Departemen Fisika FMIPA Universitas Syiah Kuala
                        </p>
                    </div>
                </div>

                <!-- Form Body -->
                <form @submit.prevent="submitForm" class="p-8 space-y-8">

                    <!-- Section 1: Data Instansi -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out">

                        <!-- Formal Letter Header -->
                        <div class="bg-white border border-gray-200 rounded-2xl p-8 mb-6">
                            <div class="text-center mb-8">
                                <h3 class="text-lg font-bold text-gray-800 mb-4 uppercase">
                                    FORM PERMOHONAN IZIN KUNJUNGAN LABORATORIUM<br>
                                    UNTUK INSTANSI/SEKOLAH DI LUAR LINGKUNGAN<br>
                                    UNIVERSITAS SYIAH KUALA
                                </h3>
                            </div>

                            <div class="mb-6">
                                <p class="text-gray-700 mb-2">Kepada Yth.</p>
                                <p class="text-gray-700 mb-1">Sdr. Ketua Departemen Fisika</p>
                                <p class="text-gray-700 mb-1">Fakultas MIPA USK</p>
                                <p class="text-gray-700 mb-4">Darussalam, Banda Aceh</p>

                                <p class="text-gray-700 mb-4"><strong>Perihal: Izin Melaksanakan Kunjungan Laboratorium</strong></p>

                                <p class="text-gray-700 mb-4">Dengan hormat,</p>

                                <p class="text-gray-700 mb-6 leading-relaxed">
                                    Bersama ini disampaikan bahwa instansi/sekolah kami merencanakan akan melakukan kunjungan
                                    ke laboratorium saudara. Sehubungan dengan hal tersebut, maka saya mengharapkan kepada
                                    saudara untuk memberikan izin kunjungan yang bersangkutan untuk dapat menggunakan fasilitas
                                    laboratorium yang saudara pimpin.
                                </p>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-2xl p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-building mr-3 text-primary"></i>
                                Data Instansi & Penanggung Jawab
                            </h3>

                            <div class="space-y-6">
                                <!-- Data format sesuai form resmi -->
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">1) Nama Instansi</label>
                                    </div>
                                    <div class="md:col-span-1 text-center">:</div>
                                    <div class="md:col-span-9">
                                        <input type="text"
                                               x-model="formData.institutionName"
                                               placeholder="Nama lengkap instansi/sekolah"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300"
                                               required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">2) Alamat Instansi</label>
                                    </div>
                                    <div class="md:col-span-1 text-center">:</div>
                                    <div class="md:col-span-9">
                                        <input type="text"
                                               x-model="formData.institutionAddress"
                                               placeholder="Alamat lengkap instansi"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300"
                                               required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">3) Penanggung Jawab</label>
                                    </div>
                                    <div class="md:col-span-1 text-center">:</div>
                                    <div class="md:col-span-9">
                                        <input type="text"
                                               x-model="formData.contactName"
                                               placeholder="Nama penanggung jawab kunjungan"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300"
                                               required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">4) Jabatan/NIP</label>
                                    </div>
                                    <div class="md:col-span-1 text-center">:</div>
                                    <div class="md:col-span-9">
                                        <input type="text"
                                               x-model="formData.contactPosition"
                                               placeholder="Jabatan dan NIP (jika ada)"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300"
                                               required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">5) No. HP/WA</label>
                                    </div>
                                    <div class="md:col-span-1 text-center">:</div>
                                    <div class="md:col-span-9">
                                        <input type="tel"
                                               x-model="formData.contactPhone"
                                               placeholder="Nomor WhatsApp yang dapat dihubungi"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300"
                                               required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">6) Email</label>
                                    </div>
                                    <div class="md:col-span-1 text-center">:</div>
                                    <div class="md:col-span-9">
                                        <input type="email"
                                               x-model="formData.contactEmail"
                                               placeholder="email@domain.com"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300"
                                               required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">7) Rencana Kunjungan</label>
                                    </div>
                                    <div class="md:col-span-1 text-center">:</div>
                                    <div class="md:col-span-9">
                                        <div class="space-y-3">
                                            <input type="date"
                                                   x-model="formData.visitDate"
                                                   :min="getMinDate()"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300"
                                                   required>
                                            <select x-model="formData.visitTime"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300"
                                                    required>
                                                <option value="">Pilih waktu kunjungan</option>
                                                <option value="08:00-11:00">Pagi (08:00 - 11:00)</option>
                                                <option value="13:00-16:00">Siang (13:00 - 16:00)</option>
                                            </select>
                                            <p class="text-xs text-gray-500">Tanggal dimulainya kunjungan sampai dengan selesai (minimal H+3)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Detail Kunjungan -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out"
                         style="transition-delay: 0.2s;">

                        <div class="bg-gray-50 rounded-2xl p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-info-circle mr-3 text-green-600"></i>
                                Detail Kunjungan
                            </h3>

                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">8) Jumlah Pengunjung</label>
                                    </div>
                                    <div class="md:col-span-1 text-center">:</div>
                                    <div class="md:col-span-9">
                                        <div class="flex items-center space-x-3">
                                            <input type="number"
                                                   x-model.number="formData.visitorCount"
                                                   min="1"
                                                   max="25"
                                                   class="w-32 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300"
                                                   required>
                                            <span class="text-gray-600">orang (maksimal 25 orang)</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">9) Tujuan Kunjungan</label>
                                    </div>
                                    <div class="md:col-span-1 text-center">:</div>
                                    <div class="md:col-span-9">
                                        <textarea x-model="formData.purpose"
                                                  rows="4"
                                                  placeholder="Jelaskan tujuan dan agenda kunjungan laboratorium..."
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition-all duration-300"
                                                  required></textarea>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">10) Fasilitas yang Ingin Dikunjungi</label>
                                    </div>
                                    <div class="md:col-span-1 text-center">:</div>
                                    <div class="md:col-span-9">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <template x-for="facility in availableFacilities" :key="facility.id">
                                                <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                                    <input type="checkbox"
                                                           :value="facility.id"
                                                           x-model="formData.facilities"
                                                           class="w-4 h-4 text-secondary border-gray-300 rounded focus:ring-secondary">
                                                    <span class="text-sm text-gray-700" x-text="facility.name"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Penutup Formal -->
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <p class="text-gray-700 leading-relaxed mb-6">
                                    Sebagai informasi kami sampaikan bahwa peserta kunjungan akan mematuhi semua peraturan yang
                                    telah ditetapkan oleh laboratorium saudara dan segala sesuatu yang menyebabkan kerugian akan menjadi
                                    tanggung jawab instansi yang bersangkutan.
                                </p>

                                <p class="text-gray-700 mb-8">
                                    Demikian permohonan ini kami sampaikan, atas perhatian dan kerjasamanya diucapkan terima kasih.
                                </p>

                                <!-- Signature Section -->
                                <div class="flex justify-end">
                                    <div class="text-center">
                                        <p class="text-gray-700 mb-1">Darussalam, <span x-text="getCurrentDate()"></span></p>
                                        <p class="text-gray-700 mb-12">Pimpinan Instansi,</p>

                                        <div class="border-b border-gray-400 w-48 mb-2"></div>
                                        <p class="text-gray-700 text-sm">Nama & Tanda Tangan</p>
                                        <p class="text-gray-700 text-sm">NIP. (jika ada)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Ketentuan -->
                    <div x-data="{ animated: false }"
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="transition-all duration-1000 ease-out"
                         style="transition-delay: 0.4s;">

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
                                            Saya selaku pimpinan instansi menyetujui dan akan memastikan semua peserta
                                            kunjungan mematuhi ketentuan dan peraturan laboratorium yang telah ditetapkan
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
                    institutionName: '',
                    institutionAddress: '',
                    contactName: '',
                    contactPosition: '',
                    contactEmail: '',
                    contactPhone: '',
                    visitorCount: 1,
                    visitDate: '',
                    visitTime: '',
                    purpose: '',
                    facilities: [],
                    agreement: false
                },

                availableFacilities: [
                    { id: 'spektroskopi', name: 'Lab Spektroskopi' },
                    { id: 'optik', name: 'Lab Optik' },
                    { id: 'gelombang', name: 'Lab Gelombang' },
                    { id: 'preparasi', name: 'Ruang Preparasi' },
                    { id: 'meeting', name: 'Ruang Meeting' },
                    { id: 'perpustakaan', name: 'Perpustakaan Lab' }
                ],

                getCurrentDate() {
                    const today = new Date();
                    const options = {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    return today.toLocaleDateString('id-ID', options);
                },

                getMinDate() {
                    const today = new Date();
                    today.setDate(today.getDate() + 3); // H+3
                    return today.toISOString().split('T')[0];
                },

                isFormValid() {
                    const requiredFields = [
                        'institutionName', 'institutionAddress', 'contactName',
                        'contactPosition', 'contactEmail', 'contactPhone',
                        'visitDate', 'visitTime', 'purpose'
                    ];

                    const fieldsValid = requiredFields.every(field =>
                        this.formData[field] && this.formData[field].toString().trim() !== ''
                    );

                    return fieldsValid &&
                           this.formData.visitorCount >= 1 &&
                           this.formData.visitorCount <= 25 &&
                           this.formData.agreement;
                },

                async submitForm() {
                    if (!this.isFormValid()) return;

                    this.submitting = true;

                    try {
                        // Simulate API call
                        await new Promise(resolve => setTimeout(resolve, 2000));

                        // Generate visit ID
                        const visitId = 'KNJ-' + new Date().getFullYear() + '-' +
                                       String(Math.floor(Math.random() * 999) + 1).padStart(3, '0');

                        // Prepare form data
                        const submitData = {
                            visitId: visitId,
                            formData: this.formData,
                            submittedAt: new Date().toISOString(),
                            formalLetter: true // Indicator that this generates formal letter
                        };

                        console.log('Formal visit request submitted:', submitData);

                        // Redirect to confirmation page
                        window.location.href = `/layanan/kunjungan/confirmation/${visitId}`;

                    } catch (error) {
                        console.error('Error submitting form:', error);
                        alert('Terjadi kesalahan saat mengirim permohonan. Silakan coba lagi.');
                    } finally {
                        this.submitting = false;
                    }
                }
            }
        }
    </script>

</x-public.layouts.main>
