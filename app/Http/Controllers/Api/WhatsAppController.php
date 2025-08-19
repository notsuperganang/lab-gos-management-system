<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BorrowRequest;
use App\Models\TestingRequest;
use App\Models\VisitRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class WhatsAppController extends Controller
{


    /**
     * Get admin WhatsApp templates
     */
    public function adminTemplates(Request $request): JsonResponse
    {
        try {
            $requestType = $request->query('request_type');

            // Validate request type
            if (!in_array($requestType, ['borrow', 'visit', 'testing'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request type. Must be "borrow", "visit", or "testing".'
                ], 400);
            }

            $templates = config("whatsapp.admin_templates.{$requestType}", []);

            // Format templates for API response
            $formattedTemplates = [];
            foreach ($templates as $key => $template) {
                $formattedTemplates[] = [
                    'id' => $template['id'],
                    'name' => $template['name'],
                    'preview' => $this->truncateText($template['template'], 100),
                    'placeholders' => $template['placeholders']
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedTemplates
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get admin WhatsApp templates', [
                'request_type' => $requestType,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil template WhatsApp.'
            ], 500);
        }
    }

    /**
     * Generate WhatsApp link for admin to message user
     */
    public function adminGenerateLink(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validated = $request->validate([
                'request_type' => ['required', Rule::in(['borrow', 'visit', 'testing'])],
                'request_id' => 'required|string',
                'template_id' => 'required|string',
                'notes' => 'nullable|string|max:500'
            ]);

            // Get the request model
            $requestModel = $this->getRequestModel($validated['request_type'], $validated['request_id']);

            if (!$requestModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found.'
                ], 404);
            }

            // Get recipient phone number
            $phoneNumber = $this->getRequestPhoneNumber($validated['request_type'], $requestModel);
            $normalizedPhone = $this->normalizePhoneNumber($phoneNumber);

            if (!$normalizedPhone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or missing phone number.'
                ], 422);
            }

            // Get template
            $template = $this->getTemplate($validated['request_type'], $validated['template_id']);

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found.'
                ], 404);
            }

            // Build message from template
            $message = $this->buildMessageFromTemplate($template, $requestModel, $validated['request_type']);

            // Append notes if provided
            if (!empty($validated['notes'])) {
                $message .= "\n\nCatatan: " . $validated['notes'];
            }

            // Generate WhatsApp URL
            $url = $this->generateWhatsAppUrl($normalizedPhone, $message);

            // Log activity
            $this->logWhatsAppActivity($validated['request_type'], $validated['request_id'], $normalizedPhone, 'admin');

            return response()->json([
                'success' => true,
                'data' => [
                    'to' => $normalizedPhone,
                    'message' => $message,
                    'url' => $url
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to generate admin WhatsApp link', [
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat link WhatsApp.'
            ], 500);
        }
    }

    /**
     * Get request model based on type
     */
    private function getRequestModel(string $type, string $requestId)
    {
        switch ($type) {
            case 'borrow':
                return BorrowRequest::where('request_id', $requestId)->first();
            case 'visit':
                return VisitRequest::where('request_id', $requestId)->first();
            case 'testing':
                return TestingRequest::where('request_id', $requestId)->first();
            default:
                return null;
        }
    }

    /**
     * Get phone number from request model
     */
    private function getRequestPhoneNumber(string $type, $requestModel): string
    {
        switch ($type) {
            case 'borrow':
                return $requestModel->supervisor_phone ?? '';
            case 'visit':
                return $requestModel->visitor_phone ?? '';
            case 'testing':
                return $requestModel->client_phone ?? '';
            default:
                return '';
        }
    }

    /**
     * Normalize phone number to E.164 format
     */
    private function normalizePhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digits
        $phone = preg_replace('/\D/', '', $phoneNumber);

        // Handle Indonesian numbers
        if (str_starts_with($phone, '0')) {
            // Replace leading 0 with +62
            $phone = '+62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '62')) {
            // Add + if missing
            $phone = '+' . $phone;
        } elseif (!str_starts_with($phone, '+')) {
            // Assume Indonesian number without country code
            $phone = '+62' . $phone;
        }

        return $phone;
    }

    /**
     * Get template by request type and template ID
     */
    private function getTemplate(string $requestType, string $templateId): ?array
    {
        $templates = config("whatsapp.admin_templates.{$requestType}", []);

        foreach ($templates as $template) {
            if ($template['id'] === $templateId) {
                return $template;
            }
        }

        return null;
    }

    /**
     * Build message from template with placeholders
     */
    private function buildMessageFromTemplate(array $template, $requestModel, string $requestType): string
    {
        $message = $template['template'];
        $placeholders = $this->resolvePlaceholders($requestModel, $requestType);

        foreach ($placeholders as $placeholder => $value) {
            $message = str_replace("{{$placeholder}}", $value, $message);
        }

        return $message;
    }

    /**
     * Resolve placeholders from request model
     */
    private function resolvePlaceholders($requestModel, string $requestType): array
    {
        $defaultValues = config('whatsapp.default_values', []);
        $currentUser = Auth::user();

        $placeholders = [
            'REQUEST_ID' => $requestModel->request_id,
            'LAB_ADDRESS' => $defaultValues['LAB_ADDRESS'] ?? 'Lab GOS USK',
            'ADMIN_NAME' => $currentUser ? $currentUser->name : ($defaultValues['ADMIN_NAME'] ?? 'Tim Admin Lab GOS')
        ];

        switch ($requestType) {
            case 'borrow':
                $placeholders = array_merge($placeholders, [
                    'NAME' => $requestModel->supervisor_name,
                    'PHONE' => $requestModel->supervisor_phone,
                    'BORROW_DATES' => $requestModel->borrow_date->format('d M Y') . ' - ' . $requestModel->return_date->format('d M Y'),
                    'RETURN_DATE' => $requestModel->return_date->format('d M Y'),
                    'EQUIPMENT_LIST' => $this->formatEquipmentList($requestModel->borrowRequestItems),
                    'DURATION_DAYS' => $requestModel->borrow_date->diffInDays($requestModel->return_date)
                ]);
                break;

            case 'visit':
                $placeholders = array_merge($placeholders, [
                    'NAME' => $requestModel->visitor_name,
                    'PHONE' => $requestModel->visitor_phone,
                    'DATE' => $requestModel->visit_date->format('d M Y'),
                    'TIME' => $requestModel->visit_time,
                    'GROUP_SIZE' => $requestModel->group_size
                ]);
                break;

            case 'testing':
                $placeholders = array_merge($placeholders, [
                    'NAME' => $requestModel->client_name,
                    'PHONE' => $requestModel->client_phone,
                    'SAMPLE_NAME' => $requestModel->sample_name,
                    'TESTING_TYPE' => $this->getTestingTypeLabel($requestModel->testing_type),
                    'DATE' => $requestModel->sample_delivery_schedule ? $requestModel->sample_delivery_schedule->format('d M Y') : 'Belum ditentukan',
                    'DURATION_DAYS' => $requestModel->estimated_duration ?? 'Belum ditentukan',
                    'EST_COMPLETION_DATE' => $requestModel->completion_date ? $requestModel->completion_date->format('d M Y') : 'Belum ditentukan',
                    'COMPLETION_DATE' => $requestModel->completion_date ? $requestModel->completion_date->format('d M Y') : 'Belum ditentukan',
                    'COST_IDR' => $requestModel->cost ? $this->formatCurrency($requestModel->cost) : 'Belum ditentukan'
                ]);
                break;
        }

        return $placeholders;
    }

    /**
     * Get testing type label
     */
    private function getTestingTypeLabel(string $testingType): string
    {
        $labels = [
            'uv_vis_spectroscopy' => 'UV-Vis Spektroskopi',
            'ftir_spectroscopy' => 'FTIR Spektroskopi',
            'optical_microscopy' => 'Mikroskopi Optik',
            'custom' => 'Pengujian Khusus'
        ];

        return $labels[$testingType] ?? $testingType;
    }

    /**
     * Format currency to IDR
     */
    private function formatCurrency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Generate WhatsApp URL
     */
    private function generateWhatsAppUrl(string $phoneNumber, string $message): string
    {
        $encodedMessage = urlencode($message);
        return "https://wa.me/{$phoneNumber}?text={$encodedMessage}";
    }

    /**
     * Log WhatsApp activity
     */
    private function logWhatsAppActivity(string $requestType, string $requestId, string $phoneNumber, string $direction): void
    {
        try {
            ActivityLog::create([
                'log_name' => 'whatsapp',
                'description' => "WhatsApp link generated for {$direction} communication",
                'subject_type' => 'whatsapp_link',
                'subject_id' => null,
                'causer_type' => Auth::user() ? get_class(Auth::user()) : null,
                'causer_id' => Auth::id(),
                'properties' => json_encode([
                    'request_type' => $requestType,
                    'request_id' => $requestId,
                    'direction' => $direction,
                    'to' => $phoneNumber
                ]),
                'event' => 'whatsapp.link.generated',
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log WhatsApp activity', [
                'error' => $e->getMessage(),
                'request_type' => $requestType,
                'request_id' => $requestId
            ]);
        }
    }

    /**
     * Truncate text for preview
     */
    private function truncateText(string $text, int $length = 100): string
    {
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }


    /**
     * Format equipment list for borrow requests
     */
    private function formatEquipmentList($borrowRequestItems): string
    {
        if (!$borrowRequestItems || $borrowRequestItems->isEmpty()) {
            return 'Tidak ada alat';
        }

        $list = [];
        foreach ($borrowRequestItems as $index => $item) {
            $equipment = $item->equipment;
            $list[] = ($index + 1) . ". {$equipment->name} ({$item->quantity_requested} unit)";
        }

        return implode("\n", $list);
    }

}
