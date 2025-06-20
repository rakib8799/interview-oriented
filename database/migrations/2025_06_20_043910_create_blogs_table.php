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
        // Some useful migration related commands:
        // php artisan migrate:fresh - Dropping all tables, Preparing database by creating migration table and Running migrations serially
        // php artisan migrate:reset - Rolling back migrations (from lastly added - first migration)
        // php artisan migrate:refresh - Rolling back migrations (from lastly added - first migration) and then Running migrations serially (first - last)
        // php artisan migrate:rollback - Rolling back migrations (from lastly added - first migration) but Undoes recent changes, preserves older
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('text')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            // Table data types
            // string
            // text
            // int
            // char
            // bigInteger
            // float
            // date
            // boolean
            // json
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
