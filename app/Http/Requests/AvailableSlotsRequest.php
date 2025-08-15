<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AvailableSlotsRequest extends FormRequest
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
        return [
            'date' => [
                'required',
                'date',
                'after:' . Carbon::now()->addDays(2)->format('Y-m-d'), // H+3 minimum
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value);
                    if ($date->isWeekend()) {
                        $fail('Kunjungan laboratorium hanya tersedia pada hari kerja (Senin - Jumat)');
                    }
                }
            ],
            'duration' => [
                'required',
                'integer',
                'in:1,2,3'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.required' => 'Tanggal kunjungan harus diisi',
            'date.date' => 'Format tanggal tidak valid',
            'date.after' => 'Tanggal kunjungan minimal H+3 dari hari ini',
            'duration.required' => 'Durasi kunjungan harus dipilih',
            'duration.integer' => 'Durasi harus berupa angka',
            'duration.in' => 'Durasi kunjungan harus 1, 2, atau 3 jam'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure duration is an integer
        if ($this->has('duration')) {
            $this->merge([
                'duration' => (int) $this->input('duration')
            ]);
        }
    }
}