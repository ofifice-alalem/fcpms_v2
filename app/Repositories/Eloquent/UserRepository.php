<?php

namespace App\Repositories\Eloquent;

use App\Enums\UserStatus;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Specify Model class name for BaseRepository.
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * Find a user by username or email.
     */
    public function findByUsernameOrEmail(string $loginInput): ?User
    {
        return $this->model->newQuery()
            ->where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();
    }

    /**
     * Update the last login timestamp for the given user.
     */
    public function updateLastLogin(int $userId): bool
    {
        return (bool) $this->model->newQuery()
            ->where('id', $userId)
            ->update(['last_login_at' => now()]);
    }

    /**
     * Check if the user account is active.
     */
    public function isUserActive(User $user): bool
    {
        return $user->status === UserStatus::ACTIVE;
    }
}
