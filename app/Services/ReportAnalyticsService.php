<?php

namespace App\Services;

use App\Repositories\Contracts\ReportRepositoryInterface;

class ReportAnalyticsService
{
    public function __construct(
        protected ReportRepositoryInterface $reportRepo
    ) {}

    /**
     * Get aggregated metrics and logs for a consultant.
     */
    public function getConsultantReportData(int $consultantId, array $filters = []): array
    {
        return $this->reportRepo->getConsultantMetrics($consultantId, $filters);
    }

    /**
     * Get enterprise analytics for admin/HR.
     */
    public function getEnterpriseReportData(array $filters = []): array
    {
        return $this->reportRepo->getEnterpriseMetrics($filters);
    }

    /**
     * Get detailed visit log.
     */
    public function getVisitDetails(int $visitId)
    {
        return $this->reportRepo->getVisitDetail($visitId);
    }

    /**
     * Format export data into a clean structure for CSV / Excel generation.
     */
    public function prepareExportRows(array $filters = [], ?int $consultantId = null): array
    {
        $rawVisits = $this->reportRepo->getExportData($filters, $consultantId);

        $rows = [];
        foreach ($rawVisits as $visit) {
            $consultantName = $visit['daily_record']['consultant']['full_name'] ?? 'غير معروف';
            $empNumber = $visit['daily_record']['consultant']['employee_number'] ?? '-';
            $siteName = $visit['site']['name'] ?? 'غير معروف';
            $city = $visit['site']['city'] ?? '-';
            $recordDate = $visit['daily_record']['work_date'] ?? $visit['daily_record']['record_date'] ?? '-';
            $checkIn = $visit['check_in_time'] ? substr($visit['check_in_time'], 11, 5) : '-';
            $checkOut = $visit['check_out_time'] ? substr($visit['check_out_time'], 11, 5) : '-';
            $statusStr = $visit['status'] === 'completed' ? 'مكتملة' : ($visit['status'] === 'in_progress' ? 'قيد التنفيذ' : $visit['status']);

            $rows[] = [
                'record_date'     => $recordDate,
                'consultant_name' => $consultantName,
                'employee_number' => $empNumber,
                'site_name'       => $siteName,
                'city'            => $city,
                'check_in'        => $checkIn,
                'check_out'       => $checkOut,
                'status'          => $statusStr,
                'notes'           => $visit['notes'] ?? '',
            ];
        }

        return $rows;
    }

    /**
     * Get sites performance aggregated report.
     */
    public function getSitesPerformanceData(array $filters = []): array
    {
        return $this->reportRepo->getSitesPerformanceReport($filters);
    }

    /**
     * Get task breakdown report for a specific site.
     */
    public function getSpecificSiteTaskData(int $siteId, array $filters = []): array
    {
        return $this->reportRepo->getSpecificSiteTaskBreakdown($siteId, $filters);
    }

    /**
     * Get consultants performance aggregated report.
     */
    public function getConsultantsPerformanceData(array $filters = []): array
    {
        return $this->reportRepo->getConsultantsPerformanceReport($filters);
    }

    /**
     * Get site breakdown for a specific consultant.
     */
    public function getConsultantSitesData(int $consultantId, array $filters = []): array
    {
        return $this->reportRepo->getConsultantSitesBreakdown($consultantId, $filters);
    }

    /**
     * Get task execution breakdown for a specific consultant across sites.
     */
    public function getConsultantTasksData(int $consultantId, array $filters = []): array
    {
        return $this->reportRepo->getConsultantTasksBreakdown($consultantId, $filters);
    }
}
