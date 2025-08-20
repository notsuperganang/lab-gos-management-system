<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\BorrowRequest;
use App\Models\VisitRequest;
use App\Models\TestingRequest;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $admin;
    private User $superAdmin;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
        
        // Create test data
        $this->createTestData();
    }

    /**
     * Test dashboard stats endpoint with admin authentication
     */
    public function test_dashboard_stats_returns_success_for_admin(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/admin-api/dashboard/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'summary' => [
                        'total_pending_requests',
                        'total_equipment',
                        'available_equipment',
                        'equipment_utilization_rate',
                        'recent_activity_count',
                        'pending_trend',
                        'total_active_requests',
                        'pending_borrow_requests',
                        'active_borrow_requests',
                        'pending_visit_requests',
                        'active_visit_requests',
                        'pending_testing_requests',
                        'active_testing_requests',
                    ],
                    'equipment_analytics' => [
                        'total_count',
                        'availability' => [
                            'available',
                            'fully_utilized',
                            'low_stock',
                        ],
                        'status_distribution' => [
                            'active',
                            'maintenance',
                            'retired',
                        ],
                    ],
                    'request_analytics' => [
                        'period_summary' => [
                            'total_requests',
                            'borrow_requests',
                            'visit_requests',
                            'testing_requests',
                        ],
                    ],
                    'trend_data' => [
                        'daily_trends',
                    ],
                    'quick_insights' => [
                        'most_requested_equipment',
                    ],
                    'alerts',
                ],
                'meta' => [
                    'timestamp',
                    'timezone',
                    'cache_duration',
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test dashboard stats endpoint with super admin authentication
     */
    public function test_dashboard_stats_returns_success_for_super_admin(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->getJson('/admin-api/dashboard/stats');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test dashboard stats endpoint requires authentication
     */
    public function test_dashboard_stats_requires_authentication(): void
    {
        $response = $this->getJson('/admin-api/dashboard/stats');

        $response->assertStatus(302); // Redirects to login for web middleware
    }

    /**
     * Test dashboard stats endpoint requires admin role
     */
    public function test_dashboard_stats_requires_admin_role(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->getJson('/admin-api/dashboard/stats');

        $response->assertStatus(403); // Forbidden due to role middleware
    }

    /**
     * Test dashboard stats with date range parameters
     */
    public function test_dashboard_stats_with_date_range(): void
    {
        $dateFrom = now()->subDays(7)->format('Y-m-d');
        $dateTo = now()->format('Y-m-d');

        $response = $this->actingAs($this->admin)
            ->getJson("/admin-api/dashboard/stats?date_from={$dateFrom}&date_to={$dateTo}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test dashboard stats with invalid date parameters
     */
    public function test_dashboard_stats_validates_date_parameters(): void
    {
        // Invalid date format
        $response = $this->actingAs($this->admin)
            ->getJson('/admin-api/dashboard/stats?date_from=invalid-date');

        $response->assertStatus(422);

        // Date from after date to
        $dateFrom = now()->format('Y-m-d');
        $dateTo = now()->subDays(7)->format('Y-m-d');

        $response = $this->actingAs($this->admin)
            ->getJson("/admin-api/dashboard/stats?date_from={$dateFrom}&date_to={$dateTo}");

        $response->assertStatus(422);
    }

    /**
     * Test activity logs endpoint
     */
    public function test_activity_logs_returns_success(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/admin-api/activity-logs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'log_name',
                            'description',
                            'event',
                            'subject_type',
                            'subject_id',
                            'causer_type',
                            'causer_id',
                            'causer',
                            'properties',
                            'created_at',
                            'created_at_human',
                            'created_at_iso',
                            'category',
                            'importance',
                            'icon',
                            'color',
                        ],
                    ],
                ],
                'meta' => [
                    'total',
                    'timestamp',
                    'filters_applied',
                ],
            ])
            ->assertJson(['success' => true]);
    }

    /**
     * Test activity logs with filtering parameters
     */
    public function test_activity_logs_with_filters(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/admin-api/activity-logs?type=created&per_page=5&search=test');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test notifications endpoint
     */
    public function test_notifications_returns_success(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/admin-api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson(['success' => true]);
    }

    /**
     * Test dashboard stats response contains correct data types
     */
    public function test_dashboard_stats_data_types(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/admin-api/dashboard/stats');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Assert numeric values are integers
        $this->assertIsInt($data['summary']['total_pending_requests']);
        $this->assertIsInt($data['summary']['total_equipment']);
        $this->assertIsInt($data['summary']['available_equipment']);
        $this->assertIsNumeric($data['summary']['equipment_utilization_rate']);
        $this->assertIsInt($data['summary']['recent_activity_count']);
        
        // Assert arrays are present
        $this->assertIsArray($data['quick_insights']['most_requested_equipment']);
        $this->assertIsArray($data['alerts']);
        $this->assertIsArray($data['trend_data']['daily_trends']);
    }

    /**
     * Test dashboard stats with cache refresh
     */
    public function test_dashboard_stats_with_cache_refresh(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/admin-api/dashboard/stats?refresh_cache=true');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Create test data for dashboard functionality
     */
    private function createTestData(): void
    {
        // Create categories
        $category = Category::factory()->create();
        
        // Create equipment
        Equipment::factory()->count(10)->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        Equipment::factory()->count(2)->create([
            'category_id' => $category->id,
            'status' => 'maintenance',
        ]);

        // Create requests
        BorrowRequest::factory()->count(5)->create(['status' => 'pending']);
        BorrowRequest::factory()->count(3)->create(['status' => 'approved']);
        
        VisitRequest::factory()->count(2)->create(['status' => 'pending']);
        VisitRequest::factory()->count(4)->create(['status' => 'approved']);
        
        TestingRequest::factory()->count(1)->create(['status' => 'pending']);
        TestingRequest::factory()->count(2)->create(['status' => 'in_progress']);

        // Create activity logs
        ActivityLog::factory()->count(15)->create([
            'causer_type' => User::class,
            'causer_id' => $this->admin->id,
        ]);
    }
}