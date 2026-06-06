<?php
namespace App\Http\Controllers;

use App\Services\ProjectService;
use App\Utilities\ResponseFormatters\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller {
    use ApiResponse;

    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService) {
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse {
        try {
            $status = $request->query("status"); // optionally filter by 'inprogress' or 'done'
            $projects = $this->projectService->getAllProjects($status);
            return $this->successResponse("Projects retrieved successfully.", $projects->toArray());
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to retrieve projects.", $e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse {
        $validated = $request->validate([
            "team_id" => "required|integer|exists:teams,id",
            "title" => "required|string",
            "description" => "required|string",
        ]);

        try {
            $project = $this->projectService->createProject($validated);
            return $this->successResponse("Project created successfully.", $project->toArray(), 201);
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to create project.", $e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse {
        try {
            $project = $this->projectService->getProjectById($id);
            return $this->successResponse("Project retrieved successfully.", $project->toArray());
        } catch (\Exception $e) {
            return $this->errorResponse("Project not found.", $e, 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse {
        $validated = $request->validate([
            "team_id" => "integer|exists:teams,id",
            "title" => "string",
            "description" => "string",
        ]);

        try {
            $project = $this->projectService->updateProject($id, $validated);
            return $this->successResponse("Project updated successfully.", $project->toArray());
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to update project.", $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse {
        try {
            $this->projectService->deleteProject($id);
            return $this->successResponse("Project deleted successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to delete project.", $e);
        }
    }
}
