<?php

namespace App\Http\Requests\Admin;

use App\Enums\StaffType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
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

        // Prefer direct role column (matches login & middleware logic)
        $role = strtolower($user->role ?? '');
        if (in_array($role, ['admin', 'super_admin'])) {
            return true;
        }

        // Fallback to Spatie roles if used; accept both naming variants
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
        $staffId = $isUpdating ? $this->route('staff')->id : null;

        return [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'staff_type' => ['required', Rule::enum(StaffType::class)],
            'specialization' => 'nullable|string|max:255',
            'education' => 'nullable|string|max:500',
            'email' => [
                'nullable',
                'email',
                'max:255',
                $isUpdating
                    ? 'unique:staff_members,email,' . $staffId
                    : 'unique:staff_members,email'
            ],
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:2000',
            'research_interests' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            'remove_photo' => $isUpdating ? 'boolean' : 'nullable',
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
            'name.required' => 'Staff member name is required.',
            'name.max' => 'Name must not exceed 255 characters.',
            'position.required' => 'Position is required.',
            'position.max' => 'Position must not exceed 255 characters.',
            'staff_type.required' => 'Staff type is required.',
            'staff_type.enum' => 'Please select a valid staff type.',
            'specialization.max' => 'Specialization must not exceed 255 characters.',
            'education.max' => 'Education information must not exceed 500 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'email.max' => 'Email must not exceed 255 characters.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'bio.max' => 'Biography must not exceed 2000 characters.',
            'research_interests.max' => 'Research interests must not exceed 1000 characters.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order cannot be negative.',
            'sort_order.max' => 'Sort order cannot exceed 999.',
            'photo.image' => 'Photo must be an image file.',
            'photo.mimes' => 'Photo must be a JPEG, PNG, JPG, or GIF file.',
            'photo.max' => 'Photo size must not exceed 2MB.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate phone number format if provided
            if ($this->filled('phone')) {
                $phone = preg_replace('/[^0-9+]/', '', $this->get('phone'));
                if (strlen($phone) < 8 || strlen($phone) > 20) {
                    $validator->errors()->add('phone', 'Phone number must be between 8-20 digits.');
                }
            }

            // Enforce only one Kepala Laboratorium
            if ($this->filled('staff_type') && $this->get('staff_type') === \App\Enums\StaffType::KEPALA_LABORATORIUM->value) {
                $query = \App\Models\StaffMember::where('staff_type', \App\Enums\StaffType::KEPALA_LABORATORIUM);
                // Exclude current record when updating
                if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
                    $current = $this->route('staff');
                    if ($current) {
                        $query->where('id', '!=', $current->id);
                    }
                }
                if ($query->exists()) {
                    $validator->errors()->add('staff_type', 'Hanya boleh ada 1 Kepala Laboratorium.');
                }
            }

            // Validate sort order uniqueness for active staff
            if ($this->filled('sort_order') && $this->boolean('is_active', true)) {
                $query = \App\Models\StaffMember::where('sort_order', $this->get('sort_order'))
                    ->where('is_active', true);

                if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
                    $query->where('id', '!=', $this->route('staff')->id);
                }

                if ($query->exists()) {
                    $validator->errors()->add('sort_order', 'This sort order is already taken by another active staff member.');
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
            'is_active' => 'active status',
            'sort_order' => 'display order',
            'research_interests' => 'research interests',
        ];
    }
}