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
        Schema::create('blocked_time_slots', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // Indexes for performance
            $table->index(['date'], 'idx_blocked_slots_date');
            $table->index(['date', 'start_time', 'end_time'], 'idx_blocked_slots_time');
            
            // Ensure no duplicate blocked slots
            $table->unique(['date', 'start_time', 'end_time'], 'unique_blocked_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_time_slots');
    }
};
