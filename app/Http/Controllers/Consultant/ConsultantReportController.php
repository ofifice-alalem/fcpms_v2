<?php

namespace App\Http\Controllers\Consultant;

use App\Exports\PerformanceReportExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExportReportRequest;
use App\Http\Requests\Admin\GenerateReportRequest;
use App\Models\Consultant;
use App\Services\ReportAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ConsultantReportController extends Controller
{
    public function __construct(
        protected ReportAnalyticsService $reportService
    ) {}

    /**
     * Display consultant personal report dashboard.
     */
    public function index(GenerateReportRequest $request): InertiaResponse
    {
        $user = $request->user();
        $consultant = $user->consultant ?? Consultant::where('user_id', $user->id)->first();

        if (!$consultant) {
            abort(403, 'حساب المستخدم الحالي غير مرتبط بملف استشاري ميداني.');
        }

        $filters = $request->validated();
        $metrics = $this->reportService->getConsultantReportData($consultant->id, $filters);

        return Inertia::render('Consultant/Reports/Index', [
            'consultant' => $consultant,
            'metrics'    => $metrics,
            'filters'    => $filters,
        ]);
    }

    /**
     * Show visit details log.
     */
    public function showVisitDetail(int $visit): JsonResponse
    {
        $details = $this->reportService->getVisitDetails($visit);
        if (!$details) {
            return response()->json(['message' => 'الزيارة غير موجودة'], 440);
        }

        return response()->json([
            'success' => true,
            'data'    => $details,
        ]);
    }

    /**
     * Export personal report to Excel/CSV/PDF.
     */
    public function export(ExportReportRequest $request)
    {
        $user = $request->user();
        $consultant = $user->consultant ?? Consultant::where('user_id', $user->id)->first();

        if (!$consultant) {
            abort(403, 'حساب المستخدم الحالي غير مرتبط بملف استشاري ميداني.');
        }

        $filters = $request->validated();
        $format = $request->input('format', 'excel');
        $rows = $this->reportService->prepareExportRows($filters, $consultant->id);

        $fileName = 'تقاريري_الشخصية_ميدان_' . now()->format('Y-m-d') . '.' . ($format === 'excel' ? 'xlsx' : $format);

        if ($format === 'csv') {
            return Excel::download(new PerformanceReportExport($rows), $fileName, \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new PerformanceReportExport($rows), $fileName);
    }
}
