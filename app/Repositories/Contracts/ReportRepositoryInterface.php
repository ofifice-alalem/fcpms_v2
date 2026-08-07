<?php

namespace App\Repositories\Contracts;

use App\Models\SiteVisit;

interface ReportRepositoryInterface
{
    /**
     * Get aggregated metrics and logs for a specific consultant.
     */
    public function getConsultantMetrics(int $consultantId, array $filters = []): array;

    /**
     * Get enterprise-wide aggregated metrics and analytics for admin/HR.
     */
    public function getEnterpriseMetrics(array $filters = []): array;

    /**
     * Get detailed visit information including task responses and attachments.
     */
    public function getVisitDetail(int $visitId): ?SiteVisit;

    /**
     * Get raw dataset for exporting reports.
     */
    public function getExportData(array $filters = [], ?int $consultantId = null): array;

    /**
     * Get sites performance aggregated report (visits, unique consultants, regular & on-demand tasks).
     */
    public function getSitesPerformanceReport(array $filters = []): array;

    /**
     * Get task breakdown report for a specific site (frequency of tasks & distinct consultants count).
     */
    public function getSpecificSiteTaskBreakdown(int $siteId, array $filters = []): array;

    /**
     * Get consultants performance aggregated report (visited sites count, daily tasks, on-demand tasks).
     */
    public function getConsultantsPerformanceReport(array $filters = []): array;

    /**
     * Get site breakdown for a specific consultant (sites visited, visit count, task counts).
     */
    public function getConsultantSitesBreakdown(int $consultantId, array $filters = []): array;

    /**
     * Get task execution breakdown for a specific consultant (tasks executed and frequency per site).
     */
    public function getConsultantTasksBreakdown(int $consultantId, array $filters = []): array;
}
