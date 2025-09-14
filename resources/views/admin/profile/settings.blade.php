@extends('admin.layouts.app')

@section('title', 'Pengaturan Profil')
@section('page-title', 'Pengaturan Profil')
@section('header-title', 'Pengaturan Profil')
@section('header-subtitle', 'Kelola informasi pribadi dan pengaturan akun Anda')

@section('content')
<div class="space-y-6" x-data="profileSettings()">
    
    <!-- Profile Information Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Informasi Profil</h3>
            <p class="text-sm text-gray-600 mt-1">Perbarui informasi pribadi dan foto profil Anda.</p>
        </div>
        
        <form @submit.prevent="updateProfile" class="p-6 space-y-6">
            <!-- Avatar Section -->
            <div class="flex items-start space-x-6">
                <div class="flex-shrink-0">
                    <div class="relative">
                        <!-- Current Avatar -->
                        <template x-if="user && user.avatar_path">
                            <img :src="`{{ asset('storage/') }}/${user.avatar_path}`" 
                                 :alt="user.name"
                                 class="h-20 w-20 rounded-full object-cover border-4 border-white shadow-lg">
                        </template>
                        
                        <!-- Default Avatar -->
                        <template x-if="!user || !user.avatar_path">
                            <div class="h-20 w-20 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                                <span class="text-white text-2xl font-bold" x-text="user ? user.name.charAt(0).toUpperCase() : 'A'"></span>
                            </div>
                        </template>
                        
                        <!-- Upload Button Overlay -->
                        <button type="button" @click="$refs.avatarInput.click()"
                                class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center text-white opacity-0 hover:opacity-100 transition-opacity duration-200 cursor-pointer">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Hidden File Input -->
                    <input type="file" x-ref="avatarInput" @change="handleAvatarChange" 
                           accept="image/*" class="hidden">
                </div>
                
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Foto Profil</h4>
                    <p class="text-sm text-gray-600 mb-3">Unggah foto profil baru. JPG, PNG atau GIF (maksimal 2MB).</p>
                    
                    <div class="flex space-x-3">
                        <button type="button" @click="$refs.avatarInput.click()"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Ganti Foto
                        </button>
                        
                        <button type="button" x-show="user && user.avatar_path" @click="removeAvatar"
                                class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" id="name" x-model="form.name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-gray-400">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                    <input type="email" id="email" x-model="form.email"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-gray-400">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="tel" id="phone" x-model="form.phone"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-gray-400">
                </div>
                
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                    <input type="text" id="position" x-model="form.position"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-gray-400">
                </div>
                
                <!-- Role Display -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Peran</label>
                    <div class="inline-flex px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm font-medium"
                         :class="user?.role === 'super_admin' ? 'text-purple-800 bg-purple-100 border-purple-200' : 'text-blue-800 bg-blue-100 border-blue-200'">
                        <span x-text="user ? (user.role === 'super_admin' ? 'Super Administrator' : 'Administrator') : 'Administrator'"></span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Peran akun Anda tidak dapat diubah melalui halaman ini.</p>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" :disabled="updating"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                    <svg x-show="updating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="updating ? 'Memperbarui...' : 'Perbarui Profil'"></span>
                </button>
            </div>
        </form>
    </div>
    
    <!-- Password Change Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Ubah Kata Sandi</h3>
            <p class="text-sm text-gray-600 mt-1">Perbarui kata sandi Anda untuk menjaga keamanan akun.</p>
        </div>
        
        <form @submit.prevent="updatePassword" class="p-6 space-y-6">
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Saat Ini</label>
                <input type="password" id="current_password" x-model="passwordForm.current_password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-gray-400">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Baru</label>
                    <input type="password" id="new_password" x-model="passwordForm.new_password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-gray-400">
                    <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" id="confirm_password" x-model="passwordForm.confirm_password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-gray-400">
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" :disabled="updatingPassword"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                    <svg x-show="updatingPassword" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="updatingPassword ? 'Memperbarui...' : 'Ubah Kata Sandi'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('profileSettings', () => ({
        user: null,
        updating: false,
        updatingPassword: false,
        form: {
            name: '',
            email: '',
            phone: '',
            position: '',
            avatar: null
        },
        passwordForm: {
            current_password: '',
            new_password: '',
            confirm_password: ''
        },

        init() {
            this.loadUserData();
        },

        async loadUserData() {
            // Get user data from parent adminApp component if available
            const adminApp = this.$root;
            if (adminApp.user) {
                this.user = adminApp.user;
                // Populate form with current user data
                this.form.name = this.user.name || '';
                this.form.email = this.user.email || '';
                this.form.phone = this.user.phone || '';
                this.form.position = this.user.position || '';
                return;
            }

            // Fallback: Load user data from API if not available in parent
            try {
                const token = localStorage.getItem('admin_token');
                const response = await fetch('/api/user', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    this.user = await response.json();
                    // Update parent component's user data
                    if (adminApp) {
                        adminApp.user = this.user;
                    }
                    // Populate form with current user data
                    this.form.name = this.user.name || '';
                    this.form.email = this.user.email || '';
                    this.form.phone = this.user.phone || '';
                    this.form.position = this.user.position || '';
                } else {
                    throw new Error('Gagal memuat data pengguna');
                }
            } catch (error) {
                console.error('Error loading user data:', error);
                this.showNotification('Gagal memuat data pengguna', 'error');
            }
        },

        async updateProfile() {
            this.updating = true;
            try {
                const token = localStorage.getItem('admin_token');
                const formData = new FormData();
                
                formData.append('name', this.form.name);
                formData.append('email', this.form.email);
                formData.append('phone', this.form.phone || '');
                formData.append('position', this.form.position || '');
                
                if (this.form.avatar) {
                    formData.append('avatar', this.form.avatar);
                }

                const response = await fetch('/api/admin/profile', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    this.user = data.user;
                    this.form.avatar = null; // Reset avatar file input
                    this.showNotification('Profil berhasil diperbarui!', 'success');
                } else {
                    throw new Error(data.message || 'Gagal memperbarui profil');
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                this.showNotification(error.message || 'Gagal memperbarui profil', 'error');
            } finally {
                this.updating = false;
            }
        },

        async updatePassword() {
            if (this.passwordForm.new_password !== this.passwordForm.confirm_password) {
                this.showNotification('Kata sandi baru tidak cocok', 'error');
                return;
            }

            if (this.passwordForm.new_password.length < 8) {
                this.showNotification('Kata sandi baru harus minimal 8 karakter', 'error');
                return;
            }

            this.updatingPassword = true;
            try {
                const token = localStorage.getItem('admin_token');
                const response = await fetch('/api/admin/profile/password', {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        current_password: this.passwordForm.current_password,
                        new_password: this.passwordForm.new_password,
                        new_password_confirmation: this.passwordForm.confirm_password
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // Clear password form
                    this.passwordForm = {
                        current_password: '',
                        new_password: '',
                        confirm_password: ''
                    };
                    this.showNotification('Kata sandi berhasil diperbarui!', 'success');
                } else {
                    throw new Error(data.message || 'Gagal memperbarui kata sandi');
                }
            } catch (error) {
                console.error('Error updating password:', error);
                this.showNotification(error.message || 'Gagal memperbarui kata sandi', 'error');
            } finally {
                this.updatingPassword = false;
            }
        },

        handleAvatarChange(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    this.showNotification('Ukuran file harus kurang dari 2MB', 'error');
                    return;
                }

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    this.showNotification('Silakan pilih file gambar', 'error');
                    return;
                }

                this.form.avatar = file;
                this.showNotification('Avatar dipilih. Klik "Perbarui Profil" untuk menyimpan perubahan.', 'info');
            }
        },

        async removeAvatar() {
            if (!confirm('Apakah Anda yakin ingin menghapus foto profil Anda?')) {
                return;
            }

            try {
                const token = localStorage.getItem('admin_token');
                const response = await fetch('/api/admin/profile/avatar', {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.user.avatar_path = null;
                    this.showNotification('Foto profil berhasil dihapus!', 'success');
                } else {
                    throw new Error(data.message || 'Gagal menghapus foto profil');
                }
            } catch (error) {
                console.error('Error removing avatar:', error);
                this.showNotification(error.message || 'Gagal menghapus foto profil', 'error');
            }
        },

        showNotification(message, type = 'info') {
            // Simple notification system - you can enhance this with a proper toast library
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    }));
});
</script>
@endpush