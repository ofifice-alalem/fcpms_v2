<?php

namespace App\Services;

use App\Models\TaskDefinition;
use App\Repositories\Contracts\TaskDefinitionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    public function __construct(
        protected TaskDefinitionRepositoryInterface $taskRepo
    ) {}

    public function getFilteredTasks(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->taskRepo->getFilteredTasks($filters, $perPage);
    }

    public function getTaskDetails(int $id): ?TaskDefinition
    {
        return $this->taskRepo->findWithRelations($id);
    }

    public function createTask(array $data): TaskDefinition
    {
        return $this->taskRepo->createFullTask($data);
    }

    public function updateTask(TaskDefinition $task, array $data): TaskDefinition
    {
        return $this->taskRepo->updateFullTask($task, $data);
    }

    public function toggleActive(TaskDefinition $task): TaskDefinition
    {
        return $this->taskRepo->toggleActive($task);
    }

    public function deleteTask(TaskDefinition $task): bool
    {
        return $this->taskRepo->deleteTask($task);
    }
}
