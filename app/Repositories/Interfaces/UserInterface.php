<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * User Repository Interface
 *
 * Defines the contract for user data access operations.
 * This interface provides methods for retrieving, updating, and filtering user records.
 */
interface UserInterface
{
    /**
     * Find a user by ID
     *
     * @param int $id The user ID to search for
     * @return User|null Returns the User model instance if found, null otherwise
     */
    public function find(int $id): ?User;

    /**
     * Update a user record
     *
     * @param int $id The ID of the user to update
     * @param array $data Associative array containing the fields to update
     * @return bool Returns true if the update was successful, false otherwise
     */
    public function update(int $id, array $data): bool;

    /**
     * Filter users based on given criteria
     *
     * @param array $filters Associative array of filter criteria (e.g., ['name' => 'John', 'status' => 'active'])
     * @return LengthAwarePaginator Paginated collection of filtered user results
     */
    public function filter(array $filters): LengthAwarePaginator;

    /**
     * Add balance to a user's account.
     *
     * Increases the user's balance by the given amount.
     *
     * @param  \App\Models\User $user    The user whose balance will be updated.
     * @param  float            $amount  The amount to add to the user's balance.
     * @return bool  True on success, false on failure.
     */
    public function addBalance(User $user, float $amount): bool;

    public function checkBalance(User $user, float $amount): bool;

    public function sumTotalUsersMonthly(?Carbon $from, ?Carbon $to): int;

    public function getForSelect(string $email): LengthAwarePaginator;
}
