# ⚙️ Backend Implementation: Phase 04 — منشئ المهام الديناميكي والتكليفات (Task Builder & Assignments)

---

## 1. Overview & Architectural Scope
- **اسم المرحلة**: `phase_04_task_builder`
- **الهدف التنفيذي**: بناء المنطق البرمجي الصلب لمنشئ المهام الميدانية الديناميكي (Dynamic Task Builder)، وإدارة المكونات التفاعلية وخياراتها، وتطبيق منطق الحقول الشرطية الذكية (Smart Adaptive Forms)، وتكليف المهام للمواقع والاستشاريين، وتغطية جميع الخصائص الخمس (عرض الكل، إنشاء مهمة بمكوناتها، تعديل البنية، عرض التفاصيل والتجربة الحية، والحذف الأرشيفي المحمي).
- **التزام المعمارية**:
  - الالتزام بنمط `Repository-Service Pattern` وحالة `Strict Mode` الموثقة في `00_architecture_and_rules/architecture.md`.
  - الامتثال الدقيق لجداول الموديول في `00_architecture_and_rules/database.md#task-builder-module`.
  - تطبيق قواعد الأعمال الخاصة بالمهام: `BR-027` (أنواع المهام `daily` و `on_demand`)، `BR-028` (تنوع المكونات التفاعلية وحقول الصور)، `BR-029` (قواعد التكليف للمواضع والاستشاريين)، `BR-030` (الحقول الشرطية `conditional_parent_id`)، و `BR-031` (حماية المهام ذات الإجابات التاريخية).

---

## 2. Models & Database Schema

### 2.1 `task_definitions` (تعريف المهمة)
- `id`: `bigint PK`
- `title`: `varchar` (عنوان المهمة - مثال: "تفقد أجهزة السلامة ومطابقة المخزون")
- `description`: `text NULL`
- `task_type`: `enum('daily', 'on_demand')` (BR-027)
- `is_active`: `boolean` (default: `true`)
- `display_order`: `int` (default: `0`)
- `created_by`: `bigint FK → users NULL`
- `created_at`, `updated_at`, `deleted_at`: `timestamps` (مع `SoftDeletes`)
- **العلاقات**: `hasMany(TaskComponent::class)`, `hasMany(TaskSiteAssignment::class)`, `hasMany(TaskConsultantAssignment::class)`, `hasMany(TaskResponse::class)`.

### 2.2 `task_components` (المكونات الديناميكية)
- `id`: `bigint PK`
- `task_definition_id`: `bigint FK → task_definitions`
- `label`: `varchar` (عنوان الحقل - مثال: "هل طفايات الحريق صالحة؟")
- `component_type`: `enum('text', 'number', 'select', 'checkbox', 'image_upload', 'date')` (BR-028)
- `is_required`: `boolean` (default: `true`)
- `display_order`: `int`
- `conditional_parent_id`: `bigint FK → task_components NULL` (ربط بشرط حقل آخر - BR-030)
- `conditional_value`: `varchar NULL` (القيمة المسسبة لتفعيل الحقل)
- **العلاقات**: `belongsTo(TaskDefinition::class)`, `hasMany(TaskComponentOption::class)`, `belongsTo(ParentComponent, 'conditional_parent_id')`.

### 2.3 `task_component_options` (خيارات القوائم المنسدلة والمربعات)
- `id`: `bigint PK`
- `task_component_id`: `bigint FK → task_components`
- `option_label`: `varchar` (مثال: "مطابق", "غير مطابق", "يحتاج صيانة")
- `option_value`: `varchar`
- `display_order`: `int`

### 2.4 `task_site_assignments` & `task_consultant_assignments` (التكليفات)
- `task_site_assignments`: `id`, `task_definition_id (FK)`, `site_id (FK)`.
- `task_consultant_assignments`: `id`, `task_definition_id (FK)`, `consultant_id (FK)`.

---

## 3. Detailed CRUD Operations & Business Logic

### 3.1 🟢 صفحة عرض الكل (Index & Listing Operation)
- **المسار**: `GET /admin/task-builder`
- **منطق الاسترجاع**:
  - جلب المهام مع المكونات والتكليفات `with(['taskComponents.options', 'siteAssignments.site', 'consultantAssignments.consultant'])`.
  - دعائم التصفية حسب النوع (`daily` المهام اليومية الدورية، أو `on_demand` عند الطلب) والحالة (`active`/`inactive`).

