# 🛣️ Route Definitions: Phase 06 — التقارير والتحليلات والأداء (Reports & Analytics)

---

## 1. Overview & Group Configuration
- **اسم المرحلة**: `phase_06_reports_and_analytics`
- **المسارات الرئيسية**:
  - مسارات الاستشاري: `/consultant/reports`
  - مسارات الإدارة: `/admin/reports`
- **مجموعة الـ Middleware**: `['web', 'auth']`

---

## 2. Route Table Definition (Dual Perspectives Covered)

| Method | URI | Route Name | Action Method | Required Permission | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/consultant/reports` | `consultant.reports.index` | `consultantIndex` | `view-personal-reports` | عرض شاشة تقارير وسجلات الاستشاري الشخصية |
| `GET` | `/admin/reports` | `admin.reports.index` | `adminIndex` | `view-enterprise-reports` | عرض لوحة تقارير الإدارة والمؤشرات التنفيذية |
| `GET` | `/reports/visit-detail/{visit}` | `reports.visit-detail` | `showVisitDetail` | `view-personal-reports` | جلب تفاصيل وإجابات زيارة محددة للـ Modal |
| `GET` | `/reports/export` | `reports.export` | `exportReport` | `export-reports` | تصدير بيانات التقرير بصيغ Excel أو PDF |

---

## 3. Detailed API & AJAX Response Contracts

### 3.1 Response: Consultant Personal Metrics (`GET /consultant/reports` - `200 OK`):
```json
{
  "success": true,
  "data": {
    "consultant_name": "أحمد السالم",
    "employee_number": "EMP-1024",
    "completion_rate": 94.2,
    "total_days_worked": 22,
    "total_site_visits": 45,
    "visits_by_city": {
      "طرابلس": 30,
      "بنغازي": 15
    },
    "recent_visits_log": [...]
  }
}
```

### 3.2 Response: Enterprise HR Analytics (`GET /admin/reports` - `200 OK`):
```json
{
  "success": true,
  "data": {
    "system_total_visits": 1280,
    "active_consultants_count": 34,
    "system_completion_rate": 91.5,
    "consultant_rankings": [...]
  }
}
```

---

## 4. Security & Throttling
- **Throttling**: `throttle:60,1` لحماية عمليات الاستعلام والفلترة والتصدير.
- **Data Isolation**: التأكد من أن الاستشاري يستطيع استرجاع تقاريره وسجلاته الشخصية فقط، بينما تمتلك الإدارة صلاحية الاستعلام الشامل.
