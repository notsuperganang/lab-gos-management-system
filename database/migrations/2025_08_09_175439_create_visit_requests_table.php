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
        Schema::create('visit_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id', 20)->unique();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');

            // Contact Info
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 20);
            $table->string('institution');

            // Visit Info
            $table->enum('purpose', ['study-visit', 'research', 'learning', 'internship', 'others']);
            $table->date('visit_date');
            $table->enum('visit_time', ['morning', 'afternoon']);
            $table->integer('participants');
            $table->text('additional_notes')->nullable();

            // Documents
            $table->string('request_letter_path', 500)->nullable();
            $table->string('approval_letter_path', 500)->nullable();

            // System Fields
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('approval_notes')->nullable();
            $table->boolean('agreement_accepted')->default(true);
            $table->timestamps();

            $table->index('status', 'idx_visit_requests_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_requests');
    }
};
