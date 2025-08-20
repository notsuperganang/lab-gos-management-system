<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\BorrowRequest;
use App\Models\VisitRequest;
use App\Models\TestingRequest;
use App\Models\ActivityLog;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardService $dashboardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = new DashboardService();
        $this->createTestData();
    }

    /**
     * Test getDashboardStats returns correct structure
     */
    public function test_get_dashboard_stats_returns_correct_structure(): void
    {
        $dateFrom = now()->subDays(30)->format('Y-m-d');
        $dateTo = now()->format('Y-m-d');

        $stats = $this->dashboardService->getDashboardStats($dateFrom, $dateTo);

        $this->assertArrayHasKey('summary', $stats);
        $this->assertArrayHasKey('equipment_analytics', $stats);
        $this->assertArrayHasKey('request_analytics', $stats);
        $this->assertArrayHasKey('trend_data', $stats);
        $this->assertArrayHasKey('quick_insights', $stats);
        $this->assertArrayHasKey('alerts', $stats);

        // Test summary structure
        $summary = $stats['summary'];
        $this->assertArrayHasKey('total_pending_requests', $summary);
        $this->assertArrayHasKey('total_equipment', $summary);
        $this->assertArrayHasKey('available_equipment', $summary);
        $this->assertArrayHasKey('equipment_utilization_rate', $summary);

        // Test equipment analytics structure
        $equipmentAnalytics = $stats['equipment_analytics'];
        $this->assertArrayHasKey('total_count', $equipmentAnalytics);
        $this->assertArrayHasKey('availability', $equipmentAnalytics);
        $this->assertArrayHasKey('status_distribution', $equipmentAnalytics);
    }

    /**
     * Test getDashboardStats returns correct data types
     */
    public function test_get_dashboard_stats_returns_correct_data_types(): void
    {
        $dateFrom = now()->subDays(7)->format('Y-m-d');
        $dateTo = now()->format('Y-m-d');

        $stats = $this->dashboardService->getDashboardStats($dateFrom, $dateTo);

        // Test summary data types
        $summary = $stats['summary'];
        $this->assertIsInt($summary['total_pending_requests']);
        $this->assertIsInt($summary['total_equipment']);
        $this->assertIsInt($summary['available_equipment']);
        $this->assertIsFloat($summary['equipment_utilization_rate']);
        $this->assertIsInt($summary['recent_activity_count']);

        // Test equipment analytics data types
        $equipmentAnalytics = $stats['equipment_analytics'];
        $this->assertIsInt($equipmentAnalytics['total_count']);
        $this->assertIsArray($equipmentAnalytics['availability']);
        $this->assertIsArray($equipmentAnalytics['status_distribution']);

        // Test arrays
        $this->assertIsArray($stats['quick_insights']['most_requested_equipment']);
        $this->assertIsArray($stats['alerts']);
        $this->assertIsArray($stats['trend_data']['daily_trends']);
    }

    /**
     * Test getDashboardStats caching functionality
     */
    public function test_get_dashboard_stats_uses_caching(): void
    {
        $dateFrom = now()->subDays(7)->format('Y-m-d');
        $dateTo = now()->format('Y-m-d');

        // Clear cache
        Cache::flush();

        // First call should cache the result
        $stats1 = $this->dashboardService->getDashboardStats($dateFrom, $dateTo);
        
        // Verify cache was used
        $cacheKey = 'dashboard_stats_' . md5($dateFrom . $dateTo);
        $this->assertTrue(Cache::has($cacheKey));

        // Second call should use cached result
        $stats2 = $this->dashboardService->getDashboardStats($dateFrom, $dateTo);
        
        $this->assertEquals($stats1, $stats2);
    }

    /**
     * Test getDashboardStats with cache refresh
     */
    public function test_get_dashboard_stats_with_cache_refresh(): void
    {
        $dateFrom = now()->subDays(7)->format('Y-m-d');
        $dateTo = now()->format('Y-m-d');

        // First call to populate cache
        $this->dashboardService->getDashboardStats($dateFrom, $dateTo);
        
        $cacheKey = 'dashboard_stats_' . md5($dateFrom . $dateTo);
        $this->assertTrue(Cache::has($cacheKey));

        // Call with refresh should clear cache
        $this->dashboardService->getDashboardStats($dateFrom, $dateTo, true);
        
        // Cache should still exist (new data cached)
        $this->assertTrue(Cache::has($cacheKey));
    }

    /**
     * Test getRecentActivities returns correct structure
     */
    public function test_get_recent_activities_returns_correct_structure(): void
    {
        $activities = $this->dashboardService->getRecentActivities(5);

        $this->assertIsArray($activities);
        $this->assertLessThanOrEqual(5, count($activities));

        if (!empty($activities)) {
            $activity = $activities[0];
            $this->assertArrayHasKey('id', $activity);
            $this->assertArrayHasKey('event', $activity);
            $this->assertArrayHasKey('description', $activity);
            $this->assertArrayHasKey('subject_type', $activity);
            $this->assertArrayHasKey('subject_id', $activity);
            $this->assertArrayHasKey('causer', $activity);
            $this->assertArrayHasKey('properties', $activity);
            $this->assertArrayHasKey('created_at', $activity);
            $this->assertArrayHasKey('created_at_human', $activity);
        }
    }

    /**
     * Test getRecentActivities with filters
     */
    public function test_get_recent_activities_with_filters(): void
    {
        // Test with type filter
        $activities = $this->dashboardService->getRecentActivities(10, ['type' => 'created']);
        $this->assertIsArray($activities);

        // Test with search filter
        $activities = $this->dashboardService->getRecentActivities(10, ['search' => 'test']);
        $this->assertIsArray($activities);

        // Test with user_id filter
        $user = User::first();
        if ($user) {
            $activities = $this->dashboardService->getRecentActivities(10, ['user_id' => $user->id]);
            $this->assertIsArray($activities);
        }
    }

    /**
     * Test getDashboardStats handles empty data gracefully
     */
    public function test_get_dashboard_stats_handles_empty_data(): void
    {
        // Clear all test data
        Equipment::truncate();
        BorrowRequest::truncate();
        VisitRequest::truncate();
        TestingRequest::truncate();
        ActivityLog::truncate();

        $dateFrom = now()->subDays(7)->format('Y-m-d');
        $dateTo = now()->format('Y-m-d');

        $stats = $this->dashboardService->getDashboardStats($dateFrom, $dateTo);

        // Should still return valid structure with zero values
        $this->assertIsArray($stats);
        $this->assertEquals(0, $stats['summary']['total_pending_requests']);
        $this->assertEquals(0, $stats['summary']['total_equipment']);
        $this->assertIsArray($stats['alerts']);
        $this->assertIsArray($stats['quick_insights']['most_requested_equipment']);
    }

    /**
     * Test getDashboardStats calculates trends correctly
     */
    public function test_get_dashboard_stats_calculates_trends(): void
    {
        // Create some older requests for trend calculation
        BorrowRequest::factory()->count(3)->create([
            'status' => 'pending',
            'created_at' => now()->subDays(10),
        ]);

        $dateFrom = now()->subDays(7)->format('Y-m-d');
        $dateTo = now()->format('Y-m-d');

        $stats = $this->dashboardService->getDashboardStats($dateFrom, $dateTo);

        $this->assertArrayHasKey('pending_trend', $stats['summary']);
        $this->assertIsNumeric($stats['summary']['pending_trend']);
    }

    /**
     * Create test data for dashboard functionality
     */
    private function createTestData(): void
    {
        // Create categories
        $category = Category::factory()->create();
        
        // Create equipment
        Equipment::factory()->count(5)->create([
            'category_id' => $category->id,
            'status' => 'active',
            'available_quantity' => 10,
            'total_quantity' => 10,
        ]);

        Equipment::factory()->count(2)->create([
            'category_id' => $category->id,
            'status' => 'active',
            'available_quantity' => 0, // Fully utilized
            'total_quantity' => 5,
        ]);

        Equipment::factory()->count(1)->create([
            'category_id' => $category->id,
            'status' => 'maintenance',
        ]);

        // Create users
        $admin = User::factory()->create(['role' => 'admin']);

        // Create requests
        BorrowRequest::factory()->count(3)->create(['status' => 'pending']);
        BorrowRequest::factory()->count(2)->create(['status' => 'approved']);
        
        VisitRequest::factory()->count(2)->create(['status' => 'pending']);
        VisitRequest::factory()->count(1)->create(['status' => 'ready']);
        
        TestingRequest::factory()->count(1)->create(['status' => 'pending']);
        TestingRequest::factory()->count(1)->create(['status' => 'in_progress']);

        // Create activity logs
        ActivityLog::factory()->count(10)->create([
            'causer_type' => User::class,
            'causer_id' => $admin->id,
            'event' => 'created',
        ]);

        ActivityLog::factory()->count(5)->create([
            'causer_type' => User::class,
            'causer_id' => $admin->id,
            'event' => 'updated',
        ]);
    }
}