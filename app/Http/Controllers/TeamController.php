<?php
namespace App\Http\Controllers;

use App\Services\TeamService;
use App\Utilities\ResponseFormatters\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller {
    use ApiResponse;

    protected TeamService $teamService;

    public function __construct(TeamService $teamService) {
        $this->teamService = $teamService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse {
        try {
            $teams = $this->teamService->getAllTeams();
            return $this->successResponse("Teams retrieved successfully.", $teams->toArray());
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to retrieve teams.", $e);
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
            "department_id" => "required|integer|exists:departments,id",
            "leader_id" => "required|integer|exists:users,id",
            "member_id" => "nullable|integer|exists:users,id",
            "is_active" => "boolean",
        ]);

        try {
            $team = $this->teamService->createTeam($validated);
            return $this->successResponse("Team created successfully.", $team->toArray(), 201);
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to create team.", $e);
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
            $team = $this->teamService->getTeamById($id);
            return $this->successResponse("Team retrieved successfully.", $team->toArray());
        } catch (\Exception $e) {
            return $this->errorResponse("Team not found.", $e, 404);
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
            "department_id" => "integer|exists:departments,id",
            "leader_id" => "integer|exists:users,id",
            "member_id" => "nullable|integer|exists:users,id",
            "is_active" => "boolean",
        ]);

        try {
            $team = $this->teamService->updateTeam($id, $validated);
            return $this->successResponse("Team updated successfully.", $team->toArray());
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to update team.", $e);
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
            $this->teamService->deleteTeam($id);
            return $this->successResponse("Team deleted successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to delete team.", $e);
        }
    }
}
