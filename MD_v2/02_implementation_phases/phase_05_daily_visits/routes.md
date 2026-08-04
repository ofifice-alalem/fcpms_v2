# 🛣️ Route Definitions: Phase 05 — تنفيذ الزيارات اليومية الميدانية (Consultant Daily Visits)

---

## 1. Overview & Group Configuration
- **اسم المرحلة**: `phase_05_daily_visits`
- **المسار الرئيسي**: `/consultant/daily-visits`
- **مجموعة الـ Middleware**: `['web', 'auth', 'permission:execute-daily-visits']`
- **الـ Controller المسؤول**: `App\Http\Controllers\Consultant\DailyVisitController`

---

## 2. Route Table Definition (All Operations Covered)

| Method | URI | Route Name | Action Method | Required Permission | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/consultant/daily-visits` | `consultant.visits.index` | `index` | `execute-daily-visits` | عرض الشاشة الميدانية وسجل اليوم واختيار المواقع |
| `POST` | `/consultant/daily-visits/start-day` | `consultant.visits.start-day` | `startDay` | `execute-daily-visits` | فتح سجل اليوم العملي الجاري للاستشاري |
| `POST` | `/consultant/site-visits` | `consultant.site-visits.store` | `storeVisit` | `execute-daily-visits` | فتح زيارة موقع جديدة محددة من الـ Dropdown |
| `POST` | `/consultant/site-visits/{visit}/trigger-on-demand` | `consultant.site-visits.trigger-ondemand` | `triggerOnDemand` | `execute-daily-visits` | تفعيل وإضافة مهمة عند الحاجة من الـ Dropdown |
| `POST` | `/consultant/site-visits/{visit}/save-responses` | `consultant.site-visits.save-responses` | `saveResponses` | `execute-daily-visits` | حفظ وتعديل إجابات المهام ومرفقات صور الإثبات الحية |
| `GET` | `/consultant/site-visits/{visit}` | `consultant.site-visits.show` | `show` | `execute-daily-visits` | جلب تفاصيل وإجابات ومرفقات زيارة محددة |
| `DELETE`| `/consultant/site-visits/{visit}` | `consultant.site-visits.destroy` | `destroy` | `execute-daily-visits` | إلغاء زيارة معلقة قيد التنفيذ |

---

## 3. Detailed API & AJAX Response Contracts

### 3.1 Response: Open Site Visit & Auto-Load Daily Tasks (`POST /consultant/site-visits` - `201 Created`):
```json
{
  "success": true,
  "message": "تم فتح زيارة الموقع وجلب المهام اليومية الدورية بنجاح",
  "data": {
    "site_visit_id": 88,
    "site_name": "موقع طرابلس المركزي - البرج أ",
    "status": "in_progress",
    "daily_tasks": [
      {
        "task_id": 12,
        "title": "فحص أنظمة السلامة ومطفآت الحريق",
        "task_type": "daily",
        "components": [...]
      }
    ],
    "available_on_demand_tasks": [
      {"id": 45, "title": "بلاغ عطل طارئ في المولد الكهربائي"}
    ]
  }
}
```

### 3.2 Response: Trigger On-Demand Task (`POST /consultant/site-visits/88/trigger-on-demand` - `200 OK`):
```json
{
  "success": true,
  "message": "تم تفعيل وتضمين المهمة عند الحاجة لنموذج الزيارة الحالي",
  "data": {
    "task_id": 45,
    "title": "بلاغ عطل طارئ في المولد الكهربائي",
    "components": [...]
  }
}
```

### 3.3 Response: Save & Edit Responses with Photo Attachments (`POST /consultant/site-visits/88/save-responses` - `200 OK`):
```json
{
  "success": true,
  "message": "تم حفظ وتحديث الإجابات وإثباتات الصور بنجاح",
  "data": {
    "site_visit_id": 88,
    "submitted_values_count": 6,
    "attachments_count": 2
  }
}
```

---

## 4. Security & Throttling
- **Throttling**: `throttle:120,1` لإتاحة الحفظ السريع والتعديل المستمر أثناء التواجد في الميدان.
- **CSRF Protection**: إجباري لكافة الطلبات.
