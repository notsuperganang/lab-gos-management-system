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
        Schema::create('borrow_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id', 20)->unique();
            $table->enum('status', ['pending', 'approved', 'rejected', 'active', 'completed', 'cancelled'])->default('pending');

            // Borrower Info
            $table->json('members'); // Array of borrower information
            $table->string('supervisor_name');
            $table->string('supervisor_nip', 50)->nullable();
            $table->string('supervisor_email');
            $table->string('supervisor_phone', 20);

            // Schedule Info
            $table->text('purpose');
            $table->date('borrow_date');
            $table->date('return_date');
            $table->time('start_time');
            $table->time('end_time');

            // System Fields
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('approval_notes')->nullable();
            $table->timestamps();

            $table->index('status', 'idx_borrow_requests_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_requests');
    }
};
