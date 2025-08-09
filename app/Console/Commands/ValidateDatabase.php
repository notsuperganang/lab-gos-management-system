<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\{
    User, Category, Equipment, Article, SiteSetting, StaffMember, Gallery,
    BorrowRequest, BorrowRequestItem, VisitRequest, TestingRequest,
    ActivityLog, Notification
};

/**
 * Database Validation Command
 *
 * Run with: php artisan db:validate
 *
 * Save as: app/Console/Commands/ValidateDatabase.php
 */
class ValidateDatabase extends Command
{
    protected $signature = 'db:validate {--detailed : Show detailed output}';

    protected $description = 'Validate database structure and relationships for Lab GOS Management System';

    public function handle()
    {
        $this->info('🚀 Starting Lab GOS Database Validation...');
        $this->newLine();

        $passed = 0;
        $failed = 0;

        // Test 1: Check all tables exist
        $this->info('📋 Testing Table Existence...');
        $tables = [
            'users', 'categories', 'site_settings', 'staff_members', 'galleries',
            'equipment', 'articles', 'borrow_requests', 'visit_requests', 'testing_requests',
            'borrow_request_items', 'activity_logs', 'notifications'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $this->line("✅ Table '{$table}' exists");
                $passed++;
            } else {
                $this->error("❌ Table '{$table}' missing");
                $failed++;
            }
        }

        // Test 2: Check critical columns
        $this->newLine();
        $this->info('🔍 Testing Critical Columns...');

        $columnTests = [
            ['users', 'role'],
            ['users', 'is_active'],
            ['equipment', 'category_id'],
            ['equipment', 'specifications'],
            ['borrow_requests', 'members'],
            ['borrow_requests', 'request_id'],
            ['articles', 'published_by'],
            ['testing_requests', 'testing_parameters'],
            ['notifications', 'data'],
        ];

        foreach ($columnTests as [$table, $column]) {
            if (Schema::hasColumn($table, $column)) {
                $this->line("✅ Column '{$table}.{$column}' exists");
                $passed++;
            } else {
                $this->error("❌ Column '{$table}.{$column}' missing");
                $failed++;
            }
        }

        // Test 3: Check foreign key constraints
        $this->newLine();
        $this->info('🔗 Testing Foreign Key Constraints...');

