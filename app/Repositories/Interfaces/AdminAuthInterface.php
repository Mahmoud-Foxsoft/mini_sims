<?php

namespace App\Repositories\Interfaces;

use App\Models\Admin;
use Illuminate\Http\Request;

interface AdminAuthInterface
{
    /**
     * Authenticates an admin user with email and password.
     *
     * @param string $email The admin's email address
     * @param string $password The admin's password
     * @return mixed Returns authentication token or user data on success, false on failure
     */
    public function login(string $email, string $password): mixed;

    /**
     * Logs out the currently authenticated admin user.
     *
     * @param Request $request The HTTP request containing authentication data
     * @return bool Returns true if logout was successful, false otherwise
     */
    public function logout(Request $request): bool;

    /**
     * Refreshes the authentication token for the current admin session.
     *
     * @param Request $request The HTTP request containing the current token
     * @return mixed Returns new token data on success, false on failure
     */
    public function refreshToken(Request $request): mixed;

    /**
     * Retrieves the profile information of the currently authenticated admin.
     *
     * @param Request $request The HTTP request containing authentication data
     * @return Admin|null Returns Admin model instance if found, null if not authenticated or user not found
     */
    public function getAdminProfile(Request $request): Admin|null;
}
