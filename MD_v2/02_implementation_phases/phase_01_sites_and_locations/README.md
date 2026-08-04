# 📌 Phase 01: إدارة المواقع الميدانية (Sites & Locations)

---

## 1. Phase Information

| Field | Value |
| :--- | :--- |
| **Phase Number** | `phase_01` |
| **Phase Name** | إدارة المواقع الميدانية (Sites & Locations) |
| **Module Name** | `SitesModule` |
| **Status** | Approved / Documented |
| **Priority** | High |
| **Estimated Complexity** | Medium |

---

## 2. Business Purpose

بناء المنطق البرمجي والواجهات التفاعلية لإدارة دليل المواقع والمنشآت الميدانية وتغطية كافة العمليات والإجراءات: الإنشاء المباشر برمز فريد، التعديل والتحديث الدوري، تغيير حالة التشغيل، أرشفة وحذف الموقع، واسترجاع التفاصيل.

---

## 3. Scope

تغطية جميع العمليات الخمس لمدونة المواقع والمنشآت الميدانية وتطبيق قواعد الأعمال الخاصة بالمواقع (`BR-020`, `BR-021`, `BR-022`, `BR-023`).

---

## 4. In Scope

* **إنشاء موقع ميداني جديد**: برمز فريد وتحديد المدينة والعنوان المباشر والحالة التشغيلية الافتراضية `active`.
* **تحديث وتعديل بيانات موقع قائم**: تعديل الاسم والمدينة والعنوان والملاحظات الهندسية مع تجميد تعديل الرمز `code` إذا كان لديه زيارات سابقة.
* **تبديل الحالة التشغيلية للموقع**: تحويل الحالة بين `active` و `inactive` وتجميد إسناد المهام الجديدة عند التعطيل (BR-021).
* **الحذف الأرشيفي المحمي**: تطبيق `SoftDeletes` بعد التحقق من عدم وجود زيارات ميدانية جارية أو معلقة (BR-022).
* **عرض تفاصيل الموقع**: جلب بيانات وإحصائيات الزيارات الميدانية المسجلة للموقع والمهام المربوطة به داخل نافذة منبثقة.
* **واجهة Data Grid وإجراءات التجميع**: البحث الحي، الفلترة حسب المدن، وشريط الإجراءات المجمعة Floating Bulk Bar.

---

## 5. Out of Scope

* تنفيذ الزيارات الميدانية الفعلية داخل المواقع (راجع: `phase_05_daily_visits`).
* بناء استبيانات التفتيش والمهام المربوطة بالمواقع (راجع: `phase_04_task_builder`).

---

## 6. Prerequisites

* **الصلاحيات الأمنية المسندة**: `view-sites`, `create-sites`, `edit-sites`, `delete-sites`.
* **المكونات والأنماط المعمارية**: نمط `Repository-Service Pattern` ومعيار `Strict Mode`.

---

## 7. Dependencies

### 7.1 Internal Dependencies
* **جدول البيانات**: `sites`
* **النماذج المترابطة**: `SiteVisit` عبر `siteVisits()`, `TaskSiteAssignment` عبر `taskSiteAssignments()`.

### 7.2 External Dependencies
* Not yet documented.

### 7.3 Shared Components
* مكونات Spatial UI v3.0 (`SpatialTable`, `spatial-input`, `spatial-dropdown-menu`, `spatial-switch`, `spatial-modal-card`).

---

## 8. Deliverables

### 8.1 Backend Deliverables
* **Model**: `App\Models\Site`
* **Repository**: `App\Repositories\Contracts\SiteRepositoryInterface`, `App\Repositories\Eloquent\SiteRepository`
* **Service**: `App\Services\SiteService`
* **Controller**: `App\Http\Controllers\Admin\SiteController`
* **Form Requests**: `StoreSiteRequest`, `UpdateSiteRequest`
* **Feature Test**: `tests/Feature/Admin/SiteManagementTest.php`

### 8.2 Frontend Deliverables
* **Blade View**: `resources/views/pages/sites/index.blade.php`
* **Drawers & Modals**: اللوحة الجانبية `#siteFormDrawer` (بعرض `w-[480px]`), النافذة المنبثقة `#siteDetailModal`, ونافذة التأكيد `#deleteModal`.
* **JS Functions**: `openCreateSiteDrawer()`, `openEditSiteDrawer()`, `viewSiteDetails()`, `confirmDeleteSite()`.

### 8.3 Routes Deliverables
* **Main Route**: `/admin/sites` (`GET`, `POST`, `GET /{site}`, `PUT /{site}`, `PATCH /{site}/toggle-status`, `DELETE /{site}`).

### 8.4 Database Impact
* **Table**: `sites` (`id`, `code`, `name`, `address`, `city`, `status`, `notes`, `created_at`, `updated_at`, `deleted_at`).

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

* [ ] إمكانية إضافة موقع برمز فريد نجاح الاختبار `test_can_create_site_with_unique_code()`.
* [ ] منع حذف أي موقع يحتوي على زيارات معلقة نجاح الاختبار `test_cannot_delete_site_with_pending_visits()`.
* [ ] تجميد إسناد المهام للموقع عند تحويله إلى غير نشط نجاح الاختبار `test_deactivating_site_prevents_new_task_assignments()`.
* [ ] تطبيق معايير `SoftDeletes` لحفظ سجلات الزيارات التاريخية للموقع.

---

## 10. Cross References

* **Architecture Standards**:
  → See: `00_architecture_and_rules/architecture.md`
* **Business Rules (BR-020, BR-021, BR-022, BR-023)**:
  → See: `00_architecture_and_rules/business_rules.md`
* **Database Schema (Sites Module)**:
  → See: `00_architecture_and_rules/database.md#sites-module`
* **Design Rules & Tokens**:
  → See: `01_design_system_and_components/design_rules.md`
* **UI Components Catalog**:
  → See: `01_design_system_and_components/components_catalog.html`

---

## 11. Navigation

* ⚙️ **[backend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_01_sites_and_locations/backend.md)**: يحتوي على تفاصيل النماذج، طبقة المستودعات والخدمات، وطلبات التحقق واسم ملف الاختبارات.
* 🎨 **[frontend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_01_sites_and_locations/frontend.md)**: يحتوي على مكونات الواجهة، مسارات الـ Blade، تفاصيل الحقول، ودوال السكربت.
* 🛣️ **[routes.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_01_sites_and_locations/routes.md)**: يحتوي على جدول المسارات الكامل، الـ Middleware، الصلاحيات المطلوبة، ونماذج استجابة الـ JSON.

---

## 12. Developer Notes

* **فرادة الرمز الميداني (BR-020)**: يجب إجراء فحص فرادة الرمز `code` فورا وإعادة رسالة خطأ `422 Unprocessable Entity` في حال التكرار.
* **حفظ السلامة التاريخية للمستندات**: يجب تجميد تعديل رمز الموقع `code` برمجياً إذا تم تسجيل زيارات ميدانية سابقة للموقع.
* **فحص السلامة قبل الحذف (BR-022)**: يلزم استدعاء دالة الفحص في الـ Service لمنع حذف الموقع الذي لديه زيارات معلقة بحالة `pending`.
* **الحماية وحظر الاغراق**: تطبيق Middleware الـ `throttle:60,1` وحماية الـ `CSRF Protection` إجباري على كافة المسارات التحويرية.
