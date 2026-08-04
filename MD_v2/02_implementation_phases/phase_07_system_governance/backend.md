# ⚙️ Backend Implementation: Phase 07 — حوكمة النظام والصلاحيات وسجلات التدقيق (System Governance & Auditing)

---

## 1. Overview & Scope
- **اسم المرحلة**: `phase_07_system_governance`
- **الهدف التنفيذي**: بناء المنطق البرمجي لحوكمة النظام، إدارة الأدوار والصلاحيات الديناميكية عبر Spatie Permission (`Super Admin`, `HR Manager`, `Consultant`)، إدارة مفاتيح الإعدادات التشغيلية (Feature Flags & Settings)، ومراقبة سجلات الحركات والتدقيق الأمني (Audit Activity Logs)، وتغطية الخصائص الخمس كاملة (عرض دليل الأدوار والسجلات، إنشاء دور/إعداد جديد، تعديل الصلاحيات والإعدادات، عرض تفاصيل الحركات السابقة، وأرشفة وحظر الصلاحيات).
- **التزام المعمارية**:
  - الالتزام بنمط `Repository-Service Pattern` وحالة `Strict Mode` الموثقة في `00_architecture_and_rules/architecture.md`.
  - الامتثال لجداول النظام والتدقيق في `00_architecture_and_rules/database.md#system-module`.
  - تطبيق قواعد الأعمال الخاصة بالحوكمة: `BR-041` إلى `BR-045` (تتبع التغييرات عبر `activity_logs` وفرض الأمان العالي).

---

## 2. Models & Database Entities
- **النماذج الرئيسية**:
  1. `Spatie\Permission\Models\Role` (`roles`)
  2. `Spatie\Permission\Models\Permission` (`permissions`)
  3. `App\Models\Setting` (`settings`)
  4. `App\Models\ActivityLog` (`activity_logs`)

- **الحقول والعلاقات**:
  - `Setting`: `id`, `key (UNIQUE)`, `value`, `group ('general', 'notifications', 'feature_flags')`, `description`, `created_at`, `updated_at`.
  - `ActivityLog`: `id`, `user_id (FK → users)`, `action`, `model_type`, `model_id`, `old_values (json)`, `new_values (json)`, `ip_address`, `user_agent`, `created_at`.
    - العلاقات: `belongsTo(User::class)`.

---

## 3. Detailed Operations & Business Logic

### 3.1 🟢 1. صفحة عرض الأدوار والإعدادات وسجلات التدقيق (Index Operation)
- **المسار**: `GET /admin/governance`
- **منطق المعالجة**:
  - جلب قائمة الأدوار الحالية (`roles`) مع عَدَدِ المستخدمين المسندين لكل دور والصلاحيات الممنوحة له `with('permissions')`.
  - استرجاع إعدادات النظام ومفاتيح التشغيل (`Feature Flags`).
  - جلب سجلات التدقيق والحركات `activity_logs` المحدثة مؤخراً.

### 3.2 ➕ 2. عملية الإنشاء الجديد (Create Role / Setting Action)
- **إنشاء دور جديد (`POST /admin/governance/roles`)**:
  1. التحقق من فرادة اسم الدور `name`.
  2. ربط قائمة الصلاحيات المحددة `permissions` بالدور المنشأ.
- **إضافة مفتاح إعداد جديد (`POST /admin/governance/settings`)**:
  1. إضافة الـ Key والـ Value والـ Group في جدول `settings`.

### 3.3 ✏️ 3. تعديل الصلاحيات والإعدادات (Update Action)
- **المسار**: `PUT /admin/governance/roles/{role}`
- **منطق المعالجة**:
  1. منع تعديل أو سحب صلاحيات دور `Super Admin` الحاكم الحفاظ على الوصول القيادي.
  2. تحديث قائمة الصلاحيات المسندة للـ Role وتوليد حدث تدقيق في `activity_logs`.

### 3.4 👁️ 4. عرض تفاصيل سجل تدقيق محدد (Show Audit Detail Action)
- **المسار**: `GET /admin/governance/audit-logs/{log}`
- **منطق المعالجة**: استرجاع القيم القديمة والجديدة (`old_values` مقابل `new_values`) للحركة مع بيانات المستخدم وعنوان الـ IP وشاشة الاستخدام.

### 3.5 🗑️ 5. الحذف والأرشفة المحمية (Delete Action)
- **المسار**: `DELETE /admin/governance/roles/{role}`
- **منطق المعالجة**: منع حذف أي دور مسند حالياً لمستخدمين نشطين، وأرشفة الدور عبر SoftDelete آمن.

---

## 4. Repository & Service Layer
- **الواجهات والتطبيقات**:
  - `GovernanceRepositoryInterface` / `GovernanceRepository`
  - `GovernanceService`
- **طلب التحقق**: `StoreRoleRequest`, `UpdateRoleRequest`, `UpdateSettingsRequest`.

---

## 5. Testing Standards
- **ملف الاختبار**: `tests/Feature/Admin/SystemGovernanceTest.php`
- **الحالات المغطاة**:
  - `test_super_admin_role_permissions_cannot_be_revoked()`
  - `test_all_critical_model_changes_generate_activity_log_entry()`
