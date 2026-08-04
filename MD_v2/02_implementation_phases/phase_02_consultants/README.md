# 📌 Phase 02: إدارة الاستشاريين الميدانيين (Consultants Management)

---

## 1. Phase Information

| Field | Value |
| :--- | :--- |
| **Phase Number** | `phase_02` |
| **Phase Name** | إدارة الاستشاريين الميدانيين (Consultants Management) |
| **Module Name** | `ConsultantsModule` |
| **Status** | Approved / Documented |
| **Priority** | High |
| **Estimated Complexity** | High |

---

## 2. Business Purpose

بناء المنطق البرمجي والواجهات التفاعلية المصممة بـ Spatial UI v3.0 لإدارة دليل الاستشاريين الميدانيين وتغطية دورة الحياة الكاملة للحسابات: الإنشاء الموحد مع حساب المستخدم الموازي، التعديل الدوري للبيانات والتخصص، تغيير حالة التوظيف التشغيلية، وعرض تفاصيل الاستشاري والحذف الآمن عبر SoftDeletes.

---

## 3. Scope

تغطية جميع العمليات الخمس لدورة حياة سجلات وحسابات الاستشاريين الميدانيين وتطبيق قواعد الأعمال الخاضعة لها (`BR-003`, `BR-005`, `BR-015`).

---

## 4. In Scope

* **إنشاء سجل استشاري جديد وحساب مستخدم موازي**: التوليد التلقائي للرقم الوظيفي `#EMP-XXXX` (BR-003)، إنشاء حساب `User` موازٍ بـ Spatie Role `Consultant` وتعيين `status = 'active'`.
* **تعديل بيانات الملف الشخصي**: تحديث الاسم الكامل، البريد الإلكتروني، رقم الهاتف، والتخصص الميداني مع تجميد تعديل الرقم الوظيفي `#EMP-XXXX`.
* **تغيير حالة التوظيف التشغيلية**: التبديل بين الحالات (`active`, `suspended`, `vacation`) وتطبيق BR-015 عند التعليق لإلغاء الجلسات النشطة وتحديث حساب المستخدم إلى `inactive`.
* **عرض تفاصيل ملف الاستشاري**: النافذة المنبثقة `#consultantDetailModal` لعرض البيانات الشخصية، الموقع، تاريخ التعيين، التخصص، نسبة الإنجاز اليومي، وشارة الحالة.
* **الحذف الأرشيفي الآمن**: أرشفة سجل الاستشاري وحسابه الموازي عبر `SoftDeletes` بعد فحص سلامة عدم وجود زيارات معلقة بدون حذف الأرشيف التاريخي للزيارات.
* **جدول البيانات والبطاقة البارزة**: عرض Data Grid ومكون `Consultant KPI Highlight Card` وشريط العمليات المجمعة `#bulkBarFloating`.

---

## 5. Out of Scope

* إدارة جداول الدوام وقوالب المواعيد الخاصة بالاستشاري (راجع: `phase_03_work_schedules_and_leaves`).
* تسجيل وإثبات الزيارات الميدانية اليومية (راجع: `phase_05_daily_visits`).

---

## 6. Prerequisites

* **الصلاحيات الأمنية المسندة**: `view-consultants`, `create-consultants`, `edit-consultants`, `delete-consultants`.
* **الأدوار المسبقة في النظام**: دور `Consultant` في Spatie Permissions.

---

## 7. Dependencies

### 7.1 Internal Dependencies
* **جداول البيانات**: `consultants`, `users`
* **النماذج والعلاقات**:
  * `User` عبر `user()`
  * `WorkScheduleTemplate` عبر `workScheduleTemplate()`
  * `DailyRecord` عبر `dailyRecords()`
  * `ConsultantLeave` عبر `consultantLeaves()`

### 7.2 External Dependencies
* Not yet documented.

### 7.3 Shared Components
* مكونات Spatial UI v3.0 (`SpatialTable`, `spatial-drawer`, `spatial-modal-card`, `spatial-input`, `spatial-dropdown-trigger`).

---

## 8. Deliverables

