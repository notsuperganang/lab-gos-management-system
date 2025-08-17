<!-- Footer -->
<footer class="bg-primary text-white py-16 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 h-64 bg-secondary rounded-full -translate-x-32 -translate-y-32"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-secondary rounded-full translate-x-48 translate-y-48"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-5 gap-8">
            <!-- Lab Info -->
            <div x-data="{ animated: false }"
                x-scroll-animate="animated = true"
                :class="animated ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'"
                class="md:col-span-2 transition-all duration-1000 ease-out">
                <div class="flex items-center space-x-3 mb-6">
                    <img src="/assets/images/logo-fisika-putih.png"
                        alt="Logo Fisika FMIPA USK"
                        class="h-12 w-auto transform">
                    <div>
                        <h3 class="text-xl font-bold">{{ $labConfig['name'] ?? 'Lab GOS' }}</h3>
                        <p class="text-blue-200">{{ $labConfig['code'] ?? 'Laboratorium Gelombang, Optik & Spektroskopi' }}</p>
                    </div>
                </div>
                <p class="text-blue-100 mb-6 max-w-md leading-relaxed">
                    {{ $labConfig['department'] ?? 'Departemen Fisika FMIPA Universitas Syiah Kuala' }}, {{ $labConfig['address'] ?? 'Darussalam-Banda Aceh, Indonesia' }}. Advancing science through waves, optics, and spectroscopy.
                </p>
                @if(isset($labConfig['vision']))
                <p class="text-blue-100 text-sm italic">
                    "{{ Str::limit($labConfig['vision'], 120) }}"
                </p>
                @endif
            </div>

            <!-- Quick Links - Services -->
            <div x-data="{ animated: false }"
                x-scroll-animate="animated = true"
                :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                class="transition-all duration-1000 ease-out"
                style="transition-delay: 0.1s;">
                <h4 class="text-lg font-semibold mb-6 flex items-center">
                    <i class="fas fa-cogs mr-2 text-secondary"></i>
                    Layanan
                </h4>
                <div class="space-y-3">
                    <a href="{{ route('layanan.peminjaman-alat') }}" class="block text-blue-200 hover:text-secondary transition-colors duration-300 text-sm group">
                        <i class="fas fa-handshake mr-2 group-hover:animate-pulse"></i>
                        Peminjaman Alat
                    </a>
                    <a href="{{ route('layanan.kunjungan') }}" class="block text-blue-200 hover:text-secondary transition-colors duration-300 text-sm group">
                        <i class="fas fa-users mr-2 group-hover:animate-pulse"></i>
                        Kunjungan Lab
                    </a>
                    <a href="{{ route('layanan.pengujian') }}" class="block text-blue-200 hover:text-secondary transition-colors duration-300 text-sm group">
                        <i class="fas fa-vial mr-2 group-hover:animate-pulse"></i>
                        Pengujian Sampel
                    </a>
                </div>
            </div>

            <!-- Quick Links - Information -->
            <div x-data="{ animated: false }"
                x-scroll-animate="animated = true"
                :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                class="transition-all duration-1000 ease-out"
                style="transition-delay: 0.2s;">
                <h4 class="text-lg font-semibold mb-6 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-secondary"></i>
                    Informasi
                </h4>
                <div class="space-y-3">
                    <a href="{{ route('staff') }}" class="block text-blue-200 hover:text-secondary transition-colors duration-300 text-sm group">
                        <i class="fas fa-user-tie mr-2 group-hover:animate-pulse"></i>
                        Staff
                    </a>
                    <a href="{{ route('artikel') }}" class="block text-blue-200 hover:text-secondary transition-colors duration-300 text-sm group">
                        <i class="fas fa-newspaper mr-2 group-hover:animate-pulse"></i>
                        Artikel
                    </a>
                    <a href="{{ route('galeri') }}" class="block text-blue-200 hover:text-secondary transition-colors duration-300 text-sm group">
                        <i class="fas fa-images mr-2 group-hover:animate-pulse"></i>
                        Galeri
                    </a>
                </div>

                <h5 class="text-md font-semibold mt-6 mb-4 flex items-center">
                    <i class="fas fa-search mr-2 text-secondary"></i>
                    Tracking
                </h5>
                <div class="space-y-2">
                    <a href="{{ route('tracking.peminjaman-alat') }}" class="block text-blue-200 hover:text-secondary transition-colors duration-300 text-sm group">
                        <i class="fas fa-clipboard-check mr-2 group-hover:animate-pulse"></i>
                        Track Peminjaman
                    </a>
                    <a href="{{ route('tracking.kunjungan') }}" class="block text-blue-200 hover:text-secondary transition-colors duration-300 text-sm group">
                        <i class="fas fa-map-marker-alt mr-2 group-hover:animate-pulse"></i>
                        Track Kunjungan
                    </a>
                </div>
            </div>

            <!-- Contact Info -->
            <div x-data="{ animated: false }"
                x-scroll-animate="animated = true"
                :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                class="transition-all duration-1000 ease-out"
                style="transition-delay: 0.3s;">
                <h4 class="text-lg font-semibold mb-6 flex items-center">
                    <i class="fas fa-phone mr-2 text-secondary"></i>
                    Kontak
                </h4>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3 group">
                        <i class="fas fa-map-marker-alt text-secondary mt-1 group-hover:animate-bounce"></i>
                        <span class="text-blue-200 text-sm leading-relaxed">
                            {{ $labConfig['address'] ?? 'Darussalam-Banda Aceh, Indonesia 23111' }}
                        </span>
                    </div>
                    <div class="flex items-center space-x-3 group">
                        <i class="fas fa-envelope text-secondary group-hover:animate-pulse"></i>
                        <span class="text-blue-200 text-sm">
                            {{ $labConfig['contact']['email'] ?? 'lab-gos@usk.ac.id' }}
                        </span>
                    </div>
                    @if(isset($labConfig['contact']['phone']) && $labConfig['contact']['phone'])
                    <div class="flex items-center space-x-3 group">
                        <i class="fas fa-phone text-secondary group-hover:animate-pulse"></i>
                        <span class="text-blue-200 text-sm">{{ $labConfig['contact']['phone'] }}</span>
                    </div>
                    @endif
                    <div class="flex items-start space-x-3 group">
                        <i class="fas fa-clock text-secondary mt-1 group-hover:animate-pulse"></i>
                        <div class="text-blue-200 text-sm">
                            @if(isset($labConfig['operational_hours']))
                                <p>{{ $labConfig['operational_hours']['monday'] ?? 'Senin - Jumat: 08:00 - 16:00' }}</p>
                                <p>{{ $labConfig['operational_hours']['friday'] ?? 'Jumat: 08:00 - 11:30' }}</p>
                            @else
                                <p>Senin - Kamis: 08:00 - 16:00</p>
                                <p>Jumat: 08:00 - 11:30</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-blue-600 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-blue-200 text-sm">
                © {{ date('Y') }} {{ $labConfig['name'] ?? 'Laboratorium Gelombang, Optik & Spektroskopi' }}. All rights reserved.
            </p>
            <p class="text-blue-200 text-sm mt-4 md:mt-0 flex items-center">
                <i class="fas fa-heart text-red-400 mr-2 animate-pulse"></i>
                {{ $labConfig['department'] ?? 'Departemen Fisika FMIPA Universitas Syiah Kuala' }}
            </p>
        </div>
    </div>
</footer>
