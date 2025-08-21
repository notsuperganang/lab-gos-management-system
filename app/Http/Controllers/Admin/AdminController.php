<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware is handled by routes (web.php) - no need to apply here in Laravel 11+
    }

    /**
     * Show the admin dashboard.
     */
    public function dashboard(DashboardService $dashboardService): View
    {
        try {
            // Default date range - last 30 days
            $dateFrom = now()->subDays(30)->format('Y-m-d');
            $dateTo = now()->format('Y-m-d');

            // Fetch dashboard statistics
            $dashboardStats = $dashboardService->getDashboardStats($dateFrom, $dateTo);

            // Fetch recent activities (limit to 10 for initial load)
            $recentActivities = $dashboardService->getRecentActivities(10);

            // Prepare data for Alpine.js injection
            $stats = [
                'summary' => $dashboardStats['summary'] ?? [],
                'equipment_analytics' => $dashboardStats['equipment_analytics'] ?? [],
                'quick_insights' => $dashboardStats['quick_insights'] ?? [],
                'alerts' => $dashboardStats['alerts'] ?? [],
                'autoRefresh' => true
            ];

            return view('admin.dashboard', compact('stats', 'recentActivities'));

        } catch (\Exception $e) {
            \Log::error('Dashboard loading error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'exception' => $e,
            ]);

            // Provide fallback empty data to prevent Alpine errors
            $stats = [
                'summary' => [
                    'total_pending_requests' => 0,
                    'total_equipment' => 0,
                    'available_equipment' => 0,
                    'equipment_utilization_rate' => 0,
                    'total_active_requests' => 0,
                    'pending_borrow_requests' => 0,
                    'active_borrow_requests' => 0,
                    'pending_visit_requests' => 0,
                    'active_visit_requests' => 0,
                    'pending_testing_requests' => 0,
                    'active_testing_requests' => 0,
                    'pending_trend' => 0
                ],
                'equipment_analytics' => [],
                'quick_insights' => ['most_requested_equipment' => []],
                'alerts' => [],
                'autoRefresh' => true
            ];
            $recentActivities = [];

            return view('admin.dashboard', compact('stats', 'recentActivities'))
                ->with('error', 'Failed to load dashboard data. Please try refreshing the page.');
        }
    }

    // PROFIL DAN PUBLIKASI (Profile & Publications)

    /**
     * Show site settings management page.
     */
    public function siteSettings(): View
    {
        return view('admin.site-settings.index');
    }

    /**
     * Show edit site settings page.
     */
    public function editSiteSettings(): View
    {
        return view('admin.site-settings.edit');
    }

    /**
     * Update site settings.
     */
    public function updateSiteSettings(Request $request)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.site-settings.index')
                        ->with('success', 'Site settings updated successfully.');
    }

    /**
     * Show staff management page.
     */
    public function staffIndex(): View
    {
        return view('admin.staff.index');
    }

    /**
     * Show create staff page.
     */
    public function staffCreate(): View
    {
        return view('admin.staff.create');
    }

    /**
     * Store new staff member.
     */
    public function staffStore(Request $request)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.staff.index')
                        ->with('success', 'Staff member created successfully.');
    }

    /**
     * Show staff details.
     */
    public function staffShow($id): View
    {
        return view('admin.staff.show', compact('id'));
    }

    /**
     * Show edit staff page.
     */
    public function staffEdit($id): View
    {
        return view('admin.staff.edit', compact('id'));
    }

    /**
     * Update staff member.
     */
    public function staffUpdate(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.staff.index')
                        ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Delete staff member.
     */
    public function staffDestroy($id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.staff.index')
                        ->with('success', 'Staff member deleted successfully.');
    }

    /**
     * Show articles management page.
     */
    public function articlesIndex(): View
    {
        return view('admin.articles.index');
    }

    /**
     * Show create article page.
     */
    public function articlesCreate(): View
    {
        return view('admin.articles.create');
    }

    /**
     * Store new article.
     */
    public function articlesStore(Request $request)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.articles.index')
                        ->with('success', 'Article created successfully.');
    }

    /**
     * Show article details.
     */
    public function articlesShow($id): View
    {
        return view('admin.articles.show', compact('id'));
    }

    /**
     * Show edit article page.
     */
    public function articlesEdit($id): View
    {
        return view('admin.articles.edit', compact('id'));
    }

    /**
     * Update article.
     */
    public function articlesUpdate(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.articles.index')
                        ->with('success', 'Article updated successfully.');
    }

    /**
     * Delete article.
     */
    public function articlesDestroy($id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.articles.index')
                        ->with('success', 'Article deleted successfully.');
    }

    /**
     * Show gallery management page.
     */
    public function galleryIndex(): View
    {
        return view('admin.gallery.index');
    }

    /**
     * Show create gallery item page.
     */
    public function galleryCreate(): View
    {
        return view('admin.gallery.create');
    }

    /**
     * Store new gallery item.
     */
    public function galleryStore(Request $request)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.gallery.index')
                        ->with('success', 'Gallery item created successfully.');
    }

    /**
     * Show gallery item details.
     */
    public function galleryShow($id): View
    {
        return view('admin.gallery.show', compact('id'));
    }

    /**
     * Show edit gallery item page.
     */
    public function galleryEdit($id): View
    {
        return view('admin.gallery.edit', compact('id'));
    }

    /**
     * Update gallery item.
     */
    public function galleryUpdate(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.gallery.index')
                        ->with('success', 'Gallery item updated successfully.');
    }

    /**
     * Delete gallery item.
     */
    public function galleryDestroy($id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.gallery.index')
                        ->with('success', 'Gallery item deleted successfully.');
    }

    // SARANA DAN PENJADWALAN (Facilities & Scheduling)

    /**
     * Show equipment management page.
     */
    public function equipmentIndex(): View
    {
        return view('admin.equipment.index');
    }

    /**
     * Show create equipment page.
     */
    public function equipmentCreate(): View
    {
        return view('admin.equipment.create');
    }

    /**
     * Store new equipment.
     */
    public function equipmentStore(Request $request)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.equipment.index')
                        ->with('success', 'Equipment created successfully.');
    }

    /**
     * Show equipment details.
     */
    public function equipmentShow($id): View
    {
        return view('admin.equipment.show', compact('id'));
    }

    /**
     * Show edit equipment page.
     */
    public function equipmentEdit($id): View
    {
        return view('admin.equipment.edit', compact('id'));
    }

    /**
     * Update equipment.
     */
    public function equipmentUpdate(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.equipment.index')
                        ->with('success', 'Equipment updated successfully.');
    }

    /**
     * Delete equipment.
     */
    public function equipmentDestroy($id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.equipment.index')
                        ->with('success', 'Equipment deleted successfully.');
    }

    /**
     * Show visit schedule management page.
     */
    public function visitScheduleIndex(): View
    {
        return view('admin.visit-schedule.index');
    }

    /**
     * Show visit schedule calendar.
     */
    public function visitScheduleCalendar(): View
    {
        return view('admin.visit-schedule.calendar');
    }

    /**
     * Block a time slot.
     */
    public function blockTimeSlot(Request $request)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.visit-schedule.index')
                        ->with('success', 'Time slot blocked successfully.');
    }

    /**
     * Unblock a time slot.
     */
    public function unblockTimeSlot($id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.visit-schedule.index')
                        ->with('success', 'Time slot unblocked successfully.');
    }

    // LAYANAN LABORATORIUM (Laboratory Services)

    /**
     * Show borrowing requests management page.
     */
    public function borrowingIndex(): View
    {
        return view('admin.borrowing.index');
    }

    /**
     * Show borrowing request details.
     */
    public function borrowingShow($id): View
    {
        return view('admin.borrowing.show', compact('id'));
    }

    /**
     * Approve borrowing request.
     */
    public function borrowingApprove(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.borrowing.show', $id)
                        ->with('success', 'Borrowing request approved successfully.');
    }

    /**
     * Reject borrowing request.
     */
    public function borrowingReject(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.borrowing.show', $id)
                        ->with('success', 'Borrowing request rejected.');
    }

    /**
     * Complete borrowing request.
     */
    public function borrowingComplete(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.borrowing.show', $id)
                        ->with('success', 'Borrowing request completed.');
    }

    /**
     * Show visit requests management page.
     */
    public function visitsIndex(): View
    {
        return view('admin.visits.index');
    }

    /**
     * Show visit request details.
     */
    public function visitsShow($id): View
    {
        return view('admin.visits.show', compact('id'));
    }

    /**
     * Approve visit request.
     */
    public function visitsApprove(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.visits.show', $id)
                        ->with('success', 'Visit request approved successfully.');
    }

    /**
     * Reject visit request.
     */
    public function visitsReject(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.visits.show', $id)
                        ->with('success', 'Visit request rejected.');
    }

    /**
     * Complete visit request.
     */
    public function visitsComplete(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.visits.show', $id)
                        ->with('success', 'Visit request completed.');
    }

    /**
     * Show testing requests management page.
     */
    public function testingIndex(): View
    {
        return view('admin.testing.index');
    }

    /**
     * Show testing request details.
     */
    public function testingShow($id): View
    {
        return view('admin.testing.show', compact('id'));
    }

    /**
     * Approve testing request.
     */
    public function testingApprove(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.testing.show', $id)
                        ->with('success', 'Testing request approved successfully.');
    }

    /**
     * Reject testing request.
     */
    public function testingReject(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.testing.show', $id)
                        ->with('success', 'Testing request rejected.');
    }

    /**
     * Complete testing request.
     */
    public function testingComplete(Request $request, $id)
    {
        // Implementation will use API endpoints
        return redirect()->route('admin.testing.show', $id)
                        ->with('success', 'Testing request completed.');
    }
}
