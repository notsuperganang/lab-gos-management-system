<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DashboardStatsRequest extends FormRequest
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
            'date_from' => [
                'sometimes',
                'date',
                'before_or_equal:date_to',
                'after_or_equal:' . now()->subYear()->format('Y-m-d'),
            ],
            'date_to' => [
                'sometimes', 
                'date',
                'after_or_equal:date_from',
                'before_or_equal:' . now()->format('Y-m-d'),
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
            'date_from.date' => 'The start date must be a valid date.',
            'date_from.before_or_equal' => 'The start date must be before or equal to the end date.',
            'date_from.after_or_equal' => 'The start date cannot be more than a year ago.',
            'date_to.date' => 'The end date must be a valid date.',
            'date_to.after_or_equal' => 'The end date must be after or equal to the start date.',
            'date_to.before_or_equal' => 'The end date cannot be in the future.',
            'refresh_cache.boolean' => 'The refresh cache parameter must be true or false.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Set default dates if not provided
            if (!$this->has('date_from')) {
                $this->merge(['date_from' => now()->subDays(30)->format('Y-m-d')]);
            }
            
            if (!$this->has('date_to')) {
                $this->merge(['date_to' => now()->format('Y-m-d')]);
            }

            // Additional business rule: date range cannot exceed 1 year
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
            'date_from' => now()->subDays(30)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'refresh_cache' => false,
        ], $validated);
    }
}