// Equipment Management Alpine.js Component
window.equipmentData = () => ({
    // State
    items: [],
    categories: [],
    summary: null,
    loading: false,
    saving: false,
    deleting: false,
    
    // Pagination
    pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 20,
        total: 0,
        from: 0,
        to: 0,
    },
    
    // Filters
    filters: {
        search: '',
        category_id: '',
        status: '',
        condition_status: '',
        sort_by: 'created_at',
        sort_order: 'desc',
    },
    
    // Modals
    showModal: false,
    showDeleteModal: false,
    
    // Form
    form: {
        id: null,
        name: '',
        category_id: '',
        model: '',
        manufacturer: '',
        total_quantity: 1,
        available_quantity: 1,
        status: 'active',
        condition_status: 'excellent',
        purchase_date: '',
        purchase_price: '',
        location: '',
        notes: '',
        image: null,
        image_url: '',
        remove_image: false,
    },
    
    errors: {},
    imagePreview: null,
    deleteItem: null,

    // Initialization
    async init() {
        this.loading = true;
        try {
            await Promise.all([
                this.fetchItems(),
                this.fetchCategories(),
                this.fetchSummary()
            ]);
        } catch (error) {
            console.error('Failed to initialize:', error);
            this.showError('Gagal memuat data.');
        } finally {
            this.loading = false;
        }
    },

    // Fetch data methods
    async fetchItems() {
        try {
            const params = new URLSearchParams({
                page: this.pagination.current_page,
                per_page: this.pagination.per_page,
                ...this.filters
            });
            
            // Remove empty params
            for (const [key, value] of [...params.entries()]) {
                if (!value) params.delete(key);
            }

            const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
            if (!token) {
                throw new Error('Token tidak ditemukan. Silakan login ulang.');
            }

            const response = await fetch(`/api/admin/equipment?${params.toString()}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            
            if (data.success) {
                this.items = data.data || [];
                if (data.meta?.pagination) {
                    this.pagination = { 
                        ...this.pagination, 
                        ...data.meta.pagination,
                        prev_page_url: data.meta.pagination.prev_page_url,
                        next_page_url: data.meta.pagination.next_page_url
                    };
                }
            } else {
                throw new Error(data.message || 'Gagal memuat data alat.');
            }
        } catch (error) {
            console.error('Error fetching equipment:', error);
            this.showError(error.message || 'Gagal memuat data alat.');
        }
    },

    async fetchCategories() {
        try {
            const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
            if (!token) {
                throw new Error('Token tidak ditemukan. Silakan login ulang.');
            }

            const response = await fetch('/api/admin/equipment/categories', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            
            if (data.success) {
                this.categories = data.data || [];
            } else {
                throw new Error(data.message || 'Gagal memuat kategori.');
            }
        } catch (error) {
            console.error('Error fetching categories:', error);
            this.showError(error.message || 'Gagal memuat kategori.');
        }
    },

    async fetchSummary() {
        try {
            const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
            if (!token) {
                throw new Error('Token tidak ditemukan. Silakan login ulang.');
            }

            const response = await fetch('/api/admin/equipment/summary', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            
            if (data.success) {
                this.summary = data.data || {};
            } else {
                throw new Error(data.message || 'Gagal memuat ringkasan.');
            }
        } catch (error) {
            console.error('Error fetching summary:', error);
            // Don't show error for summary as it's not critical
        }
    },

    // Search and filter methods
    debouncedFetch() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.pagination.current_page = 1;
            this.fetchItems();
        }, 400);
    },

    resetFilters() {
        this.filters = {
            search: '',
            category_id: '',
            status: '',
            condition_status: '',
            sort_by: 'created_at',
            sort_order: 'desc',
        };
        this.pagination.current_page = 1;
        this.fetchItems();
    },

    changePerPage() {
        this.pagination.current_page = 1;
        this.fetchItems();
    },

    // Pagination methods
    goToPage(page) {
        if (page >= 1 && page <= this.pagination.last_page) {
            this.pagination.current_page = page;
            this.fetchItems();
        }
    },

    getVisiblePages() {
        const current = this.pagination.current_page;
        const last = this.pagination.last_page;
        const pages = [];
        
        let start = Math.max(1, current - 2);
        let end = Math.min(last, current + 2);
        
        if (end - start < 4) {
            if (start === 1) {
                end = Math.min(last, start + 4);
            } else {
                start = Math.max(1, end - 4);
            }
        }
        
        for (let i = start; i <= end; i++) {
            pages.push(i);
        }
        
        return pages;
    },

    // Modal methods
    openCreateModal() {
        this.resetForm();
        this.showModal = true;
        this.errors = {};
        this.imagePreview = null;
    },

    closeModal() {
        this.showModal = false;
        this.resetForm();
        this.errors = {};
        this.imagePreview = null;
    },

    edit(item) {
        this.form = {
            id: item.id,
            name: item.name || '',
            category_id: item.category?.id || '',
            model: item.model || '',
            manufacturer: item.manufacturer || '',
            total_quantity: item.total_quantity || 1,
            available_quantity: item.available_quantity || 1,
            status: item.status || 'active',
            condition_status: item.condition_status || 'excellent',
            purchase_date: item.purchase_date || '',
            purchase_price: item.purchase_price || '',
            location: item.location || '',
            notes: item.notes || '',
            image: null,
            image_url: item.image_url || '',
            remove_image: false,
        };
        this.showModal = true;
        this.errors = {};
        this.imagePreview = null;
    },

    resetForm() {
        this.form = {
            id: null,
            name: '',
            category_id: '',
            model: '',
            manufacturer: '',
            total_quantity: 1,
            available_quantity: 1,
            status: 'active',
            condition_status: 'excellent',
            purchase_date: '',
            purchase_price: '',
            location: '',
            notes: '',
            image: null,
            image_url: '',
            remove_image: false,
        };
    },

    // Form validation
    isFormInvalid() {
        return !this.form.name || 
               !this.form.category_id || 
               !this.form.status || 
               !this.form.condition_status ||
               this.form.total_quantity < 1 ||
               this.form.available_quantity < 0 ||
               this.form.available_quantity > this.form.total_quantity;
    },

    // Image handling
    handleImageUpload(event) {
        const file = event.target.files[0];
        if (file) {
            this.form.image = file;
            this.form.remove_image = false;
            
            // Create preview
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    },

    removeImage() {
        this.form.image = null;
        this.form.remove_image = true;
        this.imagePreview = null;
        
        // Clear file input
        const fileInput = document.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.value = '';
        }
    },

    // CRUD operations
    async save() {
        if (this.isFormInvalid() || this.saving) return;
        
        this.saving = true;
        this.errors = {};

        try {
            const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
            if (!token) {
                throw new Error('Token tidak ditemukan. Silakan login ulang.');
            }

            const formData = new FormData();
            
            // Add form fields
            Object.keys(this.form).forEach(key => {
                if (key !== 'image' && key !== 'image_url' && this.form[key] !== null && this.form[key] !== '') {
                    formData.append(key, this.form[key]);
                }
            });
            
            // Add image file if selected
            if (this.form.image) {
                formData.append('image', this.form.image);
            }
            
            // Add remove image flag
            if (this.form.remove_image) {
                formData.append('remove_image', '1');
            }

            const url = this.form.id 
                ? `/api/admin/equipment/${this.form.id}`
                : '/api/admin/equipment';
            
            const method = this.form.id ? 'PUT' : 'POST';
            
            // For PUT requests via FormData, append _method
            if (method === 'PUT') {
                formData.append('_method', 'PUT');
            }

            const response = await fetch(url, {
                method: this.form.id ? 'POST' : 'POST', // Always POST with _method for FormData
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.showSuccess(this.form.id ? 'Alat berhasil diperbarui!' : 'Alat berhasil ditambahkan!');
                this.closeModal();
                await Promise.all([
                    this.fetchItems(),
                    this.fetchSummary()
                ]);
            } else {
                if (response.status === 422 && data.errors) {
                    this.errors = data.errors;
                } else {
                    throw new Error(data.message || 'Gagal menyimpan alat.');
                }
            }
        } catch (error) {
            console.error('Error saving equipment:', error);
            this.showError(error.message || 'Gagal menyimpan alat.');
        } finally {
            this.saving = false;
        }
    },

    confirmDelete(item) {
        this.deleteItem = item;
        this.showDeleteModal = true;
    },

    async deleteConfirmed() {
        if (!this.deleteItem || this.deleting) return;
        
        this.deleting = true;

        try {
            const token = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
            if (!token) {
                throw new Error('Token tidak ditemukan. Silakan login ulang.');
            }

            const response = await fetch(`/api/admin/equipment/${this.deleteItem.id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.showSuccess('Alat berhasil dihapus!');
                this.showDeleteModal = false;
                this.deleteItem = null;
                await Promise.all([
                    this.fetchItems(),
                    this.fetchSummary()
                ]);
            } else {
                throw new Error(data.message || 'Gagal menghapus alat.');
            }
        } catch (error) {
            console.error('Error deleting equipment:', error);
            this.showError(error.message || 'Gagal menghapus alat.');
        } finally {
            this.deleting = false;
        }
    },

    // Utility methods
    async refresh() {
        this.loading = true;
        try {
            await Promise.all([
                this.fetchItems(),
                this.fetchSummary()
            ]);
        } catch (error) {
            console.error('Error refreshing:', error);
            this.showError('Gagal memuat ulang data.');
        } finally {
            this.loading = false;
        }
    },

    showSuccess(message) {
        // Create and show success toast
        this.showToast(message, 'success');
    },

    showError(message) {
        // Create and show error toast
        this.showToast(message, 'error');
    },

    showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-md shadow-lg transition-all duration-300 transform translate-x-full`;
        
        if (type === 'success') {
            toast.className += ' bg-green-500 text-white';
        } else if (type === 'error') {
            toast.className += ' bg-red-500 text-white';
        } else {
            toast.className += ' bg-blue-500 text-white';
        }
        
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 5000);
    }
});