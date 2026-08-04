# 🎨 Frontend Implementation: Phase 05 — تنفيذ الزيارات اليومية الميدانية (Consultant Daily Visits)

---

## 1. Overview & Operational UX Objectives
- **اسم المرحلة**: `phase_05_daily_visits`
- **الهدف البصري**: توفير الشاشة الميدانية الخاصة بالاستشاري (Consultant Dashboard) المصممة بـ **Spatial UI v3.0** والتي تمكنه من فتح سجل اليوم العملي، اختيار الموقع من قائمة منسدلة (`Site Dropdown`) وتوثيقه في سجله اليومي، مع **الفصل المباشر الواضح بين المهام اليومية الدورية (Daily Tasks - التي تظهر فوراً) والمهام عند الحاجة (On-Demand Tasks - التي تُختار وتُفعل من قائمة منسدلة مخصصة)** مع دعم تعديل الإجابات الحية ورفع إثباتات الصور والمعاينة الشاملة.
- **الالتزام بالقواعد البصرية**:
  - الأبعاد والـ Glassmorphic Gradients المعتمدة في `01_design_system_and_components/design_rules.md`.
  - المكونات الموثقة في `01_design_system_and_components/components_catalog.html` (بما فيها `Interactive Image Upload Dropzone` و `Smart Adaptive Form`).

---

## 2. Page Layout & Structure
- **مسار الملف في المشروع**: `resources/views/pages/consultant/daily_visits.blade.php`
- **هيكل الشاشة الميدانية**:
  - **Daily Header Card**: بطاقة زجاجية تحتوي على تاريخ اليوم، اسم الاستشاري، زر `بدء اليوم العملي` ورقم اليوم الميداني.
  - **Site Selection & Log Bar**: شريط اختيار الموقع الميداني:
    - قائمة اختيار الموقع المنسدلة (`spatial-dropdown-trigger` بارتفاع 56px / h-14) تتيح البحث الفوري باكتشاف المواقع المتاحة.
    - زر `فتح زيارة موقع` (يضيف الموقع المختار إلى جدول اليوم الفعال).
  - **Active Daily Log Timeline**: قائمة زجاجية بالمواقع التي زارها أو يزورها الاستشاري اليوم مع حالة الزيارة (`قيد التنفيذ in_progress` أو `مكتملة completed`).

---

## 3. Detailed Operational UI States & Components

### 3.1 🟢 1. شاشة فتح سجل اليوم وتحديد الموقع (Daily Record & Site Selection)
- **عنصر اختيار الموقع**: قائمة اختيار منسدلة زجاجية فخمة (`spatial-dropdown-menu` بارتفاع 56px).
- **السلوك البصري**: بمجرد اختيار الموقع والنقر على `فتح زيارة` ينزلق كرت الزيارة الخاصة بالموقع فوراً إلى سجل اليوم الفعال، وتفتح شاشة التنفيذ الميدانية الخاصة بهذا الموقع.

### 3.2 ➕ 2. شاشة تنفيذ زيارة الموقع والفصل الحاسم بين أنواع المهام

#### أ. قسم المهام اليومية الدورية (Daily Tasks Section - التلقائية):
- **طريقة الظهور**: تظهر **فوراً ومباشرةً** كقائمة تفقدية بمجرد فتح زيارة الموقع دون أي تدخل من الاستشاري.
- **مكونات النماذج التفاعلية**:
  - الحقول النصية والرقمية (`spatial-input h-14`).
  - القوائم المنسدلة المخصصة.
  - **منطقة رفع الإثباتات الحية (`Interactive Image Upload Dropzone`)**: منطقة زجاجية بقطر 18px لرفع صور وفيديوهات الإثبات الميداني مع المعاينة التلقائية المصغرة.
  - **الحقول الشرطية الذكية (`Smart Adaptive Form`)**: إظهار الحقول المشروطة تلقائياً عند اختيار شرط محدد (مثال: إظهار حقل "رفع صورة العطل" فقط عند اختيار "غير مطابق").

#### ب. قسم المهام عند الحاجة (On-Demand Tasks Section - الاختيارية):
- **طريقة الظهور**: لا تظهر تلقائياً لتجنب الازدحام.
- **محتوى القسم**:
  - قائمة منسدلة مخصصة بعنوان: `⚡ اختر مهمة عند الحاجة لإضافتها لزيارة هذا الموقع`.
  - بمجرد اختيار مهمة من هذه القائمة، ينزلق كرت المهمة المختارة ديناميكياً ويُضاف إلى نموذج الزيارة الحالية ليقوم الاستشاري بتعبئته وإرفاق إثباتاته.

### 3.3 ✏️ 3. تعديل وحفظ الإجابات الحية (Edit & Auto-Save Responses)
- طالما أن زيارة الموقع بحالة `قيد التنفيذ in_progress` يمكن للاستشاري التعديل الحر على أي قيمة أو تغيير صورة الإثبات المرفوعة.
- شريط الإجراءات أسفل الصفحة يحتوي على: زر `حفظ مسودة` وزر `إنهاء واعتمد زيارة الموقع`.

### 3.4 👁️ 4. عرض تفاصيل الزيارة المكتملة (View Submitted Visit Modal)
- **عنصر المشغل**: زر الاستكشاف 👁️ في سجل اليوم.
- **عنصر الواجهة**: النافذة المنبثقة الزجاجية `#visitDetailModal` (`spatial-modal-card`).
- **المحتوى**: عرض ملخص الزيارة، المهام اليومية والمهام عند الحاجة المنجزة، المعرض الزجاجي لصور الإثباتات المرفوعة، والوقت المحدد للزيارة.

### 3.5 🗑️ 5. إلغاء زيارة معلقة (Cancel Active Visit Modal)
- **عنصر المشغل**: زر إلغاء الزيارة 🗑️ في كرت الزيارة الحالية.
- **عنصر الواجهة**: نافذة تأكيد الإلغاء الزجاجية `#cancelVisitModal`.

---

## 4. Micro-Interactions & Form Logic
```javascript
// فتح زيارة جديدة لموقع من الـ Dropdown
function openSiteVisitFromDropdown() {
    const siteId = document.getElementById('siteSelectDropdown').value;
    if (siteId) { startSiteVisit(siteId); }
}

// تفعيل مهمة عند الحاجة وتمرير حقولها ديناميكياً
function triggerOnDemandTask() {
    const taskId = document.getElementById('onDemandTasksDropdown').value;
    if (taskId) { appendOnDemandTaskToVisit(taskId); }
}

// التعديل والمحاكاة الحية للحقول الشرطية
function handleConditionalFieldToggle(parentId, selectedValue) {
    toggleSmartAdaptiveField(parentId, selectedValue);
}
```
