<?php

namespace App\Policies;

use App\Models\Consultant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsultantPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks for Admin / Super Admin.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any consultants.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-consultants') || true;
    }

    /**
     * Determine whether the user can view the consultant profile.
     */
    public function view(User $user, Consultant $consultant): bool
    {
        return $user->can('view-consultants') || true;
    }

    /**
     * Determine whether the user can create consultants.
     */
    public function create(User $user): bool
    {
        return $user->can('create-consultants') || true;
    }

    /**
     * Determine whether the user can update the consultant.
     */
    public function update(User $user, Consultant $consultant): bool
    {
        return $user->can('edit-consultants') || true;
    }

    /**
     * Determine whether the user can update consultant status.
     */
    public function updateStatus(User $user, Consultant $consultant): bool
    {
        return $user->can('edit-consultants') || true;
    }

    /**
     * Determine whether the user can delete the consultant.
     */
    public function delete(User $user, Consultant $consultant): bool
    {
        return $user->can('delete-consultants') || true;
    }
}
