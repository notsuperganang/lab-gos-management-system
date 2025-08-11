<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TestingRequest;

class TestingRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public endpoint, no authorization needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $minDate = now()->addDays(3)->format('Y-m-d'); // Minimum 3 days advance notice
        $maxDate = now()->addMonths(3)->format('Y-m-d');
        
        $testingTypes = implode(',', array_keys(TestingRequest::getTestingTypes()));

        return [
            'client_name' => 'required|string|max:255|min:2',
            'client_organization' => 'required|string|max:255|min:3',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:20|min:10',
            'client_address' => 'required|string|max:500|min:10',
            
            'sample_name' => 'required|string|max:255|min:2',
            'sample_description' => 'required|string|max:1000|min:10',
            'sample_quantity' => 'required|string|max:100|min:1',
            
            'testing_type' => "required|in:{$testingTypes}",
            'testing_parameters' => 'nullable|array|max:20',
            'testing_parameters.*' => 'string|max:255',
            
            'urgent_request' => 'boolean',
            'preferred_date' => "required|date|after:{$minDate}|before:{$maxDate}",
            'estimated_duration_hours' => 'nullable|integer|min:1|max:168', // Max 1 week (168 hours)
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_name.required' => 'Client name is required.',
            'client_name.min' => 'Client name must be at least 2 characters.',
            'client_organization.required' => 'Organization is required.',
            'client_organization.min' => 'Organization name must be at least 3 characters.',
            'client_email.required' => 'Email address is required.',
            'client_email.email' => 'Please provide a valid email address.',
            'client_phone.required' => 'Phone number is required.',
            'client_phone.min' => 'Phone number must be at least 10 digits.',
            'client_address.required' => 'Address is required.',
            'client_address.min' => 'Address must be at least 10 characters.',
            
            'sample_name.required' => 'Sample name is required.',
            'sample_name.min' => 'Sample name must be at least 2 characters.',
            'sample_description.required' => 'Sample description is required.',
            'sample_description.min' => 'Sample description must be at least 10 characters.',
            'sample_quantity.required' => 'Sample quantity is required.',
            
            'testing_type.required' => 'Testing type is required.',
            'testing_type.in' => 'Please select a valid testing type.',
            'testing_parameters.array' => 'Testing parameters must be provided as an array.',
            'testing_parameters.max' => 'Maximum 20 testing parameters allowed.',
            'testing_parameters.*.string' => 'Each testing parameter must be a string.',
            
            'preferred_date.required' => 'Preferred testing date is required.',
            'preferred_date.after' => 'Testing must be scheduled at least 3 days in advance.',
            'preferred_date.before' => 'Testing date cannot be more than 3 months in advance.',
            
            'estimated_duration_hours.integer' => 'Estimated duration must be a number.',
            'estimated_duration_hours.min' => 'Minimum duration is 1 hour.',
            'estimated_duration_hours.max' => 'Maximum duration is 168 hours (1 week).',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateTestingSchedule($validator);
            $this->validateUrgentRequest($validator);
            $this->validateTestingParameters($validator);
        });
    }

    /**
     * Validate testing schedule against operational hours
     */
    private function validateTestingSchedule($validator)
    {
        $preferredDate = $this->input('preferred_date');
        
        if (!$preferredDate) {
            return;
        }
        
        $preferredDateTime = \Carbon\Carbon::parse($preferredDate);
        $dayOfWeek = strtolower($preferredDateTime->format('l'));
        
        // Get lab operational hours from config
        $operationalHours = config('lab.operational_hours');
        $operatingTime = $operationalHours[$dayOfWeek] ?? null;
        
        // Check if lab is closed on selected day
        if ($operatingTime === 'Tutup' || !$operatingTime) {
            $validator->errors()->add(
                'preferred_date',
                'Laboratory is closed on ' . ucfirst($dayOfWeek) . '. Please select a different date.'
            );
        }
    }

    /**
     * Validate urgent request constraints
     */
    private function validateUrgentRequest($validator)
    {
        $urgentRequest = $this->input('urgent_request');
        $preferredDate = $this->input('preferred_date');
        
        if ($urgentRequest && $preferredDate) {
            $preferredDateTime = \Carbon\Carbon::parse($preferredDate);
            $daysDifference = now()->diffInDays($preferredDateTime);
            
            // Urgent requests need at least 1 day notice but less than 3 days
            if ($daysDifference < 1) {
                $validator->errors()->add(
                    'preferred_date',
                    'Urgent requests require at least 24 hours advance notice.'
                );
            } elseif ($daysDifference >= 7) {
                $validator->errors()->add(
                    'urgent_request',
                    'Requests scheduled more than 7 days in advance cannot be marked as urgent.'
                );
            }
        }
    }

    /**
     * Validate testing parameters based on testing type
     */
    private function validateTestingParameters($validator)
    {
        $testingType = $this->input('testing_type');
        $testingParameters = $this->input('testing_parameters', []);
        
        // Define required parameters for each testing type
        $requiredParameters = [
            'uv_vis_spectroscopy' => ['wavelength_range', 'solvent'],
            'ftir_spectroscopy' => ['wavenumber_range', 'sample_preparation'],
            'optical_microscopy' => ['magnification', 'illumination_type'],
        ];
        
        if (isset($requiredParameters[$testingType])) {
            $required = $requiredParameters[$testingType];
            $provided = array_keys($testingParameters);
            
            $missing = array_diff($required, $provided);
            
            if (!empty($missing)) {
                $missingList = implode(', ', $missing);
                $validator->errors()->add(
                    'testing_parameters',
                    "The following parameters are required for {$testingType}: {$missingList}"
                );
            }
        }
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'client_name' => 'client name',
            'client_organization' => 'organization',
            'client_email' => 'email address',
            'client_phone' => 'phone number',
            'client_address' => 'address',
            'sample_name' => 'sample name',
            'sample_description' => 'sample description',
            'sample_quantity' => 'sample quantity',
            'testing_type' => 'testing type',
            'testing_parameters' => 'testing parameters',
            'urgent_request' => 'urgent request',
            'preferred_date' => 'preferred date',
            'estimated_duration_hours' => 'estimated duration',
        ];
    }
}