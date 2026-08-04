<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Retrieve all records.
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Paginate records.
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Find record by ID.
     */
    public function find(int|string $id, array $columns = ['*']): ?Model;

    /**
     * Find record by ID or fail.
     */
    public function findOrFail(int|string $id, array $columns = ['*']): Model;

    /**
     * Create a new record.
     */
    public function create(array $attributes): Model;

    /**
     * Update an existing record.
     */
    public function update(int|string $id, array $attributes): Model;

    /**
     * Delete a record by ID.
     */
    public function delete(int|string $id): bool;

    /**
     * Find records by matching attributes.
     */
    public function findWhere(array $where, array $columns = ['*']): Collection;
}
