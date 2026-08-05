<?php

namespace App\Repositories\Contracts;

use App\Models\TaskDefinition;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskDefinitionRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredTasks(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findWithRelations(int $id): ?TaskDefinition;

    public function createFullTask(array $data): TaskDefinition;

    public function updateFullTask(TaskDefinition $task, array $data): TaskDefinition;

    public function toggleActive(TaskDefinition $task): TaskDefinition;

    public function deleteTask(TaskDefinition $task): bool;
}
