<?php

namespace App\Repositories\Contracts;

use App\Models\Site;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SiteRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a site by its unique field code.
     */
    public function findByCode(string $code): ?Site;

    /**
     * Check if a site code exists (BR-020).
     */
    public function isCodeExists(string $code, ?int $ignoreId = null): bool;

    /**
     * Get paginated sites list with optional search and filters.
     */
    public function getFilteredSites(?string $search = null, ?string $city = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator;

    /**
     * Check if site has any registered visits (BR-020 code lock check).
     */
    public function hasVisits(int $siteId): bool;

    /**
     * Check if site has any pending active visits (BR-022 deletion check).
     */
    public function hasPendingVisits(int $siteId): bool;
}
