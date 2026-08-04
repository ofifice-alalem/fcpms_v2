# 🎨 Frontend Implementation: Phase 06 — التقارير والتحليلات والأداء (Reports & Analytics)

---

## 1. Overview & UI Objectives
- **اسم المرحلة**: `phase_06_reports_and_analytics`
- **الهدف البصري**: توفير واجهات التقارير والتحليلات المصممة بـ **Spatial UI v3.0** لدعم كلا الرؤيتين: **واجهة تقارير الاستشاري الشخصية (Consultant Personal Portal)** و**لوحة تحليلات الإدارة المتقدمة (HR Enterprise Dashboard)**، مع التزام تام بالخمس خصائص الأساسية (عرض الكروت الإحصائية، الفلترة المتقدمة، التعديل على خيارات العرض والتجميع، المعاينة الحية لتفاصيل السجل، وتصدير الملفات).
- **الالتزام بالقواعد البصرية**:
  - الأبعاد والـ Glassmorphic Gradients المعتمدة في `01_design_system_and_components/design_rules.md`.
  - المكونات الموثقة في `01_design_system_and_components/components_catalog.html` (بما فيها `KPI Highlight Cards` و `SpatialTable`).

---

## 2. Page Layouts & Dual Views

### View A: واجهة تقارير الاستشاري الشخصية (`resources/views/pages/consultant/reports.blade.php`)
- **Header**: العنوان `تقاريري وسجلاتي الميدانية` + زر `تصدير تقريري الشخصي (Excel/PDF)`.
- **Personal KPI Highlight Cards**:
  - `مؤشر إنجازي الشهري`: نسبة مئوية حية (مثال: **94%**) مع شريط التقدم الزجاجي `progress-bar`.
  - `إجمالي زياراتي الميدانية`: عدد المواقع التي تم زيارتها وتنفيذ مهامها.
  - `أيام الدوام المسجلة`: عدد الأيام المكتملة في سجل الحضور.
- **Filter Bar**: منتقي نطاق التواريخ (Date Range Picker) + فلترة بأسماء المدن.
- **Personal Log Data Grid (`SpatialTable`)**: جدول يعرض: `تاريخ السجل`, `الموقع المزار`, `المدينة`, `وقت الزيارة`, `حالة التنفيذ (مكتملة)`, وزر `استكشاف السجل 👁️`.

### View B: لوحة تقارير الإدارة والـ HR (`resources/views/pages/admin/reports.blade.php`)
- **Header**: العنوان `لوحة التحليلات وتقارير الأداء الميداني` + زر `تصدير التقرير التنفيذي الشامل`.
- **Enterprise KPI Cards Grid**:
  - إجمالي الزيارات الميدانية على مستوى كافة المدن.
  - عدد الاستشاريين الفعالين في الميدان.
  - نسبة الالتزام العامة بالنظام ومعدل المهام المكتملة.
- **Advanced Multi-Filter Bar**:
  - قائمة اختيار الاستشاريين (`spatial-dropdown-trigger` 56px).
  - قائمة اختيار المواقع والمدن.
  - منتقي التواريخ.
- **Enterprise Data Grid Table (`SpatialTable`)**: جدول تجميعي شامل لكل تحركات الميدان مع خيارات الفرط والتصدير المباشر.

---

## 3. Detailed UI States & Component Specifications

### 3.1 🟢 1. حالة عرض المؤشرات والجداول (Index Views)
- **بطاقات المؤشرات KPIs**: مصممة بزوايا 30px مع توهج عاكس وتدرج زجاجي شفاف ورموز SVG متجهة ناعمة.

### 3.2 ➕ 2. خاصية تخصيص وتجول التقرير (Generate Custom Report)
- اختيار نطاق التواريخ والمدن والمواقع لتوليد تجميع خاص فوري في الجدول.

### 3.3 ✏️ 3. تعديل خيارات التجميع (Modify Aggregation Criteria)
- إمكانية التبديل المباشر بين العرض التجميعي اليومي، الأسبوعي، أو الشهري.

### 3.4 👁️ 4. عرض تفاصيل زيارة/سجل محدد (View Log Detail Modal)
- **عنصر المشغل**: زر الاستكشاف 👁️ في أي صف من جدول التقارير.
- **عنصر الواجهة**: النافذة المنبثقة الزجاجية `#reportDetailModal` (`spatial-modal-card`).
- **المحتوى**: استعراض كافة الإجابات المسجلة في تلك الزيارة مع صور وفيديوهات الإثبات الميداني.

### 3.5 📄 5. تصدير التقارير (Export Action)
- زر التصدير يتيح الاختيار السريع بين صيغة `Excel` أو `PDF` مع تنبيه Toast عند اكتمال التحميل.

---

## 4. JavaScript Logic Functions
```javascript
// فلترة تقارير الاستشاري حسب التواريخ
function filterConsultantPersonalReports() {
    const dateFrom = document.getElementById('dateFromInput').value;
    const dateTo = document.getElementById('dateToInput').value;
    fetchPersonalReportsData(dateFrom, dateTo);
}

// فتح نافذة تفاصيل سجل محدد في التقرير
function viewReportLogDetails(visitId) {
    fetchVisitDetailsForReport(visitId);
    openModal('reportDetailModal');
}

// تصدير التقرير بصيغة Excel أو PDF
function exportReport(format) {
    showToast('info', `جاري تصدير التقرير بصيغة ${format.toUpperCase()}...`);
    window.location.href = `/reports/export?format=${format}`;
}
```
