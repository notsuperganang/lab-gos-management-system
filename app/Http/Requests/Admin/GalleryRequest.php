<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GalleryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(['admin', 'superadmin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdating = $this->isMethod('PUT') || $this->isMethod('PATCH');
        
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|in:' . implode(',', array_keys(\App\Models\Gallery::getCategories())),
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'boolean',
            'image' => $isUpdating 
                ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120' 
                : 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'remove_image' => $isUpdating ? 'boolean' : 'nullable',
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
            'title.required' => 'Gallery item title is required.',
            'title.max' => 'Title must not exceed 255 characters.',
            'description.max' => 'Description must not exceed 1000 characters.',
            'category.required' => 'Gallery category is required.',
            'category.in' => 'Invalid gallery category selected.',
            'alt_text.max' => 'Alt text must not exceed 255 characters.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order cannot be negative.',
            'sort_order.max' => 'Sort order cannot exceed 999.',
            'image.required' => 'Gallery image is required.',
            'image.image' => 'Uploaded file must be an image.',
            'image.mimes' => 'Image must be a JPEG, PNG, JPG, GIF, or WebP file.',
            'image.max' => 'Image size must not exceed 5MB.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Set alt_text to title if not provided
            if (empty($this->get('alt_text')) && $this->filled('title')) {
                $this->merge(['alt_text' => $this->get('title')]);
            }
            
            // Validate image dimensions if uploaded
            if ($this->hasFile('image')) {
                $image = $this->file('image');
                $dimensions = getimagesize($image->getPathname());
                
                if ($dimensions) {
                    [$width, $height] = $dimensions;
                    
                    // Minimum dimensions
                    if ($width < 200 || $height < 200) {
                        $validator->errors()->add('image', 'Image dimensions must be at least 200x200 pixels.');
                    }
                    
                    // Maximum dimensions
                    if ($width > 4096 || $height > 4096) {
                        $validator->errors()->add('image', 'Image dimensions must not exceed 4096x4096 pixels.');
                    }
                }
            }
            
            // Validate sort order uniqueness for active gallery items in same category
            if ($this->filled('sort_order') && $this->filled('category') && $this->boolean('is_active', true)) {
                $query = \App\Models\Gallery::where('category', $this->get('category'))
                    ->where('sort_order', $this->get('sort_order'))
                    ->where('is_active', true);
                
                if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
                    $query->where('id', '!=', $this->route('gallery')->id);
                }
                
                if ($query->exists()) {
                    $validator->errors()->add('sort_order', 'This sort order is already taken by another active item in the same category.');
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
            'alt_text' => 'alternative text',
            'is_active' => 'active status',
            'sort_order' => 'display order',
        ];
    }
}