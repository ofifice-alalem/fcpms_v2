<?php

use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\ConsultantController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function (Request $request) {
        $user = Auth::user();
        if ($user && ($user->hasRole('hr') || $user->hasRole('admin'))) {
            return app(\App\Http\Controllers\Admin\DashboardController::class)->index($request);
        }
        return redirect()->route('consultant.visits.index');
    })->name('dashboard');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:hr|admin')->group(function () {
        // Phase 01: Site & Location Management Routes
        Route::get('/sites', [SiteController::class, 'index'])->name('sites.index');
        Route::post('/sites', [SiteController::class, 'store'])->name('sites.store');
        Route::put('/sites/{site}', [SiteController::class, 'update'])->name('sites.update');
        Route::patch('/sites/{site}/toggle-status', [SiteController::class, 'toggleStatus'])->name('sites.toggle-status');
        Route::delete('/sites/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');

        // Phase 02: Consultants Management Routes
        Route::get('/consultants', [ConsultantController::class, 'index'])->name('consultants.index');
        Route::post('/consultants', [ConsultantController::class, 'store'])->name('consultants.store');
        Route::get('/consultants/{consultant}', [ConsultantController::class, 'show'])->name('consultants.show');
        Route::put('/consultants/{consultant}', [ConsultantController::class, 'update'])->name('consultants.update');
        Route::patch('/consultants/{consultant}/status', [ConsultantController::class, 'updateStatus'])->name('consultants.update-status');
        Route::delete('/consultants/{consultant}', [ConsultantController::class, 'destroy'])->name('consultants.destroy');

        // Phase 03: Work Schedules & Leaves Routes
        Route::get('/work-schedules', [\App\Http\Controllers\Admin\WorkScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/work-schedules/templates', [\App\Http\Controllers\Admin\WorkScheduleController::class, 'storeTemplate'])->name('schedules.templates.store');
        Route::put('/work-schedules/templates/{template}', [\App\Http\Controllers\Admin\WorkScheduleController::class, 'updateTemplate'])->name('schedules.templates.update');
        Route::delete('/work-schedules/templates/{template}', [\App\Http\Controllers\Admin\WorkScheduleController::class, 'destroyTemplate'])->name('schedules.templates.destroy');
        Route::post('/work-schedules/holidays', [\App\Http\Controllers\Admin\WorkScheduleController::class, 'storeHoliday'])->name('schedules.holidays.store');
        Route::put('/work-schedules/holidays/{holiday}', [\App\Http\Controllers\Admin\WorkScheduleController::class, 'updateHoliday'])->name('schedules.holidays.update');
        Route::delete('/work-schedules/holidays/{holiday}', [\App\Http\Controllers\Admin\WorkScheduleController::class, 'destroyHoliday'])->name('schedules.holidays.destroy');
        Route::post('/work-schedules/leaves', [\App\Http\Controllers\Admin\WorkScheduleController::class, 'storeLeave'])->name('schedules.leaves.store');
        Route::put('/work-schedules/leaves/{leave}', [\App\Http\Controllers\Admin\WorkScheduleController::class, 'updateLeave'])->name('schedules.leaves.update');
        Route::delete('/work-schedules/leaves/{leave}', [\App\Http\Controllers\Admin\WorkScheduleController::class, 'destroyLeave'])->name('schedules.leaves.destroy');

        // Phase 08: Backup System Routes
        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/create', [BackupController::class, 'create'])->name('backups.create');
        Route::post('/backups/upload', [BackupController::class, 'upload'])->name('backups.upload');
        Route::post('/backups/open-folder', [BackupController::class, 'openFolder'])->name('backups.open-folder');
        Route::post('/backups/restore/{filename}', [BackupController::class, 'restore'])->name('backups.restore');
        Route::get('/backups/download/{filename}', [BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups/{filename}', [BackupController::class, 'delete'])->name('backups.delete');

        // Phase 04: Dynamic Task Builder & Assignments Routes
        Route::get('/task-builder', [\App\Http\Controllers\Admin\TaskBuilderController::class, 'index'])->name('tasks.index');
        Route::get('/task-builder/create', [\App\Http\Controllers\Admin\TaskBuilderController::class, 'create'])->name('tasks.create');
        Route::post('/task-builder', [\App\Http\Controllers\Admin\TaskBuilderController::class, 'store'])->name('tasks.store');
        Route::get('/task-builder/{task}', [\App\Http\Controllers\Admin\TaskBuilderController::class, 'show'])->name('tasks.show');
        Route::get('/task-builder/{task}/edit', [\App\Http\Controllers\Admin\TaskBuilderController::class, 'edit'])->name('tasks.edit');
        Route::put('/task-builder/{task}', [\App\Http\Controllers\Admin\TaskBuilderController::class, 'update'])->name('tasks.update');
        Route::patch('/task-builder/{task}/toggle-active', [\App\Http\Controllers\Admin\TaskBuilderController::class, 'toggleActive'])->name('tasks.toggle-active');
        Route::delete('/task-builder/{task}', [\App\Http\Controllers\Admin\TaskBuilderController::class, 'destroy'])->name('tasks.destroy');

        // Phase 06: Reports & Analytics (Admin / Enterprise View)
        Route::get('/reports', [\App\Http\Controllers\Admin\AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sites', [\App\Http\Controllers\Admin\AdminReportController::class, 'sitesReport'])->name('reports.sites');
        Route::get('/reports/sites/{site}', [\App\Http\Controllers\Admin\AdminReportController::class, 'siteTaskBreakdown'])->name('reports.site-breakdown');
        Route::get('/reports/visit-detail/{visit}', [\App\Http\Controllers\Admin\AdminReportController::class, 'showVisitDetail'])->name('reports.visit-detail');
        Route::get('/reports/export', [\App\Http\Controllers\Admin\AdminReportController::class, 'export'])->name('reports.export');
    });

    // Phase 05: Consultant Daily Visits Portal Routes
    Route::prefix('consultant')->name('consultant.')->middleware('role:consultant')->group(function () {
        Route::get('/daily-visits', [\App\Http\Controllers\Consultant\DailyVisitController::class, 'index'])->name('visits.index');
        Route::get('/past-records', [\App\Http\Controllers\Consultant\ConsultantHistoryController::class, 'index'])->name('history.index');
        Route::post('/daily-visits/start-day', [\App\Http\Controllers\Consultant\DailyVisitController::class, 'startDay'])->name('visits.start-day');
        
        Route::get('/site-visits/create', [\App\Http\Controllers\Consultant\DailyVisitController::class, 'createVisit'])->name('site-visits.create');
        Route::get('/site-visits/{visit}/execute', [\App\Http\Controllers\Consultant\DailyVisitController::class, 'executeVisit'])->name('site-visits.execute');
        Route::post('/site-visits', [\App\Http\Controllers\Consultant\DailyVisitController::class, 'storeVisit'])->name('site-visits.store');
        Route::post('/site-visits/{visit}/trigger-on-demand', [\App\Http\Controllers\Consultant\DailyVisitController::class, 'triggerOnDemand'])->name('site-visits.trigger-ondemand');
        Route::delete('/site-visits/{visit}/on-demand/{response}', [\App\Http\Controllers\Consultant\DailyVisitController::class, 'removeOnDemandTask'])->name('site-visits.remove-ondemand');
        Route::post('/site-visits/{visit}/save-responses', [\App\Http\Controllers\Consultant\DailyVisitController::class, 'saveResponses'])->name('site-visits.save-responses');
        Route::post('/site-visits/{visit}/task-responses/{response}/submit', [\App\Http\Controllers\Consultant\DailyVisitController::class, 'submitTaskResponse'])->name('site-visits.submit-task-response');
        Route::post('/site-visits/{visit}/submit-all-drafts', [\App\Http\Controllers\Consultant\DailyVisitController::class, 'submitAllDrafts'])->name('site-visits.submit-all-drafts');
        Route::get('/site-visits/{visit}', [\App\Http\Controllers\Consultant\DailyVisitController::class, 'show'])->name('site-visits.show');
        Route::delete('/site-visits/{visit}', [\App\Http\Controllers\Consultant\DailyVisitController::class, 'destroy'])->name('site-visits.destroy');

        // Phase 06: Consultant Personal Reports Routes
        Route::get('/reports', [\App\Http\Controllers\Consultant\ConsultantReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/visit-detail/{visit}', [\App\Http\Controllers\Consultant\ConsultantReportController::class, 'showVisitDetail'])->name('reports.visit-detail');
        Route::get('/reports/export', [\App\Http\Controllers\Consultant\ConsultantReportController::class, 'export'])->name('reports.export');
    });
});

// Public Design System Catalog
Route::get('/design-system', function () {
    return Inertia::render('DesignSystemCatalog');
})->name('design-system');

// Root fallback to login or role portal
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    $user = Auth::user();
    if ($user && ($user->hasRole('hr') || $user->hasRole('admin'))) {
        return redirect()->route('admin.consultants.index');
    }
    return redirect()->route('consultant.visits.index');
});
