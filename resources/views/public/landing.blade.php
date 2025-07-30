<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab GOS - Laboratorium Gelombang, Optik dan Spektroskopi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --color-primary: #1E40AF;
            --color-secondary: #FDB813;
            --color-white: #FFFFFF;
            --color-text: #212529;
            --color-light-gray: #E9ECEF;
        }
        
        .lab-primary { background-color: var(--color-primary); }
        .lab-secondary { background-color: var(--color-secondary); }
        .lab-text-primary { color: var(--color-primary); }
        .lab-text-secondary { color: var(--color-secondary); }
        .lab-border-primary { border-color: var(--color-primary); }
        
        .hero-gradient {
            background: linear-gradient(135deg, var(--color-primary) 0%, #3B82F6 100%);
        }
        
        .section-title {
            position: relative;
            padding-bottom: 1rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--color-secondary);
        }
    </style>
</head>
<body class="min-h-full bg-gray-50">
    <!-- Navigation Header -->
    <nav class="lab-primary shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo & Lab Name -->
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 lab-secondary rounded-full flex items-center justify-center">
                        <i class="fas fa-atom text-white text-xl"></i>
                    </div>
                    <div class="text-white">
                        <h1 class="font-bold text-lg">Lab GOS</h1>
                        <p class="text-blue-200 text-xs">Gelombang, Optik & Spektroskopi</p>
                    </div>
                </div>
                
                <!-- Navigation Menu -->
                <div class="hidden md:flex space-x-8">
                    <a href="#beranda" class="text-white hover:text-yellow-300 transition duration-300 font-medium">
                        <i class="fas fa-home mr-2"></i>Beranda
                    </a>
                    <a href="#staff" class="text-white hover:text-yellow-300 transition duration-300 font-medium">
                        <i class="fas fa-users mr-2"></i>Staff & Artikel
                    </a>
                    <a href="#layanan" class="text-white hover:text-yellow-300 transition duration-300 font-medium">
                        <i class="fas fa-cogs mr-2"></i>Layanan
                    </a>
                    <a href="#fasilitas" class="text-white hover:text-yellow-300 transition duration-300 font-medium">
                        <i class="fas fa-microscope mr-2"></i>Fasilitas
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-white hover:text-yellow-300">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden pb-4">
                <div class="flex flex-col space-y-3">
                    <a href="#beranda" class="text-white hover:text-yellow-300 transition duration-300">
                        <i class="fas fa-home mr-2"></i>Beranda
                    </a>
                    <a href="#staff" class="text-white hover:text-yellow-300 transition duration-300">
                        <i class="fas fa-users mr-2"></i>Staff & Artikel
                    </a>
                    <a href="#layanan" class="text-white hover:text-yellow-300 transition duration-300">
                        <i class="fas fa-cogs mr-2"></i>Layanan
                    </a>
                    <a href="#fasilitas" class="text-white hover:text-yellow-300 transition duration-300">
                        <i class="fas fa-microscope mr-2"></i>Fasilitas
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero-gradient pt-24 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Hero Content -->
                <div class="text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                        Laboratorium 
                        <span class="lab-text-secondary">Gelombang, Optik & Spektroskopi</span>
                    </h1>
                    <p class="text-xl mb-8 text-blue-100 leading-relaxed">
                        Pusat unggulan dalam pendidikan dan penelitian di bidang Gelombang, Optik dan Spektroskopi 
                        untuk mendukung pengembangan ilmu pengetahuan dan teknologi.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button class="lab-secondary hover:bg-yellow-500 text-black px-8 py-3 rounded-lg font-semibold transition duration-300 shadow-lg">
                            <i class="fas fa-flask mr-2"></i>Layanan Lab
                        </button>
                        <button class="border-2 border-white text-white hover:bg-white hover:text-blue-800 px-8 py-3 rounded-lg font-semibold transition duration-300">
                            <i class="fas fa-info-circle mr-2"></i>Tentang Kami
                        </button>
                    </div>
                </div>
                
                <!-- Hero Image -->
                <div class="relative">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 shadow-2xl">
                        <div class="aspect-square bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-microscope text-white text-8xl opacity-80"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About & Vision Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center lab-text-primary mb-4 section-title">
                Tentang Laboratorium GOS
            </h2>
            
            <div class="grid md:grid-cols-2 gap-12 mt-12">
                <!-- About Content -->
                <div>
                    <h3 class="text-xl font-semibold lab-text-primary mb-4">Tentang Kami</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Laboratorium Gelombang, Optik dan Spektroskopi merupakan bagian dari Departemen Fisika 
                        FMIPA Universitas Syiah Kuala yang berfokus pada penelitian dan pendidikan di bidang 
                        gelombang elektromagnetik, optik, dan spektroskopi.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 lab-secondary rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            <p class="text-gray-600">Fasilitas laboratorium lengkap dan modern</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 lab-secondary rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            <p class="text-gray-600">Tenaga ahli berpengalaman di bidangnya</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 lab-secondary rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            <p class="text-gray-600">Melayani penelitian internal dan eksternal</p>
                        </div>
                    </div>
                </div>
                
                <!-- Vision & Mission -->
                <div>
                    <h3 class="text-xl font-semibold lab-text-primary mb-4">Visi Laboratorium</h3>
                    <div class="bg-gray-50 p-6 rounded-lg border-l-4 lab-border-primary">
                        <p class="text-gray-700 italic leading-relaxed">
                            "Menjadi pusat unggulan dalam pendidikan dan penelitian di bidang Gelombang, 
                            Optik dan Spektroskopi untuk mendukung pengembangan ilmu pengetahuan, 
                            mitigasi bencana, pengelolaan lingkungan, dan pembangunan berkelanjutan pada tahun 2030."
                        </p>
                    </div>
                    
                    <div class="mt-8">
                        <h4 class="font-semibold lab-text-primary mb-3">Tugas Pokok & Fungsi</h4>
                        <p class="text-gray-600 leading-relaxed">
                            Menyelenggarakan kegiatan-kegiatan praktikum dan riset yang berhubungan dengan 
                            Gelombang, Optik dan Spektroskopi serta kegiatan-kegiatan penunjang lainnya 
                            untuk memperkuat Departemen Fisika dalam upaya mempertahankan akreditasi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services/Articles Section -->
    <section id="layanan" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center lab-text-primary mb-4 section-title">
                Layanan Laboratorium
            </h2>
            
            <div class="grid md:grid-cols-3 gap-8 mt-12">
                <!-- Service 1 -->
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden">
                    <div class="lab-primary h-2"></div>
                    <div class="p-6">
                        <div class="w-12 h-12 lab-secondary rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-tools text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold lab-text-primary mb-3">
                            Penyewaan Alat Laboratorium
                        </h3>
                        <p class="text-gray-600 mb-4">
                            Layanan penyewaan peralatan laboratorium untuk keperluan penelitian dan praktikum 
                            dengan prosedur yang mudah dan aman.
                        </p>
                        <a href="#" class="lab-text-secondary hover:text-yellow-600 font-medium">
                            Selengkapnya <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service 2 -->
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden">
                    <div class="lab-primary h-2"></div>
                    <div class="p-6">
                        <div class="w-12 h-12 lab-secondary rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold lab-text-primary mb-3">
                            Kunjungan Laboratorium
                        </h3>
                        <p class="text-gray-600 mb-4">
                            Program kunjungan edukasi untuk mahasiswa, peneliti, dan institusi lain 
                            yang ingin mempelajari fasilitas laboratorium.
                        </p>
                        <a href="#" class="lab-text-secondary hover:text-yellow-600 font-medium">
                            Selengkapnya <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Service 3 -->
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden">
                    <div class="lab-primary h-2"></div>
                    <div class="p-6">
                        <div class="w-12 h-12 lab-secondary rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-vial text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold lab-text-primary mb-3">
                            Pengujian & Analisis Sampel
                        </h3>
                        <p class="text-gray-600 mb-4">
                            Layanan pengujian dan analisis sampel menggunakan teknologi spektroskopi 
                            dengan hasil yang akurat dan terpercaya.
                        </p>
                        <a href="#" class="lab-text-secondary hover:text-yellow-600 font-medium">
                            Selengkapnya <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="fasilitas" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center lab-text-primary mb-4 section-title">
                Galeri Fasilitas
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-12">
                <!-- Large Image -->
                <div class="col-span-2 row-span-2">
                    <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl h-full min-h-[300px] flex items-center justify-center">
                        <div class="text-white text-center">
                            <i class="fas fa-microscope text-6xl mb-4"></i>
                            <p class="text-lg font-medium">Mikroskop Elektronik</p>
                        </div>
                    </div>
                </div>
                
                <!-- Small Images -->
                <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-xl h-32 flex items-center justify-center">
                    <div class="text-white text-center">
                        <i class="fas fa-wave-square text-2xl mb-2"></i>
                        <p class="text-sm">Spektrometer</p>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-xl h-32 flex items-center justify-center">
                    <div class="text-white text-center">
                        <i class="fas fa-atom text-2xl mb-2"></i>
                        <p class="text-sm">Lab Optik</p>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl h-32 flex items-center justify-center">
                    <div class="text-white text-center">
                        <i class="fas fa-flask text-2xl mb-2"></i>
                        <p class="text-sm">Ruang Analisis</p>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-xl h-32 flex items-center justify-center">
                    <div class="text-white text-center">
                        <i class="fas fa-cog text-2xl mb-2"></i>
                        <p class="text-sm">Peralatan Lab</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="lab-primary text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Lab Info -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-12 h-12 lab-secondary rounded-full flex items-center justify-center">
                            <i class="fas fa-atom text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-xl">Lab GOS</h3>
                            <p class="text-blue-200">Gelombang, Optik & Spektroskopi</p>
                        </div>
                    </div>
                    <p class="text-blue-100 leading-relaxed mb-4">
                        Laboratorium Gelombang, Optik dan Spektroskopi adalah pusat penelitian dan pendidikan 
                        di Departemen Fisika FMIPA Universitas Syiah Kuala.
                    </p>
                    <p class="text-blue-200">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Darussalam-Banda Aceh, Aceh
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold text-lg mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#beranda" class="text-blue-200 hover:text-yellow-300 transition duration-300">Beranda</a></li>
                        <li><a href="#staff" class="text-blue-200 hover:text-yellow-300 transition duration-300">Staff & Artikel</a></li>
                        <li><a href="#layanan" class="text-blue-200 hover:text-yellow-300 transition duration-300">Layanan</a></li>
                        <li><a href="#fasilitas" class="text-blue-200 hover:text-yellow-300 transition duration-300">Fasilitas</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h4 class="font-semibold text-lg mb-4">Kontak</h4>
                    <div class="space-y-3">
                        <p class="text-blue-200">
                            <i class="fas fa-envelope mr-2"></i>
                            fisika@usk.ac.id
                        </p>
                        <p class="text-blue-200">
                            <i class="fas fa-globe mr-2"></i>
                            fisika.usk.ac.id
                        </p>
                        <p class="text-blue-200">
                            <i class="fas fa-clock mr-2"></i>
                            Sen-Kam: 08:00-16:00<br>
                            <span class="ml-6">Jumat: 08:00-11:30</span>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="border-t border-blue-600 mt-8 pt-8 text-center">
                <p class="text-blue-200">
                    © 2025 Laboratorium GOS - Departemen Fisika FMIPA USK. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('mobile-menu').classList.add('hidden');
            });
        });
    </script>
</body>
</html>