@extends('admin.layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')
@section('header-title', 'User Management')
@section('header-subtitle', 'Manage system users and their roles')

@section('breadcrumbs')
    <li class="flex items-center">
        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"/>
        </svg>
        <span class="ml-4 text-sm font-medium text-gray-500">User Management</span>
    </li>
@endsection

@section('page-actions')
    <button @click="showCreateUserModal = true" 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Add New User
    </button>
@endsection

@section('content')
<div x-data="userManagementData()" x-init="init()" class="space-y-6">
    
    <!-- Loading Overlay -->
    <div x-show="loading" class="admin-loading-overlay">
        <div class="admin-loading-spinner"></div>
    </div>

    <!-- Users Table -->
    <div class="admin-card">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">System Users</h2>
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" 
                               x-model="searchQuery"
                               @input="debouncedSearch()"
                               placeholder="Search users..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Role Filter -->
                    <select x-model="selectedRole" @change="loadUsers()" class="admin-form-select w-auto">
                        <option value="">All Roles</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                        <option value="user">User</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="user in users" :key="user.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center">
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
                                      x-text="user.is_active ? 'Active' : 'Inactive'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(user.created_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <button @click="editUser(user)" 
                                            class="text-blue-600 hover:text-blue-900">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    
                                    <button @click="toggleUserStatus(user)" 
                                            :class="user.is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path x-show="user.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"/>
                                            <path x-show="!user.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                    
                                    <button @click="deleteUser(user)" 
                                            class="text-red-600 hover:text-red-900"
                                            :disabled="user.id === {{ auth()->id() }}">
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
                <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new user.</p>
            </div>
        </div>
    </div>
</div>

<script>
function userManagementData() {
    return {
        users: [],
        loading: false,
        searchQuery: '',
        selectedRole: '',
        searchTimeout: null,
        
        async init() {
            await this.loadUsers();
        },
        
        async loadUsers() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.searchQuery) params.append('search', this.searchQuery);
                if (this.selectedRole) params.append('role', this.selectedRole);
                
                const response = await AdminAPI.request(`/superadmin/users?${params.toString()}`);
                if (response.success) {
                    this.users = response.data.data || response.data || [];
                }
            } catch (error) {
                this.$root.handleApiError(error);
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
            try {
                const response = await AdminAPI.request(`/superadmin/users/${user.id}/status`, {
                    method: 'PUT',
                    body: JSON.stringify({ is_active: !user.is_active })
                });
                
                if (response.success) {
                    user.is_active = !user.is_active;
                    this.$root.showToast(`User ${user.is_active ? 'activated' : 'deactivated'} successfully`, 'success');
                }
            } catch (error) {
                this.$root.handleApiError(error);
            }
        },
        
        editUser(user) {
            // Navigate to edit page or open modal
            window.location.href = `/superadmin/users/${user.id}/edit`;
        },
        
        async deleteUser(user) {
            if (!confirm(`Are you sure you want to delete user "${user.name}"?`)) {
                return;
            }
            
            try {
                const response = await AdminAPI.request(`/superadmin/users/${user.id}`, {
                    method: 'DELETE'
                });
                
                if (response.success) {
                    this.users = this.users.filter(u => u.id !== user.id);
                    this.$root.showToast('User deleted successfully', 'success');
                }
            } catch (error) {
                this.$root.handleApiError(error);
            }
        },
        
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
    }
}
</script>
@endsection