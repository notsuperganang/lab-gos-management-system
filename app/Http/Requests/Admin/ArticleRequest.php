<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        // Prefer direct role column consistency (avoids mismatch with 'super_admin')
        $role = strtolower($user->role ?? '');
        if (in_array($role, ['admin', 'super_admin'])) {
            return true;
        }

        // Fallback to Spatie roles if installed
        if (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'super_admin', 'superadmin'])) {
            return true;
        }

        return false;
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
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category' => 'required|string|in:' . implode(',', array_keys(\App\Models\Article::getCategories())),
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:50',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'remove_featured_image' => $isUpdating ? 'boolean' : 'nullable',
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
            'title.required' => 'Article title is required.',
            'title.max' => 'Article title must not exceed 255 characters.',
            'excerpt.max' => 'Article excerpt must not exceed 500 characters.',
            'content.required' => 'Article content is required.',
            'category.required' => 'Article category is required.',
            'category.in' => 'Invalid article category selected.',
            'tags.max' => 'Maximum 10 tags are allowed.',
            'tags.*.string' => 'Each tag must be a string.',
            'tags.*.max' => 'Each tag must not exceed 50 characters.',
            'is_featured.boolean' => 'Featured status must be true or false.',
            'published_at.date' => 'Published date must be a valid date.',
            'featured_image.image' => 'Featured image must be an image file.',
            'featured_image.mimes' => 'Featured image must be a JPEG, PNG, JPG, GIF, or WebP file.',
            'featured_image.max' => 'Featured image size must not exceed 5MB.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate publication logic
            if ($this->boolean('is_published') && empty($this->get('published_at'))) {
                // If publishing without specific date, use current time
                $this->merge(['published_at' => now()]);
            }

            // Validate tags format
            if ($this->has('tags') && is_array($this->get('tags'))) {
                foreach ($this->get('tags') as $index => $tag) {
                    if (!is_string($tag) || empty(trim($tag))) {
                        $validator->errors()->add("tags.{$index}", 'Tag cannot be empty.');
                    }
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
            'featured_image' => 'featured image',
            'is_published' => 'publication status',
            'published_at' => 'publication date',
        ];
    }
}
