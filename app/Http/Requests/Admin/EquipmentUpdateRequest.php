<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EquipmentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'model' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'specifications' => 'nullable|array',
            'total_quantity' => 'required|integer|min:1|max:9999',
            'available_quantity' => 'required|integer|min:0|lte:total_quantity',
            'status' => 'required|in:active,maintenance,retired',
            'condition_status' => 'required|in:excellent,good,fair,poor',
            'purchase_date' => 'nullable|date|before_or_equal:today',
            'purchase_price' => 'nullable|numeric|min:0|max:999999999999.99',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'remove_image' => 'boolean',
            'manual_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'remove_manual' => 'boolean',
            'notes' => 'nullable|string|max:2000',
            'last_maintenance_date' => 'nullable|date|before_or_equal:today',
            'next_maintenance_date' => 'nullable|date|after_or_equal:today',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama alat wajib diisi.',
            'name.max' => 'Nama alat maksimal 255 karakter.',
            'category_id.required' => 'Kategori alat wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'model.max' => 'Model maksimal 255 karakter.',
            'manufacturer.max' => 'Manufacturer maksimal 255 karakter.',
            'specifications.array' => 'Spesifikasi harus berupa array.',
            'total_quantity.required' => 'Jumlah total wajib diisi.',
            'total_quantity.integer' => 'Jumlah total harus berupa angka.',
            'total_quantity.min' => 'Jumlah total minimal 1.',
            'total_quantity.max' => 'Jumlah total maksimal 9999.',
            'available_quantity.required' => 'Jumlah tersedia wajib diisi.',
            'available_quantity.integer' => 'Jumlah tersedia harus berupa angka.',
            'available_quantity.min' => 'Jumlah tersedia tidak boleh negatif.',
            'available_quantity.lte' => 'Jumlah tersedia tidak boleh melebihi jumlah total.',
            'status.required' => 'Status alat wajib dipilih.',
            'status.in' => 'Status alat tidak valid.',
            'condition_status.required' => 'Kondisi alat wajib dipilih.',
            'condition_status.in' => 'Kondisi alat tidak valid.',
            'purchase_date.date' => 'Tanggal pembelian harus berupa tanggal yang valid.',
            'purchase_date.before_or_equal' => 'Tanggal pembelian tidak boleh di masa depan.',
            'purchase_price.numeric' => 'Harga pembelian harus berupa angka.',
            'purchase_price.min' => 'Harga pembelian tidak boleh negatif.',
            'purchase_price.max' => 'Harga pembelian terlalu besar.',
            'location.max' => 'Lokasi maksimal 255 karakter.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat JPEG, PNG, JPG, GIF, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 5MB.',
            'manual_file.file' => 'Manual harus berupa file.',
            'manual_file.mimes' => 'Manual harus berformat PDF, DOC, atau DOCX.',
            'manual_file.max' => 'Ukuran manual maksimal 10MB.',
            'notes.max' => 'Catatan maksimal 2000 karakter.',
            'last_maintenance_date.date' => 'Tanggal maintenance terakhir harus berupa tanggal yang valid.',
            'last_maintenance_date.before_or_equal' => 'Tanggal maintenance terakhir tidak boleh di masa depan.',
            'next_maintenance_date.date' => 'Tanggal maintenance berikutnya harus berupa tanggal yang valid.',
            'next_maintenance_date.after_or_equal' => 'Tanggal maintenance berikutnya tidak boleh di masa lalu.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Get the equipment being updated
            $equipment = $this->route('equipment');
            
            // Validate available quantity against currently borrowed equipment
            if ($this->filled(['total_quantity', 'available_quantity']) && $equipment) {
                $totalQuantity = (int) $this->get('total_quantity');
                $availableQuantity = (int) $this->get('available_quantity');
                $currentlyBorrowed = $equipment->getCurrentBorrowedQuantity();
                $maxAvailable = max(0, $totalQuantity - $currentlyBorrowed);
                
                if ($availableQuantity > $maxAvailable) {
                    $validator->errors()->add(
                        'available_quantity',
                        "Jumlah tersedia tidak boleh melebihi {$maxAvailable}. Saat ini ada {$currentlyBorrowed} unit yang sedang dipinjam."
                    );
                }
            }
            
            // Validate image dimensions if uploaded
            if ($this->hasFile('image')) {
                $image = $this->file('image');
                $dimensions = getimagesize($image->getPathname());
                
                if ($dimensions) {
                    [$width, $height] = $dimensions;
                    
                    // Minimum dimensions
                    if ($width < 200 || $height < 200) {
                        $validator->errors()->add('image', 'Dimensi gambar minimal 200x200 pixel.');
                    }
                    
                    // Maximum dimensions
                    if ($width > 4096 || $height > 4096) {
                        $validator->errors()->add('image', 'Dimensi gambar maksimal 4096x4096 pixel.');
                    }
                }
            }

            // Validate maintenance date logic
            if ($this->filled(['last_maintenance_date', 'next_maintenance_date'])) {
                $lastDate = $this->date('last_maintenance_date');
                $nextDate = $this->date('next_maintenance_date');
                
                if ($lastDate && $nextDate && $nextDate <= $lastDate) {
                    $validator->errors()->add('next_maintenance_date', 'Tanggal maintenance berikutnya harus setelah tanggal maintenance terakhir.');
                }
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'kategori',
            'total_quantity' => 'jumlah total',
            'available_quantity' => 'jumlah tersedia',
            'condition_status' => 'kondisi',
            'purchase_date' => 'tanggal pembelian',
            'purchase_price' => 'harga pembelian',
            'manual_file' => 'file manual',
            'last_maintenance_date' => 'tanggal maintenance terakhir',
            'next_maintenance_date' => 'tanggal maintenance berikutnya',
        ];
    }
}