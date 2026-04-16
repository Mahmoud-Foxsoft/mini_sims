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
            $users = UserFacade::filter(
                (array) $request->input('filters'),
            );
        }
        if (! $users) {
            return $this->sendError('No users found', 404);
        }

        return $this->sendResponse($users, 'Users retrieved successfully');
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
