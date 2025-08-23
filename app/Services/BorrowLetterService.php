<?php

namespace App\Services;

use App\Models\BorrowRequest;
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
            
            // Generate PDF using DomPDF with the Blade template
            $pdf = Pdf::loadView('letters.borrow', ['request' => $request])
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'DejaVu Sans',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                    'debugKeepTemp' => false,
                ]);
            
            // Generate file path
            $fileName = $request->request_id . '.pdf';
            $filePath = 'letters/' . $fileName;
            $fullPath = 'public/letters/' . $fileName;
            
            // Ensure directory exists
            $directory = storage_path('app/public/letters');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Save PDF to storage
            Storage::put($fullPath, $pdf->output());
            
            // Log successful generation
            Log::info('Borrow letter PDF generated successfully', [
                'request_id' => $request->request_id,
                'file_path' => $filePath,
                'file_size' => Storage::size($fullPath)
            ]);
            
            // Return public URL
            return Storage::url($filePath);
            
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
        $filePath = 'public/letters/' . $fileName;
        
        return Storage::exists($filePath);
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
        
        return Storage::url($filePath);
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
            $filePath = 'public/letters/' . $fileName;
            
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
                
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
}