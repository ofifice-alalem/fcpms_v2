# 🎨 Frontend Implementation: Phase 03 — جداول الدوام والعطلات والإجازات (Work Schedules & Leaves)

---

## 1. Overview & UI Objectives
- **اسم المرحلة**: `phase_03_work_schedules_and_leaves`
- **الهدف البصري**: توفير واجهة إدارة مواقيت ودوام العمل بأسلوب **Spatial UI v3.0** تتيح استعراض قوالب الدوام، تحديد أيام العمل والراحة، إضافة العطلات الرسمية، وتسجيل وتصفية إجازات الاستشاريين، مع تغطية كامل الحالات الخمس (عرض الكل، إنشاء جديد، تعديل، حذف مع تأكيد، وعرض تفاصيل القالب).
- **الالتزام بالقواعد البصرية**:
  - الأبعاد والـ Glassmorphic Gradients المعتمدة في `01_design_system_and_components/design_rules.md`.
  - المكونات الموثقة في `01_design_system_and_components/components_catalog.html`.

---

## 2. Page Layout & Structure
- **مسار الملف في المشروع**: `resources/views/pages/work_schedules/index.blade.php`
- **هيكل الصفحة**:
  - **Header**: العنوان `إدارة جداول الدوام والإجازات` + زري العمليات: `+ قالب دوام جديد` و `+ إضافة عطلة/إجازة`.
  - **Tab Navigation Bar**: تبويبات تنقل علوية بسلسس زجاجي:
    1. `قوالب الدوام (Schedule Templates)` (Active Tab)
    2. `العطلات الرسمية (Official Holidays)`
    3. `إجازات الاستشاريين (Consultant Leaves)`

---

## 3. Detailed UI States & Component Specifications

### 3.1 حالة عرض الكل (Index & Grid View)
- **قسم قوالب الدوام**: كروت زجاجية (`spatial-card`) لكل قالب تحتوي على:
  - اسم القالب (مثال: `دوام كامل - 8 ساعات`) + شارة `القالب الافتراضي Default`.
  - شريط الأيام الأسبوعية (الأحد إلى السبت) بمربعات تلوين (أخضر للأيام الفعالة، رمادي لأيام الراحة).
  - عدد الاستشاريين المسندين للقالب.
  - أزرار التحكم: زر `تفاصيل القالب 👁️`, `تعديل ✏️`, و `حذف 🗑️`.
- **قسم العطلات والإجازات**: جدول البيانات `SpatialTable` يعرض اسم المناسبة، تاريخ البدء والانتهاء، والسبب.

### 3.2 حالة الإنشـاء الجديد (Create Actions)
- **إنشاء قالب دوام جديد (`#templateDrawer`)**:
  - اسم القالب (`spatial-input h-14`).
  - وصف القالب.
  - ميزة تحديد القالب كافتراضي (`spatial-switch`).
  - مصفوفة اختيار أيام العمل (مربعات اختيار مخصصة `custom-checkbox` للأيام من الأحد للسبت).
  - زر `حفظ القالب`.
- **تسجيل إجازة / عطلة (`#leaveModal`)**:
  - تحديد الاستشاري عبر قائمة اختيار منسدلة (`spatial-dropdown-trigger` بارتفاع 56px / h-14).
  - حقول تاريخ البدء والانتهاء (Date Pickers).
  - زر `تسجيل الإجازة`.

### 3.3 حالة التعديل (Edit Template)
- **عنصر المشغل**: زر التعديل ✏️ في كرت القالب.
- **عنصر الواجهة**: نفس اللوحة الجانبية `#templateDrawer` بحالة التعديل وتعبئة الأيام المحددة سابقاً.
- **زر الإجراء**: `spatial-button` بنص `حفظ التغييرات`.

### 3.4 حالة عرض التفاصيل (View Template Details)
- **عنصر المشغل**: زر الاستكشاف 👁️ في كرت القالب.
- **عنصر الواجهة**: النافذة المنبثقة `#templateDetailModal` (`spatial-modal-card`).
- **المحتوى**: تفاصيل أيام وساعات العمل الأسبوعية وقائمة الاستشاريين المسندين لهذا القالب.

### 3.5 حالة الحذف الأرشيفي والتأكيد (Delete Template/Holiday)
- **عنصر المشغل**: زر الحذف 🗑️ في الكرت أو الجدول.
- **عنصر الواجهة**: نافذة التأكيد الزجاجية `#deleteModal`.
- **السلوك التحذيري**: إظهار تنبيه يمنع حذف أي قالب مسند لاستشاريين نشطين.

---

## 4. Micro-Interactions & Form Validation Rules
1. **Interactive Day Selector**: النقر على اسم اليوم يتغير لونه فورا بين الأخضر (يوم عمل) والرمادي (عطلة أسبوعية).
2. **Tab Switching**: تنقل انسيابي بدون إعادة تحميل الصفحة بين القوالب والعطلات والإجازات.

---

## 5. JavaScript Logic Functions
```javascript
function switchScheduleTab(tabName) { toggleTabContent(tabName); }
function openCreateTemplateDrawer() { openDrawer('templateDrawer'); }
function openEditTemplateDrawer(templateData) { fillTemplateForm(templateData); openDrawer('templateDrawer'); }
function confirmDeleteTemplate(templateId) { openModal('deleteModal'); }
```
