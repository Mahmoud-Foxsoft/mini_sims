<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Requests\UserUpdateRequest;
use App\Repositories\Facades\UserFacade;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(UserUpdateRequest $request)
    {
        $updated = UserFacade::update($request->user()->id, $request->validated());
        if (!$updated) {
            return $this->sendError('Update failed', ['error' => 'Unable to update user'], 500);
        }
        return $this->sendResponse($updated, 'User updated successfully');
    }
}
