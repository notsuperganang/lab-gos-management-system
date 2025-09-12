<x-public.layouts.main>
    <x-slot:title>
        {{ $article->title }} - Lab GOS USK
    </x-slot:title>

    <!-- Hero Section -->
    <section class="relative h-96 flex items-center justify-center">
        <!-- Static Hero Background -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
             style="background-image: url('{{ asset('assets/images/hero-bg.jpeg') }}');">
        </div>
        <div class="absolute inset-0 bg-black bg-opacity-60"></div>
        
        <div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <div x-data="{ animated: false }" 
                 x-scroll-animate.once="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                 class="transition-all duration-1200 ease-out">
                <div class="mb-4">
                    <x-articles.category-badge 
                        :category="$article->category" 
                        :label="$article->category_label"
                        class="px-4 py-2 text-sm" 
                    />
                </div>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                    {{ $article->title }}
                </h1>
                <div class="flex items-center justify-center space-x-6 text-gray-300">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <span>{{ $article->published_at->format('d M Y') }}</span>
                    </div>
                    @if($article->publisher)
                    <div class="flex items-center">
                        <i class="fas fa-user mr-2"></i>
                        <span>{{ $article->publisher->name }}</span>
                    </div>
                    @endif
                    @if($article->reading_time)
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        <span>{{ $article->reading_time }} min read</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="absolute bottom-8 left-8 z-30">
            <a href="{{ route('artikel') }}" class="bg-white bg-opacity-20 backdrop-blur-sm text-white px-4 py-2 rounded-xl hover:bg-opacity-30 transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </section>

    <!-- Article Content -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12">
                <!-- Article Meta -->
                <div class="flex items-center justify-between mb-8 pb-8 border-b border-gray-200">
                    <div class="flex items-center space-x-4">
                        @if($article->publisher)
                        <div class="w-12 h-12 bg-gradient-to-br from-primary to-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $article->publisher->name }}</h4>
                            <p class="text-sm text-gray-500">Penulis</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Social Share Buttons -->
                        <button onclick="shareArticle('facebook')" class="text-blue-600 hover:text-blue-800 transition-colors duration-300">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </button>
                        <button onclick="shareArticle('twitter')" class="text-blue-400 hover:text-blue-600 transition-colors duration-300">
                            <i class="fab fa-twitter text-xl"></i>
                        </button>
                        <button onclick="shareArticle('whatsapp')" class="text-green-600 hover:text-green-800 transition-colors duration-300">
                            <i class="fab fa-whatsapp text-xl"></i>
                        </button>
                        <button onclick="copyLink()" class="text-gray-600 hover:text-gray-800 transition-colors duration-300">
                            <i class="fas fa-link text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Featured Image (if available) -->
                @if($article->featured_image_path)
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="mb-8 transition-all duration-1000 ease-out">
                    <img src="{{ $article->featured_image_url }}" 
                         class="w-full rounded-2xl aspect-[16/9] object-cover" 
                         loading="lazy" 
                         alt="{{ $article->title }}">
                </div>
                @else
                <!-- Placeholder Image -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="mb-8 transition-all duration-1000 ease-out">
                    <img src="{{ asset('assets/images/placeholder.svg') }}" 
                         class="w-full rounded-2xl aspect-[16/9] object-cover" 
                         loading="lazy" 
                         alt="{{ $article->title }}">
                </div>
                @endif

                <!-- Article Body -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="prose prose-lg max-w-none transition-all duration-1000 ease-out">
                    <div class="text-xl text-gray-700 mb-8 font-medium leading-relaxed">
                        {{ $article->excerpt }}
                    </div>
                    
                    <div class="text-gray-700 leading-relaxed">
                        {!! $article->content !!}
                    </div>
                </div>

                <!-- Tags -->
                @if($article->tags && count($article->tags) > 0)
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Tags:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($article->tags as $tag)
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm hover:bg-primary hover:text-white transition-all duration-300 cursor-pointer">
                            #{{ $tag }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Related Articles -->
    @if($relatedArticles->count() > 0)
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ animated: false }" 
                 x-scroll-animate="animated = true"
                 :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                 class="text-center mb-16 transition-all duration-1000 ease-out">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">
                    <span class="relative inline-block">
                        📚 Artikel 
                        <span class="text-secondary">Terkait</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                    </span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Artikel lain yang mungkin menarik untuk Anda baca
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach($relatedArticles as $index => $relatedArticle)
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                     class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4 ease-out"
                     style="transition-delay: {{ $index * 0.1 }}s;">
                    
                    <div class="h-48 relative overflow-hidden">
                        <x-media.image 
                            :src="$relatedArticle->featured_image_url" 
                            :alt="$relatedArticle->title"
                            variant="card"
                            class="transition-transform duration-500 group-hover:scale-110"
                        />
                        <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                        
                        <div class="absolute top-4 left-4">
                            <x-articles.category-badge 
                                :category="$relatedArticle->category" 
                                :label="$relatedArticle->category_label"
                                class="px-3 py-1 text-xs" 
                            />
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span>{{ $relatedArticle->published_at->format('d M Y') }}</span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-800 mb-3 leading-tight group-hover:text-primary transition-colors duration-300 line-clamp-2">
                            {{ $relatedArticle->title }}
                        </h3>
                        
                        <p class="text-gray-600 leading-relaxed mb-4 text-sm line-clamp-3">
                            {{ $relatedArticle->excerpt }}
                        </p>
                        
                        <a href="{{ route('artikel.show', $relatedArticle->slug) }}" class="inline-flex items-center text-primary hover:text-secondary font-semibold transition-colors duration-300 group text-sm">
                            Baca Artikel
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- JavaScript for Social Sharing -->
    <script>
        function shareArticle(platform) {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent('{{ $article->title }}');
            const text = encodeURIComponent('{{ $article->excerpt }}');
            
            let shareUrl = '';
            
            switch(platform) {
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                    break;
                case 'whatsapp':
                    shareUrl = `https://wa.me/?text=${title} ${url}`;
                    break;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        }
        
        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(function() {
                // Show a temporary notification
                const notification = document.createElement('div');
                notification.innerHTML = '<i class="fas fa-check mr-2"></i>Link berhasil disalin!';
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 3000);
            });
        }
    </script>
</x-public.layouts.main>