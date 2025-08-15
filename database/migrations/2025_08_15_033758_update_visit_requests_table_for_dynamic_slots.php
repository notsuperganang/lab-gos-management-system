<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visit_requests', function (Blueprint $table) {
            // Rename existing columns to new structure
            $table->renameColumn('full_name', 'visitor_name');
            $table->renameColumn('email', 'visitor_email');
            $table->renameColumn('phone', 'visitor_phone');
            $table->renameColumn('purpose', 'visit_purpose');
            $table->renameColumn('participants', 'group_size');
            $table->renameColumn('additional_notes', 'purpose_description');
            
            // Add new dynamic time columns
            $table->time('start_time')->nullable()->after('visit_date');
            $table->time('end_time')->nullable()->after('start_time');
            
            // Add new optional columns
            $table->text('special_requirements')->nullable()->after('purpose_description');
            $table->json('equipment_needed')->nullable()->after('special_requirements');
            
            // Add status for ready and under_review
            $table->enum('status', ['pending', 'under_review', 'approved', 'ready', 'completed', 'rejected', 'cancelled'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_requests', function (Blueprint $table) {
            // Reverse the column renames
            $table->renameColumn('visitor_name', 'full_name');
            $table->renameColumn('visitor_email', 'email');
            $table->renameColumn('visitor_phone', 'phone');
            $table->renameColumn('visit_purpose', 'purpose');
            $table->renameColumn('group_size', 'participants');
            $table->renameColumn('purpose_description', 'additional_notes');
            
            // Drop new columns
            $table->dropColumn(['start_time', 'end_time', 'special_requirements', 'equipment_needed']);
            
            // Revert status enum
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending')->change();
        });
    }
};
