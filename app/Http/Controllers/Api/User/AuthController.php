<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerifyRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Models\UserProxy;
use App\Repositories\Facades\EmployeeFacade;
use App\Repositories\Facades\UserAuthFacade;
use App\Repositories\Facades\UserFacade;
use App\Services\FirebaseOtpService;
use App\Services\ProxyEmpireService;
use App\Services\RecaptchaService;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct() {}

    public function register(UserRegisterRequest $request)
    {
        try {
            $recaptchaService = new RecaptchaService;
            $isValid = $recaptchaService->checkToken($request->validated('recaptcha_token'), 'user_register');
            if (! $isValid) {
                return response()->json(['error' => 'reCAPTCHA verification failed'], 422);
            }
            $user = UserAuthFacade::register($request->validated());
            // $sessionData = $this->firebaseOtpService->send($request->validated('phone'), $request->validated('recaptcha_token'))['sessionInfo'];
            // $sent = TwilioService::new()->sendVerificationCode($request->validated('phone'));
            // if (! $sent) {
            //     return $this->sendError('OTP Send failed', ['error' => 'Unable to send OTP. Max attempts exceeded or phone number not found'], 400);
            // }
            if (! $user) {
                return $this->sendError('Registration failed', ['error' => 'Unable to register user'], 500);
            }

            // $user->session_id = $sessionData;
            return $this->sendResponse($user, 'User registered successfully');
        } catch (\Throwable $th) {
            return $this->sendError('Error Registering', ['error' => $th->getMessage()]);
        }
    }

    public function login(UserLoginRequest $request)
    {
        $recaptchaService = new RecaptchaService;
        $isValid = $recaptchaService->checkToken($request->validated('recaptcha_token'), 'user_login');
        if (! $isValid) {
            return response()->json(['error' => 'reCAPTCHA verification failed'], 422);
        }
        $result = UserAuthFacade::login(
            $request->validated('email'),
            $request->validated('password')
        );
        if ($result === 'Invalid credentials') {
            return $this->sendError('Login failed', ['error' => 'Invalid email or password'], 401);
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
        $recaptchaService = new RecaptchaService;
        $isValid = $recaptchaService->checkToken($request->validated('recaptcha_token'), 'forgot_password');
        if (! $isValid) {
            return response()->json(['error' => 'reCAPTCHA verification failed'], 422);
        }
        UserAuthFacade::forgotPassword($request->validated('email'));

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
        try {
            // $result = $this->firebaseOtpService->verify(
            //     $request->validated('session_id'),
            //     $request->validated('otp')
            // );
            $phone = auth()->user()->phone;
            $isValid = TwilioService::new()->verifyCode(
                $phone,
                $request->validated('otp')
            );
            if (! $isValid) {
                return $this->sendError('Invalid Otp', ['error' => 'The provided OTP is incorrect'], 400);
            }
            $result = ['phoneNumber' => $phone];
            $verified = UserAuthFacade::verifyEmail(
                $result['phoneNumber']
            );
            if (! $verified) {
                return $this->sendError('Email verification failed', ['error' => 'Invalid OTP or phone number'], 400);
            }

            return $this->sendResponse($verified, 'Email verified successfully');
        } catch (\Throwable $th) {
            return $this->sendError('Invalid Otp', ['error' => 'An error occurred during verification'], 500);
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->phone != $request->input('phone')) {
                UserAuthFacade::changePhone($user, $request->input('phone'));
            }
            if ($user->phone_verified_at) {
                return $this->sendError('Resend OTP failed', ['error' => 'Cannot change phone number after verification'], 400);
            }
            $increment = UserAuthFacade::resendOtp($user);
            // $sessionData = $this->firebaseOtpService->send(auth()->user()->phone)['sessionInfo'];
            $sent = TwilioService::new()->sendVerificationCode(auth()->user()->phone);

            if (! $increment || ! $sent) {
                return $this->sendError('Resend OTP failed', ['error' => 'Unable to resend OTP. Max attempts exceeded or phone number not found'], 400);
            }

            return $this->sendResponse(['session_id' => null], 'OTP resent successfully');
        } catch (\Throwable $th) {
            Log::error('Resend OTP error: '.$th->getMessage());

            return $this->sendError('Resend OTP failed', ['error' => 'An error occurred while resending OTP'], 500);
        }
    }

    public function changePhone(Request $request)
    {
        $user = $request->user();
        $newPhone = $request->input('new_phone');

        if (User::where('phone', $newPhone)->exists()) {
            return $this->sendError('Phone change failed', ['error' => 'Phone number already in use'], 400);
        }

        $user->phone = $newPhone;
        $user->email_verified_at = null; // Mark email as unverified
        $user->save();

        return $this->sendResponse([], 'Phone number changed successfully. Please verify your new phone number.');
    }

    public function getUserDetails(Request $request)
    {
        $user = $request->user();
        $teams = $user->teams()->get(['id', 'name', 'owner_id']);
        $maxRunning = EmployeeFacade::sumEmployeeAndMaxRunning($user->id)['max_running_emails'];
        $plan = UserFacade::getUserPlan($user);

        return $this->sendResponse(['teams' => $teams, 'maxRunning' => $maxRunning, 'plan' => $plan], 'User details fetched successfully');
    }

    public function getUserPlan(Request $request)
    {
        $user = $request->user();
        $plan = UserFacade::getUserPlan($user);

        return $this->sendResponse(['plan' => $plan], 'Plan details fetched successfully');
    }

}
