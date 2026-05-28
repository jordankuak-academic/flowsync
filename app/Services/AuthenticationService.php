<?php
namespace App\Services;

use App\Models\User;
use App\Utilities\ResponseFormatters\ApiResponse;
use App\Utilities\Security\SystemSecurity;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationService {
    use ApiResponse, SystemSecurity;

    /**
     * Handle The Administrator Login Authentication Service
     *
     * @param string $email The Administrator's Email Address
     * @param string $password The Administrator's Password
     * @param string $ipAddress The Client's Ip Address
     * @return JsonResponse The Login Response Message
     */
    public function login($email, $password, $ipAddress): JsonResponse {
        // Step 01 => Check The Client's Ip Address Is In The Blacklist.
        if ($this->isBlacklisted($ipAddress)) {
            return $this->errorResponse("Access Denied. Your Ip Address Has Been Blocked.", statusCode: 403);
        }
        // Step 02 => Check The Client's Ip Address Is In The Throttled.
        if ($this->isThrottled($ipAddress)) {
            return $this->errorResponse("Access Denied. Your Ip Address Has Been Throttled.", statusCode: 429);
        }
        // Step 03 => Check The Client's Credentials.
        $user = User::where("email", $email)->first();
        if (!$user) {
            $this->handleFailedAttempt($ipAddress, $email);
            return $this->errorResponse("Invalid Credentials. Please Check Your Email And Password.", statusCode: 401);
        }
        // Step 04 => Check The Client's Password.
        if (!Hash::check($password, $user->password)) {
            $this->handleFailedAttempt($ipAddress, $email);
            return $this->errorResponse("Invalid Credentials. Please Check Your Email And Password.", statusCode: 401);
        }
        // If All Checks Passed, Generate A New Token.
        $this->clearAttempts($ipAddress);
        Auth::login($user);
        return $this->successResponse("Login Successful.", statusCode: 200);
    }

    /**
     * Handle The Administrator Logout Authentication Service
     * Destory The Current Administrator Session
     *
     * @return bool True If The Logout Was Successful, False Otherwise
     */
    public function logout(): bool {
        try {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
