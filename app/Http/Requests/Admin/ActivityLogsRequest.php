<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityLogsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->role === 'admin' || $this->user()->role === 'super_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'type' => [
                'sometimes',
                'string',
                Rule::in(['created', 'updated', 'deleted', 'approved', 'rejected', 'cancelled', 'completed']),
            ],
            'user_id' => 'sometimes|integer|exists:users,id',
            'subject_type' => [
                'sometimes',
                'string',
                Rule::in([
                    'App\\Models\\BorrowRequest',
                    'App\\Models\\VisitRequest', 
                    'App\\Models\\TestingRequest',
                    'App\\Models\\Equipment',
                    'App\\Models\\User',
                ]),
            ],
            'subject_id' => 'sometimes|integer|min:1',
            'search' => 'sometimes|string|max:255',
            'date_from' => [
                'sometimes',
                'date',
                'before_or_equal:date_to',
            ],
            'date_to' => [
                'sometimes',
                'date',
                'after_or_equal:date_from',
                'before_or_equal:' . now()->format('Y-m-d'),
            ],
            'sort_by' => [
                'sometimes',
                'string',
                Rule::in(['created_at', 'event', 'causer_id', 'subject_type']),
            ],
            'sort_direction' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'refresh_cache' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'page.integer' => 'The page number must be a valid integer.',
            'page.min' => 'The page number must be at least 1.',
            'per_page.integer' => 'The per page value must be a valid integer.',
            'per_page.min' => 'The per page value must be at least 1.',
            'per_page.max' => 'The per page value cannot exceed 100.',
            'type.in' => 'The activity type must be one of: created, updated, deleted, approved, rejected, cancelled, completed.',
            'user_id.integer' => 'The user ID must be a valid integer.',
            'user_id.exists' => 'The specified user does not exist.',
            'subject_type.in' => 'The subject type must be a valid model type.',
            'subject_id.integer' => 'The subject ID must be a valid integer.',
            'subject_id.min' => 'The subject ID must be at least 1.',
            'search.string' => 'The search query must be a valid string.',
            'search.max' => 'The search query cannot exceed 255 characters.',
            'date_from.date' => 'The start date must be a valid date.',
            'date_from.before_or_equal' => 'The start date must be before or equal to the end date.',
            'date_to.date' => 'The end date must be a valid date.',
            'date_to.after_or_equal' => 'The end date must be after or equal to the start date.',
            'date_to.before_or_equal' => 'The end date cannot be in the future.',
            'sort_by.in' => 'The sort field must be one of: created_at, event, causer_id, subject_type.',
            'sort_direction.in' => 'The sort direction must be either asc or desc.',
            'refresh_cache.boolean' => 'The refresh cache parameter must be true or false.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // If subject_type is provided, subject_id should also be provided
            if ($this->has('subject_type') && !$this->has('subject_id')) {
                $validator->errors()->add('subject_id', 'Subject ID is required when subject type is specified.');
            }

            // If subject_id is provided, subject_type should also be provided
            if ($this->has('subject_id') && !$this->has('subject_type')) {
                $validator->errors()->add('subject_type', 'Subject type is required when subject ID is specified.');
            }

            // Validate date range is reasonable (not more than 1 year)
            if ($this->has('date_from') && $this->has('date_to')) {
                $dateFrom = \Carbon\Carbon::parse($this->get('date_from'));
                $dateTo = \Carbon\Carbon::parse($this->get('date_to'));
                
                if ($dateFrom->diffInDays($dateTo) > 365) {
                    $validator->errors()->add('date_range', 'The date range cannot exceed 365 days.');
                }
            }
        });
    }

    /**
     * Get validated data with defaults applied
     */
    public function validatedWithDefaults(): array
    {
        $validated = $this->validated();
        
        return array_merge([
            'page' => 1,
            'per_page' => 10,
            'sort_by' => 'created_at',
            'sort_direction' => 'desc',
            'refresh_cache' => false,
        ], $validated);
    }
}