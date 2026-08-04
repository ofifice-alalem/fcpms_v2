# 📌 Phase 06: التقارير والتحليلات والأداء (Reports & Analytics)

---

## 1. Phase Information

| Field | Value |
| :--- | :--- |
| **Phase Number** | `phase_06` |
| **Phase Name** | التقارير والتحليلات والأداء (Reports & Analytics) |
| **Module Name** | `ReportsModule` |
| **Status** | Approved / Documented |
| **Priority** | High |
| **Estimated Complexity** | Medium |

---

## 2. Business Purpose

بناء المحرك والواجهات التجميعية للتقارير والتحليلات لدعم كلا الطرفين: **لوحة تقارير وسجلات الاستشاري الشخصية (Consultant Personal Reports)** و**لوحة التقارير التنفيذية للـ HR والإدارة (HR Enterprise Analytics)** وتغطية الخصائص الخمس كاملة مع تصدير ملفات Excel/PDF.

---

## 3. Scope

تغطية وتوفير كافة الإحصائيات ومؤشرات KPI وتصفية البيانات الميدانية وحماية بيانات الاستشاري التنافسية (`BR-036` إلى `BR-040`).

---

## 4. In Scope

* **👨‍💼 منظور الاستشاري الشخصي (Consultant View)**:
  * شاشة التقارير الشخصية `pages/consultant/reports.blade.php`.
  * مؤشر الإنجاز الشخصي الشهري (% 94.2)، إجمالي الزيارات الميدانية، وأيام الدوام.
  * أرشيف وسجل الزيارات المزارة مصنفة حسب المدن والتواريخ مع زر استكشاف تفاصيل السجل 👁️.
  * تصدير كشف الحركات الميدانية الشخصي بصيغ Excel أو PDF.
* **🏢 منظور الموارد البشرية والإدارة (HR Enterprise View)**:
  * لوحة تحليلات الإدارة `pages/admin/reports.blade.php`.
  * كروت المؤشرات العامة للنظام، تصفية متقدمة (الاستشاري، الموقع، المدينة، نطاق التواريخ).
  * تصدير التقرير التنفيذي الشامل.

---

## 5. Out of Scope

* تعديل أو حذف بيانات الزيارات الميدانية المنجزة (راجع: `phase_05_daily_visits`).

---

## 6. Prerequisites

* **الصلاحيات الأمنية**: `view-personal-reports`, `view-enterprise-reports`, `export-reports`.
* **الكيانات المسبقة في قاعدة البيانات**: `daily_records`, `site_visits`, `task_responses`, `consultants`, `sites`.

---

## 7. Dependencies

### 7.1 Internal Dependencies
* **الجداول**: `daily_records`, `site_visits`, `task_responses`, `task_response_values`, `task_attachments`.

### 7.2 External Dependencies
* مكتبات التصدير: `Maatwebsite\Excel` و `DomPDF`.

### 7.3 Shared Components
* مكونات Spatial UI v3.0 (`KPI Highlight Cards`, `SpatialTable`, `spatial-dropdown-trigger`).

---

## 8. Deliverables

### 8.1 Backend Deliverables
* **Repository**: `ReportRepositoryInterface`, `ReportRepository`.
* **Service**: `ReportAnalyticsService`.
* **Controllers**: `App\Http\Controllers\Consultant\ConsultantReportController`, `App\Http\Controllers\Admin\AdminReportController`.
* **Form Requests**: `GenerateReportRequest`, `ExportReportRequest`.
* **Feature Test**: `tests/Feature/Analytics/ReportGenerationTest.php`.

### 8.2 Frontend Deliverables
* **Blade Views**: `resources/views/pages/consultant/reports.blade.php`, `resources/views/pages/admin/reports.blade.php`.
* **Modals & Drawers**: النافذة المنبثقة `#reportDetailModal`.
* **JS Functions**: `filterConsultantPersonalReports()`, `viewReportLogDetails()`, `exportReport()`.

### 8.3 Routes Deliverables
* **Main Routes**: `/consultant/reports` (`GET`), `/admin/reports` (`GET`), `/reports/visit-detail/{visit}` (`GET`), `/reports/export` (`GET`).

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

* [ ] عزل حماية بيانات الاستشاري واسترجاع تقاريره الشخصية فقط وفق `test_consultant_can_only_view_their_own_personal_reports()`.
* [ ] تمكين الـ HR من التصفية وتصدير التقارير الميدانية التنفيذية وفق `test_hr_can_filter_and_export_enterprise_performance_reports()`.
* [ ] سرعة استعادة التجميعات الضخمة عبر تحسين الاستعلامات والـ Caching.

---

## 10. Cross References

* **Architecture Standards**: See `00_architecture_and_rules/architecture.md`
* **Business Rules (BR-036 to BR-040)**: See `00_architecture_and_rules/business_rules.md`
* **Database Schema**: See `00_architecture_and_rules/database.md`
* **Design Rules & Components Catalog**: See `01_design_system_and_components/`

---

## 11. Navigation

* ⚙️ **[backend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_06_reports_and_analytics/backend.md)**
* 🎨 **[frontend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_06_reports_and_analytics/frontend.md)**
* <ctrl42> **[routes.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_06_reports_and_analytics/routes.md)**

---

## 12. Developer Notes

* **عزل البيانات والأمان التنافسي**: التأكد من أن الاستشاري يستطيع استرجاع بياناته وحركاته الميدانية الشخصية فقط دون الاطلاع على سجلات الزملاء الآخرين.
* **التحسين الأدائي الاستعلامي**: استخدام تجميعات SQL المحسنة والـ Server-side Caching للطلبات التنفيذية الضخمة.
