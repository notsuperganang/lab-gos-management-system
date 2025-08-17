<x-public.layouts.main>
    <x-slot:title>
        Galeri Kegiatan - Lab GOS USK
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
                        🎨 Galeri 
                        <span class="text-secondary">Kegiatan</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Dokumentasi kegiatan dan fasilitas Laboratorium Gelombang, Optik & Spektroskopi
                </p>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-2 inline-block">
                    <p class="text-white flex items-center justify-center">
                        <i class="fas fa-camera mr-2 text-secondary"></i>
                        Laboratorium Gelombang, Optik & Spektroskopi
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if($galleryItems->count() > 0 || $currentCategory || $currentSearch)
            <!-- Search and Filter Section -->
            <div>
                
                <!-- Search Box -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="mb-8 transition-all duration-1000 ease-out">
                    <form method="GET" action="{{ route('galeri') }}" class="max-w-md mx-auto">
                        <div class="relative">
                            <input type="text" 
                                   name="q" 
                                   value="{{ $currentSearch }}"
                                   placeholder="Cari foto atau kata kunci..." 
                                   class="w-full px-4 py-3 pl-12 pr-20 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent shadow-lg">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <div class="bg-primary hover:bg-blue-800 text-white px-4 py-2 rounded-xl transition-colors duration-300">
                                    Cari
                                </div>
                            </button>
                        </div>
                        <!-- Preserve current category filter -->
                        @if($currentCategory)
                            <input type="hidden" name="category" value="{{ $currentCategory }}">
                        @endif
                    </form>
                </div>

                <!-- Filter Buttons -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="flex justify-center mb-8 md:mb-10 transition-all duration-1000 ease-out">
                    <div class="bg-white rounded-2xl p-2 shadow-lg inline-flex flex-wrap gap-2">
                        <a href="{{ route('galeri', request()->only('q')) }}" 
                           class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 {{ !$currentCategory ? 'bg-primary text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-th-large mr-2"></i>
                            Semua
                        </a>
                        
                        @foreach($categories as $slug => $label)
                        <a href="{{ route('galeri', array_merge(request()->only('q'), ['category' => $slug])) }}" 
                           class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 {{ $currentCategory === $slug ? 'bg-blue-500 text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-{{ $slug === 'lab_facilities' ? 'building' : ($slug === 'equipment' ? 'tools' : ($slug === 'activities' ? 'users' : ($slug === 'events' ? 'calendar-check' : 'folder'))) }} mr-2"></i>
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Gallery Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    @foreach($galleryItems as $index => $item)
                    <div x-data="{ animated: false }" 
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                         class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4 ease-out"
                         style="transition-delay: {{ $index * 0.1 }}s;">
                        
                        <!-- Image -->
                        <div class="relative h-64 overflow-hidden">
                            <x-media.image 
                                :src="$item->image_url" 
                                :alt="$item->title"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                            />
                            <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-50 transition-all duration-500"></div>
                            
                            <!-- Category Badge -->
                            @if($item->category)
                            <div class="absolute top-4 left-4">
                                <x-galleries.category-badge 
                                    :category="$item->category" 
                                    :label="$item->category_label"
                                    class="px-3 py-1 text-xs" 
                                />
                            </div>
                            @endif

                            <!-- View Button -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500">
                                <button onclick="openGalleryModal({{ $item->id }}, @js($item->image_url), @js($item->title), @js($item->description ?? ''))" 
                                        class="bg-white bg-opacity-90 hover:bg-white text-primary px-6 py-3 rounded-full font-semibold transition-all duration-300 transform scale-90 group-hover:scale-100">
                                    <i class="fas fa-search-plus mr-2"></i>
                                    Lihat Detail
                                </button>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-primary transition-colors duration-300 line-clamp-2">
                                {{ $item->title }}
                            </h3>
                            @if($item->description)
                            <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">
                                {{ $item->description }}
                            </p>
                            @endif
                            
                            <!-- Metadata -->
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span>{{ $item->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="shareGalleryItem(@js($item->title), @js($item->image_url))" 
                                            class="text-gray-400 hover:text-primary transition-colors duration-300">
                                        <i class="fas fa-share text-sm"></i>
                                    </button>
                                    <button onclick="downloadImage(@js($item->image_url), @js($item->title))" 
                                            class="text-gray-400 hover:text-primary transition-colors duration-300">
                                        <i class="fas fa-download text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Empty State for Filtered Results -->
                @if($galleryItems->count() === 0)
                <div class="text-center py-16">
                    <div class="text-6xl text-gray-300 mb-4">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada foto ditemukan</h3>
                    <p class="text-gray-500 mb-4">Coba kata kunci lain atau pilih kategori berbeda</p>
                    @if($currentCategory || $currentSearch)
                    <div class="flex justify-center space-x-4">
                        @if($currentSearch)
                        <a href="{{ route('galeri', ['category' => $currentCategory]) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-300">
                            <i class="fas fa-times mr-2"></i>
                            Hapus Pencarian
                        </a>
                        @endif
                        <a href="{{ route('galeri') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors duration-300">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Lihat Semua Foto
                        </a>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Pagination -->
                @if($galleryItems->hasPages())
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="transition-all duration-1000 ease-out">
                    {{ $galleryItems->onEachSide(1)->withQueryString()->links('vendor.pagination.gos') }}
                </div>
                @endif
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-20">
                <div class="text-6xl text-gray-300 mb-6">
                    <i class="fas fa-images"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-600 mb-4">Belum Ada Galeri</h3>
                <p class="text-gray-500">Dokumentasi kegiatan laboratorium akan segera ditambahkan.</p>
            </div>
            @endif
        </div>
    </section>

    <!-- Stats Section -->
    @if($totalPhotos > 0)
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }" 
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="bg-gradient-to-br from-primary to-blue-600 rounded-3xl p-8 md:p-12 text-white transition-all duration-1000 ease-out">
                
                <div class="text-center mb-8">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">
                        📊 Statistik Galeri
                    </h2>
                    <p class="text-blue-100 max-w-2xl mx-auto">
                        @if($currentCategory || $currentSearch)
                            Statistik untuk filter saat ini
                        @else
                            Dokumentasi perjalanan dan pencapaian Laboratorium GOS
                        @endif
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div class="p-4">
                        <div class="text-3xl font-bold text-secondary mb-2">
                            @if($currentCategory || $currentSearch)
                                {{ $galleryItems->total() }}
                            @else
                                {{ $totalPhotos }}
                            @endif
                        </div>
                        <div class="text-blue-200">Total Foto</div>
                    </div>
                    <div class="p-4">
                        <div class="text-3xl font-bold text-secondary mb-2">{{ $totalCategories }}</div>
                        <div class="text-blue-200">Kategori</div>
                    </div>
                    
                    @if(!$currentCategory && !$currentSearch && $categoryStats->count() > 0)
                        @foreach($categoryStats->take(2) as $stat)
                        <div class="p-4">
                            <div class="text-3xl font-bold text-secondary mb-2">{{ $stat['count'] }}</div>
                            <div class="text-blue-200">{{ $stat['name'] }}</div>
                        </div>
                        @endforeach
                    @endif
                </div>

                @if(!$currentCategory && !$currentSearch && $categoryStats->count() > 2)
                <div class="mt-8 pt-8 border-t border-blue-400">
                    <h3 class="text-xl font-semibold text-center mb-6 text-blue-100">Distribusi per Kategori</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($categoryStats as $stat)
                        <div class="text-center p-3 bg-white bg-opacity-10 rounded-xl">
                            <div class="text-lg font-bold text-secondary mb-1">{{ $stat['count'] }}</div>
                            <div class="text-blue-200 text-sm">{{ $stat['name'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    <!-- Gallery Modal -->
    <div x-data="{ showModal: false, currentImage: '', currentTitle: '', currentDescription: '' }" 
         x-cloak
         @open-gallery-modal.window="showModal = true; currentImage = $event.detail.image; currentTitle = $event.detail.title; currentDescription = $event.detail.description;"
         @keydown.escape.window="showModal = false">
        
        <!-- Modal Backdrop -->
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-75 z-50"
             @click="showModal = false"></div>
        
        <!-- Modal Content -->
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-400"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-250"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="fixed inset-0 flex items-center justify-center p-4 z-50 pointer-events-none">
            
            <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-screen overflow-y-auto pointer-events-auto"
                 @click.stop>
                <div class="relative">
                    <!-- Close Button -->
                    <button @click="showModal = false" 
                            class="absolute top-4 right-4 z-10 bg-black bg-opacity-50 hover:bg-opacity-70 text-white rounded-full w-10 h-10 flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                        <i class="fas fa-times"></i>
                    </button>

                    <!-- Image -->
                    <div class="relative h-96 md:h-[500px] overflow-hidden rounded-t-3xl">
                        <img :src="currentImage" :alt="currentTitle" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 md:p-8">
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4" x-text="currentTitle"></h3>
                        <p class="text-gray-600 leading-relaxed mb-6" 
                           x-text="currentDescription" 
                           x-show="currentDescription"></p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button @click="shareGalleryItem(currentTitle, currentImage)" 
                                        class="flex items-center text-primary hover:text-secondary transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-share mr-2"></i>
                                    Bagikan
                                </button>
                            </div>
                            <button @click="showModal = false" 
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-xl transition-all duration-300 transform hover:scale-105">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openGalleryModal(id, image, title, description) {
            window.dispatchEvent(new CustomEvent('open-gallery-modal', {
                detail: { 
                    id: id,
                    image: image, 
                    title: title, 
                    description: description 
                }
            }));
        }

        function shareGalleryItem(title, image) {
            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: 'Lihat dokumentasi dari Lab GOS USK',
                    url: window.location.href
                });
            } else {
                // Fallback to copy link
                navigator.clipboard.writeText(window.location.href).then(function() {
                    showNotification('Link berhasil disalin!');
                });
            }
        }

        function downloadImage(imageUrl, title) {
            const link = document.createElement('a');
            link.href = imageUrl;
            link.download = title.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.jpg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function showNotification(message) {
            const notification = document.createElement('div');
            notification.innerHTML = '<i class="fas fa-check mr-2"></i>' + message;
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            document.body.appendChild(notification);
            
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 3000);
        }
    </script>
</x-public.layouts.main>