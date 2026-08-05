<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskDefinitionRequest;
use App\Http\Requests\Task\UpdateTaskDefinitionRequest;
use App\Models\Consultant;
use App\Models\Site;
use App\Models\TaskDefinition;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskBuilderController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index(Request $request): Response|JsonResponse
    {
        $filters = $request->only(['search', 'task_type', 'is_active', 'site_id', 'consultant_id', 'sort']);

        $tasks = $this->taskService->getFilteredTasks($filters, 15);

        $sites = Site::select('id', 'name', 'code')->get();
        $consultants = Consultant::select('id', 'full_name', 'employee_number')->get();

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'data' => $tasks,
            ]);
        }

        return Inertia::render('Admin/TaskBuilder/Index', [
            'tasks' => $tasks,
            'filters' => $filters,
            'sites' => $sites,
            'consultants' => $consultants,
        ]);
    }

    public function create(): Response
    {
        $sites = Site::select('id', 'name', 'code')->get();
        $consultants = Consultant::select('id', 'full_name', 'employee_number')->get();

        return Inertia::render('Admin/TaskBuilder/Form', [
            'task' => null,
            'sites' => $sites,
            'consultants' => $consultants,
        ]);
    }

    public function store(StoreTaskDefinitionRequest $request): RedirectResponse|JsonResponse
    {
        $task = $this->taskService->createTask($request->validated());

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم بناء المهمة وتعيين المكونات الحركية والتكليفات بنجاح',
                'data' => $task,
            ], 201);
        }

        return redirect()->route('admin.tasks.index')->with('success', 'تم بناء وتشييد المهمة الميدانية بنجاح');
    }

    public function show(TaskDefinition $task): JsonResponse
    {
        $details = $this->taskService->getTaskDetails($task->id);

        return response()->json([
            'success' => true,
            'data' => $details,
        ]);
    }

    public function edit(TaskDefinition $task): Response
    {
        $taskDetails = $this->taskService->getTaskDetails($task->id);
        $sites = Site::select('id', 'name', 'code')->get();
        $consultants = Consultant::select('id', 'full_name', 'employee_number')->get();

        return Inertia::render('Admin/TaskBuilder/Form', [
            'task' => $taskDetails,
            'sites' => $sites,
            'consultants' => $consultants,
        ]);
    }

    public function update(UpdateTaskDefinitionRequest $request, TaskDefinition $task): RedirectResponse|JsonResponse
    {
        $updatedTask = $this->taskService->updateTask($task, $request->validated());

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث بنية المهمة بنجاح',
                'data' => $updatedTask,
            ]);
        }

        return redirect()->route('admin.tasks.index')->with('success', 'تم تحديث بنية المهمة الميدانية والتكليفات بنجاح');
    }

    public function toggleActive(TaskDefinition $task): RedirectResponse|JsonResponse
    {
        $updatedTask = $this->taskService->toggleActive($task);

        if (request()->wantsJson() && !request()->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم تعديل حالة تفعيل المهمة بنجاح',
                'data' => $updatedTask,
            ]);
        }

        return redirect()->back()->with('success', 'تم تحديث حالة تفعيل المهمة التشغيلية بنجاح');
    }

    public function destroy(TaskDefinition $task): RedirectResponse|JsonResponse
    {
        $this->taskService->deleteTask($task);

        if (request()->wantsJson() && !request()->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم أرشفة المهمة بنجاح مع الاحتفاظ بسجلات الإجابات الميدانية التاريخية',
            ]);
        }

        return redirect()->back()->with('success', 'تم أرشفة المهمة بنجاح وحفظ سجل الإجابات التاريخية');
    }
}
