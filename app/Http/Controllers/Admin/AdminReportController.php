<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PerformanceReportExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExportReportRequest;
use App\Http\Requests\Admin\GenerateReportRequest;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\SiteRepositoryInterface;
use App\Services\ReportAnalyticsService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Maatwebsite\Excel\Facades\Excel;

class AdminReportController extends Controller
{
    public function __construct(
        protected ReportAnalyticsService $reportService,
        protected ConsultantRepositoryInterface $consultantRepo,
        protected SiteRepositoryInterface $siteRepo
    ) {}

    /**
     * Display enterprise reports and performance dashboard.
     */
    public function index(GenerateReportRequest $request): InertiaResponse
    {
        $filters = $request->validated();

        $metrics = $this->reportService->getEnterpriseReportData($filters);

        $consultantsList = $this->consultantRepo->all(['id', 'full_name', 'employee_number'])
            ->map(fn($c) => [
                'id'              => $c->id,
                'full_name'       => $c->full_name,
                'employee_number' => $c->employee_number,
            ]);

        $sitesList = $this->siteRepo->all(['id', 'name', 'city'])
            ->map(fn($s) => [
                'id'   => $s->id,
                'name' => $s->name,
                'city' => $s->city,
            ]);

        $citiesList = $sitesList->pluck('city')->unique()->filter()->values()->toArray();

        return Inertia::render('Admin/Reports/Index', [
            'metrics'     => $metrics,
            'filters'     => $filters,
            'consultants' => $consultantsList,
            'sites'       => $sitesList,
            'cities'      => $citiesList,
        ]);
    }

    /**
     * Show visit detail for modal preview.
     */
    public function showVisitDetail(int $visit): JsonResponse
    {
        $details = $this->reportService->getVisitDetails($visit);
        if (!$details) {
            return response()->json(['message' => 'الزيارة الميدانية غير موجودة'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $details,
        ]);
    }

    /**
     * Export enterprise report to Excel/CSV.
     */
    public function export(ExportReportRequest $request)
    {
        $filters = $request->validated();
        $format = $request->input('format', 'excel');

        $rows = $this->reportService->prepareExportRows($filters);

        $fileName = 'التقرير_التنفيذي_الشامل_' . now()->format('Y-m-d') . '.' . ($format === 'excel' ? 'xlsx' : $format);

        if ($format === 'csv') {
            return Excel::download(new PerformanceReportExport($rows), $fileName, \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new PerformanceReportExport($rows), $fileName);
    }

    /**
     * Display Sites aggregated performance report.
     */
    public function sitesReport(GenerateReportRequest $request): InertiaResponse
    {
        $filters = $request->validated();
        $sitesReport = $this->reportService->getSitesPerformanceData($filters);

        $sitesList = $this->siteRepo->all(['id', 'name', 'city'])
            ->map(fn($s) => [
                'id'   => $s->id,
                'name' => $s->name,
                'city' => $s->city,
            ]);

        $citiesList = $sitesList->pluck('city')->unique()->filter()->values()->toArray();

        return Inertia::render('Admin/Reports/SitesReport', [
            'sitesReport' => $sitesReport,
            'filters'     => $filters,
            'sites'       => $sitesList,
            'cities'      => $citiesList,
        ]);
    }

    /**
     * Display task breakdown for a specific site.
     */
    public function siteTaskBreakdown(int $site, GenerateReportRequest $request): InertiaResponse
    {
        $filters = $request->validated();
        $breakdownData = $this->reportService->getSpecificSiteTaskData($site, $filters);

        $sitesList = $this->siteRepo->all(['id', 'name', 'city'])
            ->map(fn($s) => [
                'id'   => $s->id,
                'name' => $s->name,
                'city' => $s->city,
            ]);

        return Inertia::render('Admin/Reports/SiteTaskBreakdown', [
            'siteData'      => $breakdownData['site'],
            'taskBreakdown' => $breakdownData['tasks_breakdown'],
            'filters'       => $filters,
            'sites'         => $sitesList,
        ]);
    }
}
