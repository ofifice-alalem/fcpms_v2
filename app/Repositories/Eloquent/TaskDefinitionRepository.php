<?php

namespace App\Repositories\Eloquent;

use App\Models\TaskDefinition;
use App\Repositories\Contracts\TaskDefinitionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TaskDefinitionRepository extends BaseRepository implements TaskDefinitionRepositoryInterface
{
    public function model(): string
    {
        return TaskDefinition::class;
    }

    public function getFilteredTasks(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with([
                'components.options',
                'siteAssignments.site',
                'consultantAssignments.consultant',
            ])
            ->withCount(['components', 'siteAssignments', 'consultantAssignments', 'responses']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['task_type'])) {
            $query->where('task_type', $filters['task_type']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '' && $filters['is_active'] !== null) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($filters['site_id'])) {
            $rawSites = is_array($filters['site_id']) ? $filters['site_id'] : explode(',', (string) $filters['site_id']);
            $rawSites = array_filter(array_map('trim', $rawSites), fn($v) => $v !== '');
            if (!empty($rawSites)) {
                $numericIds = array_filter($rawSites, fn($v) => is_numeric($v));
                $textQueries = array_filter($rawSites, fn($v) => !is_numeric($v));

                $query->where(function ($q) use ($numericIds, $textQueries) {
                    $q->whereHas('siteAssignments.site', function ($sq) use ($numericIds, $textQueries) {
                        if (!empty($numericIds)) {
                            $sq->whereIn('id', $numericIds);
                        }
                        if (!empty($textQueries)) {
                            $sq->where(function ($tq) use ($textQueries) {
                                foreach ($textQueries as $term) {
                                    $tq->orWhere('name', 'like', "%{$term}%")
                                       ->orWhere('code', 'like', "%{$term}%");
                                }
                            });
                        }
                    })->orDoesntHave('siteAssignments');
                });
            }
        }

        if (!empty($filters['consultant_id'])) {
            $rawConsultants = is_array($filters['consultant_id']) ? $filters['consultant_id'] : explode(',', (string) $filters['consultant_id']);
            $rawConsultants = array_filter(array_map('trim', $rawConsultants), fn($v) => $v !== '');
            if (!empty($rawConsultants)) {
                $numericIds = array_filter($rawConsultants, fn($v) => is_numeric($v));
                $textQueries = array_filter($rawConsultants, fn($v) => !is_numeric($v));

                $query->where(function ($q) use ($numericIds, $textQueries) {
                    $q->whereHas('consultantAssignments.consultant', function ($cq) use ($numericIds, $textQueries) {
                        if (!empty($numericIds)) {
                            $cq->whereIn('id', $numericIds);
                        }
                        if (!empty($textQueries)) {
                            $cq->where(function ($tq) use ($textQueries) {
                                foreach ($textQueries as $term) {
                                    $tq->orWhere('full_name', 'like', "%{$term}%")
                                       ->orWhere('employee_number', 'like', "%{$term}%");
                                }
                            });
                        }
                    })->orDoesntHave('consultantAssignments');
                });
            }
        }

        $sort = $filters['sort'] ?? 'latest';
        if ($sort === 'latest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'title') {
            $query->orderBy('title', 'asc');
        }

        return $query->paginate($perPage);
    }

    public function findWithRelations(int $id): ?TaskDefinition
    {
        return $this->model->newQuery()
            ->with([
                'components.options',
                'siteAssignments.site',
                'consultantAssignments.consultant',
            ])
            ->find($id);
    }

    public function createFullTask(array $data): TaskDefinition
    {
        return DB::transaction(function () use ($data) {
            $task = $this->model->create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'task_type' => $data['task_type'],
                'is_active' => $data['is_active'] ?? true,
                'display_order' => $data['display_order'] ?? 0,
                'created_by' => auth()->id(),
            ]);

            $componentIdMap = [];

            if (!empty($data['components'])) {
                foreach ($data['components'] as $index => $compData) {
                    $parentId = null;
                    if (!empty($compData['conditional_parent_temp_id']) && isset($componentIdMap[$compData['conditional_parent_temp_id']])) {
                        $parentId = $componentIdMap[$compData['conditional_parent_temp_id']];
                    } elseif (!empty($compData['conditional_parent_id'])) {
                        $parentId = $compData['conditional_parent_id'];
                    }

                    $component = $task->components()->create([
                        'label' => $compData['label'],
                        'component_type' => $compData['component_type'],
                        'placeholder' => $compData['placeholder'] ?? null,
                        'is_required' => $compData['is_required'] ?? false,
                        'display_order' => $compData['display_order'] ?? $index,
                        'conditional_parent_id' => $parentId,
                        'conditional_value' => $compData['conditional_value'] ?? null,
                    ]);

                    if (isset($compData['temp_id'])) {
                        $componentIdMap[$compData['temp_id']] = $component->id;
                    }

                    if (in_array($compData['component_type'], ['select', 'checkbox', 'choice']) && !empty($compData['options'])) {
                        foreach ($compData['options'] as $optIndex => $opt) {
                            $label = is_array($opt) ? ($opt['option_label'] ?? $opt['label'] ?? '') : $opt;
                            $value = is_array($opt) ? ($opt['option_value'] ?? $opt['value'] ?? $label) : $opt;
                            $component->options()->create([
                                'option_label' => $label,
                                'option_value' => $value,
                                'display_order' => $optIndex,
                            ]);
                        }
                    }
                }
            }

            if (!empty($data['site_ids']) && is_array($data['site_ids'])) {
                $task->siteAssignments()->createMany(
                    array_map(fn($siteId) => ['site_id' => $siteId], $data['site_ids'])
                );
            }

            if (!empty($data['consultant_ids']) && is_array($data['consultant_ids'])) {
                $task->consultantAssignments()->createMany(
                    array_map(fn($cId) => ['consultant_id' => $cId], $data['consultant_ids'])
                );
            }

            return $this->findWithRelations($task->id);
        });
    }

    public function updateFullTask(TaskDefinition $task, array $data): TaskDefinition
    {
        return DB::transaction(function () use ($task, $data) {
            $task->update([
                'title' => $data['title'] ?? $task->title,
                'description' => $data['description'] ?? $task->description,
                'task_type' => $data['task_type'] ?? $task->task_type,
                'is_active' => isset($data['is_active']) ? $data['is_active'] : $task->is_active,
                'display_order' => $data['display_order'] ?? $task->display_order,
            ]);

            $hasResponses = $task->responses()->exists();

            if (!$hasResponses && isset($data['components'])) {
                $task->components()->delete();
                $componentIdMap = [];

                foreach ($data['components'] as $index => $compData) {
                    $parentId = null;
                    if (!empty($compData['conditional_parent_temp_id']) && isset($componentIdMap[$compData['conditional_parent_temp_id']])) {
                        $parentId = $componentIdMap[$compData['conditional_parent_temp_id']];
                    } elseif (!empty($compData['conditional_parent_id'])) {
                        $parentId = $compData['conditional_parent_id'];
                    }

                    $component = $task->components()->create([
                        'label' => $compData['label'],
                        'component_type' => $compData['component_type'],
                        'placeholder' => $compData['placeholder'] ?? null,
                        'is_required' => $compData['is_required'] ?? false,
                        'display_order' => $compData['display_order'] ?? $index,
                        'conditional_parent_id' => $parentId,
                        'conditional_value' => $compData['conditional_value'] ?? null,
                    ]);

                    if (isset($compData['temp_id'])) {
                        $componentIdMap[$compData['temp_id']] = $component->id;
                    }

                    if (in_array($compData['component_type'], ['select', 'checkbox', 'choice']) && !empty($compData['options'])) {
                        foreach ($compData['options'] as $optIndex => $opt) {
                            $label = is_array($opt) ? ($opt['option_label'] ?? $opt['label'] ?? '') : $opt;
                            $value = is_array($opt) ? ($opt['option_value'] ?? $opt['value'] ?? $label) : $opt;
                            $component->options()->create([
                                'option_label' => $label,
                                'option_value' => $value,
                                'display_order' => $optIndex,
                            ]);
                        }
                    }
                }
            }

            if (isset($data['site_ids'])) {
                $task->siteAssignments()->delete();
                if (!empty($data['site_ids']) && is_array($data['site_ids'])) {
                    $task->siteAssignments()->createMany(
                        array_map(fn($siteId) => ['site_id' => $siteId], $data['site_ids'])
                    );
                }
            }

            if (isset($data['consultant_ids'])) {
                $task->consultantAssignments()->delete();
                if (!empty($data['consultant_ids']) && is_array($data['consultant_ids'])) {
                    $task->consultantAssignments()->createMany(
                        array_map(fn($cId) => ['consultant_id' => $cId], $data['consultant_ids'])
                    );
                }
            }

            return $this->findWithRelations($task->id);
        });
    }

    public function toggleActive(TaskDefinition $task): TaskDefinition
    {
        $task->update(['is_active' => !$task->is_active]);
        return $task;
    }

    public function deleteTask(TaskDefinition $task): bool
    {
        if ($task->responses()->exists()) {
            $task->update(['is_active' => false]);
            return (bool) $task->delete();
        }
        $task->siteAssignments()->delete();
        $task->consultantAssignments()->delete();
        $task->components()->delete();
        return (bool) $task->delete();
    }
}
