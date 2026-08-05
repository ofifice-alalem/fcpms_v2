<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a user by username or email.
     */
    public function findByUsernameOrEmail(string $loginInput): ?User;

    /**
     * Update the last login timestamp for the given user.
     */
    public function updateLastLogin(int $userId): bool;

    /**
     * Check if the user account is active.
     */
    public function isUserActive(User $user): bool;
}
