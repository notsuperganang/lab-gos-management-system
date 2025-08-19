@props(['requestType', 'requestId', 'userName', 'userPhone'])

<div x-data="whatsappAction({
    requestType: '{{ $requestType }}',
    requestId: '{{ $requestId }}',
    userName: '{{ $userName }}',
    userPhone: '{{ $userPhone }}'
})" class="whatsapp-action-component">
    
    <!-- WhatsApp Button -->
    <div class="flex items-center space-x-3">
        <button @click="openTemplateModal" 
                :disabled="loading"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded-md transition-colors duration-200">
            
            <!-- WhatsApp Icon -->
            <svg x-show="!loading" class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
            </svg>
            
            <!-- Loading Spinner -->
            <svg x-show="loading" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            
            <span x-text="loading ? 'Memuat...' : 'WhatsApp'"></span>
        </button>

        <!-- Copy Phone Button -->
        <button @click="copyPhone" 
                class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors duration-200"
                title="Salin nomor telepon">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
            </svg>
        </button>
    </div>

    <!-- Template Selection Modal -->
    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

            <!-- Modal content -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Pilih Template WhatsApp
                            </h3>
                            
                            <!-- Template Selection -->
                            <div class="space-y-3 mb-4">
                                <template x-for="template in templates" :key="template.id">
                                    <label class="flex items-start space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                           :class="{'border-green-500 bg-green-50': selectedTemplate === template.id}">
                                        <input type="radio" 
                                               :value="template.id" 
                                               x-model="selectedTemplate"
                                               class="mt-1 text-green-600 focus:ring-green-500">
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900" x-text="template.name"></div>
                                            <div class="text-sm text-gray-500 mt-1" x-text="template.preview"></div>
                                        </div>
                                    </label>
                                </template>
                            </div>

                            <!-- Additional Notes -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan Tambahan (Opsional)
                                </label>
                                <textarea x-model="notes" 
                                          rows="3" 
                                          maxlength="500"
                                          placeholder="Tambahkan catatan khusus..."
                                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span x-text="notes.length"></span>/500 karakter
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="generateLink" 
                            :disabled="!selectedTemplate || loading"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="loading ? 'Membuat Link...' : 'Kirim via WhatsApp'"></span>
                    </button>
                    <button @click="closeModal" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div x-show="message" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed top-4 right-4 z-50 max-w-sm w-full"
         style="display: none;">
        <div :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
             class="border rounded-lg p-4 shadow-lg">
            <div class="flex items-center">
                <svg x-show="messageType === 'success'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <svg x-show="messageType === 'error'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span x-text="message"></span>
            </div>
        </div>
    </div>
</div>

<script>
function whatsappAction(props) {
    return {
        loading: false,
        showModal: false,
        templates: [],
        selectedTemplate: null,
        notes: '',
        message: '',
        messageType: 'success',

        init() {
            // Auto-hide messages after 5 seconds
            this.$watch('message', (value) => {
                if (value) {
                    setTimeout(() => {
                        this.message = '';
                    }, 5000);
                }
            });
        },

        async openTemplateModal() {
            this.showModal = true;
            this.loading = true;
            
            try {
                const response = await fetch(`/api/admin/whatsapp/templates?request_type=${props.requestType}`, {
                    headers: {
                        'Authorization': `Bearer ${document.querySelector('meta[name="api-token"]')?.content}`,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.templates = data.data;
                } else {
                    throw new Error(data.message || 'Gagal mengambil template');
                }
            } catch (error) {
                this.showMessage('Gagal mengambil template WhatsApp', 'error');
                this.closeModal();
            } finally {
                this.loading = false;
            }
        },

        closeModal() {
            this.showModal = false;
            this.selectedTemplate = null;
            this.notes = '';
        },

        async generateLink() {
            if (!this.selectedTemplate) return;

            this.loading = true;
            
            try {
                const response = await fetch('/api/admin/whatsapp/generate-link', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${document.querySelector('meta[name="api-token"]')?.content}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        request_type: props.requestType,
                        request_id: props.requestId,
                        template_id: this.selectedTemplate,
                        notes: this.notes || null
                    })openWhatsAppChat  
                });

                const data = await response.json();
                
                if (data.success) {
                    // Open WhatsApp in new tab
                    window.open(data.data.url, '_blank');
                    this.showMessage('Link WhatsApp berhasil dibuat!', 'success');
                    this.closeModal();
                } else {
                    throw new Error(data.message || 'Gagal membuat link WhatsApp');
                }
            } catch (error) {
                this.showMessage(error.message, 'error');
            } finally {
                this.loading = false;
            }
        },

        async copyPhone() {
            try {
                await navigator.clipboard.writeText(props.userPhone);
                this.showMessage('Nomor telepon berhasil disalin!', 'success');
            } catch (error) {
                this.showMessage('Gagal menyalin nomor telepon', 'error');
            }
        },

        showMessage(text, type = 'success') {
            this.message = text;
            this.messageType = type;
        }
    }
}
</script>

<style>
.whatsapp-action-component .animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
