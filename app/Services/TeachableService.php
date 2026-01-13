<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TeachableService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $courseId;

    public function __construct()
    {
        $this->apiKey = config('services.teachable.api_key');
        $this->baseUrl = config('services.teachable.base_url');
        $this->courseId = config('services.teachable.course_id');
    }

    /**
     * Create a user in Teachable
     */
    public function createUser(string $email, string $name = null): ?array
    {
        try {
            // Generate a random password for the user
            $password = bin2hex(random_bytes(16));

            $response = Http::withHeaders([
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/v1/users", [
                'email' => $email,
                'name' => $name ?? explode('@', $email)[0],
                'password' => $password,
            ]);

            Log::info('Teachable API request', [
                'url' => "{$this->baseUrl}/v1/users",
                'email' => $email,
                'status' => $response->status(),
            ]);

            if ($response->successful()) {
                Log::info('Teachable user created', [
                    'email' => $email,
                    'response' => $response->json(),
                ]);
                return $response->json();
            }

            // User might already exist - try to find them
            if ($response->status() === 422 || $response->status() === 409) {
                Log::info('User may already exist, attempting to find', ['email' => $email]);
                return $this->findUserByEmail($email);
            }

            Log::error('Failed to create Teachable user', [
                'email' => $email,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Teachable createUser exception', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Find a user by email
     */
    public function findUserByEmail(string $email): ?array
    {
        try {
            $response = Http::withHeaders([
                'apiKey' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/v1/users", [
                'email' => $email,
            ]);

            if ($response->successful()) {
                $users = $response->json('users') ?? [];
                return !empty($users) ? $users[0] : null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Teachable findUserByEmail exception', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Enroll a user in a course
     */
    public function enrollUser(int $userId, int $courseId = null): bool
    {
        try {
            $courseId = $courseId ?? (int) $this->courseId;

            $response = Http::withHeaders([
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/v1/enroll", [
                'user_id' => $userId,
                'course_id' => $courseId,
            ]);

            Log::info('Teachable enroll API request', [
                'url' => "{$this->baseUrl}/v1/enroll",
                'user_id' => $userId,
                'course_id' => $courseId,
                'status' => $response->status(),
            ]);

            if ($response->successful()) {
                Log::info('User enrolled in Teachable course', [
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'response' => $response->json(),
                ]);
                return true;
            }

            Log::error('Failed to enroll user in Teachable course', [
                'user_id' => $userId,
                'course_id' => $courseId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Teachable enrollUser exception', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Create user and enroll in course (convenience method)
     */
    public function createAndEnroll(string $email, string $name = null, int $courseId = null): bool
    {
        // Step 1: Create or find user
        $user = $this->createUser($email, $name);

        if (!$user || !isset($user['id'])) {
            Log::error('Cannot enroll - user not found or created', ['email' => $email]);
            return false;
        }

        // Step 2: Enroll user in course
        return $this->enrollUser($user['id'], $courseId);
    }
}
