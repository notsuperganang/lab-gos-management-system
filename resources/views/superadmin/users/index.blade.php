@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('header-title', 'Manajemen Pengguna')
@section('header-subtitle', 'Kelola pengguna sistem dan peran mereka')

@section('breadcrumbs')
    <li class="flex items-center">
        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"/>
        </svg>
        <span class="ml-4 text-sm font-medium text-gray-500">Manajemen Pengguna</span>
    </li>
@endsection

@section('page-actions')
    <button @click="showCreateUserModal = true"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Tambah Pengguna Baru
    </button>
@endsection

@section('content')
<div x-data="userManagement()" x-init="init()" class="space-y-6">

    <!-- Loading Overlay -->
    <div x-show="loading" class="admin-loading-overlay">
        <div class="admin-loading-spinner"></div>
    </div>

    <!-- Users Table -->
    <div class="admin-card">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Pengguna Sistem</h2>
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text"
                               x-model="searchQuery"
                               @input="debouncedSearch()"
                               placeholder="Cari pengguna..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Role Filter -->
                    <select x-model="selectedRole" @change="loadUsers()" class="admin-form-select w-auto">
                        <option value="">Semua Peran</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="user in users" :key="user.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <!-- Show avatar if available, otherwise show initials -->
                                        <template x-if="user.avatar_url">
                                            <img class="h-10 w-10 rounded-full object-cover"
                                                 :src="user.avatar_url"
                                                 :alt="user.name"
                                                 x-on:error="$event.target.style.display = 'none'; $event.target.nextElementSibling.style.display = 'flex'">
                                        </template>
                                        <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center"
                                             :class="user.avatar_url ? 'hidden' : 'flex'">
                                            <span class="text-sm font-medium text-white" x-text="user.name.charAt(0).toUpperCase()"></span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="user.name"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="user.email"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="{
                                          'bg-red-100 text-red-800': user.role === 'super_admin',
                                          'bg-blue-100 text-blue-800': user.role === 'admin',
                                          'bg-green-100 text-green-800': user.role === 'staff',
                                          'bg-gray-100 text-gray-800': user.role === 'user' || !user.role
                                      }"
                                      x-text="user.role || 'user'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="{
                                          'bg-green-100 text-green-800': user.is_active,
                                          'bg-red-100 text-red-800': !user.is_active
                                      }"
                                      x-text="user.is_active ? 'Aktif' : 'Tidak Aktif'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(user.created_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                    <button type="button" @click.stop="console.log('Edit button clicked'); editUser(user)"
                                            class="text-blue-600 hover:text-blue-900">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <button type="button" role="button" @click.prevent.stop.capture="toggleUserStatus(user)"
                                            :class="currentUser && user.id === currentUser.id ? 'text-gray-400 cursor-not-allowed' : (user.is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900')"
                                            class="inline-flex items-center pointer-events-auto"
                                            :disabled="currentUser && user.id === currentUser.id"
                                            :title="currentUser && user.id === currentUser.id ? 'Anda tidak dapat mengubah status akun sendiri' : (user.is_active ? 'Nonaktifkan pengguna' : 'Aktifkan pengguna')">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path x-show="user.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"/>
                                            <path x-show="!user.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>

                                    <button type="button" role="button" @click.prevent.stop.capture="deleteUser(user)"
                                            :class="currentUser && user.id === currentUser.id ? 'text-gray-400 cursor-not-allowed' : 'text-red-600 hover:text-red-900'"
                                            class="inline-flex items-center pointer-events-auto"
                                            :disabled="currentUser && user.id === currentUser.id"
                                            :title="currentUser && user.id === currentUser.id ? 'Anda tidak dapat menghapus akun sendiri' : 'Hapus pengguna'">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Empty State -->
            <div x-show="users.length === 0 && !loading" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pengguna ditemukan</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new user.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function registerUserManagement() {
    const factory = () => {
        return {
            users: [],
            loading: false,
            searchQuery: '',
            selectedRole: '',
            searchTimeout: null,
            currentUser: null, // Track current user independently

            async init() {
                // Load current user and users
                await this.loadCurrentUser();
                await this.loadUsers();
            },

            async loadCurrentUser() {
                try {
                    const token = localStorage.getItem('admin_token');
                    const response = await fetch('/api/user', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        this.currentUser = await response.json();
                    }
                } catch (error) {
                    console.error('Failed to load current user:', error);
                }
            },

            // Fallback error handler if root component is not available
            handleError(error) {
                console.error('API Error:', error);

                // Try to use root handler if available
                if (this.$root && typeof this.$root.handleApiError === 'function') {
                    this.$root.handleApiError(error);
                    return;
                }

                // Fallback notification
                const message = error.message || error.data?.message || 'Terjadi kesalahan. Silakan coba lagi.';
                this.showNotification(message, 'error');
            },

            // Fallback notification system
            showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
                    type === 'success' ? 'bg-green-500 text-white' :
                    type === 'error' ? 'bg-red-500 text-white' :
                    type === 'warning' ? 'bg-yellow-500 text-white' :
                    'bg-blue-500 text-white'
                }`;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 5000);
            },

            async loadUsers() {
                this.loading = true;
                try {
                    const params = new URLSearchParams();
                    if (this.searchQuery) params.append('search', this.searchQuery);
                    if (this.selectedRole) params.append('role', this.selectedRole);

                    // Use direct fetch to superadmin API
                    const token = localStorage.getItem('admin_token');

                    const response = await fetch(`/api/superadmin/users?${params.toString()}`, {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.users = data.data || data || [];
                    } else {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                } catch (error) {
                    console.error('loadUsers error:', error);
                    this.handleError(error);
                } finally {
                    this.loading = false;
                }
            },

            debouncedSearch() {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.loadUsers();
                }, 300);
            },

            async toggleUserStatus(user) {
                // Prevent users from changing their own status
                if (this.currentUser && user.id === this.currentUser.id) {
                    this.handleError(new Error('Anda tidak dapat mengubah status akun sendiri'));
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
                        body: JSON.stringify({ is_active: !user.is_active })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        user.is_active = !user.is_active;
                        // Try to use root notification if available, otherwise use local
                        if (this.$root && typeof this.$root.showNotification === 'function') {
                            this.$root.showNotification(`Pengguna berhasil ${user.is_active ? 'diaktifkan' : 'dinonaktifkan'}`, 'success');
                        } else {
                            this.showNotification(`Pengguna berhasil ${user.is_active ? 'diaktifkan' : 'dinonaktifkan'}`, 'success');
                        }
                    } else {
                        const errorData = await response.text();
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                } catch (error) {
                    console.error('toggleUserStatus error:', error);
                    this.handleError(error);
                }
            },

            editUser(user) {
                // Navigate to edit page or open modal
                window.location.href = `/superadmin/users/${user.id}/edit`;
            },

            async deleteUser(user) {
                // Check if user is trying to delete themselves
                if (this.currentUser && user.id === this.currentUser.id) {
                    this.handleError(new Error('Anda tidak dapat menghapus akun sendiri'));
                    return;
                }

                if (!confirm(`Apakah Anda yakin ingin menghapus pengguna "${user.name}"?`)) {
                    return;
                }

                try {
                    const token = localStorage.getItem('admin_token');

                    const response = await fetch(`/api/superadmin/users/${user.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        this.users = this.users.filter(u => u.id !== user.id);
                        // Try to use root notification if available, otherwise use local
                        if (this.$root && typeof this.$root.showNotification === 'function') {
                            this.$root.showNotification('Pengguna berhasil dihapus', 'success');
                        } else {
                            this.showNotification('Pengguna berhasil dihapus', 'success');
                        }
                    } else {
                        const errorData = await response.text();
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                } catch (error) {
                    console.error('deleteUser error:', error);
                    this.handleError(error);
                }
            },

            formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
        };
    };

    const registerAll = () => {
        // Register under both names for compatibility (admin vs superadmin templates)
        Alpine.data('userManagementData', factory);
        Alpine.data('userManagement', factory);
    };

    if (window.Alpine) {
        registerAll();
    } else {
        document.addEventListener('alpine:init', registerAll);
    }
})();
</script>
@endpush
