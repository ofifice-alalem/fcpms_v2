# 🛣️ Route Definitions: Phase 03 — جداول الدوام والعطلات والإجازات (Work Schedules & Leaves)

---

## 1. Overview & Group Configuration
- **اسم المرحلة**: `phase_03_work_schedules_and_leaves`
- **المسار الرئيسي**: `/admin/work-schedules`
- **مجموعة الـ Middleware**: `['web', 'auth', 'verified', 'permission:view-schedules']`
- **الـ Controller المسؤول**: `App\Http\Controllers\Admin\WorkScheduleController`

---

## 2. Route Table Definition (All Actions & Entities Covered)

| Method | URI | Route Name | Action Method | Required Permission | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/admin/work-schedules` | `admin.schedules.index` | `index` | `view-schedules` | عرض الشاشة الرئيسية لقوالب الدوام والعطلات والإجازات |
| `POST` | `/admin/work-schedules/templates` | `admin.schedules.templates.store` | `storeTemplate` | `create-schedules` | إنشاء قالب دوام جديد وتحديد أيام العمل |
| `PUT` | `/admin/work-schedules/templates/{template}` | `admin.schedules.templates.update` | `updateTemplate` | `edit-schedules` | تحديث بيانات القالب وأيام العمل المخصصة |
| `DELETE`| `/admin/work-schedules/templates/{template}` | `admin.schedules.templates.destroy` | `destroyTemplate` | `delete-schedules` | أرشفة وحذف قالب دوام غير مسند |
| `POST` | `/admin/work-schedules/holidays` | `admin.schedules.holidays.store` | `storeHoliday` | `create-schedules` | تسجيل عطلة رسمية عامة في النظام |
| `POST` | `/admin/work-schedules/leaves` | `admin.schedules.leaves.store` | `storeLeave` | `create-schedules` | تسجيل إجازة خاصة باستشاري ميداني |

---

## 3. Detailed API & AJAX Response Contracts

### 3.1 Response: Create Schedule Template (`POST /admin/work-schedules/templates` - `201 Created`):
```json
{
  "success": true,
  "message": "تم إنشاء قالب الدوام وتعيين أيام العمل بنجاح",
  "data": {
    "id": 5,
    "name": "دوام كامل - 8 ساعات",
    "is_default": true,
    "working_days": ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday"],
    "created_at": "2026-08-04T16:33:00Z"
  }
}
```

### 3.2 Response: Record Official Holiday (`POST /admin/work-schedules/holidays` - `201 Created`):
```json
{
  "success": true,
  "message": "تم إضافة العطلة الرسمية بنجاح",
  "data": {
    "id": 12,
    "name": "عيد الفطر المبارك",
    "holiday_date": "2026-03-20"
  }
}
```

### 3.3 Response: Record Consultant Leave (`POST /admin/work-schedules/leaves` - `201 Created`):
```json
{
  "success": true,
  "message": "تم تسجيل الإجازة للاستشاري وتحديث حالته التشغيلية إلى 'في إجازة'",
  "data": {
    "id": 8,
    "consultant_id": 15,
    "start_date": "2026-08-10",
    "end_date": "2026-08-15",
    "status": "vacation"
  }
}
```

### 3.4 Response: Soft Delete Template (`DELETE /admin/work-schedules/templates/5` - `200 OK`):
```json
{
  "success": true,
  "message": "تم أرشفة قالب الدوام بنجاح"
}
```

---

## 4. Security & Throttling
- **Throttling**: `throttle:60,1` على مسارات الإضافة والتحديث.
- **CSRF Protection**: إجباري لكافة الطلبات.
