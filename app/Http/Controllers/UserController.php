<?php
namespace App\Http\Controllers;

use App\Services\UserService;
use App\Utilities\ResponseFormatters\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller {
    use ApiResponse;

    protected UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse {
        try {
            $users = $this->userService->getAllUsers();
            return $this->successResponse("Users retrieved successfully.", $users->toArray());
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to retrieve users.", $e);
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
            "role_id" => "required|integer|exists:roles,id",
            "staff_id" => "required|string|unique:users,staff_id",
            "name" => "required|string",
            "username" => "required|string|unique:users,username",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:6",
            "job_title" => "required|string",
            "ic_number" => "required|string|unique:users,ic_number",
            "contact" => "required|string|unique:users,contact",
            "is_active" => "boolean",
        ]);

        try {
            $user = $this->userService->createUser($validated);
            return $this->successResponse("User created successfully.", $user->toArray(), 201);
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to create user.", $e);
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
            $user = $this->userService->getUserById($id);
            return $this->successResponse("User retrieved successfully.", $user->toArray());
        } catch (\Exception $e) {
            return $this->errorResponse("User not found.", $e, 404);
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
            "role_id" => "integer|exists:roles,id",
            "staff_id" => "string|unique:users,staff_id," . $id,
            "name" => "string",
            "username" => "string|unique:users,username," . $id,
            "email" => "email|unique:users,email," . $id,
            "password" => "nullable|string|min:6",
            "job_title" => "string",
            "ic_number" => "string|unique:users,ic_number," . $id,
            "contact" => "string|unique:users,contact," . $id,
            "is_active" => "boolean",
        ]);

        try {
            $user = $this->userService->updateUser($id, $validated);
            return $this->successResponse("User updated successfully.", $user->toArray());
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to update user.", $e);
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
            $this->userService->deleteUser($id);
            return $this->successResponse("User deleted successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to delete user.", $e);
        }
    }
}
