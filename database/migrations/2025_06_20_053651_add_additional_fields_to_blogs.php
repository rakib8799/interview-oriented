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
        Schema::table('blogs', function (Blueprint $table) {
            // onDelete('cascade') -  When a parent record (in this case, a User) is deleted, all child records (the Blogs associated with that User) that reference that parent will also be automatically deleted by the database.
            // onDelete('restrict') - Prevent the parent from being deleted if it still has associated children

            $table->foreignId('created_by')->after('status')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->after('created_by')->constrained('users')->onDelete('cascade');

            // onDelete('set null') -  When a parent record (in this case, a User) is deleted, child records set to null if it is set to null previously
            $table->foreignId('deleted_by')->nullable()->after('updated_by')->constrained('users')->onDelete('set null');

            // Soft deletes
            // Allows you to "delete" records from your database without actually removing them. Instead of a permanent DELETE operation, a deleted_at timestamp column on the table is populated when a record is soft-deleted.
            $table->softDeletes(); // deleted_at column

            $table->index(['title', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex(['title', 'status']);
            $table->dropSoftDeletes();
            $table->dropForeign(['deleted_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['deleted_by', 'updated_by', 'created_by']);
        });
    }
};
