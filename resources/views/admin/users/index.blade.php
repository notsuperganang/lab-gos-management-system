@extends('admin.layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')
@section('header-title', 'User Management')
@section('header-subtitle', 'Manage admin users and their permissions')

@section('content')
<div class="space-y-6" x-data="userManagement()">
    
    <!-- Users List Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">System Users</h3>
                <p class="text-sm text-gray-600 mt-1">Manage admin and super admin accounts</p>
            </div>
            <button @click="showCreateModal = true"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add User
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
                                    <button @click="editUser(user)"
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                        Edit
                                    </button>
                                    <button @click="toggleUserStatus(user)"
                                            :class="user.is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'"
                                            class="transition-colors duration-200">
                                        <span x-text="user.is_active ? 'Deactivate' : 'Activate'"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
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
        showCreateModal: false,

        init() {
            this.loadUsers();
        },

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
                this.showNotification('Failed to load users', 'error');
            } finally {
                this.loading = false;
            }
        },

        async toggleUserStatus(user) {
            const action = user.is_active ? 'deactivate' : 'activate';
            if (!confirm(`Are you sure you want to ${action} ${user.name}?`)) {
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
                    this.showNotification(`User ${action}d successfully`, 'success');
                } else {
                    throw new Error(data.message || `Failed to ${action} user`);
                }
            } catch (error) {
                console.error(`Error ${action}ing user:`, error);
                this.showNotification(error.message || `Failed to ${action} user`, 'error');
            }
        },

        editUser(user) {
            // For now, just show alert - this would open an edit modal
            alert(`Edit functionality for ${user.name} would be implemented here`);
        },

        formatDate(dateString) {
            if (!dateString) return 'Never';
            
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        },

        showNotification(message, type = 'info') {
            // Simple notification system
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