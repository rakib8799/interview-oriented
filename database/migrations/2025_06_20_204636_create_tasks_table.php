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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Added for authentication: tasks belong to a user
            $table->string('title'); // Required title for the task
            $table->text('description')->nullable(); // Optional description, can be null
            $table->date('due_date')->nullable(); // Optional due date, can be null
            $table->string('status')->default('pending'); // Task status, default to 'pending'
            $table->string('file_path')->nullable(); // Added for file upload
            $table->timestamps(); // Adds created_at and updated_at columns
            $table->softDeletes(); // Added for soft deletion
            $table->index(['status', 'due_date', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
