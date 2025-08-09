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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('model')->nullable();
            $table->string('manufacturer')->nullable();
            $table->json('specifications')->nullable();
            $table->integer('total_quantity')->default(1);
            $table->integer('available_quantity')->default(1);
            $table->enum('status', ['active', 'maintenance', 'retired'])->default('active');
            $table->enum('condition_status', ['excellent', 'good', 'fair', 'poor'])->default('excellent');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->string('location')->nullable();
            $table->string('image_path', 500)->nullable();
            $table->string('manual_file_path', 500)->nullable();
            $table->text('notes')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->timestamps();

            $table->index('category_id', 'idx_equipment_category');
            $table->index('status', 'idx_equipment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
