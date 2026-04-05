<?php

namespace App\Repositories;


use App\Models\Admin;
use App\Repositories\Interfaces\AdminInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class AdminRepository implements AdminInterface
{
    public function __construct(private Admin $model) {}
    public function getAllAdmins(?array $filters): LengthAwarePaginator
    {
        $query = $this->model::when($filters['search'] ?? null, function ($q) use ($filters) {
            $search = $filters['search'];
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        });

        return $query->paginate(20);
    }



    public function createAdmin(string $name, string $email, string $password): Admin
    {
        return $this->model::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    public function updateAdmin(Admin $admin, ?string $name = null, ?string $email = null, ?string $password = null): bool
    {
        $data = array_filter([
            'name' => $name,
            'email' => $email,
            'password' => $password ? Hash::make($password) : null,
        ], fn($value) => !is_null($value));

        return $admin->update($data);
    }

    public function deleteAdmin(Admin $admin): bool
    {
        return $admin->delete();
    }
}
