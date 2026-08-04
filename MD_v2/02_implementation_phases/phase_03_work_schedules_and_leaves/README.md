# 📌 Phase 03: جداول الدوام والعطلات والإجازات (Work Schedules & Leaves)

---

## 1. Phase Information

| Field | Value |
| :--- | :--- |
| **Phase Number** | `phase_03` |
| **Phase Name** | جداول الدوام والعطلات والإجازات (Work Schedules & Leaves) |
| **Module Name** | `WorkSchedulesModule` |
| **Status** | Approved / Documented |
| **Priority** | Medium |
| **Estimated Complexity** | Medium |

---

## 2. Business Purpose

بناء المنطق البرمجي والواجهات التفاعلية لإدارة قوالب الدوام وساعات العمل الرسمية، تحديد أيام العمل الأسبوعية، تسجيل العطلات الرسمية العامة، وإدارة طلبات وإجازات الاستشاريين الميدانيين وتغطية كافة الخصائص الخمس.

---

## 3. Scope

تغطية جميع العمليات لثلاثية قوالب الدوام والعطلات والإجازات وتطبيق قواعد الأعمال الخاضعة لها (`BR-006`, `BR-007`, `BR-008`, `BR-012`, `BR-015`).

---

## 4. In Scope

* **قوالب الدوام وساعات العمل**: إنشاء وتحديث قوالب الدوام وتخصيص أيام العمل 7 أيام (الأحد إلى السبت) فرادة القالب الافتراضي `is_default` (BR-007).
* **العطلات الرسمية**: تسجيل وإلغاء العطلات الرسمية وتاريخها المباشر واستثنائها من احتساب غياب الميدان (BR-012).
* **إجازات الاستشاريين**: تقديم وتسجيل إجازات الاستشاريين وتحديث حالتهم تلقائياً إلى `vacation` لحماية نسبة الأداء (BR-015).
* **عرض التفاصيل والحذف المحمي**: استعراض تفاصيل القالب `#templateDetailModal` وأرشفة وحذف قوالب الدوام غير المسندة فقط (BR-006).

---

## 5. Out of Scope

* احتساب الخصومات المالية التراكمية (راجع: `phase_06_reports_and_analytics`).

---

## 6. Prerequisites

* **الصلاحيات الأمنية**: `view-schedules`, `create-schedules`, `edit-schedules`, `delete-schedules`.
* **الجداول والكيانات**: `consultants`.

---

## 7. Dependencies

### 7.1 Internal Dependencies
* **الجداول**: `work_schedule_templates`, `work_schedule_days`, `official_holidays`, `consultant_leaves`.

### 7.2 External Dependencies
* Not yet documented.

### 7.3 Shared Components
* مكونات Spatial UI v3.0 (`SpatialTable`, `spatial-card`, `spatial-switch`, `custom-checkbox`).

---

## 8. Deliverables

### 8.1 Backend Deliverables
* **Models**: `WorkScheduleTemplate`, `WorkScheduleDay`, `OfficialHoliday`, `ConsultantLeave`.
* **Repository**: `WorkScheduleRepository`, `ConsultantLeaveRepository`.
* **Service**: `WorkScheduleService`.
* **Controller**: `App\Http\Controllers\Admin\WorkScheduleController`.
* **Form Requests**: `StoreScheduleTemplateRequest`, `StoreOfficialHolidayRequest`, `StoreConsultantLeaveRequest`.
* **Feature Test**: `tests/Feature/Admin/WorkScheduleManagementTest.php`.

### 8.2 Frontend Deliverables
* **Blade View**: `resources/views/pages/work_schedules/index.blade.php`.
* **Drawers & Modals**: `#templateDrawer`, `#leaveModal`, `#templateDetailModal`, `#deleteModal`.
* **JS Functions**: `switchScheduleTab()`, `openCreateTemplateDrawer()`, `openEditTemplateDrawer()`, `confirmDeleteTemplate()`.

### 8.3 Routes Deliverables
* **Main Routes**: `/admin/work-schedules` (`GET`, `POST /templates`, `PUT /templates/{template}`, `DELETE /templates/{template}`, `POST /holidays`, `POST /leaves`).

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

* [ ] إنشاء قالب دوام وتعيين أيام العمل وفق `test_can_create_template_with_working_days()`.
* [ ] فرادة القالب الافتراضي في النظام وفق `test_only_one_default_template_can_exist()`.
* [ ] حظر حذف قالب دوام مسند لاستشاريين وفق `test_cannot_delete_template_assigned_to_active_consultants()`.
* [ ] تحويل حالة الاستشاري تلقائياً إلى `vacation` وفق `test_adding_leave_updates_consultant_status_to_vacation()`.

---

## 10. Cross References

* **Architecture Standards**: See `00_architecture_and_rules/architecture.md`
* **Business Rules (BR-006, BR-007, BR-008, BR-012, BR-015)**: See `00_architecture_and_rules/business_rules.md`
* **Database Schema**: See `00_architecture_and_rules/database.md#work-schedule-module`
* **Design Rules & Components**: See `01_design_system_and_components/`

---

## 11. Navigation

* ⚙️ **[backend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_03_work_schedules_and_leaves/backend.md)**
* 🎨 **[frontend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_03_work_schedules_and_leaves/frontend.md)**
* 🛣️ **[routes.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_03_work_schedules_and_leaves/routes.md)**

---

## 12. Developer Notes

* **الفرادة المعاملاتية (BR-007)**: عند تفعيل `is_default = true` لقالب، يلزم إلغاء الافتراضية عن القوالب الأخرى داخل DB Transaction.
* **حماية التكليف (BR-006)**: فحص عدم وجود استشاريين نشطين مسندين للقالب قبل أرشفته أو حذفه.
