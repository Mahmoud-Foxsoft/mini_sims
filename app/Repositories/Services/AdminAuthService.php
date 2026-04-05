<?php

namespace App\Repositories\Services;

use App\Repositories\Interfaces\AdminAuthInterface;
use Illuminate\Http\Request;


class AdminAuthService
{
    public function __construct(protected AdminAuthInterface $adminAuthRepository) {}

    public function login(string $email, string $password)
    {
        return $this->adminAuthRepository->login($email, $password);
    }

    public function logout(Request $request)
    {
        return $this->adminAuthRepository->logout($request);
    }

    public function refreshToken(Request $request)
    {
        return $this->adminAuthRepository->refreshToken($request);
    }

    public function getAdminProfile(Request $request)
    {
        return $this->adminAuthRepository->getAdminProfile($request);
    }
}
