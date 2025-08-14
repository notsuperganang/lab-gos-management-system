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
            
            @if($galleryItems->count() > 0)
            <!-- Filter Section -->
            <div x-data="{ 
                activeFilter: '{{ request('category', 'semua') }}',
                setFilter(filter) {
                    this.activeFilter = filter;
                    // Update URL with category filter
                    const url = new URL(window.location);
                    if (filter === 'semua') {
                        url.searchParams.delete('category');
                    } else {
                        url.searchParams.set('category', filter);
                    }
                    window.location.href = url.toString();
                }
            }">
                
                <!-- Filter Buttons -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="flex justify-center mb-12 transition-all duration-1000 ease-out">
                    <div class="bg-white rounded-2xl p-2 shadow-lg inline-flex flex-wrap gap-2">
                        <button @click="setFilter('semua')" 
                                :class="activeFilter === 'semua' ? 'bg-primary text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-th-large mr-2"></i>
                            Semua
                        </button>
                        
                        @foreach($categories as $category)
                        @if($category)
                        <button @click="setFilter('{{ $category }}')" 
                                :class="activeFilter === '{{ $category }}' ? 'bg-blue-500 text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 capitalize">
                            <i class="fas fa-{{ $category === 'research' ? 'microscope' : ($category === 'events' ? 'calendar-check' : ($category === 'facilities' ? 'building' : 'folder')) }} mr-2"></i>
                            {{ $category }}
                        </button>
                        @endif
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
                                <span class="bg-white bg-opacity-90 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold capitalize">
                                    {{ $item->category }}
                                </span>
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

                <!-- Pagination -->
                @if($galleryItems->hasPages())
                <div class="flex justify-center">
                    {{ $galleryItems->appends(request()->query())->links() }}
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
    @if($galleryItems->count() > 0)
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
                        Dokumentasi perjalanan dan pencapaian Laboratorium GOS
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div class="p-4">
                        <div class="text-3xl font-bold text-secondary mb-2">{{ $galleryItems->total() }}</div>
                        <div class="text-blue-200">Total Foto</div>
                    </div>
                    <div class="p-4">
                        <div class="text-3xl font-bold text-secondary mb-2">{{ $categories->count() }}</div>
                        <div class="text-blue-200">Kategori</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Gallery Modal -->
    <div x-data="{ showModal: false, currentImage: '', currentTitle: '', currentDescription: '' }" 
         x-show="showModal" 
         x-cloak
         @open-gallery-modal.window="showModal = true; currentImage = $event.detail.image; currentTitle = $event.detail.title; currentDescription = $event.detail.description;"
         @keydown.escape.window="showModal = false"
         class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50">
        
        <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="relative">
                <button @click="showModal = false" class="absolute top-4 right-4 z-10 bg-black bg-opacity-50 text-white rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>

                <div class="relative h-96 md:h-[500px] overflow-hidden rounded-t-3xl">
                    <img :src="currentImage" :alt="currentTitle" class="w-full h-full object-cover">
                </div>

                <div class="p-6 md:p-8">
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4" x-text="currentTitle"></h3>
                    <p class="text-gray-600 leading-relaxed mb-6" x-text="currentDescription" x-show="currentDescription"></p>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button @click="shareGalleryItem(currentTitle, currentImage)" class="flex items-center text-primary">
                                <i class="fas fa-share mr-2"></i>
                                Bagikan
                            </button>
                        </div>
                        <button @click="showModal = false" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-xl">
                            Tutup
                        </button>
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