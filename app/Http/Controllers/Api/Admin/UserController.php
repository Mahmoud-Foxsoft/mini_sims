<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserUpdateRequest;
use App\Repositories\Facades\UserFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of users with subscription info.
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->input('select')) {
            $users = UserFacade::getForSelect($request->input('email') ?? '');
        } else {
            $users = UserFacade::getPaginatedWithSubscriptions(
                (array) $request->input('filters'),
                (int) $request->input('per_page', 25)
            );
        }
        if (! $users) {
            return $this->sendError('No users found', 404);
        }

        return $this->sendResponse($users, 'Users retrieved successfully');
    }

    /**
     * Display the specified user with full details.
     */
    public function show(int $id): JsonResponse
    {
        $user = UserFacade::getAdminDetailsById($id);

        if (! $user) {
            return $this->sendError('User not found', [], 404);
        }

        return $this->sendResponse($user, 'User retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUserUpdateRequest $request, int $id): JsonResponse
    {
        $updated = UserFacade::update($id, $request->validated());
        if (! $updated) {
            return $this->sendError('Update failed', ['error' => 'Unable to update user'], 500);
        }

        return $this->sendResponse($updated, 'User updated successfully');
    }
}
