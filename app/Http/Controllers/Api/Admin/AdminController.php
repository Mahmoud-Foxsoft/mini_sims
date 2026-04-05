<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminCreateRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Models\Admin;
use App\Repositories\Facades\AdminFacade;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $admins = AdminFacade::getAllAdmins((array) $request->input('filters'));
        if ($admins->isEmpty()) {
            return $this->sendResponse([], 'No admins found');
        }
        return $this->sendResponse($admins, 'Admins retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AdminCreateRequest $request)
    {
        $data = $request->validated();
        $admin = AdminFacade::createAdmin($data['name'], $data['email'], $data['password']);
        if (!$admin) {
            return $this->sendError('Admin creation failed', [], 500);
        }
        return $this->sendResponse($admin, 'Admin created successfully');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  AdminUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AdminUpdateRequest $request, Admin $admin)
    {
        $data = $request->validated();
        $admin = AdminFacade::updateAdmin(
            $admin,
            $data['name'] ?? null,
            $data['email'] ?? null,
            $data['password'] ?? null
        );
        if (!$admin) {
            return $this->sendError('Admin update failed', [], 500);
        }
        return $this->sendResponse($admin, 'Admin updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Admin $admin)
    {
        $deleted = AdminFacade::deleteAdmin($admin);
        if (!$deleted) {
            return $this->sendError('Admin deletion failed', [], 404);
        }
        return $this->sendResponse(null, 'Admin deleted successfully');
    }
}
