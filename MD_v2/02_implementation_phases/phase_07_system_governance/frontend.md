# 🎨 Frontend Implementation: Phase 07 — حوكمة النظام والصلاحيات وسجلات التدقيق (System Governance & Auditing)

---

## 1. Overview & UI Objectives
- **اسم المرحلة**: `phase_07_system_governance`
- **الهدف البصري**: توفير واجهة حوكمة النظام وإدارة الصلاحيات المصممة بـ **Spatial UI v3.0** التي تمنح مدراء النظام إمكانية إدارة الأدوار والصلاحيات (Roles & Permissions)، التحكم في مفاتيح التشغيل والإعدادات العامة (Feature Flags & Settings)، ومتابعة سجلات التدقيق الأمني (Audit Logs)، وتغطية كافة الخصائص الخمس (عرض الأدوار والسجلات، إضافة دور جديد، تعديل الصلاحيات والإعدادات، عرض التفاصيل والمقارنات البرمجية، والحذف المحمي مع التأكيد).
- **الالتزام بالقواعد البصرية**:
  - الأبعاد والـ Glassmorphic Gradients المعتمدة في `01_design_system_and_components/design_rules.md`.
  - المكونات الموثقة في `01_design_system_and_components/components_catalog.html`.

---

## 2. Page Layout & Structure
- **مسار الملف في المشروع**: `resources/views/pages/admin/governance.blade.php`
- **هيكل الصفحة**:
  - **Header**: العنوان `حوكمة النظام والأدوار وسجلات الأمان` + زر `+ إنشاء دور صلاحيات جديد`.
  - **Tab Navigation Bar**: شريط تبويبات تنقل زجاجي انسيابي:
    1. `الأدوار والصلاحيات (Roles & Permissions)` (Active Tab)
    2. `إعدادات النظام و Feature Flags`
    3. `سجلات التدقيق والحركات (Audit Activity Logs)`

---

## 3. Detailed UI States & Component Specifications

### 3.1 🟢 1. حالة عرض الأدوار والسجلات (Index & Grid View)
- **قسم الأدوار والصلاحيات**: كروت زجاجية (`spatial-card`) لكل دور (Super Admin, HR Manager, Consultant) تضم:
  - اسم الدور وشعار الأمان الملون.
  - عدد المستخدمين المسندين للدور وعدد الصلاحيات الممنوحة.
  - أزرار التحكم: زر `عرض الصلاحيات 👁️`, زر `تعديل ✏️`, وزر `حذف 🗑️`.
- **قسم سجلات التدقيق**: جدول بيانات `SpatialTable` يعرض: `المستخدم`, `الإجراء`, `الكيان المستهدف`, `عنوان IP`, `التاريخ والوقت`, وزر `استكشاف الحركة 👁️`.

### 3.2 ➕ 2. خاصية الإنشاء الجديد (Create Role Drawer)
- **عنصر المشغل**: زر `+ إنشاء دور صلاحيات جديد`.
- **عنصر الواجهة**: اللوحة الجانبية `#roleDrawer` (`w-[600px]`).
- **المحتوى**:
  - اسم الدور باللغة العربية والإنجليزية (`spatial-input h-14`).
  - **مصفوفة الصلاحيات المتبوبة (Categorized Permissions Matrix)**: مربعات اختيار مخصصة (`custom-checkbox`) مقسمة حسب الموديولات (المواقع، الاستشاريين، الدوام، منشئ المهام، التقارير).
  - زر `حفظ وبناء الدور`.

### 3.3 ✏️ 3. تعديل الصلاحيات والإعدادات (Edit Role & Settings)
- **عنصر المشغل**: زر التعديل ✏️ في كرت الدور أو جدول الإعدادات.
- **عنصر الواجهة**: نفس اللوحة الجانبية `#roleDrawer` بحالة التعديل وتعبئة الصلاحيات المسندة مسبقاً.

### 3.4 👁️ 4. عرض تفاصيل الحركة ومقارنة البيانات (View Audit Log Detail Modal)
- **عنصر المشغل**: زر الاستكشاف 👁️ في جدول سجلات التدقيق.
- **عنصر الواجهة**: النافذة المنبثقة الزجاجية `#auditLogDetailModal` (`spatial-modal-card max-w-2xl`).
- **المحتوى التفاعلي**:
  - مقارنة بصرية بين البيانات القديمة `old_values` والبيانات الجديدة `new_values` (JSON Diff View).
  - عنوان الـ IP والموقع والمتصفح والمستخدم المنفذ للحركة.

### 3.5 🗑️ 5. حالة الحذف الأرشيفي المحمي (Delete Role Modal)
- **عنصر المشغل**: زر الحذف 🗑️ في كرت الدور.
- **عنصر الواجهة**: نافذة التأكيد الزجاجية `#deleteRoleModal`.
- **التحذير المحمي**: حظر حذف أي دور يحتوي على مستخدمين نشطين وإلزام إعادة توزيعهم أولاً.

---

## 4. JavaScript Logic Functions
```javascript
function openCreateRoleDrawer() { openDrawer('roleDrawer'); }
function openEditRoleDrawer(roleData) { fillRoleForm(roleData); openDrawer('roleDrawer'); }

function viewAuditLogDetails(logId) {
    fetchAuditLogDiff(logId);
    openModal('auditLogDetailModal');
}
```
