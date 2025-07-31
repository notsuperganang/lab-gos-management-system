<!-- Footer -->
<footer class="bg-primary text-white py-16 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 h-64 bg-secondary rounded-full -translate-x-32 -translate-y-32"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-secondary rounded-full translate-x-48 translate-y-48"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
            <!-- Lab Info -->
            <div x-data="{ animated: false }"
                x-scroll-animate="animated = true"
                :class="animated ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'"
                class="md:col-span-2 transition-all duration-1000 ease-out">
                <div class="flex items-center space-x-3 mb-6">
                    <img src="/assets/images/logo-fisika-putih.png"
                        alt="Logo Fisika FMIPA USK"
                        class="h-12 w-auto transform hover:rotate-12 transition-transform duration-500">
                    <div>
                        <h3 class="text-xl font-bold">Lab GOS</h3>
                        <p class="text-blue-200">Laboratorium Gelombang, Optik & Spektroskopi</p>
                    </div>
                </div>
                <p class="text-blue-100 mb-6 max-w-md leading-relaxed">
                    Departemen Fisika FMIPA Universitas Syiah Kuala, Darussalam-Banda Aceh, Indonesia. Advancing science through waves, optics, and spectroscopy.
                </p>
            </div>

            <!-- Contact Info -->
            <div x-data="{ animated: false }"
                x-scroll-animate="animated = true"
                :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                class="md:col-span-2 transition-all duration-1000 ease-out"
                style="transition-delay: 0.2s;">
                <h4 class="text-lg font-semibold mb-6 flex items-center">
                    <i class="fas fa-phone mr-2 text-secondary"></i>
                    Kontak
                </h4>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3 group">
                        <i class="fas fa-map-marker-alt text-secondary mt-1 group-hover:animate-bounce"></i>
                        <span class="text-blue-200 text-sm leading-relaxed">Darussalam-Banda Aceh, Indonesia 23111</span>
                    </div>
                    <div class="flex items-center space-x-3 group">
                        <i class="fas fa-envelope text-secondary group-hover:animate-pulse"></i>
                        <span class="text-blue-200 text-sm">labgos@unsyiah.ac.id</span>
                    </div>
                    <div class="flex items-center space-x-3 group">
                        <i class="fas fa-phone text-secondary group-hover:animate-pulse"></i>
                        <span class="text-blue-200 text-sm">+62 651-7552922</span>
                    </div>
                    <div class="flex items-start space-x-3 group">
                        <i class="fas fa-clock text-secondary mt-1 group-hover:animate-pulse"></i>
                        <div class="text-blue-200 text-sm">
                            <p>Senin - Jumat: 08:00 - 16:00</p>
                            <p>Sabtu: 08:00 - 12:00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-blue-600 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-blue-200 text-sm">
                © 2025 Laboratorium Gelombang, Optik & Spektroskopi. All rights reserved.
            </p>
            <p class="text-blue-200 text-sm mt-4 md:mt-0 flex items-center">
                <i class="fas fa-heart text-red-400 mr-2 amain.blade.phpnimate-pulse"></i>
                Departemen Fisika FMIPA Universitas Syiah Kuala
            </p>
        </div>
    </div>
</footer>