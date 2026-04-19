<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerifyRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Services\RecaptchaService;
use App\Repositories\Facades\UserAuthFacade;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $recaptchaService = new RecaptchaService();
        $isValid = $recaptchaService->checkToken($request->validated('recaptcha_token'), 'user_register');
        if (!$isValid) {
            return response()->json(['error' => 'reCAPTCHA verification failed'], 422);
        }
        $user = UserAuthFacade::register($request->validated());
        if (! $user) {
            return $this->sendError('Registration failed', ['error' => 'Unable to register user'], 500);
        }

        return $this->sendResponse([], 'User registered successfully');
    }

    public function login(UserLoginRequest $request)
    {
        $recaptchaService = new RecaptchaService();
        $isValid = $recaptchaService->checkToken($request->validated('recaptcha_token'), 'user_login');
        if (!$isValid) {
            return response()->json(['error' => 'reCAPTCHA verification failed'], 422);
        }
        $result = UserAuthFacade::login(
            $request->validated('email'),
            $request->validated('password')
        );
        if ($result === 'Invalid credentials') {
            return $this->sendError('Login failed', ['error' => 'Invalid email or password'], 401);
        } elseif ($result === 'Email not verified') {
            return $this->sendError('Login failed', ['error' => 'Email not verified. Please verify your email before logging in.'], 403);
        }

        return $this->sendResponse($result, 'User logged in successfully');
    }

    public function logout(Request $request)
    {
        $result = UserAuthFacade::logout($request);
        if (! $result) {
            return $this->sendError('Logout failed', ['error' => 'Unable to logout user'], 500);
        }

        return $this->sendResponse($result, 'User logged out successfully');
    }

    public function refreshToken(Request $request)
    {
        $result = UserAuthFacade::refreshToken($request);
        if (! $result) {
            return $this->sendError('Token refresh failed', ['error' => 'Unable to refresh token'], 500);
        }

        return $this->sendResponse($result, 'Token refreshed successfully');
    }

    public function getUserProfile(Request $request)
    {
        $result = UserAuthFacade::getUserProfile($request);
        if (! $result) {
            return $this->sendError('User not found', ['error' => 'No user found'], 404);
        }

        return $this->sendResponse($result, 'User profile fetched successfully');
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $recaptchaService = new RecaptchaService();
        $isValid = $recaptchaService->checkToken($request->validated('recaptcha_token'), 'forgot_password');
        if (!$isValid) {
            return response()->json(['error' => 'reCAPTCHA verification failed'], 422);
        }
        $result = UserAuthFacade::forgotPassword($request->validated('email'));
        if (is_array($result) && isset($result['error'])) {
            return $this->sendError('Too many requests', ['error' => $result['error']], 429);
        }

        return $this->sendResponse([], 'Password reset link sent successfully');
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $validated = $request->validated();
        $result = UserAuthFacade::resetPassword(
            $validated['email'],
            $validated['password'],
            $validated['token']
        );
        if (! $result) {
            return $this->sendError('Password reset failed', ['error' => 'Invalid token or email'], 400);
        }

        return $this->sendResponse($result, 'Password reset successfully');
    }

    public function verifyEmail(EmailVerifyRequest $request)
    {
        $result = UserAuthFacade::verifyEmail(
            $request->validated('otp'),
            $request->validated('email')
        );
        if (! $result) {
            return $this->sendError('Email verification failed', ['error' => 'Invalid OTP or email'], 400);
        }

        return $this->sendResponse($result, 'Email verified successfully');
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $result = UserAuthFacade::resendOtp($request->input('email'));
        if (is_array($result) && isset($result['error'])) {
            return $this->sendError('Too many requests', ['error' => $result['error']], 429);
        }
        if (! $result) {
            return $this->sendError('Resend OTP failed', ['error' => 'Unable to resend OTP. Email not found'], 400);
        }

        return $this->sendResponse(null, 'OTP resent successfully');
    }

    public function rotateApiKey(Request $request)
    {
        $result = UserAuthFacade::rotateApiKey($request);
        if (! $result) {
            return $this->sendError('API key rotation failed', ['error' => 'Unable to rotate API key'], 500);
        }

        return $this->sendResponse(['api_key' => $result], 'API key rotated successfully');
    }
}
