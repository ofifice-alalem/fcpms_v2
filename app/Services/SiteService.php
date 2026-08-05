<?php

namespace App\Services;

use App\Enums\SiteStatus;
use App\Models\Site;
use App\Repositories\Contracts\SiteRepositoryInterface;
use Illuminate\Validation\ValidationException;

class SiteService
{
    public function __construct(
        protected SiteRepositoryInterface $siteRepository
    ) {}

    /**
     * Create a new field site (BR-020).
     */
    public function createSite(array $data): Site
    {
        if ($this->siteRepository->isCodeExists($data['code'])) {
            throw ValidationException::withMessages([
                'code' => ['رمز الموقع مستخدم بالفعل، يرجى أدخال رمز فريد.'],
            ]);
        }

        return $this->siteRepository->create($data);
    }

    /**
     * Update existing field site (BR-020 code lock on historical visits).
     */
    public function updateSite(int $siteId, array $data): Site
    {
        $site = $this->siteRepository->findOrFail($siteId);

        // BR-020: Lock code editing if site already has registered visits
        if (isset($data['code']) && $data['code'] !== $site->code) {
            if ($this->siteRepository->hasVisits($siteId)) {
                throw ValidationException::withMessages([
                    'code' => ['لا يمكن تعديل رمز الموقع نظراً لوجود سجل زيارات تاريخية مسجلة له.'],
                ]);
            }

            if ($this->siteRepository->isCodeExists($data['code'], $siteId)) {
                throw ValidationException::withMessages([
                    'code' => ['رمز الموقع مستخدم بالفعل من قبل موقع آخر.'],
                ]);
            }
        }

        return $this->siteRepository->update($siteId, $data);
    }

    /**
     * Toggle site status between active and inactive (BR-021).
     */
    public function toggleStatus(int $siteId): Site
    {
        $site = $this->siteRepository->findOrFail($siteId);
        $newStatus = $site->status === SiteStatus::ACTIVE ? SiteStatus::INACTIVE->value : SiteStatus::ACTIVE->value;

        return $this->siteRepository->update($siteId, ['status' => $newStatus]);
    }

    /**
     * Safely soft-delete site (BR-022 pending visit check).
     */
    public function deleteSite(int $siteId): bool
    {
        if ($this->siteRepository->hasPendingVisits($siteId)) {
            throw ValidationException::withMessages([
                'site' => ['لا يمكن حذف الموقع نظراً لوجود زيارات ميدانية معلقة قيد التنفيذ.'],
            ]);
        }

        return $this->siteRepository->delete($siteId);
    }
}
