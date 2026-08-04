# 🛣️ Route Definitions: Phase 07 — حوكمة النظام والصلاحيات وسجلات التدقيق (System Governance & Auditing)

---

## 1. Overview & Group Configuration
- **اسم المرحلة**: `phase_07_system_governance`
- **المسار الرئيسي**: `/admin/governance`
- **مجموعة الـ Middleware**: `['web', 'auth', 'verified', 'role:Super Admin']` (حصرية بالإدارة العليا وحوكمة النظام)
- **الـ Controller المسؤول**: `App\Http\Controllers\Admin\GovernanceController`

---

## 2. Route Table Definition (All Governance Actions Covered)

| Method | URI | Route Name | Action Method | Required Permission | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/admin/governance` | `admin.governance.index` | `index` | `manage-governance` | عرض شاشة الأدوار والصلاحيات والإعدادات وسجلات التدقيق |
| `POST` | `/admin/governance/roles` | `admin.governance.roles.store` | `storeRole` | `manage-governance` | إنشاء دور صلاحيات جديد وتعيين مصفوفة الصلاحيات |
| `PUT` | `/admin/governance/roles/{role}` | `admin.governance.roles.update` | `updateRole` | `manage-governance` | تحديث مصفوفة الصلاحيات المسندة لدور معين |
| `DELETE`| `/admin/governance/roles/{role}` | `admin.governance.roles.destroy` | `destroyRole` | `manage-governance` | أرشفة وحذف دور صلاحيات غير مسند للمستخدمين |
| `POST` | `/admin/governance/settings` | `admin.governance.settings.update` | `updateSettings` | `manage-governance` | تحديث مفاتيح الإعدادات التشغيلية والـ Feature Flags |
| `GET` | `/admin/governance/audit-logs/{log}` | `admin.governance.audit-logs.show` | `showAuditLog` | `manage-governance` | جلب مقارنة القيم القديمة والجديدة لحركة تدقيق محددة |

---

## 3. Detailed API & AJAX Response Contracts

### 3.1 Response: Create New Role (`POST /admin/governance/roles` - `201 Created`):
```json
{
  "success": true,
  "message": "تم إنشاء دور الصلاحيات وتعيين المصفوفة بنجاح",
  "data": {
    "id": 4,
    "name": "مشرف ميداني أخصائي",
    "permissions_count": 12,
    "created_at": "2026-08-04T16:47:00Z"
  }
}
```

### 3.2 Response: Get Audit Log Diff (`GET /admin/governance/audit-logs/105` - `200 OK`):
```json
{
  "success": true,
  "data": {
    "id": 105,
    "user_name": "أحمد السالم",
    "action": "updated",
    "model_type": "Consultant",
    "model_id": 15,
    "old_values": {
      "employment_status": "active"
    },
    "new_values": {
      "employment_status": "suspended"
    },
    "ip_address": "192.168.1.50",
    "created_at": "2026-08-04T16:20:00Z"
  }
}
```

---

## 4. Security & Throttling
- **Throttling**: `throttle:30,1` لطلب أعلى مستويات الأمان الإداري.
- **CSRF Protection**: إجباري لكافة الطلبات.
