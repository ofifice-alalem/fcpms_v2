# 🛣️ Route Definitions: Phase 04 — منشئ المهام الديناميكي والتكليفات (Task Builder & Assignments)

---

## 1. Overview & Group Configuration
- **اسم المرحلة**: `phase_04_task_builder`
- **المسار الرئيسي**: `/admin/task-builder`
- **مجموعة الـ Middleware**: `['web', 'auth', 'verified', 'permission:view-tasks']`
- **الـ Controller المسؤول**: `App\Http\Controllers\Admin\TaskBuilderController`

---

## 2. Route Table Definition (All Actions & Operations Covered)

| Method | URI | Route Name | Action Method | Required Permission | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/admin/task-builder` | `admin.tasks.index` | `index` | `view-tasks` | عرض دليل المهام والمكونات الديناميكية مع الفلترة |
| `POST` | `/admin/task-builder` | `admin.tasks.store` | `store` | `create-tasks` | إنشاء مهمة جديدة وبناء مكوناتها وحقولها الشرطية وتكليفاتها |
| `GET` | `/admin/task-builder/{task}` | `admin.tasks.show` | `show` | `view-tasks` | جلب تفاصيل المهمة ومكوناتها للمعالجة والمعاينة الحية |
| `PUT` | `/admin/task-builder/{task}` | `admin.tasks.update` | `update` | `edit-tasks` | تحديث بنية المهمة والحقول والتكليفات الميدانية |
| `PATCH` | `/admin/task-builder/{task}/toggle-active` | `admin.tasks.toggle-active` | `toggleActive` | `edit-tasks` | تفعيل/تجميد المهمة التشغيلية (`is_active`) |
| `DELETE`| `/admin/task-builder/{task}` | `admin.tasks.destroy` | `destroy` | `delete-tasks` | أرشفة وحذف سجل المهمة آمنًا عبر SoftDelete |

---

## 3. Detailed API & AJAX Response Contracts

### 3.1 Response: Create New Dynamic Task (`POST /admin/task-builder` - `201 Created`):
```json
{
  "success": true,
  "message": "تم بناء المهمة وتعيين المكونات الحركية والتكليفات بنجاح",
  "data": {
    "id": 12,
    "title": "فحص أنظمة السلامة ومطفآت الحريق",
    "task_type": "daily",
    "is_active": true,
    "components_count": 4,
    "assigned_sites_count": 3,
    "created_at": "2026-08-04T16:36:00Z"
  }
}
```

### 3.2 Response: Get Single Task Preview Data (`GET /admin/task-builder/12` - `200 OK`):
```json
{
  "success": true,
  "data": {
    "id": 12,
    "title": "فحص أنظمة السلامة ومطفآت الحريق",
    "task_type": "daily",
    "components": [
      {
        "id": 101,
        "label": "هل طفايات الحريق صالحة للاستخدام؟",
        "component_type": "select",
        "is_required": true,
        "options": [
          {"id": 1, "label": "نعم مطابقة", "value": "yes"},
          {"id": 2, "label": "لا تحتاچ صيانة", "value": "no"}
        ]
      },
      {
        "id": 102,
        "label": "قم برفع صورة إثبات طفاية الحريق",
        "component_type": "image_upload",
        "is_required": true,
        "conditional_parent_id": 101,
        "conditional_value": "no"
      }
    ]
  }
}
```

### 3.3 Response: Soft Delete Task (`DELETE /admin/task-builder/12` - `200 OK`):
```json
{
  "success": true,
  "message": "تم أرشفة المهمة بنجاح مع الاحتفاظ بسجلات الإجابات الميدانية التاريخية"
}
```

---

## 4. Security & Throttling
- **Throttling**: `throttle:60,1` لحماية مسارات البناء والتعديل.
- **CSRF Protection**: إجباري لكافة الطلبات.
