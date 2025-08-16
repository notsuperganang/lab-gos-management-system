<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class SiteController extends Controller
{
    /**
     * Get site settings and laboratory information
     * 
     * Returns laboratory information including name, contact details,
     * operational hours, services offered, and rules.
     */
    public function settings(): JsonResponse
    {
        try {
            // Get site settings from database
            $siteSettings = SiteSetting::all()->pluck('content', 'key');
            
            // Get lab configuration
            $labConfig = config('lab');
            
            // Merge database settings with lab config
            $settings = [
                'lab' => [
                    'name' => $labConfig['name'],
                    'code' => $labConfig['code'],
                    'department' => $labConfig['department'],
                    'address' => $labConfig['address'],
                    'vision' => $labConfig['vision'],
                    'colors' => $labConfig['colors'],
                ],
                'contact' => $labConfig['contact'],
                'operational_hours' => $labConfig['operational_hours'],
                'services' => $labConfig['services'],
                'rules' => $labConfig['rules'],
                'forms' => $labConfig['forms'],
                'site_settings' => $siteSettings,
                'whatsapp_admin_phones' => config('whatsapp.admin_phones', []),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Site settings retrieved successfully',
                'data' => $settings
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve site settings',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}