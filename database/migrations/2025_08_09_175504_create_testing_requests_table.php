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
        Schema::create('testing_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id', 20)->unique();
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled'])->default('pending');

            // Client Info
            $table->string('client_name');
            $table->string('client_organization');
            $table->string('client_email');
            $table->string('client_phone', 20);
            $table->text('client_address');

            // Sample Info
            $table->string('sample_name');
            $table->text('sample_description');
            $table->string('sample_quantity', 100);
            $table->enum('testing_type', ['uv_vis_spectroscopy', 'ftir_spectroscopy', 'optical_microscopy', 'custom']);
            $table->json('testing_parameters')->nullable();
            $table->boolean('urgent_request')->default(false);

            // Schedule
            $table->date('preferred_date');
            $table->integer('estimated_duration_hours')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_completion_date')->nullable();

            // Results
            $table->json('result_files_path')->nullable();
            $table->text('result_summary')->nullable();
            $table->decimal('cost_estimate', 15, 2)->nullable();
            $table->decimal('final_cost', 15, 2)->nullable();

            // System Fields
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('approval_notes')->nullable();
            $table->timestamps();

            $table->index('status', 'idx_testing_requests_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testing_requests');
    }
};
