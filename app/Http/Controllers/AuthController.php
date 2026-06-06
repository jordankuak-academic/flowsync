<?php
namespace App\Http\Controllers;

use App\Services\AuthenticationService;
use App\Utilities\ResponseFormatters\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller {
    use ApiResponse;

    protected AuthenticationService $authService;

    public function __construct(AuthenticationService $authService) {
        $this->authService = $authService;
    }

    /**
     * Handle user login.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        return $this->authService->login(
            $request->input("email"),
            $request->input("password"),
            $request->ip()
        );
    }

    /**
     * Handle user logout.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse {
        $result = $this->authService->logout();
        if ($result) {
            return $this->successResponse("Logout Successful.");
        }
        return $this->errorResponse("An error occurred during logout.");
    }
}
