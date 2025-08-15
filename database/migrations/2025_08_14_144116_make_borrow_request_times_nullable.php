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
        // Use raw SQL to avoid requiring doctrine/dbal
        DB::statement("ALTER TABLE borrow_requests 
            MODIFY start_time TIME NULL,
            MODIFY end_time TIME NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set default time values before making NOT NULL to avoid constraint errors
        DB::statement("UPDATE borrow_requests 
            SET start_time = '00:00:00' WHERE start_time IS NULL");
        DB::statement("UPDATE borrow_requests 
            SET end_time = '00:00:00' WHERE end_time IS NULL");
            
        // Make columns NOT NULL again
        DB::statement("ALTER TABLE borrow_requests 
            MODIFY start_time TIME NOT NULL,
            MODIFY end_time TIME NOT NULL");
    }
};