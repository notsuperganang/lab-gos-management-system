<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BlockedTimeSlot;
use App\Models\VisitRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Carbon\Carbon;

class AdminCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user for testing
        $this->admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test that availability endpoint returns correct slot structure
     */
    public function test_availability_returns_correct_slot_structure(): void
    {
        Sanctum::actingAs($this->admin);

        $futureDate = now()->addDays(7)->format('Y-m-d');
        
        $response = $this->getJson("/api/admin/visit/availability?date={$futureDate}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'summary' => [
                            'date',
                            'formatted_date',
                            'day_name',
                            'is_weekend',
                            'is_past',
                            'total_slots',
                            'available_slots',
                            'booked_slots',
                            'blocked_slots',
                            'availability_percentage'
                        ],
                        'slots' => [
                            '*' => [
                                'start_time',
                                'end_time',
                                'status',
                                'visit_request',
                                'blocked_info'
                            ]
                        ]
                    ]
                ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals(7, count($response->json('data.slots'))); // 7 hours (8-12, 13-16)
    }

    /**
     * Test that calendar endpoint returns month overview
     */
    public function test_calendar_returns_month_overview(): void
    {
        Sanctum::actingAs($this->admin);

        $currentYear = now()->year;
        $currentMonth = now()->month;
        
        $response = $this->getJson("/api/admin/visit/calendar?year={$currentYear}&month={$currentMonth}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'overall_stats' => [
                            'month',
                            'year',
                            'month_name',
                            'total_working_days',
                            'total_slots',
                            'total_available_slots',
                            'total_booked_slots',
                            'total_blocked_slots',
                            'overall_availability_percentage',
                            'booking_rate',
                            'blocked_rate'
                        ],
                        'daily_summary' => [
                            '*' => [
                                'date',
                                'formatted_date',
                                'day_name',
                                'total_slots',
                                'available_slots',
                                'booked_slots',
                                'blocked_slots',
                                'availability_percentage'
                            ]
                        ]
                    ]
                ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals($currentMonth, $response->json('data.overall_stats.month'));
        $this->assertEquals($currentYear, $response->json('data.overall_stats.year'));
    }

    /**
     * Test blocking a time slot via toggle endpoint
     */
    public function test_toggle_can_block_time_slot(): void
    {
        Sanctum::actingAs($this->admin);

        $futureDate = now()->addDays(5)->format('Y-m-d');
        
        $response = $this->putJson('/api/admin/visit/blocks/toggle', [
            'date' => $futureDate,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'reason' => 'Test blocking'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'status' => 'blocked'
                    ]
                ]);

        // Verify the blocked slot was created in the database
        $this->assertDatabaseHas('blocked_time_slots', [
            'date' => $futureDate,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'reason' => 'Test blocking',
            'created_by' => $this->admin->id
        ]);
    }

    /**
     * Test unblocking a time slot via toggle endpoint
     */
    public function test_toggle_can_unblock_time_slot(): void
    {
        Sanctum::actingAs($this->admin);

        $futureDate = now()->addDays(5)->format('Y-m-d');
        
        // First create a blocked slot
        $blockedSlot = BlockedTimeSlot::create([
            'date' => $futureDate,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'reason' => 'Test blocking',
            'created_by' => $this->admin->id
        ]);

        // Now toggle it to unblock
        $response = $this->putJson('/api/admin/visit/blocks/toggle', [
            'date' => $futureDate,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'status' => 'available'
                    ]
                ]);

        // Verify the blocked slot was deleted from the database
        $this->assertDatabaseMissing('blocked_time_slots', [
            'id' => $blockedSlot->id
        ]);
    }

    /**
     * Test toggle validation for 1-hour slots only
     */
    public function test_toggle_validates_one_hour_slots_only(): void
    {
        Sanctum::actingAs($this->admin);

        $futureDate = now()->addDays(5)->format('Y-m-d');
        
        // Test with 2-hour slot (should fail)
        $response = $this->putJson('/api/admin/visit/blocks/toggle', [
            'date' => $futureDate,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00'
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Time slot must be exactly 1 hour'
                ]);
    }

    /**
     * Test toggle validation for top-of-hour slots only
     */
    public function test_toggle_validates_top_of_hour_only(): void
    {
        Sanctum::actingAs($this->admin);

        $futureDate = now()->addDays(5)->format('Y-m-d');
        
        // Test with 30-minute slot (should fail)
        $response = $this->putJson('/api/admin/visit/blocks/toggle', [
            'date' => $futureDate,
            'start_time' => '09:30:00',
            'end_time' => '10:30:00'
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Time slot must start at top of hour (e.g., 09:00:00)'
                ]);
    }

    /**
     * Test that weekend dates are rejected
     */
    public function test_toggle_rejects_weekend_dates(): void
    {
        Sanctum::actingAs($this->admin);

        // Find a future Saturday
        $saturday = now()->next(Carbon::SATURDAY)->format('Y-m-d');
        
        $response = $this->putJson('/api/admin/visit/blocks/toggle', [
            'date' => $saturday,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00'
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Cannot manage slots on weekends'
                ]);
    }

    /**
     * Test that blocked slots affect availability correctly
     */
    public function test_blocked_slots_affect_availability(): void
    {
        Sanctum::actingAs($this->admin);

        $futureDate = now()->addDays(5)->format('Y-m-d');
        
        // Block a slot
        BlockedTimeSlot::create([
            'date' => $futureDate,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'reason' => 'Test blocking',
            'created_by' => $this->admin->id
        ]);

        // Check availability
        $response = $this->getJson("/api/admin/visit/availability?date={$futureDate}");

        $response->assertStatus(200);
        
        $slots = $response->json('data.slots');
        $blockedSlot = collect($slots)->firstWhere('start_time', '09:00');
        
        $this->assertEquals('blocked', $blockedSlot['status']);
        $this->assertNotNull($blockedSlot['blocked_info']);
        $this->assertEquals('Test blocking', $blockedSlot['blocked_info']['reason']);

        // Check summary counts
        $summary = $response->json('data.summary');
        $this->assertEquals(1, $summary['blocked_slots']);
        $this->assertEquals(6, $summary['available_slots']); // 7 total - 1 blocked
    }

    /**
     * Test that booked slots are properly indicated
     */
    public function test_booked_slots_are_properly_indicated(): void
    {
        Sanctum::actingAs($this->admin);

        $futureDate = now()->addDays(5)->format('Y-m-d');
        
        // Create a visit request (simulating a booking)
        VisitRequest::create([
            'request_id' => 'VR' . now()->format('YmdHis'),
            'status' => 'approved',
            'visitor_name' => 'Test Visitor',
            'visitor_email' => 'visitor@test.com',
            'visitor_phone' => '123456789',
            'institution' => 'Test Institution',
            'visit_purpose' => 'research',
            'visit_date' => $futureDate,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'group_size' => 1,
            'purpose_description' => 'Test visit',
            'submitted_at' => now(),
        ]);

        // Check availability
        $response = $this->getJson("/api/admin/visit/availability?date={$futureDate}");

        $response->assertStatus(200);
        
        $slots = $response->json('data.slots');
        
        // The 10:00 and 11:00 slots should be booked
        $slot10 = collect($slots)->firstWhere('start_time', '10:00');
        $slot11 = collect($slots)->firstWhere('start_time', '11:00');
        
        $this->assertEquals('booked', $slot10['status']);
        $this->assertEquals('booked', $slot11['status']);

        // Check summary counts
        $summary = $response->json('data.summary');
        $this->assertEquals(2, $summary['booked_slots']);
        $this->assertEquals(5, $summary['available_slots']); // 7 total - 2 booked
    }

    /**
     * Test authentication is required for all endpoints
     */
    public function test_authentication_required(): void
    {
        $futureDate = now()->addDays(5)->format('Y-m-d');
        
        // Test availability endpoint
        $response = $this->getJson("/api/admin/visit/availability?date={$futureDate}");
        $response->assertStatus(401);

        // Test calendar endpoint
        $response = $this->getJson("/api/admin/visit/calendar?year=2025&month=8");
        $response->assertStatus(401);

        // Test toggle endpoint
        $response = $this->putJson('/api/admin/visit/blocks/toggle', [
            'date' => $futureDate,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00'
        ]);
        $response->assertStatus(401);
    }

    /**
     * Test admin role is required
     */
    public function test_admin_role_required(): void
    {
        // Create a regular user (non-admin)
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $futureDate = now()->addDays(5)->format('Y-m-d');
        
        // Test that non-admin users can't access the endpoints
        $response = $this->getJson("/api/admin/visit/availability?date={$futureDate}");
        $response->assertStatus(403);

        $response = $this->putJson('/api/admin/visit/blocks/toggle', [
            'date' => $futureDate,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00'
        ]);
        $response->assertStatus(403);
    }

    /**
     * Test validation errors are properly formatted
     */
    public function test_validation_errors_properly_formatted(): void
    {
        Sanctum::actingAs($this->admin);

        // Test missing date
        $response = $this->getJson('/api/admin/visit/availability');
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'message',
                    'errors' => [
                        'date'
                    ]
                ]);

        // Test invalid date format
        $response = $this->getJson('/api/admin/visit/availability?date=invalid-date');
        $response->assertStatus(422);

        // Test missing fields in toggle
        $response = $this->putJson('/api/admin/visit/blocks/toggle', []);
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'message',
                    'errors'
                ]);
    }
}