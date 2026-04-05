<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Repositories\Facades\AdminAuthFacade;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        // $recaptchaService = new RecaptchaService;
        // $isValid = $recaptchaService->checkToken($request->validated('recaptcha_token'), 'admin_login');
        // if (! $isValid) {
        //     return response()->json(['error' => 'reCAPTCHA verification failed'], 422);
        // }

        $admin = AdminAuthFacade::login($data['email'], $data['password']);
        if (! $admin) {
            return $this->sendError('Invalid credentials', 401);
        }

        return $this->sendResponse($admin, 'Admin logged in successfully');
    }

    public function logout(Request $request)
    {
        $response = AdminAuthFacade::logout($request);
        if (! $response) {
            return $this->sendError('Logout failed', 500);
        }

        return $this->sendResponse($response, 'Admin logged out successfully');
    }

    public function getProfile(Request $request)
    {
        $admin = AdminAuthFacade::getAdminProfile($request);
        if (! $admin) {
            return $this->sendError('Admin profile not found', 404);
        }

        return $this->sendResponse($admin, 'Admin profile retrieved successfully');
    }

    public function refreshToken(Request $request)
    {
        $admin = AdminAuthFacade::refreshToken($request);
        if (! $admin) {
            return $this->sendError('Token refresh failed', 401);
        }

        return $this->sendResponse($admin, 'Token refreshed successfully');
    }
}
