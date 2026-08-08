<?php

namespace App\Services;

use App\Models\TaskDefinition;
use App\Repositories\Contracts\TaskDefinitionRepositoryInterface;
use App\Helpers\ActivityLogger;
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
        $task = $this->taskRepo->createFullTask($data);

        ActivityLogger::log(
            'create_task',
            'TaskDefinition',
            $task->id,
            "تم إنشاء تعريف مهمة جديد: {$task->name_ar}",
            null,
            $task->toArray()
        );

        return $task;
    }

    public function updateTask(TaskDefinition $task, array $data): TaskDefinition
    {
        $task->load(['components.options', 'siteAssignments', 'consultantAssignments']);
        $oldValues = $task->toArray();
        $updatedTask = $this->taskRepo->updateFullTask($task, $data);
        $updatedTask->load(['components.options', 'siteAssignments', 'consultantAssignments']);

        ActivityLogger::log(
            'update_task',
            'TaskDefinition',
            $updatedTask->id,
            "تم تعديل المهمة: {$updatedTask->name_ar}",
            $oldValues,
            $updatedTask->toArray()
        );

        return $updatedTask;
    }

    public function toggleActive(TaskDefinition $task): TaskDefinition
    {
        $oldState = $task->is_active;
        $updatedTask = $this->taskRepo->toggleActive($task);

        ActivityLogger::log(
            'toggle_task_active',
            'TaskDefinition',
            $updatedTask->id,
            "تم تغيير حالة تفعيل المهمة {$updatedTask->name_ar} إلى " . ($updatedTask->is_active ? 'نشطة' : 'غير نشطة'),
            ['is_active' => $oldState],
            ['is_active' => $updatedTask->is_active]
        );

        return $updatedTask;
    }

    public function deleteTask(TaskDefinition $task): bool
    {
        $taskData = $task->toArray();
        $taskId = $task->id;
        $taskName = $task->name_ar;

        $deleted = $this->taskRepo->deleteTask($task);

        if ($deleted) {
            ActivityLogger::log(
                'delete_task',
                'TaskDefinition',
                $taskId,
                "تم حذف المهمة: {$taskName}",
                $taskData,
                null
            );
        }

        return $deleted;
    }
}
