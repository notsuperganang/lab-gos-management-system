<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorium Gelombang, Optik & Spektroskopi - Departemen Fisika USK</title>
    
    <!-- Laravel Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="font-sans antialiased bg-white text-gray-800 overflow-x-hidden">

    <!-- Header/Navbar with Scroll Transition -->
    <nav class="fixed w-full top-0 z-50 transition-all duration-700" 
         x-data="{ 
            mobileOpen: false, 
            scrolled: false,
            init() {
                window.addEventListener('scroll', () => {
                    this.scrolled = window.scrollY > 50;
                });
            }
         }"
         :class="scrolled ? 'bg-white shadow-lg' : 'bg-transparent'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo with Smooth Transition -->
                <div class="flex items-center relative">
                    <!-- Logo Putih (untuk navbar transparan) -->
                    <img src="/assets/images/logo-fisika-putih.png" 
                         alt="Logo Fisika FMIPA USK" 
                         class="h-12 w-auto transition-all duration-700 transform hover:scale-105"
                         :class="scrolled ? 'opacity-0 scale-95' : 'opacity-100 scale-100'">
                    <!-- Logo Hitam (untuk navbar putih) -->
                    <img src="/assets/images/logo-fisika-hitam.png" 
                         alt="Logo Fisika FMIPA USK" 
                         class="absolute top-0 left-0 h-12 w-auto transition-all duration-700 transform hover:scale-105"
                         :class="scrolled ? 'opacity-100 scale-100' : 'opacity-0 scale-95'">
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex space-x-8">
                    <a href="/" class="font-medium border-b-2 border-transparent hover:border-secondary transition-all duration-500 transform hover:-translate-y-1"
                       :class="scrolled ? 'text-gray-800 hover:text-primary' : 'text-white hover:text-secondary'">Beranda</a>
                    <a href="/staff" class="font-medium border-b-2 border-transparent hover:border-secondary transition-all duration-500 transform hover:-translate-y-1"
                       :class="scrolled ? 'text-gray-800 hover:text-primary' : 'text-white hover:text-secondary'">Staff</a>
                    <a href="/artikel" class="font-medium border-b-2 border-transparent hover:border-secondary transition-all duration-500 transform hover:-translate-y-1"
                       :class="scrolled ? 'text-gray-800 hover:text-primary' : 'text-white hover:text-secondary'">Artikel</a>
                    <a href="/layanan" class="font-medium border-b-2 border-transparent hover:border-secondary transition-all duration-500 transform hover:-translate-y-1"
                       :class="scrolled ? 'text-gray-800 hover:text-primary' : 'text-white hover:text-secondary'">Layanan</a>
                    <a href="/fasilitas" class="font-medium border-b-2 border-transparent hover:border-secondary transition-all duration-500 transform hover:-translate-y-1"
                       :class="scrolled ? 'text-gray-800 hover:text-primary' : 'text-white hover:text-secondary'">Fasilitas</a>
                </div>

                <!-- Mobile menu button -->
                <button @click="mobileOpen = !mobileOpen" class="md:hidden transition-colors duration-500"
                        :class="scrolled ? 'text-gray-800' : 'text-white'">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div x-show="mobileOpen" 
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="md:hidden pb-4 backdrop-blur-md bg-white bg-opacity-95 rounded-lg mx-4 mb-4">
                <div class="flex flex-col space-y-3 p-4">
                    <a href="/" class="text-gray-800 hover:text-primary transition-colors duration-300 font-medium">Beranda</a>
                    <a href="/staff" class="text-gray-800 hover:text-primary transition-colors duration-300 font-medium">Staff</a>
                    <a href="/artikel" class="text-gray-800 hover:text-primary transition-colors duration-300 font-medium">Artikel</a>
                    <a href="/layanan" class="text-gray-800 hover:text-primary transition-colors duration-300 font-medium">Layanan</a>
                    <a href="/fasilitas" class="text-gray-800 hover:text-primary transition-colors duration-300 font-medium">Fasilitas</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Full Background -->
    <section id="beranda" class="relative min-h-screen flex items-center justify-center">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
             style="background-image: url('/assets/images/hero-bg.jpeg');">
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        </div>
        
        <!-- Content -->
        <div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
            <div x-data="{ animated: false }" 
                 x-scroll-animate.once="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                 class="transition-all duration-1500 ease-out">
                <p class="text-lg md:text-xl mb-4 text-gray-200">
                    Selamat Datang di
                </p>
                <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    <span class="relative">
                        Laboratorium
                        <div class="absolute -inset-1 bg-gradient-to-r from-primary to-blue-600 rounded-lg blur opacity-20"></div>
                    </span><br>
                    <span class="text-secondary relative">
                        ✨ Gelombang, Optik & Spektroskopi
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-3 inline-block mb-8">
                    <p class="text-base md:text-lg text-white flex items-center justify-center">
                        <i class="fas fa-university mr-3 text-secondary"></i>
                        Departemen Fisika - Fakultas Matematika dan Ilmu Pengetahuan Alam - Universitas Syiah Kuala
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="/layanan" class="group bg-secondary hover:bg-yellow-500 text-gray-800 px-8 py-4 rounded-full font-semibold transition-all duration-500 transform hover:scale-105 hover:shadow-2xl">
                        <span class="flex items-center justify-center">
                            <i class="fas fa-rocket mr-2 group-hover:animate-bounce"></i>
                            Jelajahi Layanan
                        </span>
                    </a>
                    <a href="/fasilitas" class="group border-2 border-white text-white hover:bg-white hover:text-primary px-8 py-4 rounded-full font-semibold transition-all duration-500 transform hover:scale-105 hover:shadow-2xl">
                        <span class="flex items-center justify-center">
                            <i class="fas fa-microscope mr-2 group-hover:animate-pulse"></i>
                            Lihat Fasilitas
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
            <i class="fas fa-chevron-down text-2xl opacity-70"></i>
        </div>
    </section>

    <!-- Visi Misi Section -->
    <section id="visi-misi" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }" 
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="text-center mb-16 transition-all duration-1000 ease-out">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">
                    <span class="relative inline-block">
                        Visi & Misi 
                        <span class="text-secondary">Laboratorium</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                    </span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Komitmen kami dalam mengembangkan ilmu pengetahuan dan teknologi di bidang Gelombang, Optik dan Spektroskopi
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Visi -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
                     class="transition-all duration-1200 ease-out">
                    <div class="bg-gradient-to-br from-primary to-blue-600 p-8 rounded-2xl text-white shadow-xl transform hover:scale-105 transition-all duration-500">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 bg-secondary rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-eye text-white text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold">Visi Laboratorium</h3>
                        </div>
                        <p class="text-blue-100 leading-relaxed text-lg">
                            "Menjadi pusat unggulan dalam pendidikan dan penelitian di bidang Gelombang, Optik dan Spektroskopi untuk mendukung pengembangan ilmu pengetahuan, mitigasi bencana, pengelolaan lingkungan, dan pembangunan berkelanjutan pada tahun 2030."
                        </p>
                    </div>
                </div>

                <!-- Misi & Tentang -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'"
                     class="space-y-6 transition-all duration-1200 ease-out">
                    <div class="bg-gradient-to-br from-secondary to-yellow-600 p-6 rounded-2xl text-white shadow-xl transform hover:scale-105 transition-all duration-500">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-bullseye text-white text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold">Misi</h3>
                        </div>
                        <p class="text-yellow-100 leading-relaxed">
                            Menyelenggarakan kegiatan-kegiatan praktikum dan riset yang berhubungan dengan Gelombang, Optik dan Spektroskopi serta kegiatan-kegiatan penunjang lainnya untuk memperkuat Departemen Fisika.
                        </p>
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-all duration-500">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-microscope text-white text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Tentang Lab</h3>
                        </div>
                        <p class="text-gray-700 leading-relaxed">
                            Laboratorium Gelombang, Optik dan Spektroskopi adalah laboratorium di lingkungan Departemen Fisika, Fakultas MIPA, Universitas Syiah Kuala yang menyelenggarakan kegiatan-kegiatan praktikum dan riset yang berhubungan dengan Gelombang, Optik dan Spektroskopi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Artikel Section -->
    <section id="artikel" class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }" 
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="text-center mb-16 transition-all duration-1000 ease-out">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">
                    <span class="relative inline-block">
                        📰 Artikel 
                        <span class="text-secondary">Terbaru</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-500 rounded-full"></div>
                    </span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Berita dan informasi terbaru seputar kegiatan laboratorium dan perkembangan ilmu pengetahuan
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Artikel 1 -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                     class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4 hover:rotate-1 ease-out"
                     style="transition-delay: 0.1s;">
                    <div class="h-48 bg-gradient-to-br from-primary to-blue-600 relative overflow-hidden">
                        <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-newspaper text-white text-4xl group-hover:scale-110 transition-transform duration-500"></i>
                        </div>
                        <div class="absolute top-4 right-4 bg-secondary text-gray-800 px-3 py-1 rounded-full text-sm font-semibold">
                            Baru
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span>15 Januari 2025</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-primary transition-colors duration-300">
                            Penelitian Spektroskopi Terbaru di Lab GOS
                        </h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Tim peneliti Lab GOS berhasil mengembangkan metode spektroskopi baru yang dapat meningkatkan akurasi analisis material hingga 95%.
                        </p>
                        <a href="#" class="inline-flex items-center text-primary hover:text-secondary font-semibold transition-colors duration-300 group-hover:translate-x-2 transform">
                            Baca Selengkapnya
                            <i class="fas fa-arrow-right ml-2 group-hover:animate-pulse"></i>
                        </a>
                    </div>
                </div>

                <!-- Artikel 2 -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                     class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4 hover:-rotate-1 ease-out"
                     style="transition-delay: 0.2s;">
                    <div class="h-48 bg-gradient-to-br from-secondary to-yellow-600 relative overflow-hidden">
                        <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-users text-white text-4xl group-hover:scale-110 transition-transform duration-500"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span>10 Januari 2025</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-secondary transition-colors duration-300">
                            Kunjungan Mahasiswa Internasional ke Lab GOS
                        </h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Sebanyak 25 mahasiswa dari berbagai negara ASEAN mengunjungi Lab GOS untuk program pertukaran ilmu pengetahuan.
                        </p>
                        <a href="#" class="inline-flex items-center text-primary hover:text-secondary font-semibold transition-colors duration-300 group-hover:translate-x-2 transform">
                            Baca Selengkapnya
                            <i class="fas fa-arrow-right ml-2 group-hover:animate-pulse"></i>
                        </a>
                    </div>
                </div>

                <!-- Artikel 3 -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                     class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4 hover:rotate-1 ease-out"
                     style="transition-delay: 0.3s;">
                    <div class="h-48 bg-gradient-to-br from-green-600 to-green-700 relative overflow-hidden">
                        <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-award text-white text-4xl group-hover:scale-110 transition-transform duration-500"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span>5 Januari 2025</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-green-600 transition-colors duration-300">
                            Penghargaan Internasional untuk Lab GOS
                        </h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Lab GOS meraih penghargaan sebagai laboratorium terbaik di bidang spektroskopi dari organisasi ilmiah internasional.
                        </p>
                        <a href="#" class="inline-flex items-center text-primary hover:text-secondary font-semibold transition-colors duration-300 group-hover:translate-x-2 transform">
                            Baca Selengkapnya
                            <i class="fas fa-arrow-right ml-2 group-hover:animate-pulse"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="galeri" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }" 
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="text-center mb-16 transition-all duration-1000 ease-out">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">
                    <span class="relative inline-block">
                        🎨 Galeri 
                        <span class="text-secondary">Kegiatan</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-purple-500 rounded-full"></div>
                    </span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Dokumentasi kegiatan dan fasilitas Laboratorium Gelombang, Optik & Spektroskopi
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Gallery Item 1 -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
                     class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:scale-105 ease-out md:col-span-2 md:row-span-2"
                     style="transition-delay: 0.1s;">
                    <div class="aspect-square md:aspect-[2/2] bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center relative">
                        <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                        <i class="fas fa-microscope text-white text-6xl group-hover:scale-110 transition-transform duration-500 relative z-10"></i>
                        <div class="absolute bottom-4 left-4 right-4 text-white z-10">
                            <h3 class="font-bold text-lg mb-1">Peralatan Spektroskopi</h3>
                            <p class="text-sm text-blue-200">Fasilitas utama laboratorium</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 2 -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
                     class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:scale-105 ease-out"
                     style="transition-delay: 0.2s;">
                    <div class="aspect-square bg-gradient-to-br from-secondary to-yellow-600 flex items-center justify-center relative">
                        <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                        <i class="fas fa-users text-white text-3xl group-hover:scale-110 transition-transform duration-500 relative z-10"></i>
                        <div class="absolute bottom-2 left-2 right-2 text-white z-10">
                            <h3 class="font-bold text-sm mb-1">Praktikum</h3>
                            <p class="text-xs text-yellow-200">Mahasiswa</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 3 -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
                     class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:scale-105 ease-out"
                     style="transition-delay: 0.3s;">
                    <div class="aspect-square bg-gradient-to-br from-green-600 to-green-700 flex items-center justify-center relative">
                        <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                        <i class="fas fa-vial text-white text-3xl group-hover:scale-110 transition-transform duration-500 relative z-10"></i>
                        <div class="absolute bottom-2 left-2 right-2 text-white z-10">
                            <h3 class="font-bold text-sm mb-1">Penelitian</h3>
                            <p class="text-xs text-green-200">Eksperimen</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 4 -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
                     class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:scale-105 ease-out md:col-span-2"
                     style="transition-delay: 0.4s;">
                    <div class="aspect-square md:aspect-[2/1] bg-gradient-to-br from-purple-600 to-purple-700 flex items-center justify-center relative">
                        <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                        <i class="fas fa-atom text-white text-4xl group-hover:scale-110 transition-transform duration-500 relative z-10"></i>
                        <div class="absolute bottom-4 left-4 right-4 text-white z-10">
                            <h3 class="font-bold text-lg mb-1">Laboratorium Modern</h3>
                            <p class="text-sm text-purple-200">Fasilitas lengkap untuk penelitian optik</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="layanan" class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }" 
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="text-center mb-16 transition-all duration-1000 ease-out">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">
                    <span class="relative inline-block">
                        🚀 Layanan 
                        <span class="text-secondary">Laboratorium</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-green-500 to-secondary rounded-full"></div>
                    </span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Berbagai layanan yang kami sediakan untuk mendukung kegiatan akademik dan penelitian
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                     class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-6 hover:rotate-1 ease-out"
                     style="transition-delay: 0.1s;">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary to-blue-600 rounded-2xl flex items-center justify-center mb-6 mx-auto group-hover:scale-110 group-hover:rotate-12 transition-all duration-700 shadow-lg group-hover:shadow-2xl">
                        <i class="fas fa-handshake text-white text-2xl group-hover:animate-pulse"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center text-gray-800 mb-4 group-hover:text-primary transition-colors duration-500">Peminjaman Alat</h3>
                    <p class="text-gray-600 text-center mb-6 leading-relaxed">
                        Layanan peminjaman peralatan laboratorium untuk keperluan praktikum dan penelitian mahasiswa serta dosen dengan sistem booking online.
                    </p>
                    <div class="text-center">
                        <button class="group-hover:scale-105 bg-primary hover:bg-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-500 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <span class="flex items-center justify-center">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Ajukan Peminjaman
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Service 2 -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                     class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-6 hover:-rotate-1 ease-out"
                     style="transition-delay: 0.2s;">
                    <div class="w-20 h-20 bg-gradient-to-br from-secondary to-yellow-600 rounded-2xl flex items-center justify-center mb-6 mx-auto group-hover:scale-110 group-hover:rotate-12 transition-all duration-700 shadow-lg group-hover:shadow-2xl">
                        <i class="fas fa-users text-white text-2xl group-hover:animate-pulse"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center text-gray-800 mb-4 group-hover:text-secondary transition-colors duration-500">Kunjungan Lab</h3>
                    <p class="text-gray-600 text-center mb-6 leading-relaxed">
                        Layanan kunjungan laboratorium untuk keperluan edukasi, riset, atau kerjasama dengan pihak eksternal disertai tour fasilitas.
                    </p>
                    <div class="text-center">
                        <button class="group-hover:scale-105 bg-secondary hover:bg-yellow-600 text-gray-800 px-6 py-3 rounded-xl transition-all duration-500 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <span class="flex items-center justify-center">
                                <i class="fas fa-door-open mr-2"></i>
                                Daftar Kunjungan
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Service 3 -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                     class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-6 hover:rotate-1 ease-out"
                     style="transition-delay: 0.3s;">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-600 to-green-700 rounded-2xl flex items-center justify-center mb-6 mx-auto group-hover:scale-110 group-hover:rotate-12 transition-all duration-700 shadow-lg group-hover:shadow-2xl">
                        <i class="fas fa-vial text-white text-2xl group-hover:animate-pulse"></i>
                    </div>
                    <h3 class="text-xl font-bold text-center text-gray-800 mb-4 group-hover:text-green-600 transition-colors duration-500">Pengujian Sampel</h3>
                    <p class="text-gray-600 text-center mb-6 leading-relaxed">
                        Layanan pengujian dan analisis sampel menggunakan peralatan spektroskopi dan optik modern dengan hasil akurat.
                    </p>
                    <div class="text-center">
                        <button class="group-hover:scale-105 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl transition-all duration-500 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <span class="flex items-center justify-center">
                                <i class="fas fa-flask mr-2"></i>
                                Ajukan Pengujian
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="kontak" class="bg-primary text-white py-16 relative overflow-hidden">
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
                    <i class="fas fa-heart text-red-400 mr-2 animate-pulse"></i>
                    Departemen Fisika FMIPA Universitas Syiah Kuala
                </p>
            </div>
        </div>
    </footer>

</body>
</html>