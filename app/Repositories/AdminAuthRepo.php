<?php

namespace App\Repositories;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\AdminAuthInterface;
use Illuminate\Http\Request;

class AdminAuthRepo implements AdminAuthInterface
{
    public function __construct(private Admin $model) {}

    public function login(string $email, string $password): mixed
    {
        $admin = $this->model->where('email', $email)->first();
        if (!$admin || !Hash::check($password, $admin->password)) {
            return null;
        }
        $token = $admin->createToken('Admin Token', ['admin-api'])->accessToken;
        $admin->token = $token;
        return $admin;
    }

    public function logout(Request $request): bool
    {
        $admin = $request->user('admin');
        if (!$admin) {
            return false;
        }
        $admin->token()->revoke();
        return true;
    }

    public function refreshToken(Request $request): mixed
    {
        $admin = $request->user('admin') ?? null;
        if (!$admin) {
            return null;
        }
        $token = $admin->createToken('Admin Token', ['admin-api'])->accessToken;
        $admin->token = $token;
        return $admin;
    }

    public function getAdminProfile(Request $request): ?Admin
    {
        $admin = $request->user('admin') ?? null;
        return $admin;
    }
}
