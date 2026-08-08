<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSiteRequest;
use App\Http\Requests\Admin\UpdateSiteRequest;
use App\Repositories\Contracts\SiteRepositoryInterface;
use App\Services\SiteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Support\Facades\Gate;

class SiteController extends Controller
{
    public function __construct(
        protected SiteRepositoryInterface $siteRepository,
        protected SiteService $siteService
    ) {}

    /**
     * Display paginated sites listing with search, filters, and sorting.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('view-sites');

        $search = $request->query('search');
        $city = $request->query('city');
        $status = $request->query('status');
        $sort = $request->query('sort');

        $sites = $this->siteRepository->getFilteredSites(
            search: $search,
            city: $city,
            status: $status,
            sort: $sort,
            perPage: 15
        );

        return Inertia::render('Admin/Sites/Index', [
            'sites' => $sites,
            'filters' => [
                'search' => $search,
                'city' => $city,
                'status' => $status,
                'sort' => $sort,
            ],
        ]);
    }

    /**
     * Store a newly created site in storage.
     */
    public function store(StoreSiteRequest $request): RedirectResponse
    {
        Gate::authorize('create-sites');

        $this->siteService->createSite($request->validated());

        return redirect()->route('admin.sites.index')
            ->with('success', 'تم إنشاء الموقع الميداني بنجاح.');
    }

    /**
     * Update the specified site in storage.
     */
    public function update(UpdateSiteRequest $request, int $id): RedirectResponse
    {
        Gate::authorize('edit-sites');

        $this->siteService->updateSite($id, $request->validated());

        return redirect()->route('admin.sites.index')
            ->with('success', 'تم تحديث بيانات الموقع الميداني بنجاح.');
    }

    /**
     * Toggle operational status (active/inactive) of a site.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        Gate::authorize('edit-sites');

        $site = $this->siteService->toggleStatus($id);

        $statusText = $site->status->value === 'active' ? 'تفعيل' : 'تعطيل';

        return redirect()->route('admin.sites.index')
            ->with('success', "تم {$statusText} الموقع بنجاح.");
    }

    /**
     * Soft-delete a site if no pending visits exist.
     */
    public function destroy(int $id): RedirectResponse
    {
        Gate::authorize('delete-sites');

        $this->siteService->deleteSite($id);

        return redirect()->route('admin.sites.index')
            ->with('success', 'تم حذف الموقع وأرشفته بنجاح.');
    }
}
