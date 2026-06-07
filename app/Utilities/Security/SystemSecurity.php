<?php
namespace App\Utilities\Security;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

trait SystemSecurity {
    /**
     * Check If The IP Address Is Throttled.
     *
     * @param mixed $ipAddress The IP Address To Check If It Is Throttled
     * @throws Exception If Error Occurred While Checking If IP Address Is Throttled
     * @return bool True If IP Address Is Throttled, False Otherwise
     */
    protected function isThrottled($ipAddress): bool {
        try {
            $cacheKey = "throttle_" . str_replace(".", "_", $ipAddress);
            return Cache::has($cacheKey);
        } catch (Exception $exception) {
            throw new Exception("Error While Checking If IP Address Is Throttled: {$exception->getMessage()}");
        }
    }

    /**
     * Check If The IP Address Is Blacklisted.
     *
     * @param mixed $ipAddress The IP Address To Check If It Is Blacklisted
     * @param mixed $duration The Duration In Minutes To Check If The Blacklist Expired
     * @throws Exception If Error Occurred While Checking If IP Address Is Blacklisted
     * @return bool True If IP Address Is Blacklisted, False Otherwise
     */
    protected function isBlacklisted($ipAddress, $duration = 1440): bool {
        try {
            $filePath = "security/ip_blacklists/blacklists.json";

            if (!Storage::disk("local")->exists($filePath)) {
                return false;
            }

            $blacklists = json_decode(Storage::disk("local")->get($filePath), true) ?? [];

            foreach ($blacklists as $blacklist) {
                if ($blacklist["ip_address"] === $ipAddress) {
                    $blacklistTime = Carbon::parse($blacklist["blacklisted_at"]);

                    if ($blacklistTime->addMinutes($duration)->isFuture()) {
                        return true;
                    } else {
                        $this->removeBlacklist($ipAddress);
                        return false;
                    }
                }
            }
            return false;
        } catch (Exception $exception) {
            throw new Exception("Error While Checking If IP Address Is Blacklisted: {$exception->getMessage()}");
        }
    }

    /**
     * Handle Failed Attempt When User Login Failed Attempts Or Reach Maximum Login Failed Attempts.
     *
     * @param string $ipAddress The User IP Address
     * @param string $email The User Email Address
     * @param int $maxAttempts The Maximum Login Failed Attempts Allowed
     * @throws Exception If Error Occurred While Handling Failed Attempt
     * @return void
     */
    protected function handleFailedAttempt($ipAddress, $email, $maxAttempts = 5): void {
        try {
            $fileName = "failed_attempts_" . str_replace(".", "_", $ipAddress) . ".json";
            $filePath = "security/ip_attempts/{$fileName}";
            $this->createSecurityJsonFile($filePath);

            $attempts = json_decode(Storage::disk("local")->get($filePath), true) ?? [];
            $attempts[] = [
                "ip_address" => $ipAddress,
                "email" => $email,
                "attempted_at" => Carbon::now()->toISOString(),
                "user_agent" => request()->userAgent(),
            ];

            Storage::disk("local")->put($filePath, json_encode($attempts, JSON_PRETTY_PRINT));

            if (\count($attempts) >= $maxAttempts) {
                $this->applyBlacklist($ipAddress);
                $this->clearAttempts($ipAddress);
            }

            $this->throttleMonitoring($ipAddress, $attempts);
        } catch (Exception $exception) {
            throw new Exception("Error While Handling Failed Attempt: {$exception->getMessage()}");
        }
    }

    /**
     * Clear Failed Attempts For The Specified IP Address.
     *
     * @param string $ipAddress The IP Address To Clear Failed Attempts
     * @throws Exception If Error Occurred While Clearing Failed Attempts
     * @return void
     */
    protected function clearAttempts($ipAddress): void {
        try {
            $fileName = "failed_attempts_" . str_replace(".", "_", $ipAddress) . ".json";
            $filePath = "security/ip_attempts/{$fileName}";

            if (Storage::disk("local")->exists($filePath)) {
                Storage::disk("local")->delete($filePath);
            }
        } catch (Exception $exception) {
            throw new Exception("Error While Clearing Failed Attempts: {$exception->getMessage()}");
        }
    }

