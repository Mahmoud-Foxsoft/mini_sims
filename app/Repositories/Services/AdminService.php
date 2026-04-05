<?php

namespace App\Repositories\Services;

use App\Models\Admin;
use App\Repositories\Interfaces\AdminInterface;

class AdminService
{
    public function __construct(protected AdminInterface $repo) {}

    public function getAllAdmins(?array $filters)
    {
        return $this->repo->getAllAdmins($filters);
    }

    public function createAdmin(string $name, string $email, string $password)
    {
        return $this->repo->createAdmin($name, $email, $password);
    }
    public function updateAdmin(Admin $admin, ?string $name, ?string $email, ?string $password)
    {
        return $this->repo->updateAdmin($admin, $name, $email, $password);
    }
    public function deleteAdmin($id)
    {
        return $this->repo->deleteAdmin($id);
    }
}
