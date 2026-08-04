<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    public function __construct()
    {
        $this->makeModel();
    }

    /**
     * Specify Model class name.
     */
    abstract public function model(): string;

    /**
     * Instantiate model.
     */
    public function makeModel(): Model
    {
        $model = app($this->model());

        if (! $model instanceof Model) {
            throw new \RuntimeException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    public function findOrFail(int|string $id, array $columns = ['*']): Model
    {
        return $this->model->findOrFail($id, $columns);
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function update(int|string $id, array $attributes): Model
    {
        $record = $this->findOrFail($id);
        $record->update($attributes);

        return $record->fresh();
    }

    public function delete(int|string $id): bool
    {
        $record = $this->findOrFail($id);

        return (bool) $record->delete();
    }

    public function findWhere(array $where, array $columns = ['*']): Collection
    {
        return $this->model->where($where)->get($columns);
    }
}
