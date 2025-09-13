<?php

namespace App\Services;

use App\Models\BorrowRequest;
use App\Models\SiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BorrowLetterService
{
    /**
     * Generate PDF letter for borrow request
     *
     * @param BorrowRequest $request
     * @return string Public URL to the generated PDF
     * @throws \Exception
     */
    public function generate(BorrowRequest $request): string
    {
        try {
            // Eager load required relationships
            $request->load(['borrowRequestItems.equipment']);

            // Get lab head information from site settings
            $labHeadInfo = $this->getLabHeadInfo();

            // Prepare data for the template
            $data = [
                'request' => $request,
                'labHead' => $labHeadInfo
            ];

            // Generate PDF using DomPDF with the Blade template
            $pdf = Pdf::loadView('letters.borrow', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'DejaVu Sans',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                    'debugKeepTemp' => false,
                    'chroot' => public_path(),
                    'allowLocalFileAccess' => true,
                ]);

            // Generate file path
            $fileName = $request->request_id . '.pdf';
            $filePath = 'letters/' . $fileName;

            // Ensure directory exists
            $directory = storage_path('app/public/letters');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save PDF to storage using public disk
            Storage::disk('public')->put($filePath, $pdf->output());

            // Log successful generation
            Log::info('Borrow letter PDF generated successfully', [
                'request_id' => $request->request_id,
                'file_path' => $filePath,
                'file_size' => Storage::disk('public')->size($filePath),
                'lab_head' => $labHeadInfo['name'] ?? 'Unknown'
            ]);

            // Return public URL
            return Storage::disk('public')->url($filePath);

        } catch (\Exception $e) {
            Log::error('Failed to generate borrow letter PDF', [
                'request_id' => $request->request_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Failed to generate borrow letter PDF: ' . $e->getMessage());
        }
    }

    /**
     * Check if letter PDF exists for a borrow request
     *
     * @param BorrowRequest $request
     * @return bool
     */
    public function exists(BorrowRequest $request): bool
    {
        $fileName = $request->request_id . '.pdf';
        $filePath = 'letters/' . $fileName;

        return Storage::disk('public')->exists($filePath);
    }

    /**
     * Get the public URL for an existing letter PDF
     *
     * @param BorrowRequest $request
     * @return string|null
     */
    public function getUrl(BorrowRequest $request): ?string
    {
        if (!$this->exists($request)) {
            return null;
        }

        $fileName = $request->request_id . '.pdf';
        $filePath = 'letters/' . $fileName;

        return Storage::disk('public')->url($filePath);
    }

    /**
     * Delete the letter PDF for a borrow request
     *
     * @param BorrowRequest $request
     * @return bool
     */
    public function delete(BorrowRequest $request): bool
    {
        try {
            $fileName = $request->request_id . '.pdf';
            $filePath = 'letters/' . $fileName;

            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);

                Log::info('Borrow letter PDF deleted', [
                    'request_id' => $request->request_id,
                    'file_path' => $filePath
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to delete borrow letter PDF', [
                'request_id' => $request->request_id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get or generate letter PDF for a borrow request
     * Returns existing PDF if available, generates new one if missing and request is approved
     *
     * @param BorrowRequest $request
     * @return string|null Public URL to PDF or null if not available/applicable
     * @throws \Exception
     */
    public function getOrGenerate(BorrowRequest $request): ?string
    {
        // Check if PDF already exists
        if ($this->exists($request)) {
            return $this->getUrl($request);
        }

        // Only generate for approved requests
        if ($request->status !== 'approved' && $request->status !== 'active' && $request->status !== 'completed') {
            return null;
        }

        // Generate and return new PDF
        return $this->generate($request);
    }

    /**
     * Regenerate letter PDF (delete existing and create new)
     *
     * @param BorrowRequest $request
     * @return string Public URL to the regenerated PDF
     * @throws \Exception
     */
    public function regenerate(BorrowRequest $request): string
    {
        // Delete existing PDF if it exists
        $this->delete($request);

        // Generate new PDF
        return $this->generate($request);
    }

    /**
     * Get lab head information from site settings
     *
     * @return array Lab head information with fallbacks
     */
    private function getLabHeadInfo(): array
    {
        try {
            $labHeadSetting = SiteSetting::getValue('lab_head');

            if (is_array($labHeadSetting)) {
                return [
                    'name' => $labHeadSetting['name'] ?? 'Kepala Laboratorium',
                    'nip' => $labHeadSetting['nip'] ?? '',
                    'email' => $labHeadSetting['email'] ?? '',
                    'phone' => $labHeadSetting['phone'] ?? '',
                    'office' => $labHeadSetting['office'] ?? ''
                ];
            }

            // If settings are not found or malformed, return defaults
            return [
                'name' => '',
                'nip' => '',
                'email' => '',
                'phone' => '',
                'office' => ''
            ];

        } catch (\Exception $e) {
            Log::warning('Failed to fetch lab head info from settings, using defaults', [
                'error' => $e->getMessage()
            ]);

            return [
                'name' => '',
                'nip' => '',
                'email' => '',
                'phone' => '',
                'office' => ''
            ];
        }
    }
}
