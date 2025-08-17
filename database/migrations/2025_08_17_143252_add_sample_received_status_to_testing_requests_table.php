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
        // Add sample_received to the status enum
        DB::statement("ALTER TABLE testing_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'sample_received', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove sample_received from the status enum
        DB::statement("ALTER TABLE testing_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};