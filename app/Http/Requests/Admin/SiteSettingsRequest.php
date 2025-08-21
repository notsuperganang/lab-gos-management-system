<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SiteSettingsRequest extends FormRequest
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
        
        // Use same logic as RoleMiddleware - check role field directly
        $userRole = strtolower($user->role ?? '');
        return in_array($userRole, ['admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'settings' => 'required|array|min:1',
            'settings.*.key' => 'required|string|max:100|regex:/^[a-z0-9_\-\.]+$/',
            'settings.*.title' => 'nullable|string|max:255',
            'settings.*.content' => 'nullable|string|max:10000',
            'settings.*.type' => 'required|in:text,textarea,json,boolean,number',
            'settings.*.is_active' => 'boolean',
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
            'settings.required' => 'Settings data is required.',
            'settings.array' => 'Settings must be provided as an array.',
            'settings.min' => 'At least one setting must be provided.',
            'settings.*.key.required' => 'Setting key is required.',
            'settings.*.key.max' => 'Setting key must not exceed 100 characters.',
            'settings.*.key.regex' => 'Setting key can only contain lowercase letters, numbers, underscores, hyphens, and dots.',
            'settings.*.title.max' => 'Setting title must not exceed 255 characters.',
            'settings.*.content.max' => 'Setting content must not exceed 10,000 characters.',
            'settings.*.type.required' => 'Setting type is required.',
            'settings.*.type.in' => 'Setting type must be one of: text, textarea, json, boolean, number.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->has('settings') || !is_array($this->get('settings'))) {
                return;
            }
            
            $keys = [];
            
            foreach ($this->get('settings') as $index => $setting) {
                $key = $setting['key'] ?? null;
                $type = $setting['type'] ?? null;
                $content = $setting['content'] ?? '';
                
                // Check for duplicate keys
                if ($key) {
                    if (in_array($key, $keys)) {
                        $validator->errors()->add("settings.{$index}.key", 'Duplicate setting key found.');
                    } else {
                        $keys[] = $key;
                    }
                }
                
                // Validate content based on type
                if ($type && !empty($content)) {
                    switch ($type) {
                        case 'json':
                            $decoded = json_decode($content, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                $validator->errors()->add(
                                    "settings.{$index}.content", 
                                    'Invalid JSON format: ' . json_last_error_msg()
                                );
                            }
                            break;
                            
                        case 'number':
                            if (!is_numeric($content)) {
                                $validator->errors()->add(
                                    "settings.{$index}.content", 
                                    'Content must be a valid number.'
                                );
                            }
                            break;
                            
                        case 'boolean':
                            if (!in_array(strtolower($content), ['0', '1', 'true', 'false', ''])) {
                                $validator->errors()->add(
                                    "settings.{$index}.content", 
                                    'Boolean content must be true, false, 1, or 0.'
                                );
                            }
                            break;
                    }
                }
                
                // Validate restricted keys (prevent overwriting critical settings)
                if ($key && in_array($key, $this->getRestrictedKeys())) {
                    $validator->errors()->add(
                        "settings.{$index}.key", 
                        'This setting key is restricted and cannot be modified.'
                    );
                }
            }
        });
    }

    /**
     * Get restricted setting keys that cannot be modified.
     *
     * @return array<string>
     */
    protected function getRestrictedKeys(): array
    {
        return [
            'app.key',
            'app.env',
            'database.default',
            'database.connections',
            'cache.default',
            'session.driver',
            'queue.default',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'settings.*.key' => 'setting key',
            'settings.*.title' => 'setting title',
            'settings.*.content' => 'setting content',
            'settings.*.type' => 'setting type',
            'settings.*.is_active' => 'active status',
        ];
    }
}