### 3.2 ➕ عملية الإنشـاء الجديد (Create Operation - Multi-tier DB Transaction)
- **المسار**: `POST /admin/task-builder`
- **منطق المعالجة البرمجية الصارم**:
  ```php
  public function createFullTask(array $data): TaskDefinition
  {
      return DB::transaction(function () use ($data) {
          // 1. Create Task Definition (BR-027)
          $task = $this->taskRepo->create([
              'title' => $data['title'],
              'description' => $data['description'] ?? null,
              'task_type' => $data['task_type'],
              'is_active' => $data['is_active'] ?? true,
              'created_by' => auth()->id(),
          ]);

          // 2. Create Dynamic Components & Options (BR-028, BR-030)
          foreach ($data['components'] as $compData) {
              $component = $task->taskComponents()->create([
                  'label' => $compData['label'],
                  'component_type' => $compData['component_type'],
                  'is_required' => $compData['is_required'] ?? true,
                  'display_order' => $compData['display_order'] ?? 0,
                  'conditional_parent_id' => $compData['conditional_parent_id'] ?? null,
                  'conditional_value' => $compData['conditional_value'] ?? null,
              ]);

              // If type is 'select' or 'checkbox', insert options
              if (in_array($compData['component_type'], ['select', 'checkbox']) && !empty($compData['options'])) {
                  foreach ($compData['options'] as $opt) {
                      $component->options()->create([
                          'option_label' => $opt['label'],
                          'option_value' => $opt['value'],
                      ]);
                  }
              }
          }

          // 3. Assign Sites & Consultants (BR-029)
          if (!empty($data['site_ids'])) {
              $task->siteAssignments()->createMany(
                  array_map(fn($id) => ['site_id' => $id], $data['site_ids'])
              );
          }
          if (!empty($data['consultant_ids'])) {
              $task->consultantAssignments()->createMany(
                  array_map(fn($id) => ['consultant_id' => $id], $data['consultant_ids'])
              );
          }

          return $task;
      });
  }
  ```

### 3.3 ✏️ عملية التحديث والتعديل (Update Operation)
- **المسار**: `PUT /admin/task-builder/{task}`
- **منطق المعالجة**:
  - تحديث بيانات المهمة الأساسية وإعادة المكونات والتكليفات بأمان داخل DB Transaction.
  - إذا كانت المهمة تمت الإجابة عليها سابقاً بواسطة استشاريين (`task_responses()->exists()`)، يمنع النظام حذف المكونات القديمة ويسمح فقط بتعديل المسميات أو التكليفات لحفظ السلامة التاريخية للإجابات (BR-031).

### 3.4 👁️ خاصية عرض التفاصيل (Show Task Details Contract)
- **المسار**: `GET /admin/task-builder/{task}`
- **الهدف**: استرجاع هيكلية المهمة الكاملة مع شجرة الحقول الشرطية لعرض المعاينة الحية للنموذج التفاعلي.

### 3.5 🗑️ عملية الحذف الأرشيفي (Delete Operation)
- **المسار**: `DELETE /admin/task-builder/{task}`
- **منطق المعالجة**:
  - تطبيق **BR-031**: فحص وجود إجابات سابقة. إذا كانت المهمة تحتوي على إجابات ميدانية، يتم فقط تحويل `is_active = false` وأرشفتها عبر **SoftDelete** مع الحفاظ الكامل على سجلات الإجابات والمرفقات في قاعدة البيانات.

---

## 4. Repository & Service Layer Contracts
- **الواجهات والتطبيقات**:
  - `TaskDefinitionRepositoryInterface` / `TaskDefinitionRepository`
  - `TaskService`
- **طلب التحقق**: `StoreTaskDefinitionRequest`, `UpdateTaskDefinitionRequest`.

---

## 5. Testing Standards
- **ملف الاختبار**: `tests/Feature/Admin/TaskBuilderManagementTest.php`
- **الحالات المغطاة**:
  - `test_can_create_task_with_dynamic_components_and_conditional_rules()`
  - `test_task_assignments_to_sites_and_consultants()`
  - `test_cannot_hard_delete_task_with_existing_responses()`
