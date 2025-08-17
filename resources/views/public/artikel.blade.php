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
            
            @if($featured)
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
                            <x-media.image 
                                :src="$featured->featured_image_url" 
                                :alt="$featured->title"
                                class="w-full h-full object-cover"
                            />
                            <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                            <div class="absolute top-4 left-4">
                                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                    <i class="fas fa-star mr-1"></i>
                                    Featured
                                </span>
                            </div>
                        </div>
                        
                        <!-- Content Section -->
                        <div class="md:w-1/2 p-8">
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <x-articles.category-badge 
                                    :category="$featured->category" 
                                    :label="$featured->category_label"
                                    class="px-2 py-1 text-xs mr-3" 
                                />
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>{{ $featured->published_at->format('d M Y') }}</span>
                                @if($featured->publisher)
                                <i class="fas fa-user ml-4 mr-2"></i>
                                <span>{{ $featured->publisher->name }}</span>
                                @endif
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 leading-tight">
                                {{ $featured->title }}
                            </h3>
                            <p class="text-gray-600 leading-relaxed mb-6">
                                {{ $featured->excerpt }}
                            </p>
                            <div class="flex items-center justify-between">
                                <a href="{{ route('artikel.show', $featured->slug) }}" class="inline-flex items-center text-primary hover:text-secondary font-semibold transition-colors duration-300 group">
                                    Baca Selengkapnya
                                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                                </a>
                                <div class="flex items-center text-gray-500">
                                    <span class="flex items-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span class="text-sm">{{ $featured->reading_time }} min</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Filter & Articles Section -->
            <div>
                
                <!-- Search and Filter -->
                <div class="mb-6 md:mb-8 lg:mb-10">
                    <!-- Search Box -->
                    <div x-data="{ animated: false }" 
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="mb-8 transition-all duration-1000 ease-out">
                        <form method="GET" action="{{ route('artikel') }}" class="max-w-md mx-auto">
                            <div class="relative">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Cari artikel..." 
                                       class="w-full px-4 py-3 pl-12 pr-20 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent shadow-lg">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <div class="bg-primary hover:bg-blue-800 text-white px-4 py-2 rounded-xl transition-colors duration-300">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </button>
                            </div>
                            <!-- Preserve current search -->
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                        </form>
                    </div>

                    <!-- Filter Buttons -->
                    <div x-data="{ animated: false }" 
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                         class="flex justify-center transition-all duration-1000 ease-out">
                    <div class="bg-white rounded-2xl p-2 shadow-lg inline-flex flex-wrap gap-2">
                        <a href="{{ route('artikel', request()->only('search')) }}" 
                           class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 {{ !$currentCategory ? 'bg-primary text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-th-large mr-2"></i>
                            Semua
                        </a>
                        
                        @foreach($categories as $key => $label)
                        <a href="{{ route('artikel', array_merge(request()->only('search'), ['category' => $key])) }}" 
                           class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 {{ $currentCategory === $key ? 'bg-blue-500 text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-{{ $key === 'news' ? 'newspaper' : ($key === 'research' ? 'microscope' : ($key === 'announcement' ? 'bullhorn' : ($key === 'publication' ? 'book' : 'folder'))) }} mr-2"></i>
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Articles Grid -->
                <div class="mt-12"></div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($others as $index => $article)
                        <div x-data="{ animated: false }" 
                             x-scroll-animate="animated = true"
                             :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                             class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4 hover:rotate-1 ease-out"
                             style="transition-delay: {{ $index * 0.1 }}s;">
                            
                            <!-- Article Image -->
                            <div class="relative h-48 overflow-hidden">
                                <x-media.image 
                                    :src="$article->featured_image_url" 
                                    :alt="$article->title"
                                    variant="card"
                                    class="transition-transform duration-500 group-hover:scale-110"
                                />
                                <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                                
                                <!-- Category Badge -->
                                <div class="absolute top-4 left-4">
                                    <x-articles.category-badge 
                                        :category="$article->category" 
                                        :label="$article->category_label"
                                        class="px-3 py-1 text-xs" 
                                    />
                                </div>
                            </div>
                            
                            <!-- Article Content -->
                            <div class="p-6">
                                <div class="flex items-center text-sm text-gray-500 mb-3">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span>{{ $article->published_at->format('d M Y') }}</span>
                                    @if($article->publisher)
                                    <i class="fas fa-user ml-4 mr-2"></i>
                                    <span>{{ $article->publisher->name }}</span>
                                    @endif
                                </div>
                                
                                <h3 class="text-lg font-bold text-gray-800 mb-3 leading-tight group-hover:text-primary transition-colors duration-300 line-clamp-2">
                                    {{ $article->title }}
                                </h3>
                                
                                <p class="text-gray-600 leading-relaxed mb-4 text-sm line-clamp-3">
                                    {{ $article->excerpt }}
                                </p>
                                
                                <!-- Action Bar -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <a href="{{ route('artikel.show', $article->slug) }}" class="inline-flex items-center text-primary hover:text-secondary font-semibold transition-colors duration-300 group text-sm">
                                        Baca Selengkapnya
                                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                                    </a>
                                    
                                    <div class="flex items-center text-gray-500">
                                        @if($article->reading_time)
                                        <span class="flex items-center">
                                            <i class="fas fa-clock mr-1 text-xs"></i>
                                            <span class="text-xs">{{ $article->reading_time }} min</span>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Empty State -->
                @if($others->count() === 0)
                <div class="text-center py-16">
                    <div class="text-6xl text-gray-300 mb-4">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada artikel ditemukan</h3>
                    <p class="text-gray-500">Coba pilih kategori lain atau reset filter</p>
                    @if($currentCategory)
                    <div class="mt-4">
                        <a href="{{ route('artikel') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors duration-300">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Lihat Semua Artikel
                        </a>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Pagination -->
                @if($others->hasPages())
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="transition-all duration-1000 ease-out">
                    {{ $others->onEachSide(1)->withQueryString()->links('vendor.pagination.gos') }}
                </div>
                @endif

                <!-- Statistics -->
                @if($others->total() > 0 || $featured)
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
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-6 text-center">
                        @php
                            $totalArticles = \App\Models\Article::published()->count();
                            $researchCount = \App\Models\Article::published()->byCategory('research')->count();
                            $newsCount = \App\Models\Article::published()->byCategory('news')->count();
                            $announcementCount = \App\Models\Article::published()->byCategory('announcement')->count();
                            $publicationCount = \App\Models\Article::published()->byCategory('publication')->count();
                        @endphp
                        
                        @if($currentCategory)
                            <div class="p-4">
                                <div class="text-3xl font-bold text-primary mb-2">{{ $others->total() + (($featured && $featured->category === $currentCategory) ? 1 : 0) }}</div>
                                <div class="text-gray-600">{{ $categories[$currentCategory] ?? 'Artikel' }}</div>
                            </div>
                        @else
                            <div class="p-4">
                                <div class="text-3xl font-bold text-primary mb-2">{{ $totalArticles }}</div>
                                <div class="text-gray-600">Total Artikel</div>
                            </div>
                            
                            @if($researchCount > 0)
                            <div class="p-4">
                                <div class="text-3xl font-bold text-blue-500 mb-2">{{ $researchCount }}</div>
                                <div class="text-gray-600">Penelitian</div>
                            </div>
                            @endif
                            
                            @if($newsCount > 0)
                            <div class="p-4">
                                <div class="text-3xl font-bold text-green-500 mb-2">{{ $newsCount }}</div>
                                <div class="text-gray-600">Berita</div>
                            </div>
                            @endif
                            
                            @if($announcementCount > 0)
                            <div class="p-4">
                                <div class="text-3xl font-bold text-purple-500 mb-2">{{ $announcementCount }}</div>
                                <div class="text-gray-600">Pengumuman</div>
                            </div>
                            @endif
                            
                            @if($publicationCount > 0)
                            <div class="p-4">
                                <div class="text-3xl font-bold text-orange-500 mb-2">{{ $publicationCount }}</div>
                                <div class="text-gray-600">Publikasi</div>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

</x-public.layouts.main>