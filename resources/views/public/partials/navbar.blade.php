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
                <a href="/"
                    class="font-medium border-b-2 hover:border-secondary transition-all duration-500 transform hover:-translate-y-1 {{ request()->is('/') ? 'border-secondary' : 'border-transparent' }}"
                    :class="scrolled 
           ? '{{ request()->is('/') ? 'text-primary' : 'text-gray-800 hover:text-primary' }}'
           : '{{ request()->is('/') ? 'text-secondary' : 'text-white hover:text-secondary' }}'">
                    Beranda
                </a>

                <a href="/staff"
                    class="font-medium border-b-2 hover:border-secondary transition-all duration-500 transform hover:-translate-y-1 {{ request()->is('staff*') ? 'border-secondary' : 'border-transparent' }}"
                    :class="scrolled 
           ? '{{ request()->is('staff*') ? 'text-primary' : 'text-gray-800 hover:text-primary' }}'
           : '{{ request()->is('staff*') ? 'text-secondary' : 'text-white hover:text-secondary' }}'">
                    Staff
                </a>

                <a href="/artikel"
                    class="font-medium border-b-2 hover:border-secondary transition-all duration-500 transform hover:-translate-y-1 {{ request()->is('artikel*') ? 'border-secondary' : 'border-transparent' }}"
                    :class="scrolled 
           ? '{{ request()->is('artikel*') ? 'text-primary' : 'text-gray-800 hover:text-primary' }}'
           : '{{ request()->is('artikel*') ? 'text-secondary' : 'text-white hover:text-secondary' }}'">
                    Artikel
                </a>

                <a href="/layanan"
                    class="font-medium border-b-2 hover:border-secondary transition-all duration-500 transform hover:-translate-y-1 {{ request()->is('layanan*') ? 'border-secondary' : 'border-transparent' }}"
                    :class="scrolled 
           ? '{{ request()->is('layanan*') ? 'text-primary' : 'text-gray-800 hover:text-primary' }}'
           : '{{ request()->is('layanan*') ? 'text-secondary' : 'text-white hover:text-secondary' }}'">
                    Layanan
                </a>

                <a href="/fasilitas"
                    class="font-medium border-b-2 hover:border-secondary transition-all duration-500 transform hover:-translate-y-1 {{ request()->is('fasilitas*') ? 'border-secondary' : 'border-transparent' }}"
                    :class="scrolled 
           ? '{{ request()->is('fasilitas*') ? 'text-primary' : 'text-gray-800 hover:text-primary' }}'
           : '{{ request()->is('fasilitas*') ? 'text-secondary' : 'text-white hover:text-secondary' }}'">
                    Fasilitas
                </a>
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
                <a href="/staff" class="text-primary font-bold">Staff</a>
                <a href="/artikel" class="text-gray-800 hover:text-primary transition-colors duration-300 font-medium">Artikel</a>
                <a href="/layanan" class="text-gray-800 hover:text-primary transition-colors duration-300 font-medium">Layanan</a>
                <a href="/fasilitas" class="text-gray-800 hover:text-primary transition-colors duration-300 font-medium">Fasilitas</a>
            </div>
        </div>
    </div>
</nav>