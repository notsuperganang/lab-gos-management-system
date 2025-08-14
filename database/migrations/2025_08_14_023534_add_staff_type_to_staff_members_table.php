<?php

use App\Enums\StaffType;
use App\Models\StaffMember;
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
        Schema::table('staff_members', function (Blueprint $table) {
            $table->enum('staff_type', StaffType::values())
                  ->default(StaffType::DOSEN->value)
                  ->after('position');
            
            $table->index('staff_type', 'idx_staff_type');
        });

        // Backfill existing staff members based on their position
        $this->backfillStaffTypes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_members', function (Blueprint $table) {
            $table->dropIndex('idx_staff_type');
            $table->dropColumn('staff_type');
        });
    }

    /**
     * Backfill staff_type for existing staff members based on position
     */
    private function backfillStaffTypes(): void
    {
        $staffMembers = StaffMember::all();

        foreach ($staffMembers as $staff) {
            if ($staff->position) {
                $staffType = StaffType::fromPosition($staff->position);
                $staff->update(['staff_type' => $staffType->value]);
            }
        }

        echo "✅ Backfilled staff_type for " . $staffMembers->count() . " staff members\n";
    }
};