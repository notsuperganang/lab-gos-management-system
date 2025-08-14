<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_published');
            $table->index('is_featured', 'idx_articles_featured');
        });

        // Add MariaDB virtual column for unique constraint (only one featured article at a time)
        // This leverages MariaDB's ability to have multiple NULL values but only one non-NULL value
        DB::statement('ALTER TABLE articles ADD COLUMN featured_lock TINYINT AS (CASE WHEN is_featured=1 THEN 1 ELSE NULL END) VIRTUAL');
        DB::statement('CREATE UNIQUE INDEX uniq_articles_featured_lock ON articles (featured_lock)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the unique index and virtual column first
        DB::statement('DROP INDEX uniq_articles_featured_lock ON articles');
        
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('idx_articles_featured');
            $table->dropColumn(['featured_lock', 'is_featured']);
        });
    }
};