### 8.1 Backend Deliverables
* **Model**: `App\Models\Consultant`
* **Repository**: `App\Repositories\Contracts\ConsultantRepositoryInterface`, `App\Repositories\Eloquent\ConsultantRepository`
* **Service**: `App\Services\ConsultantService`
* **Controller**: `App\Http\Controllers\Admin\ConsultantController`
* **Form Requests**: `StoreConsultantRequest`, `UpdateConsultantRequest`, `ChangeConsultantStatusRequest`
* **Events**: `ConsultantUpdatedEvent`
* **Feature Test**: `tests/Feature/Admin/ConsultantManagementTest.php`

### 8.2 Frontend Deliverables
* **Blade View**: `resources/views/pages/consultants/index.blade.php`
* **Drawers & Modals**: اللوحة الجانبية `consultantDrawer` (בעرض `w-[480px]`), النافذة المنبثقة `#consultantDetailModal`, ونافذة التأكيد `#deleteModal`.
* **JS Functions**: `openCreateConsultantDrawer()`, `openEditConsultantDrawer()`, `viewConsultantProfile()`, `confirmDeleteConsultant()`, `executeDeleteConsultant()`.

### 8.3 Routes Deliverables
* **Main Route**: `/admin/consultants` (`GET`, `POST`, `GET /{consultant}`, `PUT /{consultant}`, `PATCH /{consultant}/status`, `DELETE /{consultant}`).

### 8.4 Database Impact
* **Tables**: `consultants` (`id`, `user_id`, `employee_number`, `full_name`, `phone`, `hire_date`, `specialization`, `work_schedule_template_id`, `employment_status`, `notes`, `created_at`, `updated_at`, `deleted_at`) و `users`.

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

* [ ] إنشاء حساب استشاري وحساب مستخدم موازي بنجاح وفق الاختبار `test_can_create_consultant_with_user_account()`.
* [ ] نجاح تحديث بيانات الاستشاري وتجميد الرقم الوظيفي وفق `test_can_update_consultant_profile()`.
* [ ] إلغاء تفعيل الجلسات فور تعليق الاستشاري وفق `test_changing_status_to_suspended_revokes_sessions()`.
* [ ] تطبيق الـ `SoftDeletes` والحفاظ على السجلات التاريخية للزيارات وفق `test_soft_deleting_consultant_preserves_historical_records()`.

---

## 10. Cross References

* **Architecture Standards**:
  → See: `00_architecture_and_rules/architecture.md`
* **Business Rules (BR-003, BR-005, BR-015)**:
  → See: `00_architecture_and_rules/business_rules.md`
* **Database Schema (Consultants & Users Module)**:
  → See: `00_architecture_and_rules/database.md#consultants`
* **Design Rules & Spatial Tokens**:
  → See: `01_design_system_and_components/design_rules.md`
* **UI Components Catalog**:
  → See: `01_design_system_and_components/components_catalog.html`

---

## 11. Navigation

* ⚙️ **[backend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_02_consultants/backend.md)**: يحتوي على المعاملات المزدوجة، فئات النماذج والخدمات، وطلبات التحقق واسم ملف الاختبارات.
* 🎨 **[frontend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_02_consultants/frontend.md)**: يحتوي على مكونات الواجهة البصرية، بطاقة الاستشاري البارزة، تفاصيل الحقول، ودوال السكربت.
* 🛣️ **[routes.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_02_consultants/routes.md)**: يحتوي على جدول المسارات الكامل، مجموعات الـ Middleware، الصلاحيات، ونماذج استجابة الـ JSON.

---

## 12. Developer Notes

* **التوليد الفريد للرقم الوظيفي (BR-003)**: توليد الرقم الوظيفي المرجعي `#EMP-XXXX` يتم تلقائياً داخل DB Transaction وتجميد تعديله نهائياً بعد الإنشاء.
* **إدارة الجلسات الحالية (BR-015)**: عند تحويل حالة الاستشاري إلى `suspended` أو `vacation` يلزم إلغاء الجلسات النشطة (Revoke Tokens / Flush Sessions) وتحديث الـ User الموازي إلى `status = 'inactive'`.
* **الحفاظ على الأرشيف المالي والميداني**: حظر مسح سجلات الزيارات التاريخية المسجلة باسم الاستشاري عند إجراء الحذف الأرشيفي (`SoftDeletes`).
* **معدل الحماية والـ Throttling**: تطبيق `throttle:60,1` والـ `CSRF Protection` إجباري لكافة المسارات.
