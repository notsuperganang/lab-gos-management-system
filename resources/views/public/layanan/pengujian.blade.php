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
    <div x-data="{ showModal: false, selectedService: '' }" 
         x-show="showModal" 
         @open-testing-form.window="showModal = true; selectedService = $event.detail.service || ''"
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
             class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
            
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Formulir Pengujian Sampel</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors duration-300">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <form action="{{ route('api.requests.testing') }}" method="POST" enctype="multipart/form-data" id="testingForm">
                    @csrf
                    
                    <!-- Service Selection -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Jenis Pengujian</label>
                        <select x-model="selectedService" name="testing_type" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Pilih Jenis Pengujian</option>
                            <option value="ftir">Spektroskopi FTIR</option>
                            <option value="uv_vis">Spektroskopi UV-Vis</option>
                            <option value="optical">Karakterisasi Optik</option>
                        </select>
                    </div>

                    <!-- Basic Information -->
                    <div class="grid md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                            <input type="text" name="requester_name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" name="requester_email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">No. Telepon</label>
                            <input type="tel" name="requester_phone" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Institusi</label>
                            <input type="text" name="institution" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>

                    <!-- Sample Information -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Nama Sampel</label>
                        <input type="text" name="sample_name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Deskripsi Sampel</label>
                        <textarea name="sample_description" rows="3" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                    </div>

                    <!-- Testing Parameters -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Parameter Pengujian</label>
                        <textarea name="testing_parameters" rows="3" placeholder="Jelaskan parameter spesifik yang diinginkan..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Dokumen Pendukung</label>
                        <input type="file" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-primary hover:bg-blue-800 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Pengajuan
                        </button>
                        <button type="button" @click="showModal = false" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-100 transition-colors duration-300">
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

        // Handle form submission
        document.getElementById('testingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Pengajuan berhasil dikirim! ID Pengujian: ' + result.data.request_id);
                    this.reset();
                    document.querySelector('[x-data]').__x.$data.showModal = false;
                } else {
                    alert('Terjadi kesalahan: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan saat mengirim pengajuan');
            }
        });
    </script>
</x-public.layouts.main>