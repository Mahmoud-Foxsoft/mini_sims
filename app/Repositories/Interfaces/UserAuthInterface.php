<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Http\Request;

interface UserAuthInterface
{
    /**
     * Register a new user
     * @param array $data
     * @return User|null
     */
    public function register(array $data): User|null;
    /**
     * Login a user and return an access token
     * @param string $email
     * @param string $password
     * @return mixed|null
     */
    public function login(string $email, string $password): mixed;
    /**
     * Logout the authenticated user
     * @param Request $request
     * @return bool
     */
    public function logout(Request $request): bool;
    /**
     * Refresh the access token for the authenticated user
     * @param Request $request
     * @return mixed
     */
    public function refreshToken(Request $request): mixed;
    /**
     * Get the authenticated user's profile
     * @param Request $request
     * @return User|null
     */
    public function getUserProfile(Request $request): User|null;
    /**
     * Send a password reset link to the user's email
     * @param string $email
     * @return string
     */
    public function forgotPassword(string $email): string|null;
    /**
     * Reset the user's password
     * @param array $data
     * @return string
     */
    public function resetPassword(string $email, string $password, string $token): string|null;
    /**
     * Verify user email using OTP
     * @param int $otp
     * @param string $email
     * @return string
     */
    public function verifyEmail(int $otp, string $email): string|null;

    /**
     * Regenerate and resend OTP to user's email
     * @param string $email
     * @return User|null
     */
    public function regenerateOtp(string $email): ?User;
}
