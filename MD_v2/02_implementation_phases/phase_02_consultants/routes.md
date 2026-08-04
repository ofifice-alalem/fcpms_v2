# 🛣️ Route Definitions: Phase 02 — إدارة الاستشاريين الميدانيين (Consultants Management)

---

## 1. Overview & Group Configuration
- **اسم المرحلة**: `phase_02_consultants`
- **المسار الرئيسي**: `/admin/consultants`
- **مجموعة الـ Middleware**: `['web', 'auth', 'verified', 'permission:view-consultants']`
- **الـ Controller المسؤول**: `App\Http\Controllers\Admin\ConsultantController`

---

## 2. Route Table Definition (All CRUD Actions Covered)

| Method | URI | Route Name | Action Method | Required Permission | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/admin/consultants` | `admin.consultants.index` | `index` | `view-consultants` | عرض دليل الاستشاريين الميدانيين مع البحث والتصفية |
| `POST` | `/admin/consultants` | `admin.consultants.store` | `store` | `create-consultants` | إنشاء سجل حساب استشاري جديد مع حساب المستخدم الموازي |
| `GET` | `/admin/consultants/{consultant}` | `admin.consultants.show` | `show` | `view-consultants` | جلب ملف واستعراض تفاصيل استشاري محدد للـ Modal |
| `PUT` | `/admin/consultants/{consultant}` | `admin.consultants.update` | `update` | `edit-consultants` | تحديث البيانات الشخصية والتخصص والتواصل |
| `PATCH` | `/admin/consultants/{consultant}/status` | `admin.consultants.update-status` | `updateStatus` | `edit-consultants` | تحديث حالة التوظيف التشغيلية (`active`, `suspended`, `vacation`) |
| `DELETE`| `/admin/consultants/{consultant}` | `admin.consultants.destroy` | `destroy` | `delete-consultants` | أرشفة وحذف سجل الاستشاري آمنًا عبر SoftDelete |

---

## 3. Detailed API & AJAX Response Contracts

### 3.1 Response: Create New Consultant (`POST /admin/consultants` - `201 Created`):
```json
{
  "success": true,
  "message": "تم إنشاء حساب الاستشاري بنجاح",
  "data": {
    "id": 15,
    "user_id": 42,
    "employee_number": "EMP-1025",
    "full_name": "أحمد السالم",
    "phone": "051-234-5678",
    "email": "ahmed.salem@fcpms.ly",
    "city": "طرابلس",
    "employment_status": "active",
    "created_at": "2026-08-04T16:25:00Z"
  }
}
```

### 3.2 Response: Update Consultant Profile (`PUT /admin/consultants/15` - `200 OK`):
```json
{
  "success": true,
  "message": "تم تحديث بيانات الاستشاري بنجاح",
  "data": {
    "id": 15,
    "employee_number": "EMP-1025",
    "full_name": "أحمد السالم",
    "phone": "051-987-6543",
    "specialization": "هندسة مدنية وشبكات"
  }
}
```

### 3.3 Response: Change Status (`PATCH /admin/consultants/15/status` - `200 OK`):
```json
{
  "success": true,
  "message": "تم تغيير حالة الاستشاري إلى موقوف وتعليق جلسات الدخول",
  "data": {
    "id": 15,
    "employment_status": "suspended",
    "user_status": "inactive"
  }
}
```

### 3.4 Response: Soft Delete (`DELETE /admin/consultants/15` - `200 OK`):
```json
{
  "success": true,
  "message": "تم نقل سجل الاستشاري إلى الأرشيف بنجاح وحفظ البيانات التاريخية"
}
```

### 3.5 Error Response (`422 Unprocessable Entity`):
```json
{
  "success": false,
  "message": "بيانات غير صالحة",
  "errors": {
    "email": ["البريد الإلكتروني مستخدم سابقاً من قِبل حساب آخر"],
    "employee_number": ["الرقم الوظيفي مكرر"]
  }
}
```

---

## 4. Security & Throttling
- **Throttling**: `throttle:60,1` لحماية مسارات الإضافة والتعديل والحذف.
- **CSRF Protection**: إجباري لكافة طلبات `POST`, `PUT`, `PATCH`, `DELETE`.