    /**
     * Monitoring The User Request To Apply Throttle For Prevent Brute Force Attack.
     *
     * @param string $ipAddress The User IP Address
     * @param array $attempts The User Attempts
     * @param int $throttleFrequency The Throttle Frequency In Minutes
     * @param int $maxRequest The Maximum Request Allowed In Throttle Frequency
     * @param int $throttleDuration The Throttle Duration In Seconds
     * @throws Exception If Error Occurred While Monitoring Throttle
     * @return void
     */
    private function throttleMonitoring($ipAddress, $attempts, $throttleFrequency = 1, $maxRequest = 10, $throttleDuration = 30): void {
        try {
            $timeNow = Carbon::now();
            $windowStart = $timeNow->copy()->subMinutes($throttleFrequency);

            $recentAttempts = array_filter($attempts, function($attempt) use ($windowStart): bool {
                $attemptTime = Carbon::parse($attempt["attempted_at"]);
                return $attemptTime->greaterThan($windowStart);
            });

            if (\count($recentAttempts) >= $maxRequest) {
                $cacheKey = "throttle_" . str_replace(".", "_", $ipAddress);
                Cache::put($cacheKey, [
                    "throttled_at" =>Carbon::now()->toISOString(),
                    "expires_at" => Carbon::now()->addSeconds($throttleDuration)->toISOString(),
                ], $throttleDuration);
            }
        } catch (Exception $exception) {
            throw new Exception("Error While Monitoring Throttle: {$exception->getMessage()}");
        }
    }

    /**
     * Apply Blacklist For The Specified IP Address.
     *
     * @param string $ipAddress The IP Address To Apply Blacklist
     * @throws Exception If Error Occurred While Applying Blacklist
     * @return void
     */
    private function applyBlacklist($ipAddress): void {
        try {
            $filePath = "security/ip_blacklists/blacklists.json";
            $this->createSecurityJsonFile($filePath);

            $blacklists = json_decode(Storage::disk("local")->get($filePath), true) ?? [];

            foreach ($blacklists as $blacklist) {
                if ($blacklist["ip_address"] === $ipAddress) {
                    return;
                }
            }

            $blacklists[] = [
                "ip_address" => $ipAddress,
                "blacklisted_at" => Carbon::now()->toISOString(),
                "reason" => "Exceeded Maximum Login Failed Attempts",
            ];

            Storage::disk("local")->put($filePath, json_encode($blacklists, JSON_PRETTY_PRINT));
        } catch (Exception $exception) {
            throw new Exception("Error While Applying Blacklist: {$exception->getMessage()}");
        }
    }

    /**
     * Remove Blacklist For The Specified IP Address.
     *
     * @param string $ipAddress The IP Address To Remove Blacklist
     * @throws Exception If Error Occurred While Removing Blacklist
     * @return void
     */
    private function removeBlacklist($ipAddress): void {
        try {
            $filePath = "security/ip_blacklists/blacklists.json";

            if (!Storage::disk("local")->exists($filePath)) {
                return;
            }

            $blacklists = json_decode(Storage::disk("local")->get($filePath), true) ?? [];
            $filteredBlacklists = array_filter($blacklists, fn($blacklist): bool => $blacklist["ip_address"] !== $ipAddress);

            Storage::disk("local")->put($filePath, json_encode($filteredBlacklists, JSON_PRETTY_PRINT));
        } catch (Exception $exception) {
            throw new Exception("Error While Removing Blacklist: {$exception->getMessage()}");
        }
    }

    /**
     * Create Security JSON File.
     *
     * @param string $filePath The Path Of The Security JSON File Location
     * @throws Exception If Error Occurred While Creating Security JSON File
     * @return void
     */
    private function createSecurityJsonFile($filePath): void {
        try {
            if (!Storage::disk("local")->exists($filePath)) {
                $directory = dirname($filePath);
                if (!Storage::disk("local")->exists($directory)) {
                    Storage::disk("local")->makeDirectory($directory);
                }
                Storage::disk("local")->put($filePath, json_encode([]));
            }
        } catch (Exception $exception) {
            throw new Exception("Error While Creating Security JSON File: {$exception->getMessage()}");
        }
    }
}
