<!-- resources/views/public/staff.blade.php -->

<x-public.layouts.main>
    {{-- Mendefinisikan judul halaman untuk layout --}}
    <x-slot:title>
        Staff Laboratorium - Lab GOS USK
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
                        Staff 
                        <span class="text-secondary">Laboratorium</span>
                        <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-secondary to-yellow-400 rounded-full animate-pulse"></div>
                    </span>
                </h1>
                <p class="text-xl text-gray-200 mb-6">
                    Tim profesional yang berdedikasi untuk kemajuan ilmu pengetahuan
                </p>
                <div class="bg-primary bg-opacity-20 backdrop-blur-sm rounded-full px-6 py-2 inline-block">
                    <p class="text-white flex items-center justify-center">
                        <i class="fas fa-users mr-2 text-secondary"></i>
                        Laboratorium Gelombang, Optik & Spektroskopi
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Staff Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if($staffMembers->count() > 0 || $currentType)
            <!-- Filter Section -->
            <div>
                <!-- Filter Buttons -->
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="flex justify-center mb-12 transition-all duration-1000 ease-out">
                    <div class="bg-white rounded-2xl p-2 shadow-lg inline-flex flex-wrap gap-2">
                        <a href="{{ route('staff') }}" 
                           class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 {{ !$currentType ? 'bg-primary text-white shadow-lg scale-105' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-users mr-2"></i>
                            Semua
                        </a>
                        
                        @foreach($staffTypes as $typeValue => $typeLabel)
                        @php
                            $activeClass = match($typeValue) {
                                'dosen' => 'bg-secondary text-gray-800 shadow-lg scale-105',
                                'laboran' => 'bg-green-500 text-white shadow-lg scale-105',
                                'teknisi' => 'bg-purple-500 text-white shadow-lg scale-105',
                                'kepala_laboratorium' => 'bg-red-500 text-white shadow-lg scale-105',
                                default => 'text-gray-600 hover:bg-gray-100'
                            };
                            
                            $icon = match($typeValue) {
                                'dosen' => 'fa-graduation-cap',
                                'laboran' => 'fa-flask',
                                'teknisi' => 'fa-tools',
                                'kepala_laboratorium' => 'fa-crown',
                                default => 'fa-user'
                            };
                        @endphp
                        
                        <a href="{{ route('staff', ['type' => $typeValue]) }}" 
                           class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 {{ $currentType === $typeValue ? $activeClass : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas {{ $icon }} mr-2"></i>
                            {{ $typeLabel }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Staff Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($staffMembers as $index => $staff)
                    <div x-data="{ animated: false }" 
                         x-scroll-animate="animated = true"
                         :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                         class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 transform hover:-translate-y-4 hover:rotate-1 ease-out"
                         style="transition-delay: {{ $index * 0.1 }}s;">
                        
                        <!-- Staff Photo -->
                        <div class="relative h-64 overflow-hidden">
                            @if($staff->photo_url)
                                <img src="{{ $staff->photo_url }}" 
                                     alt="{{ $staff->name }}"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            @endif
                            
                            <!-- Fallback Gradient Background -->
                            <div class="absolute inset-0 bg-gradient-to-br from-primary to-blue-600" 
                                 style="{{ $staff->photo_url ? 'display: none' : 'display: block' }}"></div>
                            <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-500"></div>
                            
                            <!-- Avatar Icon (shown when no photo) -->
                            <div class="absolute inset-0 flex items-center justify-center" 
                                 style="{{ $staff->photo_url ? 'display: none' : 'display: flex' }}">
                                <div class="w-24 h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-user text-white text-3xl"></i>
                                </div>
                            </div>
                            
                            <!-- Staff Type Badge -->
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-secondary text-gray-800">
                                    {{ $staff->staff_type_label }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Staff Info -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-primary transition-colors duration-300">{{ $staff->name }}</h3>
                            <p class="text-gray-600 font-semibold mb-2">{{ $staff->position }}</p>
                            @if($staff->specialization)
                            <p class="text-sm text-gray-500 mb-4">{{ $staff->specialization }}</p>
                            @endif
                            
                            <!-- Contact Info -->
                            <div class="space-y-2">
                                @if($staff->email)
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-envelope mr-2 text-primary"></i>
                                    <span>{{ $staff->email }}</span>
                                </div>
                                @endif
                                
                                @if($staff->phone)
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-phone mr-2 text-primary"></i>
                                    <span>{{ $staff->phone }}</span>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Social Links -->
                            <div class="flex space-x-3 mt-4 pt-4 border-t border-gray-100">
                                @if($staff->email)
                                <a href="mailto:{{ $staff->email }}" 
                                   class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors duration-300 transform hover:scale-110">
                                    <i class="fas fa-envelope text-sm"></i>
                                </a>
                                @endif
                                
                                @if($staff->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $staff->phone) }}" 
                                   target="_blank"
                                   class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 hover:bg-green-200 transition-colors duration-300 transform hover:scale-110">
                                    <i class="fab fa-whatsapp text-sm"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <!-- Empty State -->
                    <div class="col-span-full text-center py-16">
                        <div class="text-6xl text-gray-300 mb-4">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada staff ditemukan</h3>
                        <p class="text-gray-500">Coba pilih kategori lain atau reset filter</p>
                        @if($currentType)
                        <div class="mt-4">
                            <a href="{{ route('staff') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors duration-300">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Lihat Semua Staff
                            </a>
                        </div>
                        @endif
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($staffMembers->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $staffMembers->links() }}
                </div>
                @endif

                <!-- Stats Section -->
                @if(!$currentType)
                <div x-data="{ animated: false }" 
                     x-scroll-animate="animated = true"
                     :class="animated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="mt-16 bg-white rounded-2xl p-8 shadow-lg transition-all duration-1000 ease-out">
                    <h3 class="text-2xl font-bold text-center text-gray-800 mb-8">
                        <span class="relative inline-block">
                            📊 Statistik Tim
                            <div class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                        </span>
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-6 text-center">
                        @php
                            $totalStaff = \App\Models\StaffMember::active()->count();
                            $dosenCount = \App\Models\StaffMember::active()->type(\App\Enums\StaffType::DOSEN)->count();
                            $laboranCount = \App\Models\StaffMember::active()->type(\App\Enums\StaffType::LABORAN)->count();
                            $teknisiCount = \App\Models\StaffMember::active()->type(\App\Enums\StaffType::TEKNISI)->count();
                            $kepalaCount = \App\Models\StaffMember::active()->type(\App\Enums\StaffType::KEPALA_LABORATORIUM)->count();
                        @endphp
                        
                        <div class="p-4">
                            <div class="text-3xl font-bold text-primary mb-2">{{ $totalStaff }}</div>
                            <div class="text-gray-600">Total Staff</div>
                        </div>
                        
                        @if($kepalaCount > 0)
                        <div class="p-4">
                            <div class="text-3xl font-bold text-red-500 mb-2">{{ $kepalaCount }}</div>
                            <div class="text-gray-600">Kepala Lab</div>
                        </div>
                        @endif
                        
                        @if($dosenCount > 0)
                        <div class="p-4">
                            <div class="text-3xl font-bold text-secondary mb-2">{{ $dosenCount }}</div>
                            <div class="text-gray-600">Dosen</div>
                        </div>
                        @endif
                        
                        @if($laboranCount > 0)
                        <div class="p-4">
                            <div class="text-3xl font-bold text-green-500 mb-2">{{ $laboranCount }}</div>
                            <div class="text-gray-600">Laboran</div>
                        </div>
                        @endif
                        
                        @if($teknisiCount > 0)
                        <div class="p-4">
                            <div class="text-3xl font-bold text-purple-500 mb-2">{{ $teknisiCount }}</div>
                            <div class="text-gray-600">Teknisi</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-20">
                <div class="text-6xl text-gray-300 mb-6">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-600 mb-4">Belum Ada Data Staff</h3>
                <p class="text-gray-500">Informasi staff laboratorium akan segera ditambahkan.</p>
            </div>
            @endif
        </div>
    </section>
    
</x-public.layouts.main>