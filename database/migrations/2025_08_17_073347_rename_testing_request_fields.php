<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('testing_requests', function (Blueprint $table) {
            // Add new columns
            $table->date('sample_delivery_schedule')->nullable()->after('urgent_request');
            $table->smallInteger('estimated_duration')->nullable()->after('sample_delivery_schedule'); // days
            $table->date('completion_date')->nullable()->after('actual_completion_date');
            $table->decimal('cost', 15, 2)->nullable()->after('cost_estimate');
        });

        // Backfill data from old columns to new ones
        DB::statement('UPDATE testing_requests SET sample_delivery_schedule = preferred_date');
        DB::statement('UPDATE testing_requests SET completion_date = actual_completion_date');
        DB::statement('UPDATE testing_requests SET cost = cost_estimate');
        
        // Convert hours to days (round up to next day)
        DB::statement('UPDATE testing_requests SET estimated_duration = CEIL(COALESCE(estimated_duration_hours, 0) / 24.0)');

        // Make new columns non-nullable where appropriate
        Schema::table('testing_requests', function (Blueprint $table) {
            $table->date('sample_delivery_schedule')->nullable(false)->change();
        });

        // Drop old columns
        Schema::table('testing_requests', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_date',
                'estimated_duration_hours', 
                'actual_start_date',
                'actual_completion_date',
                'final_cost'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testing_requests', function (Blueprint $table) {
            // Re-add old columns
            $table->date('preferred_date')->nullable()->after('urgent_request');
            $table->integer('estimated_duration_hours')->nullable()->after('preferred_date');
            $table->date('actual_start_date')->nullable()->after('estimated_duration_hours');
            $table->date('actual_completion_date')->nullable()->after('actual_start_date');
            $table->decimal('final_cost', 15, 2)->nullable()->after('cost');
        });

        // Backfill data back to old columns
        DB::statement('UPDATE testing_requests SET preferred_date = sample_delivery_schedule');
        DB::statement('UPDATE testing_requests SET actual_completion_date = completion_date');
        DB::statement('UPDATE testing_requests SET final_cost = cost');
        
        // Convert days back to hours (estimate 24 hours per day)
        DB::statement('UPDATE testing_requests SET estimated_duration_hours = COALESCE(estimated_duration, 0) * 24');

        // Make old columns non-nullable where they were before
        Schema::table('testing_requests', function (Blueprint $table) {
            $table->date('preferred_date')->nullable(false)->change();
        });

        // Drop new columns
        Schema::table('testing_requests', function (Blueprint $table) {
            $table->dropColumn([
                'sample_delivery_schedule',
                'estimated_duration',
                'completion_date', 
                'cost'
            ]);
        });
    }
};