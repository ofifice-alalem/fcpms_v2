<?php

namespace App\Services;

use App\Enums\SiteStatus;
use App\Models\Site;
use App\Repositories\Contracts\SiteRepositoryInterface;
use App\Helpers\ActivityLogger;
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

        $site = $this->siteRepository->create($data);

        ActivityLogger::log(
            'create_site',
            'Site',
            $site->id,
            "تم إنشاء موقع ميداني جديد: {$site->name} ({$site->code})",
            null,
            $site->toArray()
        );

        return $site;
    }

    /**
     * Update existing field site (BR-020 code lock on historical visits).
     */
    public function updateSite(int $siteId, array $data): Site
    {
        $site = $this->siteRepository->findOrFail($siteId);
        $oldData = $site->toArray();

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

        $updatedSite = $this->siteRepository->update($siteId, $data);

        ActivityLogger::log(
            'update_site',
            'Site',
            $updatedSite->id,
            "تم تحديث بيانات الموقع الميداني: {$updatedSite->name}",
            $oldData,
            $updatedSite->toArray()
        );

        return $updatedSite;
    }

    /**
     * Toggle site status between active and inactive (BR-021).
     */
    public function toggleStatus(int $siteId): Site
    {
        $site = $this->siteRepository->findOrFail($siteId);
        $oldStatus = $site->status;
        $newStatus = $site->status === SiteStatus::ACTIVE ? SiteStatus::INACTIVE->value : SiteStatus::ACTIVE->value;

        $updatedSite = $this->siteRepository->update($siteId, ['status' => $newStatus]);

        ActivityLogger::log(
            'toggle_site_status',
            'Site',
            $updatedSite->id,
            "تم تغيير حالة الموقع الميداني {$updatedSite->name} إلى {$newStatus}",
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );

        return $updatedSite;
    }

    /**
     * Safely soft-delete site (BR-022 pending visit check).
     */
    public function deleteSite(int $siteId): bool
    {
        $site = $this->siteRepository->findOrFail($siteId);
        $siteData = $site->toArray();

        if ($this->siteRepository->hasPendingVisits($siteId)) {
            throw ValidationException::withMessages([
                'site' => ['لا يمكن حذف الموقع نظراً لوجود زيارات ميدانية معلقة قيد التنفيذ.'],
            ]);
        }

        $deleted = $this->siteRepository->delete($siteId);

        if ($deleted) {
            ActivityLogger::log(
                'delete_site',
                'Site',
                $siteId,
                "تم حذف الموقع الميداني: {$site->name}",
                $siteData,
                null
            );
        }

        return $deleted;
    }
}
