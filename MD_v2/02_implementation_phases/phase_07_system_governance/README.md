# 📌 Phase 07: حوكمة النظام والصلاحيات وسجلات التدقيق (System Governance & Auditing)

---

## 1. Phase Information

| Field | Value |
| :--- | :--- |
| **Phase Number** | `phase_07` |
| **Phase Name** | حوكمة النظام والصلاحيات وسجلات التدقيق (System Governance & Auditing) |
| **Module Name** | `GovernanceModule` |
| **Status** | Approved / Documented |
| **Priority** | Medium |
| **Estimated Complexity** | Medium |

---

## 2. Business Purpose

بناء الواجهات والمنطق البرمجي لحوكمة النظام، إدارة الأدوار والصلاحيات الديناميكية عبر Spatie Permission (`Super Admin`, `HR Manager`, `Consultant`)، إدارة مفاتيح الإعدادات التشغيلية (`Feature Flags & Settings`)، ومتابعة سجلات التدقيق الأمني (`Audit Activity Logs`).

---

## 3. Scope

تغطية حوكمة النظام والأدوار وتتبع التغييرات على البيانات وتطبيق قواعد الحوكمة والأمان (`BR-041` إلى `BR-045`).

---

## 4. In Scope

* **إدارة الأدوار والصلاحيات (Roles & Permissions)**: إنشاء وتحديث الأدوار، تخصيص مصفوفة الصلاحيات المتبوبة، وحظر سحب صلاحيات `Super Admin`.
* **إعدادات النظام و Feature Flags**: إدارة مفاتيح الإعدادات العامة وقيمها التشغيلية في جدول `settings`.
* **سجلات التدقيق الأمني (Audit Logs)**: تسجيل تتبع تغيير البيانات في `activity_logs` وتخزين القيم القديمة والجديدة (`old_values` مقابل `new_values`).
* **شاشة مقارنة الحركات الفردية**: النافذة المنبثقة `#auditLogDetailModal` لعرض الـ JSON Diff، عنوان IP، وبيانات الجهاز.
* **حظر الحذف غير الآمن**: منع حذف أي دور مسند حالياً لمستخدمين نشطين.

---

## 5. Out of Scope

* المصادقة الأولية وتأمين الجلسات (راجع: `phase_00_authentication`).

---

## 6. Prerequisites

* **الصلاحيات ومجموعات Middleware**: `role:Super Admin`, `manage-governance`, `web`, `auth`.
* **الكيانات المسبقة في قاعدة البيانات**: `users`, `roles`, `permissions`.

---

## 7. Dependencies

### 7.1 Internal Dependencies
* **الجداول**: `roles`, `permissions`, `model_has_roles`, `role_has_permissions`, `settings`, `activity_logs`.

### 7.2 External Dependencies
* حزمة `Spatie\Permission`.

### 7.3 Shared Components
* مكونات Spatial UI v3.0 (`SpatialTable`, `spatial-card`, `custom-checkbox`).

---

## 8. Deliverables

### 8.1 Backend Deliverables
* **Models**: `Spatie\Permission\Models\Role`, `Spatie\Permission\Models\Permission`, `App\Models\Setting`, `App\Models\ActivityLog`.
* **Repository**: `GovernanceRepositoryInterface`, `GovernanceRepository`.
* **Service**: `GovernanceService`.
* **Controller**: `App\Http\Controllers\Admin\GovernanceController`.
* **Form Requests**: `StoreRoleRequest`, `UpdateRoleRequest`, `UpdateSettingsRequest`.
* **Feature Test**: `tests/Feature/Admin/SystemGovernanceTest.php`.

### 8.2 Frontend Deliverables
* **Blade View**: `resources/views/pages/admin/governance.blade.php`.
* **Drawers & Modals**: اللوحة الجانبية `#roleDrawer` (بعرض 600px), النافذة المنبثقة `#auditLogDetailModal`, ونافذة التأكيد `#deleteRoleModal`.
* **JS Functions**: `openCreateRoleDrawer()`, `openEditRoleDrawer()`, `viewAuditLogDetails()`.

### 8.3 Routes Deliverables
* **Main Routes**: `/admin/governance` (`GET`, `POST /roles`, `PUT /roles/{role}`, `DELETE /roles/{role}`, `POST /settings`, `GET /audit-logs/{log}`).

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

* [ ] حظر سحب أو تعديل صلاحيات الـ Super Admin وفق `test_super_admin_role_permissions_cannot_be_revoked()`.
* [ ] التوليد التلقائي لسجل التدقيق عند إجراء أي تغيير حساس وفق `test_all_critical_model_changes_generate_activity_log_entry()`.
* [ ] نجاح المقارنة البصرية وتخزين الـ JSON Diff للحركات الميدانية.

---

## 10. Cross References

* **Architecture Standards**: See `00_architecture_and_rules/architecture.md`
* **Business Rules (BR-041 to BR-045)**: See `00_architecture_and_rules/business_rules.md`
* **Database Schema**: See `00_architecture_and_rules/database.md#system-module`
* **Design Rules & Components Catalog**: See `01_design_system_and_components/`

---

## 11. Navigation

* ⚙️ **[backend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_07_system_governance/backend.md)**
* 🎨 **[frontend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_07_system_governance/frontend.md)**
* 🛣️ **[routes.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_07_system_governance/routes.md)**

---

## 12. Developer Notes

* **الحماية القيادية الحاكمة**: يجب وضع فحص أمان يمنع تعديل أو سحب صلاحيات دور `Super Admin`.
* **تصفية وحفظ سجلات التدقيق**: تخزين البيانات القديمة والجديدة بصيغة JSON نظيفة وسحب عنوان الـ IP والـ User Agent لكل عملية تحويرية.
