<?php

namespace App\Repositories\Contracts;

use App\Models\DailyRecord;
use App\Models\SiteVisit;
use App\Models\TaskResponse;
use Illuminate\Database\Eloquent\Collection;

interface SiteVisitRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active site visit for a daily record.
     */
    public function getActiveVisit(DailyRecord $dailyRecord): ?SiteVisit;

    /**
     * Check if a visit to a site already exists for today's record.
     */
    public function hasVisitedSiteToday(DailyRecord $dailyRecord, int $siteId): bool;

    /**
     * Open a new site visit in progress.
     */
    public function openVisit(DailyRecord $dailyRecord, int $siteId, ?string $notes = null): SiteVisit;

    /**
     * Find site visit with all relations (tasks, values, attachments, site).
     */
    public function findWithDetails(int $visitId): ?SiteVisit;

    /**
     * Cancel an active site visit in progress.
     */
    public function cancelVisit(SiteVisit $siteVisit): bool;

    /**
     * Save or update task response values and attachments.
     */
    public function saveTaskResponses(SiteVisit $siteVisit, array $responsesData, array $attachmentsData = []): SiteVisit;
}
