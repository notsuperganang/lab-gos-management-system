<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsAppService
{
    private $apiUrl;
    private $phoneNumberId;
    private $accessToken;
    private $timeout;
    private $maxRetries;
    private $retryDelay;

    public function __construct()
    {
        $this->apiUrl = config('whatsapp.api_url');
        $this->phoneNumberId = config('whatsapp.phone_number_id');
        $this->accessToken = config('whatsapp.access_token');
        $this->timeout = config('whatsapp.timeout', 30);
        $this->maxRetries = config('whatsapp.max_retries', 3);
        $this->retryDelay = config('whatsapp.retry_delay', 5);
    }

    /**
     * Send WhatsApp message to a phone number
     */
    public function sendMessage(string $phoneNumber, string $message): array
    {
        if (!config('whatsapp.enabled', false)) {
            Log::info('WhatsApp is disabled, message not sent', [
                'phone' => $this->maskPhoneNumber($phoneNumber),
                'message_preview' => substr($message, 0, 50) . '...'
            ]);
            return ['success' => false, 'message' => 'WhatsApp service is disabled'];
        }

        if (!$this->accessToken || !$this->phoneNumberId) {
            Log::error('WhatsApp configuration incomplete');
            return ['success' => false, 'message' => 'WhatsApp configuration incomplete'];
        }

        $phoneNumber = $this->normalizePhoneNumber($phoneNumber);
        
        if (!$this->validatePhoneNumber($phoneNumber)) {
            Log::error('Invalid phone number format', ['phone' => $this->maskPhoneNumber($phoneNumber)]);
            return ['success' => false, 'message' => 'Invalid phone number format'];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phoneNumber,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ];

        return $this->sendWithRetry($payload, $phoneNumber);
    }

    /**
     * Send message using template
     */
    public function sendTemplateMessage(string $phoneNumber, string $templateName, array $variables = []): array
    {
        $template = $this->loadTemplate($templateName);
        
        if (!$template) {
            Log::error('Template not found', ['template' => $templateName]);
            return ['success' => false, 'message' => 'Template not found'];
        }

        $message = $this->replaceVariables($template, $variables);
        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Send message to multiple phone numbers (admin notification)
     */
    public function sendToAdmins(string $templateName, array $variables = []): array
    {
        $adminNumbers = array_filter(config('whatsapp.admin_numbers', []));
        
        if (empty($adminNumbers)) {
            Log::warning('No admin phone numbers configured for WhatsApp notifications');
            return ['success' => false, 'message' => 'No admin numbers configured'];
        }

        $results = [];
        foreach ($adminNumbers as $adminNumber) {
            if ($adminNumber) {
                $result = $this->sendTemplateMessage($adminNumber, $templateName, $variables);
                $results[] = array_merge($result, ['phone' => $this->maskPhoneNumber($adminNumber)]);
            }
        }

        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $totalCount = count($results);

        Log::info('Admin notification sent', [
            'template' => $templateName,
            'success_count' => $successCount,
            'total_count' => $totalCount
        ]);

        return [
            'success' => $successCount > 0,
            'message' => "Sent to {$successCount}/{$totalCount} admins",
            'results' => $results
        ];
    }

    /**
     * Load message template from storage
     */
    private function loadTemplate(string $templateName): ?string
    {
        $templatePath = config('whatsapp.templates_path') . "/{$templateName}.txt";
        
        if (!file_exists($templatePath)) {
            return null;
        }

        return file_get_contents($templatePath);
    }

    /**
     * Replace variables in template
     */
    private function replaceVariables(string $template, array $variables): string
    {
        // Add default lab information
        $defaultVariables = [
            'lab_name' => config('whatsapp.lab_name'),
            'lab_phone' => config('whatsapp.lab_phone'),
            'lab_email' => config('whatsapp.lab_email'),
            'working_hours' => config('whatsapp.working_hours'),
            'lab_address' => 'Gedung FMIPA USK, Darussalam, Banda Aceh'
        ];

        $variables = array_merge($defaultVariables, $variables);

        foreach ($variables as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            } elseif (is_bool($value)) {
                $value = $value ? 'Ya' : 'Tidak';
            } elseif (is_null($value)) {
                $value = '-';
            }

            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }

    /**
     * Normalize phone number to international format
     */
    private function normalizePhoneNumber(string $phoneNumber): string
    {
        // Remove all non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If starts with 0, replace with country code
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = config('whatsapp.country_code') . substr($phoneNumber, 1);
        }
        
        // If doesn't start with +, add it
        if (!str_starts_with($phoneNumber, '+')) {
            // If doesn't start with 62 (Indonesia code), add it
            if (!str_starts_with($phoneNumber, '62')) {
                $phoneNumber = config('whatsapp.country_code') . $phoneNumber;
            } else {
                $phoneNumber = '+' . $phoneNumber;
            }
        }
        
        return $phoneNumber;
    }

    /**
     * Validate phone number format
     */
    private function validatePhoneNumber(string $phoneNumber): bool
    {
        // Indonesian phone number validation
        return preg_match('/^\+62\d{8,13}$/', $phoneNumber);
    }

    /**
     * Mask phone number for logging privacy
     */
    private function maskPhoneNumber(string $phoneNumber): string
    {
        if (strlen($phoneNumber) > 6) {
            return substr($phoneNumber, 0, 3) . '***' . substr($phoneNumber, -3);
        }
        return '***';
    }

    /**
     * Send API request with retry mechanism
     */
    private function sendWithRetry(array $payload, string $phoneNumber): array
    {
        $url = "{$this->apiUrl}/{$this->phoneNumberId}/messages";
        
        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->accessToken,
                        'Content-Type' => 'application/json',
                    ])
                    ->post($url, $payload);

                if ($response->successful()) {
                    $responseData = $response->json();
                    
                    Log::info('WhatsApp message sent successfully', [
                        'phone' => $this->maskPhoneNumber($phoneNumber),
                        'message_id' => $responseData['messages'][0]['id'] ?? null,
                        'attempt' => $attempt
                    ]);

                    return [
                        'success' => true,
                        'message' => 'Message sent successfully',
                        'message_id' => $responseData['messages'][0]['id'] ?? null,
                        'attempt' => $attempt
                    ];
                }

                $error = $response->json();
                Log::warning('WhatsApp API error', [
                    'phone' => $this->maskPhoneNumber($phoneNumber),
                    'status' => $response->status(),
                    'error' => $error,
                    'attempt' => $attempt
                ]);

                // Don't retry for client errors (4xx)
                if ($response->status() >= 400 && $response->status() < 500) {
                    break;
                }

            } catch (\Exception $e) {
                Log::warning('WhatsApp service exception', [
                    'phone' => $this->maskPhoneNumber($phoneNumber),
                    'error' => $e->getMessage(),
                    'attempt' => $attempt
                ]);
            }

            // Wait before retry (except for the last attempt)
            if ($attempt < $this->maxRetries) {
                sleep($this->retryDelay);
            }
        }

        return [
            'success' => false,
            'message' => 'Failed to send message after ' . $this->maxRetries . ' attempts',
            'attempts' => $this->maxRetries
        ];
    }

    /**
     * Get service health status
     */
    public function getHealthStatus(): array
    {
        return [
            'enabled' => config('whatsapp.enabled', false),
            'configured' => !empty($this->accessToken) && !empty($this->phoneNumberId),
            'api_url' => $this->apiUrl,
            'admin_numbers_count' => count(array_filter(config('whatsapp.admin_numbers', []))),
            'templates_path' => config('whatsapp.templates_path'),
            'templates_exist' => file_exists(config('whatsapp.templates_path'))
        ];
    }
}