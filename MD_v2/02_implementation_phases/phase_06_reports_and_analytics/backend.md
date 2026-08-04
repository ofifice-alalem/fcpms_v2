# ⚙️ Backend Implementation: Phase 06 — التقارير والتحليلات والأداء (Reports & Analytics)

---

## 1. Overview & Dual-Perspective Scope
- **اسم المرحلة**: `phase_06_reports_and_analytics`
- **الهدف التنفيذي**: بناء المنطق البرمجي والمحركات التجميعية لشاشة التقارير والتحليلات للطرفين: **لوحة تقارير وسجلات الاستشاري الشخصية (Consultant Personal Reports)** و**لوحة التقارير التنفيذية للـ HR والإدارة (HR Enterprise Analytics)**، مع تغطية الخصائص الخمس كاملة (عرض المؤشرات، فلترة واستخراج النظرة العامة، تعديل خيارات التقرير والتجميع، عرض التفاصيل للزيارة، وتصدير التقارير بصيغ PDF/Excel).
- **التزام المعمارية**:
  - الالتزام بنمط `Repository-Service Pattern` وحالة `Strict Mode` الموثقة في `00_architecture_and_rules/architecture.md`.
  - الامتثال لقواعد البيانات الحاكمة والتأكد من تحسين الاستعلامات المالية والميدانية ذات الأداء العالي (Optimized Aggregations & Server-side Caching).
  - تطبيق قواعد الأعمال: `BR-036` إلى `BR-040` (حساب نسب الإنجاز، أوقات الزيارات، وتأكيد حماية بيانات الاستشاري).

---

## 2. Dual-Perspective Report Engines

### 2.1 👨‍💼 منظور الاستشاري الميداني (Consultant Personal Reports)
- **الخصائص والبيانات المستهدفة**:
  - **سجل الحضور والمناودات الشخصي (Attendance Log)**: عرض أيام الدوام، وقت تسجيل بدء اليوم وإنحائه، والغياب/الإجازات.
  - **أرشيف زيارات المواقع (Visited Sites Log)**: قائمة كاملة بالمواقع التي زارها الاستشاري مع توزيعها حسب المدن والمواعيد.
  - **مؤشر الإنجاز الشخصي (Personal KPI & Completion Rate)**: نسبة حية تحسب إجمالي المهام المسندة مقابل المنجزة (% الإنجاز التراكمي والشهري).
  - **تصدير كشف الحركات الميدانية**: إمكانية تصدير التقرير الشخصي بصيغ Excel أو PDF.

### 2.2 🏢 منظور الموارد البشرية والإدارة (HR & Enterprise Analytics)
- **الخصائص والبيانات المستهدفة**:
  - **مؤشرات الأداء العامة (Global System KPIs)**: إجمالي الزيارات الميدانية، عدد الاستشاريين النشطين، نسبة الالتزام الكلية بالنظام.
  - **التقارير المقارنة والتصفية المتقدمة**: التصفية حسب نطاق التواريخ، المدينة، الاستشاري، أو حالة المهمة.
  - **جدول التجميع الشامل**: استعراض كافة السجلات وإمكانية تصدير التقرير التنفيذي المكتمل.

---

## 3. Detailed Operations & Aggregation Queries

### 3.1 🟢 1. استرجاع تقارير الاستشاري الشخصية (`GET /consultant/reports`)
- **الاستعلام المحسن (Optimized Query)**:
  ```php
  public function getConsultantPersonalMetrics(int $consultantId, array $filters): array
  {
      $query = DailyRecord::where('consultant_id', $consultantId)
          ->with(['siteVisits.site', 'siteVisits.taskResponses']);
          
      if (!empty($filters['date_from'])) {
          $query->whereDate('record_date', '>=', $filters['date_from']);
      }
      if (!empty($filters['date_to'])) {
          $query->whereDate('record_date', '<=', $filters['date_to']);
      }

      $records = $query->get();
      
      // Calculate Personal Completion Rate
      $totalVisits = $records->pluck('siteVisits')->flatten()->count();
      $completedVisits = $records->pluck('siteVisits')->flatten()->where('status', 'completed')->count();
      $completionRate = $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100, 1) : 0;

      return [
          'total_days_worked' => $records->count(),
          'total_site_visits' => $totalVisits,
          'completion_rate'   => $completionRate,
          'records_history'   => $records,
      ];
  }
  ```

### 3.2 🟢 2. استرجاع تقارير الـ HR العامة (`GET /admin/reports`)
- **المنطق البرمجي**: تجميع إحصائيات النظام الشاملة على مستوى كافة المدن والمواقع والاستشاريين مع تفعيل الـ Caching المؤقت للطلبات الضخمة.

### 3.3 👁️ 3. عرض تفاصيل التقرير لعنصر محدد (`GET /reports/visit-detail/{visit}`)
- **المنطق البرمجي**: استرجاع كشف الإجابات الميدانية المنجزة لزيارة محددة مع صور ومرفقات الإثبات.

### 3.4 📄 4. تصدير التقارير (Export Action `GET /reports/export`)
- **المنطق البرمجي**: توليد ملفات Excel عبر `Maatwebsite\Excel` أو ملفات PDF عبر `DomPDF` ببيانات التقرير المفلترة.

---

## 4. Repository & Service Layer
- **الواجهات والتطبيقات**:
  - `ReportRepositoryInterface` / `ReportRepository`
  - `ReportAnalyticsService`
- **طلب التحقق**: `GenerateReportRequest`, `ExportReportRequest`.

---

## 5. Testing Standards
- **ملف الاختبار**: `tests/Feature/Analytics/ReportGenerationTest.php`
- **الحالات المغطاة**:
  - `test_consultant_can_only_view_their_own_personal_reports()`
  - `test_hr_can_filter_and_export_enterprise_performance_reports()`
