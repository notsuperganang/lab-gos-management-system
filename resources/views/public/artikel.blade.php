<x-public.layouts.main>
    <x-slot:title>
        Artikel - Lab GOS USK
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
                        📰 Artikel & 
                        <span class="text-secondary">Berita</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Informasi terkini seputar penelitian, kegiatan, dan perkembangan laboratorium
                </p>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-2 inline-block">
                    <p class="text-white flex items-center justify-center">
                        <i class="fas fa-newspaper mr-2 text-secondary"></i>
                        Laboratorium Gelombang, Optik & Spektroskopi
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Featured Article -->
            <div x-data="{ animated: false }" 
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="mb-16 transition-all duration-1000 ease-out">
                <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">
                    <span class="relative inline-block">
                        ⭐ Artikel Utama
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                    </span>
                </h2>
                
                <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                    <div class="md:flex">
                        <!-- Image Section -->
                        <div class="md:w-1/2 h-64 md:h-auto relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary to-blue-600"></div>
                            <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-microscope text-white text-6xl"></i>
                            </div>
                            <div class="absolute top-4 left-4">
                                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                    <i class="fas fa-fire mr-1"></i>
                                    Trending
                                </span>
                            </div>
                        </div>
                        
                        <!-- Content Section -->
                        <div class="md:w-1/2 p-8">
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold mr-3">Penelitian</span>
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>20 Januari 2025</span>
                                <i class="fas fa-user ml-4 mr-2"></i>
                                <span>Dr. Ahmad Reza</span>
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 leading-tight">
                                Terobosan Baru dalam Spektroskopi Laser untuk Deteksi Material Nano
                            </h3>
                            <p class="text-gray-600 leading-relaxed mb-6">
                                Tim peneliti Lab GOS berhasil mengembangkan teknik spektroskopi laser revolusioner yang mampu mendeteksi material berukuran nano dengan tingkat akurasi hingga 99.8%. Penelitian ini membuka peluang besar dalam bidang nanoteknologi dan aplikasi medis di masa depan.
                            </p>
                            <div class="flex items-center justify-between">
                                <a href="#" class="inline-flex items-center text-primary hover:text-secondary font-semibold transition-colors duration-300 group">
                                    Baca Selengkapnya
                                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                                </a>
                                <div class="flex items-center space-x-4 text-gray-500">
                                    <span class="flex items-center">
                                        <i class="fas fa-eye mr-1"></i>
                                        <span class="text-sm">1,245</span>
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-heart mr-1"></i>
                                        <span class="text-sm">89</span>
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-share mr-1"></i>
                                        <span class="text-sm">23</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Articles Section -->
            <div x-data="{ 
                activeFilter: 'semua',
                articles: [
                    { 
                        title: 'Workshop Spektroskopi untuk Mahasiswa Baru', 
                        excerpt: 'Kegiatan pengenalan laboratorium dan pelatihan dasar spektroskopi untuk mahasiswa semester pertama.',
                        category: 'kegiatan', 
                        date: '18 Januari 2025', 
                        author: 'Devi Anggraini',
                        views: 542,
                        likes: 34,
                        image: 'workshop'
                    },
                    { 
                        title: 'Publikasi Hasil Penelitian di Journal Nature', 
                        excerpt: 'Paper penelitian tentang aplikasi optik kuantum berhasil dipublikasikan di jurnal internasional bergengsi.',
                        category: 'penelitian', 
                        date: '15 Januari 2025', 
                        author: 'Dr. Siti Nurhaliza',
                        views: 891,
                        likes: 67,
                        image: 'research'
                    },
                    { 
                        title: 'Pengumuman Jadwal Praktikum Semester Genap', 
                        excerpt: 'Informasi lengkap mengenai jadwal praktikum dan aturan baru untuk semester genap 2024/2025.',
                        category: 'pengumuman', 
                        date: '12 Januari 2025', 
                        author: 'Rizky Pratama',
                        views: 1234,
                        likes: 45,
                        image: 'announcement'
                    },
                    { 
                        title: 'Kolaborasi Internasional dengan Universitas Tokyo', 
                        excerpt: 'Lab GOS menjalin kerjasama penelitian dengan Tokyo Institute of Technology dalam bidang optik quantum.',
                        category: 'penelitian', 
                        date: '10 Januari 2025', 
                        author: 'Prof. Dr. Fatimah Al-Zahra',
                        views: 723,
                        likes: 78,
                        image: 'collaboration'
                    },
                    { 
                        title: 'Open House Lab GOS 2025', 
                        excerpt: 'Acara tahunan untuk memperkenalkan fasilitas dan kegiatan laboratorium kepada masyarakat umum.',
                        category: 'kegiatan', 
                        date: '8 Januari 2025', 
                        author: 'Lina Marlina',
                        views: 456,
                        likes: 29,
                        image: 'openhouse'
                    },
                    { 
                        title: 'Penyebaran Informasi Beasiswa Penelitian', 
                        excerpt: 'Tersedia beasiswa untuk mahasiswa yang ingin melakukan penelitian lanjutan di bidang spektroskopi.',
                        category: 'pengumuman', 
                        date: '5 Januari 2025', 
                        author: 'Dr. Muhammad Iqbal',
                        views: 687,
                        likes: 52,
                        image: 'scholarship'
                    }
                ],
                get filteredArticles() {
                    if (this.activeFilter === 'semua') {
                        return this.articles;
                    }
                    return this.articles.filter(article => article.category === this.activeFilter);
                },
                setFilter(filter) {
                    this.activeFilter = filter;
                },
                getCategoryColor(category) {
                    const colors = {
                        'penelitian': 'from-blue-500 to-blue-700',
                        'kegiatan': 'from-green-500 to-green-700', 
                        'pengumuman': 'from-purple-500 to-purple-700'
                    };
                    return colors[category] || 'from-gray-500 to-gray-700';
                },
                getCategoryBadgeColor(category) {
                    const colors = {
                        'penelitian': 'bg-blue-100 text-blue-800',
                        'kegiatan': 'bg-green-100 text-green-800',
                        'pengumuman': 'bg-purple-100 text-purple-800'
                    };
                    return colors[category] || 'bg-gray-100 text-gray-800';
                }
            }">
                
                <!-- Filter Buttons -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="flex justify-center mb-12 transition-all duration-1000 ease-out">
                    <div class="bg-white rounded-2xl p-2 shadow-lg inline-flex space-x-2">
                        <button @click="setFilter('semua')" 
                                :class="activeFilter === 'semua' ? 'bg-primary text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-th-large mr-2"></i>
                            Semua
                        </button>
                        <button @click="setFilter('penelitian')" 
                                :class="activeFilter === 'penelitian' ? 'bg-blue-500 text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-microscope mr-2"></i>
                            Penelitian
                        </button>
                        <button @click="setFilter('kegiatan')" 
                                :class="activeFilter === 'kegiatan' ? 'bg-green-500 text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Kegiatan
                        </button>
                        <button @click="setFilter('pengumuman')" 
                                :class="activeFilter === 'pengumuman' ? 'bg-purple-500 text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-bullhorn mr-2"></i>
                            Pengumuman
                        </button>
                    </div>
                </div>

                <!-- Articles Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <template x-for="(article, index) in filteredArticles" :key="article.title">
                        <div x-data="{ animated: false }" 
                             x-scroll-animate="animated = true"
                             :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                             class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4 hover:rotate-1 ease-out"
                             :style="`transition-delay: ${index * 0.1}s`">
                            
                            <!-- Article Image -->
                            <div class="relative h-48 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br"
                                     :class="getCategoryColor(article.category)"></div>
                                <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                                
                                <!-- Icon based on category -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <i class="text-white text-4xl group-hover:scale-110 transition-transform duration-500"
                                       :class="{
                                           'fas fa-flask': article.category === 'penelitian',
                                           'fas fa-users': article.category === 'kegiatan',
                                           'fas fa-megaphone': article.category === 'pengumuman'
                                       }"></i>
                                </div>
                                
                                <!-- Category Badge -->
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                                          :class="getCategoryBadgeColor(article.category)"
                                          x-text="article.category">
                                    </span>
                                </div>

                                <!-- Stats Badge -->
                                <div class="absolute top-4 right-4 flex space-x-2">
                                    <span class="bg-black bg-opacity-50 text-white px-2 py-1 rounded-full text-xs flex items-center">
                                        <i class="fas fa-eye mr-1"></i>
                                        <span x-text="article.views"></span>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Article Content -->
                            <div class="p-6">
                                <div class="flex items-center text-sm text-gray-500 mb-3">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span x-text="article.date"></span>
                                    <i class="fas fa-user ml-4 mr-2"></i>
                                    <span x-text="article.author"></span>
                                </div>
                                
                                <h3 class="text-lg font-bold text-gray-800 mb-3 leading-tight group-hover:text-primary transition-colors duration-300" 
                                    x-text="article.title"></h3>
                                
                                <p class="text-gray-600 leading-relaxed mb-4 text-sm" 
                                   x-text="article.excerpt"></p>
                                
                                <!-- Action Bar -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <a href="#" class="inline-flex items-center text-primary hover:text-secondary font-semibold transition-colors duration-300 group text-sm">
                                        Baca Selengkapnya
                                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                                    </a>
                                    
                                    <div class="flex items-center space-x-3 text-gray-500">
                                        <button class="flex items-center hover:text-red-500 transition-colors duration-300">
                                            <i class="fas fa-heart mr-1 text-xs"></i>
                                            <span class="text-xs" x-text="article.likes"></span>
                                        </button>
                                        <button class="flex items-center hover:text-blue-500 transition-colors duration-300">
                                            <i class="fas fa-share mr-1 text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredArticles.length === 0" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="text-center py-16">
                    <div class="text-6xl text-gray-300 mb-4">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada artikel ditemukan</h3>
                    <p class="text-gray-500">Coba pilih kategori lain atau reset filter</p>
                </div>

                <!-- Load More Button -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="text-center mt-12 transition-all duration-1000 ease-out">
                    <button class="bg-primary hover:bg-blue-800 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                        <i class="fas fa-plus mr-2"></i>
                        Muat Lebih Banyak
                    </button>
                </div>

                <!-- Statistics -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="mt-16 bg-white rounded-2xl p-8 shadow-lg transition-all duration-1000 ease-out">
                    <h3 class="text-2xl font-bold text-center text-gray-800 mb-8">
                        <span class="relative inline-block">
                            📊 Statistik Artikel
                            <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                        </span>
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                        <div class="p-4">
                            <div class="text-3xl font-bold text-primary mb-2" x-text="articles.length"></div>
                            <div class="text-gray-600">Total Artikel</div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-blue-500 mb-2" x-text="articles.filter(a => a.category === 'penelitian').length"></div>
                            <div class="text-gray-600">Penelitian</div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-green-500 mb-2" x-text="articles.filter(a => a.category === 'kegiatan').length"></div>
                            <div class="text-gray-600">Kegiatan</div>
                        </div>
                        <div class="p-4">
                            <div class="text-3xl font-bold text-purple-500 mb-2" x-text="articles.filter(a => a.category === 'pengumuman').length"></div>
                            <div class="text-gray-600">Pengumuman</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-public.layouts.main>