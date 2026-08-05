<?php

namespace App\Repositories\Eloquent;

use App\Models\Site;
use App\Repositories\Contracts\SiteRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SiteRepository extends BaseRepository implements SiteRepositoryInterface
{
    public function model(): string
    {
        return Site::class;
    }

    public function findByCode(string $code): ?Site
    {
        return $this->model->newQuery()->where('code', $code)->first();
    }

    public function isCodeExists(string $code, ?int $ignoreId = null): bool
    {
        $query = $this->model->newQuery()->where('code', $code);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    public function getFilteredSites(?string $search = null, ?string $city = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->withCount(['visits', 'taskAssignments']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($city) {
            $query->where('city', $city);
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function hasVisits(int $siteId): bool
    {
        $site = $this->find($siteId);

        return $site ? $site->visits()->exists() : false;
    }

    public function hasPendingVisits(int $siteId): bool
    {
        $site = $this->find($siteId);

        return $site ? $site->visits()->where('status', 'pending')->exists() : false;
    }
}
