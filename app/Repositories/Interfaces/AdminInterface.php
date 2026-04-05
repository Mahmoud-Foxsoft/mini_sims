<?php

namespace App\Repositories\Interfaces;

use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminInterface
{
    /**
     * Retrieves all administrators with optional filtering and pagination.
     *
     * @param array|null $filters Optional array of filters to apply to the query
     * @return LengthAwarePaginator  collection of administrators
     */
    public function getAllAdmins(?array $filters): LengthAwarePaginator;

    /**
     * Creates a new administrator record.
     *
     * @param string $name The name of the administrator
     * @param string $password The password for the administrator
     * @param string $email The email of the administrator
     * @return Admin The newly created administrator instance
     */

    public function createAdmin(string $name, string $email, string $password): ?Admin;
    /**
     * Updates an existing administrator record.
     *
     * @param Admin $admin The administrator instance to update
     * @param string $name The new name for the administrator
     * @return bool True if the update was successful, false otherwise
     */
    public function updateAdmin(Admin $admin, ?string $name, ?string $email, ?string $password): bool;

    /**
     * Deletes an administrator record.
     *
     * @param mixed $id The administrator ID to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteAdmin(Admin $admin): bool;

}
