# 📌 Phase 05: تنفيذ الزيارات اليومية الميدانية (Consultant Daily Visits)

---

## 1. Phase Information

| Field | Value |
| :--- | :--- |
| **Phase Number** | `phase_05` |
| **Phase Name** | تنفيذ الزيارات اليومية الميدانية (Consultant Daily Visits) |
| **Module Name** | `DailyActivityModule` |
| **Status** | Approved / Documented |
| **Priority** | High |
| **Estimated Complexity** | High |

---

## 2. Business Purpose

بناء وتوفير الشاشة والمنطق البرمجي للبوابة الميدانية الخاصة بالاستشاري (Consultant Field Portal) لتسجيل الحضور اليومي، فتح وتوثيق زيارات المواقع، وتطبيق الفصل التام بين المهام اليومية الإجبارية والمهام المخصصة عند الحاجة، ورفع إثباتات الصور الحية مع دعم التعديل طالما أن الزيارة قيد التنفيذ.

---

## 3. Scope

تغطية العمليات الإجرائية الخمس للاستشاري في الميدان وتطبيق قواعد العمل (`BR-018`, `BR-019`, `BR-023`, `BR-028`, `BR-030`).

---

## 4. In Scope

* **فتح سجل اليوم الفعال**: تسجيل بدء اليوم العملي للاستشاري وحفظ تاريخ اليوم (`record_date = today()`) (BR-018).
* **اختيار موقع وفتح زيارة جارية (`Site Dropdown`)**: اختيار الموقع الميداني من قائمة منسدلة z-index عالية وإدراجه في جدول اليوم الفعال للاستشاري (BR-023).
* **الفصل بين أنواع المهام (BR-019)**:
  * **المهام اليومية (`daily`)**: إظهارها وتوليد نماذج تفقدها **فوراً ومباشرةً** عند فتح الزيارة.
  * **المهام عند الحاجة (`on_demand`)**: تفعيلها وإضافتها لنموذج الزيارة حصرياً عبر **قائمة منسدلة مخصصة للمهام عند الحاجة**.
* **تعديل الإجابات الميدانية المباشرة**: السماح بتعديل القيم الحية طالما أن الزيارة بحالة `in_progress`.
* **منطقة رفع الإثباتات الحية (`Interactive Image Upload Dropzone`)**: المعاينة الفورية واستضافة الصور والمرفقات لكل مهمة.
* **عرض التفاصيل وإلغاء الزيارة المعلقة**: النافذة المنبثقة `#visitDetailModal` وإلغاء الزيارة المعلقة غير المكتملة عبر `#cancelVisitModal`.

---

## 5. Out of Scope

* بناء وتصميم المهام والحقول الشرطية (راجع: `phase_04_task_builder`).
* استخراج تقارير الأداء الشخصية أو التنفيذية (راجع: `phase_06_reports_and_analytics`).

---

## 6. Prerequisites

* **الصلاحيات ومجموعات Middleware**: `execute-daily-visits`, `web`, `auth`.
* **الكيانات المسبقة في قاعدة البيانات**: `consultants`, `sites`, `task_definitions`.

---

## 7. Dependencies

### 7.1 Internal Dependencies
* **الجداول**: `daily_records`, `site_visits`, `task_responses`, `task_response_values`, `task_attachments`.

### 7.2 External Dependencies
* Not yet documented.

### 7.3 Shared Components
* مكونات Spatial UI v3.0 (`Interactive Image Upload Dropzone`, `Smart Adaptive Form`, `spatial-dropdown-trigger`).

---

## 8. Deliverables

### 8.1 Backend Deliverables
* **Models**: `DailyRecord`, `SiteVisit`, `TaskResponse`, `TaskResponseValue`, `TaskAttachment`.
* **Repository**: `DailyRecordRepository`, `SiteVisitRepository`.
* **Service**: `ConsultantVisitService`.
* **Controller**: `App\Http\Controllers\Consultant\DailyVisitController`.
* **Form Requests**: `StartDailyRecordRequest`, `OpenSiteVisitRequest`, `SaveTaskResponsesRequest`.
* **Feature Test**: `tests/Feature/Consultant/DailyVisitsExecutionTest.php`.

### 8.2 Frontend Deliverables
* **Blade View**: `resources/views/pages/consultant/daily_visits.blade.php`.
* **Components & Modals**: قائمة اختيار المواقع المنسدلة (`Site Dropdown`), قائمة المهام عند الحاجة المنسدلة, النافذة المنبثقة `#visitDetailModal`, ونافذة إلغاء الزيارة `#cancelVisitModal`.
* **JS Functions**: `openSiteVisitFromDropdown()`, `triggerOnDemandTask()`, `handleConditionalFieldToggle()`.

### 8.3 Routes Deliverables
* **Main Routes**: `/consultant/daily-visits` (`GET`, `POST /start-day`), `/consultant/site-visits` (`POST`, `POST /{visit}/trigger-on-demand`, `POST /{visit}/save-responses`, `GET /{visit}`, `DELETE /{visit}`).

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

* [ ] فتح سجل اليوم واختيار موقع من القائمة المنسدلة وفق `test_consultant_can_start_daily_record_and_select_site_from_dropdown()`.
* [ ] التحميل التلقائي للمهام اليومية واشتراط اختيار المهام عند الحاجة من قائمة منسدلة وفق `test_daily_tasks_auto_load_while_ondemand_tasks_require_dropdown_trigger()`.
* [ ] نجاح تعديل الإجابات الميدانية أثناء الزيارة القائمة وفق `test_consultant_can_edit_responses_during_active_visit()`.

---

## 10. Cross References

* **Architecture Standards**: See `00_architecture_and_rules/architecture.md`
* **Business Rules (BR-018, BR-019, BR-023, BR-028, BR-030)**: See `00_architecture_and_rules/business_rules.md`
* **Database Schema**: See `00_architecture_and_rules/database.md#daily-activity-module`
* **Design Rules & Components Catalog**: See `01_design_system_and_components/`

---

## 11. Navigation

* ⚙️ **[backend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_05_daily_visits/backend.md)**
* 🎨 **[frontend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_05_daily_visits/frontend.md)**
* 🛣️ **[routes.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_05_daily_visits/routes.md)**

---

## 12. Developer Notes

* **الروزنامة اليومية (BR-018)**: التحقق من قيد السجل اليومي الفريد ومنع فتح سجلين لنفس الاستشاري باليوم الواحد.
* **الفصل التشغيلي (BR-019)**: المهام اليومية تظهر تلقائياً، والمهام عند الحاجة تُضاف حتمياً عبر الاختيار من الـ Dropdown المخصص.
* **مرونة التعديل الحية**: السماح بالتعديل المستمر طالما أن حالة الزيارة `in_progress`.
