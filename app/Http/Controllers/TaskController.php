<?php
namespace App\Http\Controllers;

use App\Services\TaskService;
use App\Utilities\ResponseFormatters\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller {
    use ApiResponse;

    protected TaskService $taskService;

    public function __construct(TaskService $taskService) {
        $this->taskService = $taskService;
    }

    /**
     * Store a newly created task.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse {
        $validated = $request->validate([
            "project_id" => "required|integer|exists:projects,id",
            "title" => "required|string",
            "assignees" => "nullable|array",
            "due_date" => "nullable|date",
            "priority" => "nullable|in:low,medium,high,urgent,Low,Normal,High,Urgent",
            "status" => "nullable|in:pending,in_progress,completed,none,in-progress,done",
        ]);

        // Standardize priority/status formatting to match migrations enum values if needed
        $validated = $this->standardizeTaskData($validated);

        try {
            $task = $this->taskService->createTask($validated);
            return $this->successResponse("Task created successfully.", $task->toArray(), 201);
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to create task.", $e);
        }
    }

    /**
     * Update the specified task.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse {
        $validated = $request->validate([
            "project_id" => "integer|exists:projects,id",
            "title" => "string",
            "assignees" => "nullable|array",
            "due_date" => "nullable|date",
            "priority" => "nullable|in:low,medium,high,urgent,Low,Normal,High,Urgent",
            "status" => "nullable|in:pending,in_progress,completed,none,in-progress,done",
        ]);

        $validated = $this->standardizeTaskData($validated);

        try {
            $task = $this->taskService->updateTask($id, $validated);
            return $this->successResponse("Task updated successfully.", $task->toArray());
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to update task.", $e);
        }
    }

    /**
     * Remove the specified task.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse {
        try {
            $this->taskService->deleteTask($id);
            return $this->successResponse("Task deleted successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to delete task.", $e);
        }
    }

    /**
     * Store a newly created microtask.
     *
     * @param Request $request
     * @param int $taskId
     * @return JsonResponse
     */
    public function storeMicroTask(Request $request, int $taskId): JsonResponse {
        $validated = $request->validate([
            "title" => "required|string",
            "assignees" => "nullable|array",
            "due_date" => "nullable|date",
            "priority" => "nullable|in:low,medium,high,urgent,Low,Normal,High,Urgent",
            "status" => "nullable|in:pending,in_progress,completed,none,in-progress,done",
        ]);

        $validated = $this->standardizeTaskData($validated);
        $validated["task_id"] = $taskId; // Associate the microtask with the given task

        try {
            $microTask = $this->taskService->createMicroTask($validated);
            return $this->successResponse("Microtask created successfully.", $microTask->toArray(), 201);
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to create microtask.", $e);
        }
    }

    /**
     * Update the specified microtask.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateMicroTask(Request $request, int $id): JsonResponse {
        $validated = $request->validate([
            "title" => "string",
            "assignees" => "nullable|array",
            "due_date" => "nullable|date",
            "priority" => "nullable|in:low,medium,high,urgent,Low,Normal,High,Urgent",
            "status" => "nullable|in:pending,in_progress,completed,none,in-progress,done",
        ]);

        $validated = $this->standardizeTaskData($validated);

        try {
            $microTask = $this->taskService->updateMicroTask($id, $validated);
            return $this->successResponse("Microtask updated successfully.", $microTask->toArray());
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to update microtask.", $e);
        }
    }

    /**
     * Remove the specified microtask.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroyMicroTask(int $id): JsonResponse {
        try {
            $this->taskService->deleteMicroTask($id);
            return $this->successResponse("Microtask deleted successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to delete microtask.", $e);
        }
    }

    /**
     * Standardize priority and status to match migration enum options.
     *
     * @param array $data
     * @return array
     */
    private function standardizeTaskData(array $data): array {
        if (isset($data["priority"])) {
            $priority = strtolower($data["priority"]);
            if ($priority === "normal") {
                $priority = "medium";
            }
            $data["priority"] = $priority;
        }

        if (isset($data["status"])) {
            $status = strtolower($data["status"]);
            if ($status === "none") {
                $status = "pending";
            } elseif ($status === "in-progress") {
                $status = "in_progress";
            } elseif ($status === "done") {
                $status = "completed";
            }
            $data["status"] = $status;
        }

        return $data;
    }
}
