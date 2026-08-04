# 📌 Phase 04: منشئ المهام الديناميكي والتكليفات الميدانية (Task Builder & Assignments)

---

## 1. Phase Information

| Field | Value |
| :--- | :--- |
| **Phase Number** | `phase_04` |
| **Phase Name** | منشئ المهام الميدانية الديناميكي وتكليفات الميدان (Task Builder & Assignments) |
| **Module Name** | `TaskBuilderModule` |
| **Status** | Approved / Documented |
| **Priority** | High |
| **Estimated Complexity** | High |

---

## 2. Business Purpose

بناء وتشييد المحرك التفاعلي لمنشئ المهام الميدانية، وتوفير قدرة كاملة على تصميم استبيانات التفتيش وحقول الإدخال المتنوعة ومناطق رفع الإثباتات الحية وتطبيق الحقول الشرطية الذكية (`Smart Adaptive Forms`) وتكليف هذه المهام للمواقع والاستشاريين المعنيين.

---

## 3. Scope

تغطية كافة خصائص بناء وتعديل وتكليف المهام اليومية والمهام عند الطلب وتنسيق مكونات الاستبيانات وحفظ سلامة الاستجابات التاريخية (`BR-027`, `BR-028`, `BR-029`, `BR-030`, `BR-031`).

---

## 4. In Scope

* **تصنيف وتحديد نوع المهمة**: تمييز المهام بين المهام اليومية الدورية (`daily`) والمهام عند الطلب (`on_demand`) (BR-027).
* **مُنشئ المكونات الديناميكية (Dynamic Components)**: دعم حقول النصوص، الأرقام، خيارات القوائم المنسدلة، مربعات الاختيار، ومناطق رفع الإثباتات الحية (`image_upload`) (BR-028).
* **محرك الحقول الشرطية الذكية (Smart Adaptive Forms)**: ربط تفعيل وإظهار الحقول المشروطة بـ `conditional_parent_id` و `conditional_value` (BR-030).
* **مصفوفة التكليفات الميدانية**: ربط المهام بالمواقع الميدانية وتكليف الاستشاريين المعنيين بها (BR-029).
* **المعاينة الحية التفاعلية**: توفير شاشة معاينة تفاعلية بضغط زر واحدة لاستكشاف نموذج المهمة والحقول الشرطية قبل الاعتماد.
* **أرشفة وحماية سجلات التاريخ (SoftDeletes)**: منع الحذف الصلب وتطبيق `SoftDeletes` وحظر تعديل هيكل المهام ذات الإجابات التاريخية (BR-031).

---

## 5. Out of Scope

* إجابة وتنفيذ المهام من قِبل الاستشاري أثناء الزيارة الميدانية (راجع: `phase_05_daily_visits`).
* تحليل واستخراج تقارير إجابات المهام (راجع: `phase_06_reports_and_analytics`).

---

## 6. Prerequisites

* **الصلاحيات الأمنية**: `view-tasks`, `create-tasks`, `edit-tasks`, `delete-tasks`.
* **الكيانات المسبقة في قاعدة البيانات**: `sites`, `consultants`, `users`.

---

## 7. Dependencies

### 7.1 Internal Dependencies
* **الجداول**: `task_definitions`, `task_components`, `task_component_options`, `task_site_assignments`, `task_consultant_assignments`.

### 7.2 External Dependencies
* Not yet documented.

### 7.3 Shared Components
* مكونات Spatial UI v3.0 (`Interactive Image Upload Dropzone`, `Smart Adaptive Form`, `SpatialTable`, `spatial-switch`).

---

## 8. Deliverables

### 8.1 Backend Deliverables
* **Models**: `TaskDefinition`, `TaskComponent`, `TaskComponentOption`, `TaskSiteAssignment`, `TaskConsultantAssignment`.
* **Repository**: `TaskDefinitionRepositoryInterface`, `TaskDefinitionRepository`.
* **Service**: `TaskService`.
* **Controller**: `App\Http\Controllers\Admin\TaskBuilderController`.
* **Form Requests**: `StoreTaskDefinitionRequest`, `UpdateTaskDefinitionRequest`.
* **Feature Test**: `tests/Feature/Admin/TaskBuilderManagementTest.php`.

### 8.2 Frontend Deliverables
* **Blade View**: `resources/views/pages/task_builder/index.blade.php`.
* **Drawers & Modals**: `#taskBuilderDrawer` (بعرض 960px), النافذة المنبثقة `#taskPreviewModal`, ونافذة التأكيد `#deleteTaskModal`.
* **JS Functions**: `openTaskBuilder()`, `addDynamicComponent()`, `toggleConditionalBuilder()`, `previewTaskInteractive()`.

### 8.3 Routes Deliverables
* **Main Routes**: `/admin/task-builder` (`GET`, `POST`, `GET /{task}`, `PUT /{task}`, `PATCH /{task}/toggle-active`, `DELETE /{task}`).

---

## 9. Success Criteria & Definition of Done (DoD)

> [!IMPORTANT]
> **لا تعتبر المرحلة منتهية حتى يتحقق الآتي:**
> * [ ] ✓ جميع الـ Routes والـ Endpoints تعمل بدون أخطاء.
> * [ ] ✓ جميع سياسات الحماية والصلاحيات (Policies & Permissions) تفرض وتعمل بدقة.
> * [ ] ✓ جميع اختبارات الميزات (Feature Tests) ناجحة بنسبة 100%.
> * [ ] ✓ جميع صفحات وعناصر الواجهة (Spatial UI) مكتملة ومطابقة للمواصفات البصرية.
> * [ ] ✓ جميع قواعد الأعمال المحددة (Business Rules) مطبقة ومختبرة بالكامل.

---

* [ ] بناء مهمة جديدة بمكوناتها وتكليفاتها وفق `test_can_create_task_with_dynamic_components_and_conditional_rules()`.
* [ ] إسناد المهام بنجاح للمواقع والاستشاريين وفق `test_task_assignments_to_sites_and_consultants()`.
* [ ] منع الحذف الفعلي لأي مهمة تحتوي على إجابات سابقة وفق `test_cannot_hard_delete_task_with_existing_responses()`.

---

## 10. Cross References

* **Architecture Standards**: See `00_architecture_and_rules/architecture.md`
* **Business Rules (BR-027 to BR-031)**: See `00_architecture_and_rules/business_rules.md`
* **Database Schema**: See `00_architecture_and_rules/database.md#task-builder-module`
* **Design Rules & Components Catalog**: See `01_design_system_and_components/`

---

## 11. Navigation

* ⚙️ **[backend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_04_task_builder/backend.md)**
* 🎨 **[frontend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_04_task_builder/frontend.md)**
* 🛣️ **[routes.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_04_task_builder/routes.md)**

---

## 12. Developer Notes

* **المعاملات المزدوجة (DB Transactions)**: حفظ وتعديل المهمة ومكوناتها وتكليفاتها يتم حتمياً داخل `DB::transaction` موحدة.
* **الحماية من الحلقات الدائرية**: التأكد من ربط الـ `conditional_parent_id` بحذر ومنع الربط المتبادل الدائري.
* **الالتزام بالـ SoftDeletes**: عدم مسح أي مهمة تمت الإجابة عليها لحفظ التاريخ الميداني والمالي.
