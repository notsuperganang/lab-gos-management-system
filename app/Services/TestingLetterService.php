<?php

namespace App\Services;

use App\Models\TestingRequest;
use App\Models\SiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TestingLetterService
{
    /**
     * Generate PDF authorization letter for testing request
     *
     * @param TestingRequest $request
     * @return string Public URL to the generated PDF
     * @throws \Exception
     */
    public function generate(TestingRequest $request): string
    {
        try {
            // Get lab head information from site settings
            $labHeadInfo = $this->getLabHeadInfo();

            // Prepare data for the template
            $data = [
                'request' => $request,
                'labHead' => $labHeadInfo
            ];

            // Generate PDF using DomPDF with the Blade template
            $pdf = Pdf::loadView('letters.testing', $data)
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
            Log::info('Testing authorization letter PDF generated successfully', [
                'request_id' => $request->request_id,
                'file_path' => $filePath,
                'file_size' => Storage::disk('public')->size($filePath),
                'client_name' => $request->client_name,
                'client_organization' => $request->client_organization,
                'testing_type' => $request->testing_type,
                'sample_name' => $request->sample_name,
                'lab_head' => $labHeadInfo['name'] ?? 'Unknown'
            ]);

            // Return public URL
            return Storage::disk('public')->url($filePath);

        } catch (\Exception $e) {
            Log::error('Failed to generate testing authorization letter PDF', [
                'request_id' => $request->request_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Failed to generate testing authorization letter PDF: ' . $e->getMessage());
        }
    }

    /**
     * Check if letter PDF exists for a testing request
     *
     * @param TestingRequest $request
     * @return bool
     */
    public function exists(TestingRequest $request): bool
    {
        $fileName = $request->request_id . '.pdf';
        $filePath = 'letters/' . $fileName;

        return Storage::disk('public')->exists($filePath);
    }

    /**
     * Get the public URL for an existing letter PDF
     *
     * @param TestingRequest $request
     * @return string|null
     */
    public function getUrl(TestingRequest $request): ?string
    {
        if (!$this->exists($request)) {
            return null;
        }

        $fileName = $request->request_id . '.pdf';
        $filePath = 'letters/' . $fileName;

        return Storage::disk('public')->url($filePath);
    }

    /**
     * Delete the letter PDF for a testing request
     *
     * @param TestingRequest $request
     * @return bool
     */
    public function delete(TestingRequest $request): bool
    {
        try {
            $fileName = $request->request_id . '.pdf';
            $filePath = 'letters/' . $fileName;

            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);

                Log::info('Testing authorization letter PDF deleted', [
                    'request_id' => $request->request_id,
                    'file_path' => $filePath
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to delete testing authorization letter PDF', [
                'request_id' => $request->request_id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get or generate letter PDF for a testing request
     * Returns existing PDF if available, generates new one if missing and request is approved
     *
     * @param TestingRequest $request
     * @return string|null Public URL to PDF or null if not available/applicable
     * @throws \Exception
     */
    public function getOrGenerate(TestingRequest $request): ?string
    {
        // Check if PDF already exists
        if ($this->exists($request)) {
            return $this->getUrl($request);
        }

        // Only generate for approved or later status requests
        if (!in_array($request->status, ['approved', 'sample_received', 'in_progress', 'completed'])) {
            return null;
        }

        // Generate and return new PDF
        return $this->generate($request);
    }

    /**
     * Regenerate letter PDF (delete existing and create new)
     *
     * @param TestingRequest $request
     * @return string Public URL to the regenerated PDF
     * @throws \Exception
     */
    public function regenerate(TestingRequest $request): string
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