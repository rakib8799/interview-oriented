<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Services\TaskServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use App\Models\User; // Import User model for type hinting
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Add this trait

class TaskApiController extends Controller
{
    use AuthorizesRequests; // Use the trait to enable authorize() method

    protected $taskService;

    /**
     * TaskApiController constructor.
     * @param TaskServiceInterface $taskService
     */
    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
        // Policies will be explicitly called in each method or via Form Requests.
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        // Explicitly authorize the 'viewAny' action.
        // This relies on the TaskPolicy@viewAny method.
        $this->authorize('viewAny', Task::class);

        // Get filters, sort, and pagination parameters from the request
        $search = $request->input('search');
        $statusFilter = $request->input('status_filter');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $perPage = (int) $request->input('per_page', 10);
        $withTrashed = $request->boolean('with_trashed', false); // Added for soft deletes filter

        // Define allowed sortable columns
        $allowedSortBy = ['title', 'due_date', 'status', 'created_at'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'created_at';
        }

        // Define allowed sort order
        $allowedSortOrder = ['asc', 'desc'];
        if (!in_array(strtolower($sortOrder), $allowedSortOrder)) {
            $sortOrder = 'desc';
        }

        // Fetch tasks using the service, passing the current user object for authorization scope
        $tasks = $this->taskService->getAllTasks(
            $user, // Pass the User object to service for role-based task fetching
            $search,
            $statusFilter,
            $sortBy,
            $sortOrder,
            $perPage,
            $withTrashed // Pass withTrashed parameter
        );

        // Return paginated data as JSON
        return response()->json([
            'message' => 'Tasks retrieved successfully',
            'data' => $tasks->items(),
            'pagination' => [
                'total' => $tasks->total(),
                'per_page' => $tasks->perPage(),
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'from' => $tasks->firstItem(),
                'to' => $tasks->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * Authorization is now handled by TaskStoreRequest's authorize() method.
     *
     * @param TaskStoreRequest $request
     * @return JsonResponse
     */
    public function store(TaskStoreRequest $request): JsonResponse
    {
        // No explicit $this->authorize('create', Task::class); here.
        // It's handled by TaskStoreRequest's authorize() method, ensuring
        // the controller is only hit if authorization passes.

        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('file_path')) {
            $filePath = $request->file('file_path')->store('tasks_attachments', 'public');
            $validatedData['file_path'] = $filePath;
        }

        $task = $this->taskService->createTask($validatedData);

        return response()->json([
            'message' => 'Task created successfully!',
            'task' => $task,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function show(Task $task): JsonResponse
    {
        // Explicitly authorize the 'view' action for the specific task.
        $this->authorize('view', $task);

        return response()->json([
            'message' => 'Task retrieved successfully',
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * Authorization is now handled by TaskUpdateRequest's authorize() method.
     *
     * @param TaskUpdateRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(TaskUpdateRequest $request, Task $task): JsonResponse
    {
        // No explicit $this->authorize('update', $task); here.
        // It's handled by TaskUpdateRequest's authorize() method.

        $validatedData = $request->validated();

        // Handle file upload
        if ($request->hasFile('file_path')) {
            // Delete old file if exists
            if ($task->file_path && Storage::disk('public')->exists($task->file_path)) {
                Storage::disk('public')->delete($task->file_path);
            }
            $filePath = $request->file('file_path')->store('tasks_attachments', 'public');
            $validatedData['file_path'] = $filePath;
        } elseif ($request->boolean('clear_file')) { // Option to clear existing file
            if ($task->file_path && Storage::disk('public')->exists($task->file_path)) {
                Storage::disk('public')->delete($task->file_path);
            }
            $validatedData['file_path'] = null;
        }


        $updated = $this->taskService->updateTask($task, $validatedData);

        if ($updated) {
            $task->refresh(); // Refresh the model to get latest data including accessors
            return response()->json([
                'message' => 'Task updated successfully!',
                'task' => $task,
            ]);
        }

        return response()->json(['message' => 'Task update failed or no changes were made.'], 400);
    }

    /**
     * Remove the specified resource from storage. (Soft delete)
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        // Explicitly authorize the 'delete' action for the specific task.
        $this->authorize('delete', $task);

        $deleted = $this->taskService->deleteTask($task); // This is now a soft delete

        if ($deleted) {
            return response()->json(['message' => 'Task soft-deleted successfully!']);
        }

        return response()->json(['message' => 'Task deletion failed.'], 400);
    }

    /**
     * Restore a soft-deleted task.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function restore(Task $task): JsonResponse
    {
        $this->authorize('restore', $task); // Explicit policy check for restore
        $restored = $this->taskService->restoreTask($task);

        if ($restored) {
            return response()->json(['message' => 'Task restored successfully!', 'task' => $task->refresh()]);
        }

        return response()->json(['message' => 'Task restoration failed.'], 400);
    }

    /**
     * Permanently delete a task.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function forceDelete(Task $task): JsonResponse
    {
        $this->authorize('forceDelete', $task); // Explicit policy check for forceDelete

        // Delete associated file when force deleting
        if ($task->file_path && Storage::disk('public')->exists($task->file_path)) {
            Storage::disk('public')->delete($task->file_path);
        }

        $deleted = $this->taskService->forceDeleteTask($task);

        if ($deleted) {
            return response()->json(['message' => 'Task permanently deleted!']);
        }

        return response()->json(['message' => 'Task permanent deletion failed.'], 400);
    }
}
