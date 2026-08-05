<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * Authenticate user credentials and enforce business rules BR-001 & BR-002.
     *
     * @throws ValidationException
     */
    public function authenticate(string $login, string $password, bool $remember = false): User
    {
        $user = $this->userRepository->findByUsernameOrEmail($login);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        // BR-002: Check active status
        if (! $this->userRepository->isUserActive($user)) {
            throw ValidationException::withMessages([
                'login' => ['الحساب غير نشط أو موقوف مؤقتاً. يرجى مراجعة إدارة الموارد البشرية.'],
            ]);
        }

        // Authenticate & create session
        Auth::login($user, $remember);

        // BR-002: Update last login timestamp
        $this->userRepository->updateLastLogin($user->id);

        return $user;
    }
}