        try {
            $foreignKeys = DB::select("
                SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE REFERENCED_TABLE_NAME IS NOT NULL
                AND TABLE_SCHEMA = DATABASE()
            ");

            $expectedForeignKeys = [
                'equipment.category_id -> categories.id',
                'articles.published_by -> users.id',
                'borrow_requests.reviewed_by -> users.id',
                'visit_requests.reviewed_by -> users.id',
                'testing_requests.reviewed_by -> users.id',
                'testing_requests.assigned_to -> users.id',
                'borrow_request_items.borrow_request_id -> borrow_requests.id',
                'borrow_request_items.equipment_id -> equipment.id',
            ];

            $foundKeys = [];
            foreach ($foreignKeys as $fk) {
                $foundKeys[] = "{$fk->TABLE_NAME}.{$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}";
            }

            foreach ($expectedForeignKeys as $expectedKey) {
                if (in_array($expectedKey, $foundKeys)) {
                    $this->line("✅ Foreign key: {$expectedKey}");
                    $passed++;
                } else {
                    $this->error("❌ Missing foreign key: {$expectedKey}");
                    $failed++;
                }
            }
        } catch (\Exception $e) {
            $this->error("❌ Could not check foreign keys: " . $e->getMessage());
            $failed++;
        }

        // Test 4: Check indexes
        $this->newLine();
        $this->info('📊 Testing Database Indexes...');

        $expectedIndexes = [
            'equipment' => ['idx_equipment_category', 'idx_equipment_status'],
            'borrow_requests' => ['idx_borrow_requests_status'],
            'visit_requests' => ['idx_visit_requests_status'],
            'testing_requests' => ['idx_testing_requests_status'],
            'articles' => ['idx_articles_published'],
            'staff_members' => ['idx_staff_active'],
            'galleries' => ['idx_galleries_category'],
            'activity_logs' => ['idx_subject', 'idx_causer'],
            'notifications' => ['idx_notifiable'],
        ];

        foreach ($expectedIndexes as $table => $indexes) {
            try {
                $tableIndexes = DB::select("SHOW INDEX FROM {$table}");
                $indexNames = array_column($tableIndexes, 'Key_name');

                foreach ($indexes as $expectedIndex) {
                    if (in_array($expectedIndex, $indexNames)) {
                        $this->line("✅ Index '{$table}.{$expectedIndex}' exists");
                        $passed++;
                    } else {
                        $this->error("❌ Index '{$table}.{$expectedIndex}' missing");
                        $failed++;
                    }
                }
            } catch (\Exception $e) {
                $this->error("❌ Could not check indexes for {$table}: " . $e->getMessage());
                $failed++;
            }
        }

        // Test 5: Test Model Relationships
        $this->newLine();
        $this->info('🔄 Testing Model Relationships...');

        try {
            // Create test data if needed
            $this->createTestData();

            // Test Equipment -> Category relationship
            $equipment = Equipment::with('category')->first();
            if ($equipment && $equipment->category) {
                $this->line("✅ Equipment->Category relationship works");
                $passed++;
            } else {
                $this->error("❌ Equipment->Category relationship failed");
                $failed++;
            }

            // Test Category -> Equipment relationship
            $category = Category::with('equipment')->first();
            if ($category && $category->equipment->count() >= 0) {
                $this->line("✅ Category->Equipment relationship works");
                $passed++;
            } else {
                $this->error("❌ Category->Equipment relationship failed");
                $failed++;
            }

            // Test Article -> User relationship
            $article = Article::with('publisher')->first();
            if ($article && $article->publisher) {
                $this->line("✅ Article->Publisher relationship works");
                $passed++;
            } else {
                $this->error("❌ Article->Publisher relationship failed");
                $failed++;
            }

            // Test BorrowRequest -> BorrowRequestItems relationship
            $borrowRequest = BorrowRequest::with('borrowRequestItems')->first();
            if ($borrowRequest && $borrowRequest->borrowRequestItems->count() >= 0) {
                $this->line("✅ BorrowRequest->BorrowRequestItems relationship works");
                $passed++;
            } else {
                $this->error("❌ BorrowRequest->BorrowRequestItems relationship failed");
                $failed++;
            }

        } catch (\Exception $e) {
            $this->error("❌ Relationship testing failed: " . $e->getMessage());
            $failed++;
        }

        // Test 6: Test JSON/Array Casting
        $this->newLine();
        $this->info('🎯 Testing JSON/Array Casting...');

        try {
            $equipment = Equipment::first();
            if ($equipment && is_array($equipment->specifications)) {
                $this->line("✅ Equipment specifications JSON casting works");
                $passed++;
            } else {
                $this->error("❌ Equipment specifications JSON casting failed");
                $failed++;
            }

            $borrowRequest = BorrowRequest::first();
            if ($borrowRequest && is_array($borrowRequest->members)) {
                $this->line("✅ BorrowRequest members JSON casting works");
                $passed++;
            } else {
                $this->error("❌ BorrowRequest members JSON casting failed");
                $failed++;
            }

            $article = Article::first();
            if ($article && (is_array($article->tags) || is_null($article->tags))) {
                $this->line("✅ Article tags JSON casting works");
                $passed++;
            } else {
                $this->error("❌ Article tags JSON casting failed");
                $failed++;
            }

        } catch (\Exception $e) {
            $this->error("❌ JSON casting testing failed: " . $e->getMessage());
            $failed++;
        }

        // Test 7: Test Enum Values
        $this->newLine();
        $this->info('🏷️ Testing Enum Values...');

        $enumTests = [
            ['users', 'role', ['super_admin', 'admin']],
            ['equipment', 'status', ['active', 'maintenance', 'retired']],
            ['equipment', 'condition_status', ['excellent', 'good', 'fair', 'poor']],
            ['borrow_requests', 'status', ['pending', 'approved', 'rejected', 'active', 'completed', 'cancelled']],
            ['visit_requests', 'status', ['pending', 'approved', 'rejected', 'completed', 'cancelled']],
            ['visit_requests', 'purpose', ['study-visit', 'research', 'learning', 'internship', 'others']],
            ['testing_requests', 'status', ['pending', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled']],
            ['testing_requests', 'testing_type', ['uv_vis_spectroscopy', 'ftir_spectroscopy', 'optical_microscopy', 'custom']],
        ];

        foreach ($enumTests as [$table, $column, $values]) {
            try {
                $columnInfo = DB::select("SHOW COLUMNS FROM {$table} LIKE '{$column}'")[0] ?? null;
                if ($columnInfo && str_contains($columnInfo->Type, 'enum')) {
                    $this->line("✅ Enum column '{$table}.{$column}' configured");
                    $passed++;
                } else {
                    $this->error("❌ Enum column '{$table}.{$column}' not properly configured");
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("❌ Could not check enum for {$table}.{$column}: " . $e->getMessage());
                $failed++;
            }
        }

        // Final Results
        $this->newLine();
        $this->info('📊 Validation Results:');
        $this->line("✅ Passed: {$passed}");
        $this->line("❌ Failed: {$failed}");
        $total = $passed + $failed;
        $percentage = $total > 0 ? round(($passed / $total) * 100, 2) : 0;
        $this->line("📈 Success Rate: {$percentage}%");

        if ($failed === 0) {
            $this->info('🎉 All database validation tests passed!');
            return 0;
        } else {
            $this->error("⚠️  {$failed} validation tests failed. Please check the errors above.");
            return 1;
        }
    }

    private function createTestData()
    {
        // Create minimal test data if tables are empty
        if (User::count() === 0) {
            User::create([
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]);
        }

        if (Category::count() === 0) {
            Category::create([
                'name' => 'Test Category',
                'description' => 'Test category for validation',
                'is_active' => true,
            ]);
        }

        if (Equipment::count() === 0) {
            Equipment::create([
                'name' => 'Test Equipment',
                'category_id' => Category::first()->id,
                'specifications' => ['test' => 'value'],
                'total_quantity' => 1,
                'available_quantity' => 1,
            ]);
        }

        if (Article::count() === 0) {
            Article::create([
                'title' => 'Test Article',
                'slug' => 'test-article',
                'content' => 'Test content',
                'author_name' => 'Test Author',
                'published_by' => User::first()->id,
                'tags' => ['test'],
            ]);
        }

        if (BorrowRequest::count() === 0) {
            $request = BorrowRequest::create([
                'request_id' => 'BR' . now()->format('Ymd') . '001',
                'members' => [['name' => 'Test Member', 'nim' => '123456']],
                'supervisor_name' => 'Test Supervisor',
                'supervisor_email' => 'supervisor@test.com',
                'supervisor_phone' => '123456789',
                'purpose' => 'Test purpose',
                'borrow_date' => now()->addDay(),
                'return_date' => now()->addDays(2),
                'start_time' => '08:00',
                'end_time' => '17:00',
            ]);

            BorrowRequestItem::create([
                'borrow_request_id' => $request->id,
                'equipment_id' => Equipment::first()->id,
                'quantity_requested' => 1,
            ]);
        }
    }
}

/**
 * Register this command in app/Console/Kernel.php:
 *
 * protected $commands = [
 *     Commands\ValidateDatabase::class,
 * ];
 */
