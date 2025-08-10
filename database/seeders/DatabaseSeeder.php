<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Lab GOS Database Seeding...');
        $this->command->newLine();

        // Disable foreign key checks to avoid constraint issues during seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Phase 1: Independent Data (no foreign key dependencies)
            $this->command->info('📋 Phase 1: Seeding Independent Data...');

            $this->call([
                UserSeeder::class,           // Create admin users first
                CategorySeeder::class,       // Equipment categories
                SiteSettingSeeder::class,    // Lab settings and information
                StaffMemberSeeder::class,    // Lab staff profiles
                GallerySeeder::class,        // Image galleries
            ]);

            $this->command->newLine();

            // Phase 2: Dependent Data (requires foreign keys from Phase 1)
            $this->command->info('🔧 Phase 2: Seeding Equipment and Content...');

            $this->call([
                EquipmentSeeder::class,      // Lab equipment (depends on categories)
                ArticleSeeder::class,        // Articles (depends on users)
            ]);

            $this->command->newLine();

            // Phase 3: Request Data (depends on users and equipment)
            $this->command->info('📝 Phase 3: Seeding Service Requests...');

            $this->call([
                BorrowRequestSeeder::class,  // Equipment borrowing requests
                VisitRequestSeeder::class,   // Lab visit requests
                TestingRequestSeeder::class, // Sample testing requests
            ]);

            $this->command->newLine();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Display seeding summary
            $this->displaySeedingSummary();

        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->command->error('❌ Seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Display a summary of seeded data
     */
    private function displaySeedingSummary(): void
    {
        $this->command->info('📊 Seeding Summary:');
        $this->command->line('=====================================');

        // Count records in each table
        $tables = [
            'users' => 'Users (Admin Accounts)',
            'categories' => 'Equipment Categories',
            'equipment' => 'Lab Equipment',
            'site_settings' => 'Site Settings',
            'staff_members' => 'Staff Members',
            'articles' => 'Articles & News',
            'galleries' => 'Gallery Images',
            'borrow_requests' => 'Equipment Borrow Requests',
            'borrow_request_items' => 'Borrow Request Items',
            'visit_requests' => 'Lab Visit Requests',
            'testing_requests' => 'Sample Testing Requests',
        ];

        foreach ($tables as $table => $description) {
            try {
                $count = DB::table($table)->count();
                $this->command->line("✅ {$description}: {$count} records");
            } catch (\Exception $e) {
                $this->command->line("❌ {$description}: Error counting records");
            }
        }

        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->newLine();

        // Display login information
        $this->displayLoginInformation();

        // Display next steps
        $this->displayNextSteps();
    }

    /**
     * Display login information for testing
     */
    private function displayLoginInformation(): void
    {
        $this->command->info('👤 Login Information for Testing:');
        $this->command->line('=====================================');
        $this->command->line('Super Admin:');
        $this->command->line('  Email: ganangsetyohadi@gmail.com');
        $this->command->line('  Password: password');
        $this->command->newLine();
        $this->command->line('Admin Users:');
        $this->command->line('  Email: admin1@labgos.ac.id');
        $this->command->line('  Email: admin2@labgos.ac.id');
        $this->command->line('  Email: admin3@labgos.ac.id');
        $this->command->line('  Password: password (for all admin accounts)');
        $this->command->newLine();
    }

    /**
     * Display next steps and recommendations
     */
    private function displayNextSteps(): void
    {
        $this->command->info('🚀 Next Steps:');
        $this->command->line('=====================================');
        $this->command->line('1. Run database validation:');
        $this->command->line('   php artisan db:validate');
        $this->command->newLine();
        $this->command->line('2. Test the application:');
        $this->command->line('   - Login with admin credentials');
        $this->command->line('   - Check equipment catalog');
        $this->command->line('   - Review sample requests');
        $this->command->line('   - Test relationship queries');
        $this->command->newLine();
        $this->command->line('3. Development recommendations:');
        $this->command->line('   - Set up file storage for images/documents');
        $this->command->line('   - Configure mail settings for notifications');
        $this->command->line('   - Set up backup procedures');
        $this->command->line('   - Configure caching for better performance');
        $this->command->newLine();
        $this->command->line('4. Production checklist:');
        $this->command->line('   - Change default passwords');
        $this->command->line('   - Update email addresses to real ones');
        $this->command->line('   - Configure proper file paths');
        $this->command->line('   - Set up SSL certificates');
        $this->command->line('   - Configure backup strategies');
        $this->command->newLine();
    }
}

/**
 * Usage Instructions:
 *
 * 1. Run all seeders:
 *    php artisan db:seed
 *
 * 2. Run specific seeder:
 *    php artisan db:seed --class=UserSeeder
 *
 * 3. Fresh migration with seeding:
 *    php artisan migrate:fresh --seed
 *
 * 4. Re-run seeders (will duplicate data):
 *    php artisan db:seed --force
 *
 * Note: The seeders are designed to create realistic test data
 * for development and demonstration purposes. For production,
 * modify the data accordingly and change default passwords.
 */
