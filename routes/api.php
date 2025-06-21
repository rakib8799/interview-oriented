<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Support\Facades\Storage; // Import Storage facade

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Public routes (authentication)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes (requires Sanctum token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Task management API routes
        Route::apiResource('tasks', TaskApiController::class);

        // Custom routes for soft delete features
        Route::post('tasks/{task}/restore', [TaskApiController::class, 'restore'])->withTrashed();;
        Route::delete('tasks/{task}/force-delete', [TaskApiController::class, 'forceDelete'])->withTrashed();;

        // Route to serve uploaded files (for local development/direct access)
        // In production, you'd typically serve these via a web server like Nginx/Apache
        // or a CDN, but this provides a direct API endpoint if needed.
        Route::get('/tasks/attachments/{filename}', function ($filename) {
            $path = 'tasks_attachments/' . $filename;
            if (!Storage::disk('public')->exists($path)) {
                return response()->json(['message' => 'File not found.'], 404);
            }
            return response()->download(Storage::disk('public')->path($path));
        })->where('filename', '.*'); // Allow any character in filename



    //         1. Get All Tasks with Filters, Search, Sort, and Pagination
    // Endpoint: GET /api/tasks

    // Query Parameters:

    // search (optional): A string to search for in title and description columns.
    // status_filter (optional): pending or completed.
    // sort_by (optional): Column to sort by (e.g., title, due_date, status, created_at). Defaults to created_at.
    // sort_order (optional): Sort order (asc or desc). Defaults to desc.
    // per_page (optional): Number of items per page. Defaults to 10.
    // page (optional): The page number. Defaults to 1.
    // with_trashed (optional): true or false to include soft-deleted tasks. Defaults to false.
    // Examples:

    // Search for tasks containing "meeting" in title or description:
    // GET /api/tasks?search=meeting

    // Filter for pending tasks:
    // GET /api/tasks?status_filter=pending

    // Filter for completed tasks, sorted by due date ascending:
    // GET /api/tasks?status_filter=completed&sort_by=due_date&sort_order=asc

    // Search for "report", show 5 results per page, sorted by title descending:
    // GET /api/tasks?search=report&per_page=5&sort_by=title&sort_order=desc

    // Get tasks for the second page, 3 items per page:
    // GET /api/tasks?per_page=3&page=2

    // Include soft-deleted tasks (requires appropriate user permissions based on your policies):
    // GET /api/tasks?with_trashed=true

    // Combine all: Search for "project", get pending tasks, including trashed, sorted by due date ascending, 5 per page, on the first page:
    // GET /api/tasks?search=project&status_filter=pending&with_trashed=true&sort_by=due_date&sort_order=asc&per_page=5&page=1

    });
});
