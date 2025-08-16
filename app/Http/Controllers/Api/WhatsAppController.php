<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\VisitRequest;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WhatsAppController extends Controller
{
    private $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Send WhatsApp confirmation message to admin
     */
    public function sendConfirmation(Request $request, string $type, string $requestId): JsonResponse
    {
        try {
            // Validate request type
            if (!in_array($type, ['borrow', 'visit'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request type. Must be "borrow" or "visit".'
                ], 400);
            }

            // Get the appropriate request model
            $requestModel = $this->getRequestModel($type, $requestId);
            
            if (!$requestModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found.'
                ], 404);
            }

            // Check if request can be confirmed (not cancelled)
            if ($requestModel->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot confirm a cancelled request.'
                ], 400);
            }

            // Send confirmation message to user
            $message = $this->buildConfirmationMessage($type, $requestModel);
            $phoneNumber = $this->getRequestPhoneNumber($type, $requestModel);

            $this->whatsAppService->sendTextMessage($phoneNumber, $message);

            // Send interactive button to admin
            $this->whatsAppService->sendInteractiveConfirmToAdmin(
                $phoneNumber,
                "Konfirmasi untuk {$requestModel->request_id}: Tekan tombol di bawah untuk menghubungi admin secara langsung.",
                strtoupper($type),
                $requestModel->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Pesan konfirmasi WhatsApp berhasil dikirim!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp confirmation', [
                'type' => $type,
                'request_id' => $requestId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan WhatsApp. Silakan coba lagi.'
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
                return $requestModel->supervisor_phone;
            case 'visit':
                return $requestModel->visitor_phone;
            default:
                return '';
        }
    }

    /**
     * Build confirmation message based on request type
     */
    private function buildConfirmationMessage(string $type, $requestModel): string
    {
        $baseMessage = "🏛️ *Lab GOS - USK*\n\nHalo Admin Lab GOS USK,\n\n";

        switch ($type) {
            case 'borrow':
                return $baseMessage . "Saya ingin mengkonfirmasi peminjaman alat dengan detail:\n\n" .
                    "📋 *ID Permohonan:* {$requestModel->request_id}\n" .
                    "👤 *Supervisor:* {$requestModel->supervisor_name}\n" .
                    "📞 *Kontak:* {$requestModel->supervisor_phone}\n" .
                    "📧 *Email:* {$requestModel->supervisor_email}\n" .
                    "📅 *Tanggal Peminjaman:* " . $requestModel->borrow_date->format('d M Y') . "\n" .
                    "📅 *Tanggal Pengembalian:* " . $requestModel->return_date->format('d M Y') . "\n" .
                    "🎯 *Tujuan:* {$requestModel->purpose}\n\n" .
                    "🔧 *Alat yang dipinjam:*\n" . $this->formatEquipmentList($requestModel->borrowRequestItems) . "\n\n" .
                    "Mohon konfirmasi dan informasi lebih lanjut mengenai peminjaman ini.\n\n" .
                    "Terima kasih!";

            case 'visit':
                return $baseMessage . "Saya ingin mengkonfirmasi kunjungan laboratorium dengan detail:\n\n" .
                    "📋 *ID Kunjungan:* {$requestModel->request_id}\n" .
                    "👤 *Nama:* {$requestModel->visitor_name}\n" .
                    "🏢 *Institusi:* {$requestModel->institution}\n" .
                    "📞 *Kontak:* {$requestModel->visitor_phone}\n" .
                    "📧 *Email:* {$requestModel->visitor_email}\n" .
                    "📅 *Tanggal Kunjungan:* " . $requestModel->visit_date->format('d M Y') . "\n" .
                    "⏰ *Waktu:* {$requestModel->visit_time}\n" .
                    "👥 *Jumlah Peserta:* {$requestModel->group_size} orang\n" .
                    "🎯 *Tujuan:* " . $requestModel->getPurposeLabelAttribute() . "\n\n" .
                    "Mohon konfirmasi dan informasi lebih lanjut mengenai persiapan kunjungan.\n\n" .
                    "Terima kasih!";

            default:
                return $baseMessage . "Konfirmasi untuk permohonan {$requestModel->request_id}";
        }
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

    /**
     * Get WhatsApp service health status
     */
    public function healthCheck(): JsonResponse
    {
        try {
            $status = $this->whatsAppService->getHealthStatus();
            
            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get WhatsApp service status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}