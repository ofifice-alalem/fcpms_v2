# 🛣️ Route Definitions: Phase 01 — إدارة المواقع الميدانية (Sites & Locations)

---

## 1. Overview & Group Configuration
- **اسم المرحلة**: `phase_01_sites_and_locations`
- **المسار الرئيسي**: `/admin/sites`
- **مجموعة الـ Middleware**: `['web', 'auth', 'verified', 'permission:view-sites']`
- **الـ Controller المسؤول**: `App\Http\Controllers\Admin\SiteController`

---

## 2. Route Table Definition (All Actions Covered)

| Method | URI | Route Name | Action Method | Required Permission | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/admin/sites` | `admin.sites.index` | `index` | `view-sites` | عرض جدول المواقع الميدانية مع الفلترة والبحث |
| `POST` | `/admin/sites` | `admin.sites.store` | `store` | `create-sites` | إنشاء سجل موقع ميداني جديد برمز فريد |
| `GET` | `/admin/sites/{site}` | `admin.sites.show` | `show` | `view-sites` | جلب تفاصيل موقع محدد للـ Modal الزجاجي |
| `PUT` | `/admin/sites/{site}` | `admin.sites.update` | `update` | `edit-sites` | تحديث بيانات موقع قائمة والمدينة والعنوان |
| `PATCH` | `/admin/sites/{site}/toggle-status` | `admin.sites.toggle-status` | `toggleStatus` | `edit-sites` | تبديل حالة الموقع التشغيلية (`active`/`inactive`) |
| `DELETE`| `/admin/sites/{site}` | `admin.sites.destroy` | `destroy` | `delete-sites` | أرشفة وحذف سجل الموقع آمنًا عبر SoftDelete |

---

## 3. Detailed API & AJAX Response Contracts

### 3.1 Response: Create New Site (`POST /admin/sites` - `201 Created`):
```json
{
  "success": true,
  "message": "تم إضافة الموقع الميداني بنجاح",
  "data": {
    "id": 101,
    "code": "TR-S-01",
    "name": "موقع طرابلس المركزي - البرج أ",
    "city": "طرابلس",
    "address": "شارع النصر، طرابلس",
    "status": "active",
    "created_at": "2026-08-04T16:30:00Z"
  }
}
```

### 3.2 Response: Update Site (`PUT /admin/sites/101` - `200 OK`):
```json
{
  "success": true,
  "message": "تم تحديث بيانات الموقع بنجاح",
  "data": {
    "id": 101,
    "code": "TR-S-01",
    "name": "موقع طرابلس المركزي - البرج أ (المُحدث)",
    "city": "طرابلس",
    "address": "شارع النصر، المجمع الشمالي"
  }
}
```

### 3.3 Response: Toggle Status (`PATCH /admin/sites/101/toggle-status` - `200 OK`):
```json
{
  "success": true,
  "message": "تم تغيير حالة الموقع إلى غير نشط وتجميد إسناد المهام الجديدة له",
  "data": {
    "id": 101,
    "status": "inactive"
  }
}
```

### 3.4 Response: Soft Delete Site (`DELETE /admin/sites/101` - `200 OK`):
```json
{
  "success": true,
  "message": "تم نقل سجل الموقع إلى الأرشيف وحفظ بيانات الزيارات التاريخية"
}
```

### 3.5 Error Response (`422 Unprocessable Entity`):
```json
{
  "success": false,
  "message": "بيانات الموقع غير صالحة",
  "errors": {
    "code": ["رمز الموقع مستخدم سابقاً، يرجى أدخال رمز فريد"]
  }
}
```

---

## 4. Security & Throttling
- **Throttling**: `throttle:60,1` لحماية مسارات الإضافة والتعديل والحذف.
- **CSRF Protection**: إجباري لكافة طلبات `POST`, `PUT`, `PATCH`, `DELETE`.
