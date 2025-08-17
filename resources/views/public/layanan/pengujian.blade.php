<x-public.layouts.main>
    <x-slot:title>
        Layanan Pengujian Sampel - Lab GOS USK
    </x-slot:title>

    <!-- Hero Section -->
    <section class="relative h-96 flex items-center justify-center">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
             style="background-image: url('/assets/images/hero-bg.jpeg');">
            <div class="absolute inset-0 bg-black bg-opacity-60"></div>
        </div>

        <div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <div x-data="{ animated: false }"
                 x-scroll-animate.once="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                 class="transition-all duration-1200 ease-out">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
                    <span class="relative">
                        🧪 Layanan
                        <span class="text-secondary">Pengujian Sampel</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Analisis sampel dengan teknologi spektroskopi dan optik terdepan
                </p>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-2 inline-block">
                    <p class="text-white flex items-center justify-center">
                        <i class="fas fa-vial mr-2 text-secondary"></i>
                        Laboratorium Gelombang, Optik & Spektroskopi
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- Services Overview -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }"
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="text-center mb-16 transition-all duration-1000 ease-out">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">
                    <span class="relative inline-block">
                        🔬 Jenis
                        <span class="text-secondary">Pengujian</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                    </span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Kami menyediakan berbagai layanan pengujian dengan standar internasional dan hasil yang akurat
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-16">
                @php
                $services = [
                    [
                        'title' => 'Spektroskopi FTIR',
                        'icon' => 'fas fa-wave-square',
                        'description' => 'Analisis struktur molekul dan identifikasi gugus fungsional dengan teknologi Fourier Transform Infrared',
                        'parameters' => [
                            'Range: 4000-400 cm⁻¹',
                            'Resolusi: 0.5 cm⁻¹',
                            'Sample: Solid, liquid, gas',
                            'Waktu: 2-3 hari kerja'
                        ],
                        'color' => 'from-blue-500 to-blue-700',
                        'badge' => 'bg-blue-100 text-blue-800'
                    ],
                    [
                        'title' => 'Spektroskopi UV-Vis',
                        'icon' => 'fas fa-sun',
                        'description' => 'Analisis konsentrasi dan karakteristik optik material dengan spektrofotometer UV-Visible',
                        'parameters' => [
                            'Range: 200-1100 nm',
                            'Bandwidth: 1.8 nm',
                            'Sample: Liquid, solution',
                            'Waktu: 1-2 hari kerja'
                        ],
                        'color' => 'from-purple-500 to-purple-700',
                        'badge' => 'bg-purple-100 text-purple-800'
                    ],
                    [
                        'title' => 'Karakterisasi Optik',
                        'icon' => 'fas fa-microscope',
                        'description' => 'Pengukuran sifat optik material termasuk indeks bias, transmitansi, dan reflektansi',
                        'parameters' => [
                            'Indeks bias: ±0.0001',
                            'Transmitansi: 0-100%',
                            'Sample: Solid, thin film',
                            'Waktu: 3-5 hari kerja'
                        ],
                        'color' => 'from-green-500 to-green-700',
                        'badge' => 'bg-green-100 text-green-800'
                    ]
                ];
                @endphp

                @foreach($services as $index => $service)
                <div x-data="{ animated: false }"
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                     class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4 ease-out"
                     style="transition-delay: {{ $index * 0.1 }}s;">

                    <div class="h-48 bg-gradient-to-br {{ $service['color'] }} relative overflow-hidden">
                        <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-10 transition-all duration-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="{{ $service['icon'] }} text-white text-6xl group-hover:scale-110 transition-transform duration-500"></i>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="{{ $service['badge'] }} px-3 py-1 rounded-full text-xs font-semibold">
                                Tersedia
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-primary transition-colors duration-300">
                            {{ $service['title'] }}
                        </h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            {{ $service['description'] }}
                        </p>

                        <div class="space-y-2 mb-6">
                            @foreach($service['parameters'] as $param)
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span>{{ $param }}</span>
                            </div>
                            @endforeach
                        </div>

                        <button onclick="openTestingForm('{{ strtolower(str_replace(' ', '_', $service['title'])) }}')"
                                class="w-full bg-primary hover:bg-blue-800 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Ajukan Pengujian
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }"
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="text-center mb-16 transition-all duration-1000 ease-out">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">
                    <span class="relative inline-block">
                        📋 Alur
                        <span class="text-secondary">Pengujian</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-500 rounded-full"></div>
                    </span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Proses pengujian sampel yang transparan dan profesional untuk hasil yang optimal
                </p>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                @php
                $steps = [
                    [
                        'step' => '01',
                        'title' => 'Pengajuan',
                        'description' => 'Isi formulir pengajuan dengan detail sampel dan jenis pengujian yang diinginkan',
                        'icon' => 'fas fa-file-alt',
                        'color' => 'text-blue-500'
                    ],
                    [
                        'step' => '02',
                        'title' => 'Verifikasi',
                        'description' => 'Tim lab memverifikasi permintaan dan memberikan estimasi biaya serta waktu',
                        'icon' => 'fas fa-check-double',
                        'color' => 'text-green-500'
                    ],
                    [
                        'step' => '03',
                        'title' => 'Pengujian',
                        'description' => 'Sampel dianalisis menggunakan instrumen sesuai dengan parameter yang diminta',
                        'icon' => 'fas fa-microscope',
                        'color' => 'text-purple-500'
                    ],
                    [
                        'step' => '04',
                        'title' => 'Laporan',
                        'description' => 'Hasil pengujian diserahkan dalam bentuk laporan lengkap dan sertifikat',
                        'icon' => 'fas fa-file-medical',
                        'color' => 'text-orange-500'
                    ]
                ];
                @endphp

                @foreach($steps as $index => $step)
                <div x-data="{ animated: false }"
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                     class="text-center group transition-all duration-1000 ease-out"
                     style="transition-delay: {{ $index * 0.2 }}s;">

                    <div class="relative mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-primary to-blue-600 rounded-full flex items-center justify-center mx-auto shadow-lg group-hover:shadow-2xl group-hover:scale-110 transition-all duration-500">
                            <i class="{{ $step['icon'] }} text-white text-2xl"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-secondary rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-gray-800 font-bold text-sm">{{ $step['step'] }}</span>
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:{{ $step['color'] }} transition-colors duration-300">
                        {{ $step['title'] }}
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        {{ $step['description'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Requirements Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }"
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="text-center mb-12 transition-all duration-1000 ease-out">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">
                    <span class="relative inline-block">
                        📝 Persyaratan
                        <span class="text-secondary">Sampel</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                    </span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Pastikan sampel Anda memenuhi persyaratan berikut untuk hasil pengujian yang optimal
                </p>
            </div>

            <div x-data="{ animated: false }"
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="bg-white rounded-3xl shadow-xl p-8 transition-all duration-1000 ease-out">

                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-vial text-blue-500 mr-3"></i>
                            Persyaratan Umum
                        </h3>
                        <ul class="space-y-3">
                            @php
                            $generalReqs = [
                                'Sampel dalam kondisi bersih dan kering',
                                'Volume/jumlah sampel mencukupi untuk pengujian',
                                'Sampel dikemas dalam wadah yang sesuai',
                                'Label sampel yang jelas dan informatif',
                                'Surat pengantar dari institusi/perusahaan'
                            ];
                            @endphp

                            @foreach($generalReqs as $req)
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span class="text-gray-700">{{ $req }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-exclamation-triangle text-orange-500 mr-3"></i>
                            Sampel Berbahaya
                        </h3>
                        <div class="bg-orange-50 rounded-2xl p-4 mb-4">
                            <p class="text-orange-800 text-sm leading-relaxed">
                                <i class="fas fa-info-circle mr-2"></i>
                                Untuk sampel berbahaya atau beracun, diperlukan prosedur khusus dan surat keterangan keamanan (MSDS).
                            </p>
                        </div>
                        <ul class="space-y-3">
                            @php
                            $hazardousReqs = [
                                'Material Safety Data Sheet (MSDS)',
                                'Kemasan khusus sesuai standar keamanan',
                                'Informasi tingkat bahaya dan penanganan',
                                'Persetujuan dari Tim Keselamatan Lab'
                            ];
                            @endphp

                            @foreach($hazardousReqs as $req)
                            <li class="flex items-center">
                                <i class="fas fa-shield-alt text-orange-500 mr-3"></i>
                                <span class="text-gray-700">{{ $req }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-primary">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }"
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="transition-all duration-1000 ease-out">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    Siap Untuk Mengajukan Pengujian?
                </h2>
                <p class="text-blue-100 text-xl mb-8 leading-relaxed">
                    Tim ahli kami siap membantu analisis sampel Anda dengan teknologi terdepan dan hasil yang akurat
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="openTestingForm()" class="bg-secondary hover:bg-yellow-500 text-gray-800 px-8 py-4 rounded-full font-semibold transition-all duration-500 transform hover:scale-105 shadow-2xl">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Ajukan Sekarang
                    </button>
                    <a href="/tracking/pengujian" class="border-2 border-white text-white hover:bg-white hover:text-primary px-8 py-4 rounded-full font-semibold transition-all duration-500 transform hover:scale-105">
                        <i class="fas fa-search mr-2"></i>
                        Lacak Pengujian
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testing Request Modal -->
    <div x-data="testingFormData()"
         x-show="showModal"
         @open-testing-form.window="showModal = true; selectedTestingType = $event.detail.service || ''"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">

        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="bg-white rounded-3xl shadow-2xl max-w-5xl w-full max-h-screen overflow-y-auto border border-gray-100">

            <div class="p-8 bg-gradient-to-r from-primary/5 to-secondary/5">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800 mb-2">
                            <i class="fas fa-vial text-primary mr-3"></i>
                            Formulir Pengujian Sampel
                        </h3>
                        <p class="text-gray-600">Lengkapi informasi berikut untuk mengajukan pengujian sampel</p>
                    </div>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-300 p-2 hover:bg-gray-100 rounded-full">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>

            <div class="p-8">
                <!-- Progress Indicator -->
                <div class="mb-8">
                    <div class="flex items-center justify-center space-x-4 mb-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold">1</div>
                            <span class="ml-2 text-sm font-medium text-gray-700">Klien</span>
                        </div>
                        <div class="w-16 h-1 bg-gray-200 rounded"></div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold">2</div>
                            <span class="ml-2 text-sm font-medium text-gray-700">Sampel</span>
                        </div>
                        <div class="w-16 h-1 bg-gray-200 rounded"></div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">3</div>
                            <span class="ml-2 text-sm font-medium text-gray-700">Pengujian</span>
                        </div>
                        <div class="w-16 h-1 bg-gray-200 rounded"></div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-bold">4</div>
                            <span class="ml-2 text-sm font-medium text-gray-700">Biaya</span>
                        </div>
                        <div class="w-16 h-1 bg-gray-200 rounded"></div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-white text-sm font-bold">5</div>
                            <span class="ml-2 text-sm font-medium text-gray-700">Jadwal</span>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submitForm()" id="testingForm">
                    <!-- Client Information Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b-2 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-t-lg p-3 -mx-3 border-blue-200">
                            <i class="fas fa-user text-blue-600 mr-2"></i>
                            Informasi Klien
                        </h4>

                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap *</label>
                                <input type="text" x-model="formData.client_name" required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm"
                                       placeholder="Masukkan nama lengkap">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Email *</label>
                                <input type="email" x-model="formData.client_email" required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm"
                                       placeholder="nama@email.com">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">No. Telepon *</label>
                                <input type="tel" x-model="formData.client_phone" required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm"
                                       placeholder="+62812xxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Organisasi/Institusi *</label>
                                <input type="text" x-model="formData.client_organization" required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm"
                                       placeholder="Nama universitas/perusahaan">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Alamat Lengkap *</label>
                            <textarea x-model="formData.client_address" required rows="3"
                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm"
                                      placeholder="Alamat lengkap untuk pengiriman hasil"></textarea>
                        </div>
                    </div>

                    <!-- Sample Information Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b-2 bg-gradient-to-r from-orange-50 to-red-50 rounded-t-lg p-3 -mx-3 border-orange-200">
                            <i class="fas fa-flask text-orange-600 mr-2"></i>
                            Informasi Sampel
                        </h4>

                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Nama Sampel *</label>
                                <input type="text" x-model="formData.sample_name" required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm"
                                       placeholder="Contoh: Sampel Polimer A">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Jumlah Sampel *</label>
                                <input type="text" x-model="formData.sample_quantity" required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm"
                                       placeholder="Contoh: 10 gram, 5 ml, 3 buah">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Deskripsi Sampel *</label>
                            <textarea x-model="formData.sample_description" required rows="3"
                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm"
                                      placeholder="Jelaskan karakteristik sampel, cara preparasi, dan informasi penting lainnya"></textarea>
                        </div>
                    </div>

                    <!-- Testing Information Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b-2 bg-gradient-to-r from-primary/10 to-secondary/10 rounded-t-lg p-3 -mx-3 border-primary/20">
                            <i class="fas fa-microscope text-primary mr-2"></i>
                            Informasi Pengujian
                        </h4>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Jenis Pengujian *</label>
                            <select x-model="selectedTestingType" @change="updateTestingParameters()" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm">
                                <option value="">Pilih Jenis Pengujian</option>
                                <option value="uv_vis_spectroscopy">UV-Vis Spectroscopy</option>
                                <option value="ftir_spectroscopy">FTIR Spectroscopy</option>
                                <option value="optical_microscopy">Optical Microscopy</option>
                                <option value="custom">Custom Testing</option>
                            </select>
                        </div>

                        <!-- Dynamic Testing Parameters -->
                        <div x-show="selectedTestingType === 'uv_vis_spectroscopy'" class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Parameter UV-Vis *</label>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <input type="text" x-model="testingParameters.wavelength_range"
                                           placeholder="Rentang panjang gelombang (nm)"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <input type="text" x-model="testingParameters.solvent"
                                           placeholder="Jenis pelarut"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>
                        </div>

                        <div x-show="selectedTestingType === 'ftir_spectroscopy'" class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Parameter FTIR *</label>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <input type="text" x-model="testingParameters.wavenumber_range"
                                           placeholder="Rentang bilangan gelombang (cm⁻¹)"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <input type="text" x-model="testingParameters.sample_preparation"
                                           placeholder="Metode preparasi sampel"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>
                        </div>

                        <div x-show="selectedTestingType === 'optical_microscopy'" class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Parameter Mikroskopi Optik *</label>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <input type="text" x-model="testingParameters.magnification"
                                           placeholder="Perbesaran yang diinginkan"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <input type="text" x-model="testingParameters.illumination_type"
                                           placeholder="Jenis penerangan"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Information Section -->
                    <div class="mb-8" x-show="selectedTestingType">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b-2 bg-gradient-to-r from-green-50 to-emerald-50 rounded-t-lg p-3 -mx-3 border-green-200">
                            <i class="fas fa-calculator text-green-600 mr-2"></i>
                            Informasi Biaya
                        </h4>

                        <div>
                            <!-- Base Cost Card (single full-width) -->
                            <div class="w-full bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-tag text-green-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <h5 class="font-semibold text-gray-800">Biaya Dasar</h5>
                                            <p class="text-sm text-gray-600" x-text="getTestingTypeLabel()"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-green-600" x-text="formatCurrency(getBaseCost())"></div>
                                    <p class="text-sm text-gray-500" x-text="estimatedDurationText"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Note -->
                        <div class="mt-4 bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <div class="flex items-start">
                                <i class="fas fa-lightbulb text-yellow-500 mr-2 mt-1"></i>
                                <div class="text-sm text-gray-600">
                                    <p class="font-medium text-gray-700 mb-1">Catatan Biaya:</p>
                                    <ul class="space-y-1 text-xs">
                                        <li>• Biaya dapat berubah berdasarkan kompleksitas sampel</li>
                                        <li>• Biaya final akan dikonfirmasi setelah evaluasi sampel oleh admin melalui whatsapp</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Information Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b-2 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-t-lg p-3 -mx-3 border-purple-200">
                            <i class="fas fa-calendar text-purple-600 mr-2"></i>
                            Jadwal Pengantaran
                        </h4>

                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Jadwal Pengantaran Sampel *</label>
                                <input type="date" x-model="formData.sample_delivery_schedule" required
                                       :min="minDate" :max="maxDate"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 hover:border-gray-300 bg-white shadow-sm">
                                <p class="text-sm text-gray-500 mt-1">Minimal 3 hari dari sekarang</p>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Estimasi Lama Waktu Pengujian</label>
                                <input type="text" x-model="estimatedDurationText" readonly
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 cursor-not-allowed"
                                       placeholder="Pilih jenis pengujian terlebih dahulu">
                                <p class="text-sm text-gray-500 mt-1">Durasi otomatis berdasarkan jenis pengujian</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="formData.urgent_request"
                                       class="mr-3 w-5 h-5 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary">
                                <span class="text-gray-700 font-semibold">Pengujian Mendesak</span>
                            </label>
                            <p class="text-sm text-gray-500 mt-1">Untuk pengujian dalam 1-7 hari</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4">
                        <button type="submit" :disabled="loading"
                                class="flex-1 bg-gradient-to-r from-primary to-blue-700 hover:from-blue-700 hover:to-blue-900 disabled:from-gray-400 disabled:to-gray-500 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 disabled:transform-none shadow-lg hover:shadow-xl disabled:shadow-none">
                            <span x-show="!loading">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Kirim Pengajuan
                            </span>
                            <span x-show="loading">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Mengirim...
                            </span>
                        </button>
                        <button type="button" @click="closeModal()" :disabled="loading"
                                class="px-6 py-4 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 disabled:bg-gray-100 shadow-sm hover:shadow-md">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openTestingForm(service = '') {
            window.dispatchEvent(new CustomEvent('open-testing-form', {
                detail: { service: service }
            }));
        }

        // Alpine.js component for testing form
        function testingFormData() {
            return {
                showModal: false,
                loading: false,
                selectedTestingType: '',
                formData: {
                    client_name: '',
                    client_email: '',
                    client_phone: '',
                    client_organization: '',
                    client_address: '',
                    sample_name: '',
                    sample_description: '',
                    sample_quantity: '',
                    sample_delivery_schedule: '',
                    urgent_request: false
                },
                testingParameters: {
                    wavelength_range: '',
                    solvent: '',
                    wavenumber_range: '',
                    sample_preparation: '',
                    magnification: '',
                    illumination_type: ''
                },

                // Testing type configuration
                testingTypeConfig: {
                    'uv_vis_spectroscopy': { duration_days: 3, cost: 150000 },
                    'ftir_spectroscopy': { duration_days: 5, cost: 200000 },
                    'optical_microscopy': { duration_days: 2, cost: 100000 },
                    'custom': { duration_days: 7, cost: 300000 }
                },

                get estimatedDurationText() {
                    if (!this.selectedTestingType) {
                        return '';
                    }
                    const config = this.testingTypeConfig[this.selectedTestingType];
                    return config ? `${config.duration_days} hari kerja` : '';
                },

                // Cost calculation methods
                getTestingTypeLabel() {
                    const labels = {
                        'uv_vis_spectroscopy': 'UV-Vis Spectroscopy',
                        'ftir_spectroscopy': 'FTIR Spectroscopy',
                        'optical_microscopy': 'Optical Microscopy',
                        'custom': 'Custom Testing'
                    };
                    return labels[this.selectedTestingType] || '';
                },

                getBaseCost() {
                    if (!this.selectedTestingType) {
                        return 0;
                    }
                    const config = this.testingTypeConfig[this.selectedTestingType];
                    return config ? config.cost : 0;
                },

                formatCurrency(amount) {
                    if (!amount || amount === 0) {
                        return 'Rp 0';
                    }
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(amount);
                },

                get minDate() {
                    const date = new Date();
                    date.setDate(date.getDate() + 3); // 3 days from now
                    return date.toISOString().split('T')[0];
                },

                get maxDate() {
                    const date = new Date();
                    date.setMonth(date.getMonth() + 3); // 3 months from now
                    return date.toISOString().split('T')[0];
                },

                updateTestingParameters() {
                    // Clear previous parameters
                    this.testingParameters = {
                        wavelength_range: '',
                        solvent: '',
                        wavenumber_range: '',
                        sample_preparation: '',
                        magnification: '',
                        illumination_type: ''
                    };
                },

                async submitForm() {
                    this.loading = true;

                    try {
                        // Prepare testing parameters based on selected type
                        let parameters = {};
                        if (this.selectedTestingType === 'uv_vis_spectroscopy') {
                            parameters = {
                                wavelength_range: this.testingParameters.wavelength_range,
                                solvent: this.testingParameters.solvent
                            };
                        } else if (this.selectedTestingType === 'ftir_spectroscopy') {
                            parameters = {
                                wavenumber_range: this.testingParameters.wavenumber_range,
                                sample_preparation: this.testingParameters.sample_preparation
                            };
                        } else if (this.selectedTestingType === 'optical_microscopy') {
                            parameters = {
                                magnification: this.testingParameters.magnification,
                                illumination_type: this.testingParameters.illumination_type
                            };
                        }

                        const payload = {
                            ...this.formData,
                            testing_type: this.selectedTestingType,
                            testing_parameters: parameters
                        };

                        const result = await window.LabGOS.submitTestingRequest(payload);

                        if (result.success) {
                            const requestId = result.data.request_id;
                            // Inform user
                            alert('✅ Pengajuan berhasil dikirim!\n\nID Pengujian: ' + requestId + '\n\nAnda akan diarahkan ke halaman tracking.');
                            // Store minimal tracking data (fallback for page load)
                            try {
                                sessionStorage.setItem('testingTrackingData', JSON.stringify({ requestId }));
                            } catch (e) { /* ignore storage errors */ }
                            // Redirect to tracking page (structure used by tracking-testing page)
                            // Redirect to tracking pengujian page with query parameter rid (route updated)
                            window.location.href = `/layanan/tracking-pengujian?rid=${requestId}`;
                        } else {
                            let errorMessage = 'Terjadi kesalahan: ' + result.message;
                            if (result.errors) {
                                const errorDetails = Object.values(result.errors).flat();
                                errorMessage += '\n\nDetail:\n' + errorDetails.join('\n');
                            }
                            alert(errorMessage);
                        }
                    } catch (error) {
                        console.error('Submit error:', error);
                        let errorMessage = '❌ Terjadi kesalahan saat mengirim pengajuan.';

                        if (error.status === 422 && error.response && error.response.data) {
                            const responseData = error.response.data;
                            errorMessage = responseData.message || 'Validation error';

                            if (responseData.errors) {
                                const errorDetails = Object.values(responseData.errors).flat();
                                errorMessage += '\n\nDetail:\n' + errorDetails.join('\n');
                            }
                        }

                        alert(errorMessage);
                    } finally {
                        this.loading = false;
                    }
                },

                resetForm() {
                    this.formData = {
                        client_name: '',
                        client_email: '',
                        client_phone: '',
                        client_organization: '',
                        client_address: '',
                        sample_name: '',
                        sample_description: '',
                        sample_quantity: '',
                        sample_delivery_schedule: '',
                        urgent_request: false
                    };
                    this.selectedTestingType = '';
                    this.updateTestingParameters();
                },

                closeModal() {
                    this.showModal = false;
                    if (!this.loading) {
                        this.resetForm();
                    }
                }
            }
        }
    </script>
</x-public.layouts.main>
