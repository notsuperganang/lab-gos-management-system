@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('header-title', 'Manajemen Pengguna')
@section('header-subtitle', 'Kelola pengguna sistem dan peran mereka')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('breadcrumbs')
    <li class="flex items-center">
        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"/>
        </svg>
        <span class="ml-4 text-sm font-medium text-gray-500">Manajemen Pengguna</span>
    </li>
@endsection

@section('page-actions')
    <button onclick="window.userManagementController?.openCreateModal()"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Tambah Pengguna Baru
    </button>
@endsection

@section('content')
<div x-data="userManagement()" x-init="init(); window.userManagementController = $data" class="space-y-6">

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

    <!-- Create User Modal -->
    <div x-show="showCreateModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="closeModals()"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="submitForm()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Tambah Pengguna Baru</h3>
                        </div>

                        <!-- Name Field -->
                        <div class="mb-4">
                            <label for="create-name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input x-model="form.name"
                                   id="create-name"
                                   type="text"
                                   required
                                   :class="formErrors.name ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                   class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 transition-all duration-200"
                                   placeholder="Masukkan nama lengkap">
                            <p x-show="formErrors.name" x-text="formErrors.name?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Email Field -->
                        <div class="mb-4">
                            <label for="create-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input x-model="form.email"
                                   id="create-email"
                                   type="email"
                                   required
                                   :class="formErrors.email ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                   class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 transition-all duration-200"
                                   placeholder="Masukkan alamat email">
                            <p x-show="formErrors.email" x-text="formErrors.email?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Role Field -->
                        <div class="mb-4">
                            <label for="create-role" class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                            <select x-model="form.role"
                                    id="create-role"
                                    required
                                    :class="formErrors.role ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                    class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 transition-all duration-200">
                                <option value="admin">Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                            <p x-show="formErrors.role" x-text="formErrors.role?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Password Field -->
                        <div class="mb-4">
                            <label for="create-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input x-model="form.password"
                                   id="create-password"
                                   type="password"
                                   required
                                   :class="formErrors.password ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                   class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 transition-all duration-200"
                                   placeholder="Minimal 8 karakter">
                            <p x-show="formErrors.password" x-text="formErrors.password?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Password Confirmation Field -->
                        <div class="mb-4">
                            <label for="create-password-confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                            <input x-model="form.password_confirmation"
                                   id="create-password-confirmation"
                                   type="password"
                                   required
                                   :class="formErrors.password_confirmation ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                   class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 transition-all duration-200"
                                   placeholder="Ulangi password">
                            <p x-show="formErrors.password_confirmation" x-text="formErrors.password_confirmation?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                :disabled="isLoading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isLoading">Simpan</span>
                            <span x-show="isLoading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                        <button type="button"
                                @click="closeModals()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div x-show="showEditModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="closeModals()"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="submitForm()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Edit Pengguna</h3>
                        </div>

                        <!-- Name Field -->
                        <div class="mb-4">
                            <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input x-model="form.name"
                                   id="edit-name"
                                   type="text"
                                   required
                                   :class="formErrors.name ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                   class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 transition-all duration-200"
                                   placeholder="Masukkan nama lengkap">
                            <p x-show="formErrors.name" x-text="formErrors.name?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Email Field -->
                        <div class="mb-4">
                            <label for="edit-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input x-model="form.email"
                                   id="edit-email"
                                   type="email"
                                   required
                                   :class="formErrors.email ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                   class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 transition-all duration-200"
                                   placeholder="Masukkan alamat email">
                            <p x-show="formErrors.email" x-text="formErrors.email?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Role Field -->
                        <div class="mb-4">
                            <label for="edit-role" class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                            <select x-model="form.role"
                                    id="edit-role"
                                    required
                                    :class="formErrors.role ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                    class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 transition-all duration-200">
                                <option value="admin">Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                            <p x-show="formErrors.role" x-text="formErrors.role?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Password Field (Optional for edit) -->
                        <div class="mb-4">
                            <label for="edit-password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru (Opsional)</label>
                            <input x-model="form.password"
                                   id="edit-password"
                                   type="password"
                                   :class="formErrors.password ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                   class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 transition-all duration-200"
                                   placeholder="Kosongkan jika tidak ingin mengubah password">
                            <p x-show="formErrors.password" x-text="formErrors.password?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Password Confirmation Field (Only if password is provided) -->
                        <div class="mb-4" x-show="form.password">
                            <label for="edit-password-confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input x-model="form.password_confirmation"
                                   id="edit-password-confirmation"
                                   type="password"
                                   :class="formErrors.password_confirmation ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'"
                                   class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 transition-all duration-200"
                                   placeholder="Ulangi password baru">
                            <p x-show="formErrors.password_confirmation" x-text="formErrors.password_confirmation?.[0]" class="text-red-500 text-xs mt-1"></p>
                        </div>

                        <!-- Status Field -->
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input x-model="form.is_active"
                                       type="checkbox"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Aktif</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                :disabled="isLoading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isLoading">Perbarui</span>
                            <span x-show="isLoading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memperbarui...
                            </span>
                        </button>
                        <button type="button"
                                @click="closeModals()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
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
            // Modal state
            showCreateModal: false,
            showEditModal: false,
            isLoading: false,
            currentEditUser: null,
            form: {
                name: '',
                email: '',
                role: 'admin',
                password: '',
                password_confirmation: '',
                is_active: true
            },
            formErrors: {},

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

            // Modal state
            showCreateModal: false,
            showEditModal: false,
            isLoading: false,
            currentEditUser: null,
            form: {
                name: '',
                email: '',
                role: 'admin',
                password: '',
                password_confirmation: '',
                is_active: true
            },
            formErrors: {},

            openCreateModal() {
                this.resetForm();
                this.showCreateModal = true;
            },

            editUser(user) {
                this.currentEditUser = user;
                this.form = {
                    name: user.name,
                    email: user.email,
                    role: user.role,
                    password: '',
                    password_confirmation: '',
                    is_active: user.is_active
                };
                this.formErrors = {};
                this.showEditModal = true;
            },

            closeModals() {
                this.showCreateModal = false;
                this.showEditModal = false;
                this.currentEditUser = null;
                this.resetForm();
            },

            resetForm() {
                this.form = {
                    name: '',
                    email: '',
                    role: 'admin',
                    password: '',
                    password_confirmation: '',
                    is_active: true
                };
                this.formErrors = {};
            },

            async submitForm() {
                this.isLoading = true;
                this.formErrors = {};

                try {
                    const token = localStorage.getItem('admin_token');
                    if (!token) {
                        throw new Error('Token tidak ditemukan. Silakan login ulang.');
                    }

                    const isEdit = this.showEditModal;
                    const url = isEdit
                        ? `/api/superadmin/users/${this.currentEditUser.id}`
                        : '/api/superadmin/users';
                    const method = isEdit ? 'PUT' : 'POST';

                    // Prepare form data
                    const formData = { ...this.form };
                    if (isEdit && !formData.password) {
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
                        const message = isEdit ? 'Pengguna berhasil diperbarui' : 'Pengguna berhasil ditambahkan';
                        if (this.$root && typeof this.$root.showNotification === 'function') {
                            this.$root.showNotification(message, 'success');
                        } else {
                            this.showNotification(message, 'success');
                        }
                        this.closeModals();
                        await this.loadUsers();
                    } else {
                        if (response.status === 422 && data.errors) {
                            this.formErrors = data.errors;
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    }
                } catch (error) {
                    console.error('Submit form error:', error);
                    this.handleError(error);
                } finally {
                    this.isLoading = false;
                }
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
