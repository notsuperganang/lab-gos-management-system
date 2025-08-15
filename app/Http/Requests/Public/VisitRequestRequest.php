<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\VisitRequest;

class VisitRequestRequest extends FormRequest
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
        $advanceBookingDays = config('lab.services.lab_visit.advance_booking_days', 3);
        $minDate = now()->addDays($advanceBookingDays)->format('Y-m-d');
        $maxDate = now()->addMonths(6)->format('Y-m-d');
        
        $purposes = implode(',', array_keys(VisitRequest::getPurposes()));

        return [
            // Visitor information
            'visitor_name' => 'required|string|max:255|min:2',
            'visitor_email' => 'required|email|max:255',
            'visitor_phone' => 'required|string|max:20|min:10',
            'institution' => 'required|string|max:255|min:3',
            
            // Visit details
            'visit_purpose' => "required|in:{$purposes}",
            'visit_date' => "required|date|after:{$minDate}|before:{$maxDate}",
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'group_size' => 'required|integer|min:1|max:50',
            
            // Optional fields
            'purpose_description' => 'nullable|string|max:1000',
            'special_requirements' => 'nullable|string|max:1000',
            'equipment_needed' => 'nullable|array',
            'request_letter' => 'nullable|file|mimes:pdf|max:5120', // 5MB max
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $advanceBookingDays = config('lab.services.lab_visit.advance_booking_days', 3);
        
        return [
            'visitor_name.required' => 'Nama pengunjung harus diisi.',
            'visitor_name.min' => 'Nama pengunjung minimal 2 karakter.',
            'visitor_email.required' => 'Email harus diisi.',
            'visitor_email.email' => 'Format email tidak valid.',
            'visitor_phone.required' => 'Nomor telepon harus diisi.',
            'visitor_phone.min' => 'Nomor telepon minimal 10 digit.',
            'institution.required' => 'Instansi/organisasi harus diisi.',
            'institution.min' => 'Nama instansi minimal 3 karakter.',
            
            'visit_purpose.required' => 'Tujuan kunjungan harus dipilih.',
            'visit_purpose.in' => 'Pilih tujuan kunjungan yang valid.',
            'visit_date.required' => 'Tanggal kunjungan harus diisi.',
            'visit_date.after' => "Kunjungan harus dipesan minimal {$advanceBookingDays} hari sebelumnya.",
            'visit_date.before' => 'Tanggal kunjungan tidak boleh lebih dari 6 bulan ke depan.',
            'start_time.required' => 'Waktu mulai kunjungan harus diisi.',
            'start_time.date_format' => 'Format waktu mulai tidak valid (HH:MM).',
            'end_time.required' => 'Waktu selesai kunjungan harus diisi.',
            'end_time.date_format' => 'Format waktu selesai tidak valid (HH:MM).',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai.',
            
            'group_size.required' => 'Jumlah peserta harus diisi.',
            'group_size.min' => 'Minimal 1 peserta.',
            'group_size.max' => 'Maksimal 50 peserta per kunjungan.',
            
            'request_letter.file' => 'Surat permohonan harus berupa file.',
            'request_letter.mimes' => 'Surat permohonan harus format PDF.',
            'request_letter.max' => 'Ukuran file surat permohonan maksimal 5MB.',
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
            $this->validateVisitSchedule($validator);
        });
    }

    /**
     * Validate visit schedule against operational hours and time conflicts
     */
    private function validateVisitSchedule($validator)
    {
        $visitDate = $this->input('visit_date');
        $startTime = $this->input('start_time');
        $endTime = $this->input('end_time');
        
        if (!$visitDate || !$startTime || !$endTime) {
            return;
        }
        
        $visitDateTime = \Carbon\Carbon::parse($visitDate);
        $dayOfWeek = strtolower($visitDateTime->format('l'));
        
        // Check if lab is closed on weekends
        if (in_array($dayOfWeek, ['saturday', 'sunday'])) {
            $validator->errors()->add(
                'visit_date',
                'Laboratorium tutup pada akhir pekan. Pilih hari kerja (Senin - Jumat).'
            );
            return;
        }
        
        // Validate operating hours (08:00 - 16:00)
        $startHour = (int) substr($startTime, 0, 2);
        $endHour = (int) substr($endTime, 0, 2);
        
        if ($startHour < 8 || $endHour > 16) {
            $validator->errors()->add(
                'start_time',
                'Waktu kunjungan harus dalam jam operasional (08:00 - 16:00).'
            );
        }
        
        // Validate lunch break (12:00 - 13:00)
        if ($startHour < 13 && $endHour > 12) {
            $validator->errors()->add(
                'start_time',
                'Waktu kunjungan tidak boleh melewati jam istirahat (12:00 - 13:00).'
            );
        }
        
        // Check for conflicts with existing bookings (optional - could be done in service layer)
        $this->validateTimeSlotAvailability($validator, $visitDate, $startTime, $endTime);
    }
    
    /**
     * Validate that the time slot is available (no conflicts)
     */
    private function validateTimeSlotAvailability($validator, $visitDate, $startTime, $endTime)
    {
        $visitSlotsService = app(\App\Services\VisitSlotsService::class);
        
        if (!$visitSlotsService->isSlotAvailable($visitDate, $startTime, $endTime)) {
            $validator->errors()->add(
                'start_time',
                'Waktu yang dipilih sudah terpesan. Silakan pilih waktu lain.'
            );
        }
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'visitor_name' => 'nama pengunjung',
            'visitor_email' => 'email pengunjung',
            'visitor_phone' => 'nomor telepon',
            'institution' => 'instansi/organisasi',
            'visit_date' => 'tanggal kunjungan',
            'start_time' => 'waktu mulai',
            'end_time' => 'waktu selesai',
            'visit_purpose' => 'tujuan kunjungan',
            'group_size' => 'jumlah peserta',
            'purpose_description' => 'deskripsi tujuan',
            'special_requirements' => 'kebutuhan khusus',
            'request_letter' => 'surat permohonan',
        ];
    }
}