<?php

namespace App\Services;

use App\Enums\ConsultantStatus;
use App\Enums\UserStatus;
use App\Events\ConsultantUpdatedEvent;
use App\Models\Consultant;
use App\Models\User;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class ConsultantService
{
    public function __construct(
        protected ConsultantRepositoryInterface $consultantRepository,
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * Register a new consultant and create parallel user account (BR-003).
     */
    public function registerNewConsultant(array $data): Consultant
    {
        return DB::transaction(function () use ($data) {
            // 1. Auto generate unique employee number #EMP-XXXX (BR-003)
            $empNumber = $this->generateUniqueEmpNumber();

            // 2. Validate email uniqueness
            if (User::where('email', $data['email'])->exists()) {
                throw ValidationException::withMessages([
                    'email' => ['البريد الإلكتروني مستخدم سابقاً من قِبل حساب آخر.'],
                ]);
            }

            // 3. Generate Username
            $username = $data['username'] ?? Str::slug($data['full_name']) . '_' . Str::random(4);
            if (User::where('username', $username)->exists()) {
                $username = 'emp_' . Str::random(8);
            }

            // 4. Create parallel User account
            $user = User::create([
                'name' => $data['full_name'],
                'username' => $username,
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? 'Password123!'),
                'status' => UserStatus::ACTIVE->value,
            ]);

            // Ensure consultant role exists and assign
            $consultantRole = Role::firstOrCreate(['name' => 'consultant', 'guard_name' => 'web']);
            $user->assignRole($consultantRole);

            // 5. Create Consultant Entity
            $consultantData = [
                'user_id' => $user->id,
                'employee_number' => $empNumber,
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'hire_date' => $data['hire_date'] ?? now()->toDateString(),
                'specialization' => $data['specialization'] ?? null,
                'work_schedule_template_id' => $data['work_schedule_template_id'] ?? null,
                'employment_status' => $data['employment_status'] ?? ConsultantStatus::ACTIVE->value,
                'notes' => $data['notes'] ?? null,
            ];

            return $this->consultantRepository->create($consultantData);
        });
    }

    /**
     * Update consultant profile details (BR-005 employee number frozen).
     */
    public function updateConsultant(int $id, array $data): Consultant
    {
        /** @var Consultant $consultant */
        $consultant = $this->consultantRepository->findOrFail($id);

        return DB::transaction(function () use ($consultant, $data) {
            // 1. Lock employee_number modification (BR-003 / BR-005)
            unset($data['employee_number']);

            // 2. Update parallel User details if email or name changed
            if (isset($data['email']) && $data['email'] !== $consultant->user->email) {
                if (User::where('email', $data['email'])->where('id', '!=', $consultant->user_id)->exists()) {
                    throw ValidationException::withMessages([
                        'email' => ['البريد الإلكتروني مستخدم بالفعل من قبل حساب آخر.'],
                    ]);
                }
                $consultant->user->update(['email' => $data['email']]);
            }

            if (isset($data['full_name']) && $data['full_name'] !== $consultant->full_name) {
                $consultant->user->update(['name' => $data['full_name']]);
            }

            // 3. Update Consultant model attributes
            $consultant->update([
                'full_name' => $data['full_name'] ?? $consultant->full_name,
                'phone' => array_key_exists('phone', $data) ? $data['phone'] : $consultant->phone,
                'specialization' => array_key_exists('specialization', $data) ? $data['specialization'] : $consultant->specialization,
                'hire_date' => array_key_exists('hire_date', $data) ? $data['hire_date'] : $consultant->hire_date,
                'work_schedule_template_id' => array_key_exists('work_schedule_template_id', $data) ? $data['work_schedule_template_id'] : $consultant->work_schedule_template_id,
                'notes' => array_key_exists('notes', $data) ? $data['notes'] : $consultant->notes,
            ]);

            ConsultantUpdatedEvent::dispatch($consultant);

            return $consultant->fresh(['user', 'workScheduleTemplate']);
        });
    }

    /**
     * Change employment status and handle active session revocation (BR-015).
     */
    public function changeEmploymentStatus(int $id, string $status): Consultant
    {
        /** @var Consultant $consultant */
        $consultant = $this->consultantRepository->findOrFail($id);

        return DB::transaction(function () use ($consultant, $status) {
            $consultant->update(['employment_status' => $status]);

            $user = $consultant->user;
            if ($user) {
                if (in_array($status, [ConsultantStatus::SUSPENDED->value, ConsultantStatus::VACATION->value])) {
                    // BR-015: Revoke active sessions and deactivate user account
                    $user->update(['status' => UserStatus::INACTIVE->value]);

                    // Flush database sessions if exists
                    DB::table('sessions')->where('user_id', $user->id)->delete();
                } else if ($status === ConsultantStatus::ACTIVE->value) {
                    $user->update(['status' => UserStatus::ACTIVE->value]);
                }
            }

            return $consultant->fresh(['user', 'workScheduleTemplate']);
        });
    }

    /**
     * Safely soft-delete consultant (BR-022 pending visits check).
     */
    public function deleteConsultant(int $id): bool
    {
        /** @var Consultant $consultant */
        $consultant = $this->consultantRepository->findOrFail($id);

        if ($this->consultantRepository->hasPendingVisits($id)) {
            throw ValidationException::withMessages([
                'consultant' => ['لا يمكن حذف سجل الاستشاري نظراً لوجود زيارات ميدانية معلقة مسندة إليه.'],
            ]);
        }

        return DB::transaction(function () use ($consultant) {
            if ($consultant->user) {
                // Deactivate and soft-delete user account
                $consultant->user->update(['status' => UserStatus::INACTIVE->value]);
                $consultant->user->delete();
            }

            return $consultant->delete();
        });
    }

    /**
     * Generate unique employee number #EMP-XXXX.
     */
    protected function generateUniqueEmpNumber(): string
    {
        do {
            $sequence = $this->consultantRepository->getNextEmployeeNumberSequence();
            $code = 'EMP-' . str_pad((string)$sequence, 4, '0', STR_PAD_LEFT);
            $exists = Consultant::withTrashed()->where('employee_number', $code)->exists();
            if ($exists) {
                // If sequence collisions occur, append a random increment
                $code = 'EMP-' . rand(1050, 9999);
            }
        } while (Consultant::withTrashed()->where('employee_number', $code)->exists());

        return $code;
    }
}
