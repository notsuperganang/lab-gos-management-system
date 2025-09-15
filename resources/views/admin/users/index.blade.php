@extends('admin.layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')
@section('header-title', 'User Management')
@section('header-subtitle', 'Manage admin users and their permissions')

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    .modal-backdrop {
        backdrop-filter: blur(4px);
    }

    .hover-scale:hover {
        transform: scale(1.02);
    }

    .notification-enter {
        opacity: 0;
        transform: translateX(100%);
    }

    .notification-enter-active {
        opacity: 1;
        transform: translateX(0);
        transition: all 0.3s ease-out;
    }

    .notification-exit {
        opacity: 1;
        transform: translateX(0);
    }

    .notification-exit-active {
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease-in;
    }

    .form-input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="userManagement()">

    <!-- Users List Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">System Users</h3>
                <p class="text-sm text-gray-600 mt-1">Manage admin and super admin accounts</p>
            </div>
            <button @click="openCreateModal()"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Pengguna Baru
            </button>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="p-8 text-center">
            <div class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading users...
            </div>
        </div>

        <!-- Users Table -->
        <div x-show="!loading" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="user in users" :key="user.id">
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <!-- User Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <template x-if="user.avatar_path">
                                        <img :src="`{{ asset('storage/') }}/${user.avatar_path}`"
                                             :alt="user.name"
                                             class="h-10 w-10 rounded-full object-cover">
                                    </template>
                                    <template x-if="!user.avatar_path">
                                        <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-medium" x-text="user.name.charAt(0).toUpperCase()"></span>
                                        </div>
                                    </template>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="user.name"></div>
                                        <div class="text-sm text-gray-500" x-text="user.email"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="user.role === 'super_admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'"
                                      x-text="user.role === 'super_admin' ? 'Super Admin' : 'Admin'"></span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                      x-text="user.is_active ? 'Active' : 'Inactive'"></span>
                            </td>

                            <!-- Last Login -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span x-text="formatDate(user.last_login_at)"></span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <button @click="viewUserDetails(user)"
                                            type="button"
                                            class="text-blue-600 hover:text-blue-900 transition-all duration-200 hover:scale-105 font-medium">
                                        Detail
                                    </button>
                                    <button @click="toggleUserStatus(user)"
                                            type="button"
                                            :class="user.is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'"
                                            class="transition-all duration-200 hover:scale-105 font-medium">
                                        <span x-text="user.is_active ? 'Nonaktifkan' : 'Aktifkan'"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create/Edit User Modal -->
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"
             @click="closeModal()"></div>

        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="isEditMode ? 'Edit Pengguna' : 'Tambah Pengguna Baru'"></h3>
                    <button @click="closeModal()"
                            type="button"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form @submit.prevent="saveUser()" class="p-6 space-y-4">

                    <!-- Name Field -->
                    <div>
                        <label for="user-name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input x-model="form.name"
                               id="user-name"
                               type="text"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               placeholder="Masukkan nama lengkap">
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="user-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input x-model="form.email"
                               id="user-email"
                               type="email"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               placeholder="user@example.com">
                    </div>

                    <!-- Password Field (only for create) -->
                    <div x-show="!isEditMode">
                        <label for="user-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input x-model="form.password"
                               id="user-password"
                               type="password"
                               :required="!isEditMode"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               placeholder="Minimal 8 karakter">
                    </div>

                    <!-- Password Confirmation (only for create) -->
                    <div x-show="!isEditMode">
                        <label for="user-password-confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                        <input x-model="form.password_confirmation"
                               id="user-password-confirmation"
                               type="password"
                               :required="!isEditMode"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               placeholder="Ulangi password">
                    </div>

                    <!-- Role Field -->
                    <div>
                        <label for="user-role" class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                        <select x-model="form.role"
                                id="user-role"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="">Pilih peran</option>
                            <option value="admin">Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>

                    <!-- Status Field (only for edit) -->
                    <div x-show="isEditMode">
                        <label class="flex items-center">
                            <input x-model="form.is_active"
                                   type="checkbox"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button @click="closeModal()"
                                type="button"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="submitting"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105">
                            <span x-show="!submitting" x-text="isEditMode ? 'Perbarui' : 'Simpan'"></span>
                            <span x-show="submitting" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Details Modal -->
    <div x-show="showDetailsModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"
             @click="closeDetailsModal()"></div>

        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full mx-auto"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Pengguna</h3>
                    <button @click="closeDetailsModal()"
                            type="button"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6" x-show="selectedUser">

                    <!-- User Avatar and Basic Info -->
                    <div class="flex items-center space-x-4 mb-6">
                        <template x-if="selectedUser && selectedUser.avatar_path">
                            <img :src="`{{ asset('storage/') }}/${selectedUser.avatar_path}`"
                                 :alt="selectedUser.name"
                                 class="h-16 w-16 rounded-full object-cover">
                        </template>
                        <template x-if="selectedUser && !selectedUser.avatar_path">
                            <div class="h-16 w-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-xl font-medium" x-text="selectedUser.name.charAt(0).toUpperCase()"></span>
                            </div>
                        </template>
                        <div>
                            <h4 class="text-xl font-semibold text-gray-900" x-text="selectedUser?.name"></h4>
                            <p class="text-sm text-gray-500" x-text="selectedUser?.email"></p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1"
                                  :class="selectedUser?.role === 'super_admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'"
                                  x-text="selectedUser?.role === 'super_admin' ? 'Super Admin' : 'Admin'"></span>
                        </div>
                    </div>

                    <!-- User Details Grid -->
                    <div class="grid grid-cols-1 gap-4">

                        <!-- Status -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="selectedUser?.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                  x-text="selectedUser?.is_active ? 'Aktif' : 'Tidak Aktif'"></span>
                        </div>

                        <!-- Created At -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dibuat</label>
                            <p class="text-sm text-gray-900" x-text="formatDate(selectedUser?.created_at)"></p>
                        </div>

                        <!-- Last Login -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Login Terakhir</label>
                            <p class="text-sm text-gray-900" x-text="formatDate(selectedUser?.last_login_at)"></p>
                        </div>

                        <!-- Email Verified -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Terverifikasi</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="selectedUser?.email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                  x-text="selectedUser?.email_verified_at ? 'Terverifikasi' : 'Belum Terverifikasi'"></span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button @click="editUserFromDetails()"
                                type="button"
                                class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105">
                            Edit Pengguna
                        </button>
                        <button @click="toggleUserStatus(selectedUser)"
                                type="button"
                                :class="selectedUser?.is_active ? 'text-red-600 bg-red-50 border-red-200 hover:bg-red-100 focus:ring-red-500' : 'text-green-600 bg-green-50 border-green-200 hover:bg-green-100 focus:ring-green-500'"
                                class="px-4 py-2 text-sm font-medium border rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                            <span x-text="selectedUser?.is_active ? 'Nonaktifkan' : 'Aktifkan'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('userManagement', () => ({
        users: [],
        loading: true,
        showModal: false,
        showDetailsModal: false,
        isEditMode: false,
        submitting: false,
        selectedUser: null,
        form: {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            role: '',
            is_active: true
        },

        init() {
            this.loadUsers();
        },

        // Load all users
        async loadUsers() {
            this.loading = true;
            try {
                const token = localStorage.getItem('admin_token');
                const response = await fetch('/api/superadmin/users', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.users = data.data || [];
                } else {
                    throw new Error('Failed to load users');
                }
            } catch (error) {
                console.error('Error loading users:', error);
                this.showNotification('Gagal memuat data pengguna', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Open create modal
        openCreateModal() {
            this.isEditMode = false;
            this.resetForm();
            this.showModal = true;
        },

        // Open edit modal with user data
        openEditModal(user) {
            this.isEditMode = true;
            this.selectedUser = user;
            this.form = {
                name: user.name,
                email: user.email,
                password: '',
                password_confirmation: '',
                role: user.role,
                is_active: user.is_active
            };
            this.showModal = true;
        },

        // Close create/edit modal
        closeModal() {
            this.showModal = false;
            this.resetForm();
            this.selectedUser = null;
        },

        // Reset form data
        resetForm() {
            this.form = {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                role: '',
                is_active: true
            };
        },

        // Save user (create or update)
        async saveUser() {
            if (this.submitting) return;

            // Basic validation
            if (!this.form.name || !this.form.email || !this.form.role) {
                this.showNotification('Harap lengkapi semua field yang wajib diisi', 'error');
                return;
            }

            if (!this.isEditMode && (!this.form.password || this.form.password !== this.form.password_confirmation)) {
                this.showNotification('Password dan konfirmasi password harus sama', 'error');
                return;
            }

            this.submitting = true;

            try {
                const token = localStorage.getItem('admin_token');
                const url = this.isEditMode
                    ? `/api/superadmin/users/${this.selectedUser.id}`
                    : '/api/superadmin/users';

                const method = this.isEditMode ? 'PUT' : 'POST';

                // Prepare form data
                const formData = { ...this.form };
                if (this.isEditMode && !formData.password) {
                    delete formData.password;
                    delete formData.password_confirmation;
                }

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    this.showNotification(
                        this.isEditMode ? 'Pengguna berhasil diperbarui' : 'Pengguna berhasil ditambahkan',
                        'success'
                    );
                    this.closeModal();
                    await this.loadUsers(); // Reload users list
                } else {
                    throw new Error(data.message || 'Gagal menyimpan data pengguna');
                }
            } catch (error) {
                console.error('Error saving user:', error);
                this.showNotification(error.message || 'Gagal menyimpan data pengguna', 'error');
            } finally {
                this.submitting = false;
            }
        },

        // View user details
        viewUserDetails(user) {
            this.selectedUser = user;
            this.showDetailsModal = true;
        },

        // Close details modal
        closeDetailsModal() {
            this.showDetailsModal = false;
            this.selectedUser = null;
        },

        // Edit user from details modal
        editUserFromDetails() {
            this.closeDetailsModal();
            this.openEditModal(this.selectedUser);
        },

        // Toggle user status
        async toggleUserStatus(user) {
            const action = user.is_active ? 'nonaktifkan' : 'aktifkan';
            if (!confirm(`Apakah Anda yakin ingin ${action} ${user.name}?`)) {
                return;
            }

            try {
                const token = localStorage.getItem('admin_token');
                const response = await fetch(`/api/superadmin/users/${user.id}/status`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        is_active: !user.is_active
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    user.is_active = !user.is_active;

                    // Update selectedUser if it's the same user
                    if (this.selectedUser && this.selectedUser.id === user.id) {
                        this.selectedUser.is_active = user.is_active;
                    }

                    this.showNotification(`Pengguna berhasil di${action}`, 'success');
                } else {
                    throw new Error(data.message || `Gagal ${action} pengguna`);
                }
            } catch (error) {
                console.error(`Error ${action} user:`, error);
                this.showNotification(error.message || `Gagal ${action} pengguna`, 'error');
            }
        },

        // Format date helper
        formatDate(dateString) {
            if (!dateString) return 'Belum pernah';

            const date = new Date(dateString);
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return date.toLocaleDateString('id-ID', options);
        },

        // Show notification
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 transform transition-all duration-300 ease-in-out ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;

            // Add animation classes
            notification.style.transform = 'translateX(100%)';
            document.body.appendChild(notification);

            // Trigger enter animation
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);

            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, 5000);
        }
    }));
});
</script>
@endpush
