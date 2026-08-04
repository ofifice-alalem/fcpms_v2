# 🎨 Frontend Implementation: Phase 02 — إدارة الاستشاريين الميدانيين (Consultants Management)

---

## 1. Overview & UI Objectives
- **اسم المرحلة**: `phase_02_consultants`
- **الهدف البصري**: توفير واجهة إدارة واستكشاف استشاريين متكاملة ومصممة بـ **Spatial UI v3.0** تدعم جميع حالات الاستخدام (إنشاء استشاري جديد، تعديل بيانات قائمة، عرض التفاصيل، تغيير حالة التوظيف، والحذف المنهجي مع تأكيد).
- **الالتزام بالقواعد البصرية**:
  - مطابقة الأبعاد والـ Glassmorphic Gradients المعتمدة في `01_design_system_and_components/design_rules.md`.
  - الاعتماد على المكونات الموثقة في `01_design_system_and_components/components_catalog.html`.

---

## 2. Page Layout & Structure
- **مسار الملف في المشروع**: `resources/views/pages/consultants/index.blade.php`
- **هيكل الصفحة**:
  - **Header**: يحتوي على العنوان `دليل الاستشاريين الميدانيين` + زر الإضافة `+ إضافة استشاري جديد` (يفتح `consultantDrawer` بحالة الإنشاء).
  - **Consultant KPI Highlight Card**: بطاقة الاستشاري البارزة (الاسم: **أحمد السالم**، الرقم: **`#EMP-1024`**، الحالة: `status-pill completed` (نشط)، التفاصيل: `📞 051-234-5678` | `📍 طرابلس` | `📅 01-01-2024`، مع زري `عرض الملف` و `تعديل`).
  - **Filter & Search Bar**: حقل بحث حركي مع تبويبات التصفية السريعة (`الكل`, `نشط`, `قيد`, `موقوف`) وأزرار محاكاة التحميل (`Skeleton` و `Empty State`).

---

## 3. Detailed UI States & Component Specifications

### 3.1 حالة الإنشـاء الجديد (Create New Consultant)
- **عنصر المشغل**: زر `+ إضافة استشاري جديد` في الـ Header.
- **عنصر الواجهة**: اللوحة الجانبية `consultantDrawer` (`w-[480px]`).
- **عنوان اللوحة**: `إضافة استشاري ميداني جديد`.
- **الحقول والنماذج**:
  - `الاسم الكامل`: حقل `spatial-input h-14` مع التحقق الفوري إظهار `validation-msg success` عند الإدخال.
  - `البريد الإلكتروني`: حقل `spatial-input h-14` مع إظهار `validation-msg error` إذا كان البريد مسجلاً سابقاً.
  - `رقم الهاتف`: `spatial-input h-14`.
  - `التخصص الميداني`: `spatial-input h-14`.
  - `الموقع / المدينة`: قائمة اختيار منسدلة `spatial-dropdown-trigger` بارتفاع 56px (`h-14`) تحتوي على مدن النظام (طرابلس، بنغازي، مصراتة، سبها).
  - `حالة التوظيف الابتدائية`: قائمة اختيار `نشط` أو `قيد المعاينة`.
  - `زر الإجراء`: `spatial-button w-full h-14` بنص `حفظ الاستشاري الجديد`.

### 3.2 حالة التعديل (Edit Existing Consultant)
- **عنصر المشغل**: زر التعديل ✏️ في صف الجدول أو زر `تعديل` في بطاقة KPI.
- **عنصر الواجهة**: نفس اللوحة الجانبية `consultantDrawer` لكن بحالة التعديل (`isEdit = true`).
- **عنوان اللوحة**: `تعديل بيانات الاستشاري: [اسم الاستشاري]`.
- **سلوك البيانات**: يتم تعبئة الحقول مسبقاً بقيم الاستشاري الحالية، ويكون الرقم الوظيفي `#EMP-XXXX` مجهّزاً ومثبتاً للعرض فقط دون إمكانية تعديله برمجياً.
- **زر الإجراء**: `spatial-button w-full h-14` بنص `حفظ التغييرات`.

### 3.3 حالة عرض التفاصيل (View Details Profile)
- **عنصر المشغل**: زر الاستكشاف 👁️ في صف الجدول أو زر `عرض الملف`.
- **عنصر الواجهة**: النافذة المنبثقة الزجاجية `#consultantDetailModal` (`spatial-modal-card`).
- **محتوى النافذة**:
  - صورة وشعار الاستشاري + الاسم والرقم الوظيفي.
  - جدول مصغر بالتفاصيل (الموقع، تاريخ التعيين، التخصص، نسبة الإنجاز اليومي 94%، عدد الزيارات المنجزة).
  - شارة الحالة التشغيلية `status-pill completed`.
  - زر الإغلاق السريع `إغلاق`.

### 3.4 حالة تغيير الحالة التشغيلية (Change Status)
- **عنصر المشغل**: القائمة المنسدلة المباشرة داخل الجدول أو الـ Drawer.
- **خيارات الحالة**:
  - `نشط (Active)`: شارة خضراء `status-pill completed`.
  - `موقوف مؤقتاً (Suspended)`: شارة حمراء `status-pill overdue`.
  - `في إجازة (Vacation)`: شارة صفراء `status-pill pending`.
- **السلوك البصري**: يتغير لون الشارة فورا وتصدر إشارة Toast توضح تغيير الحالة.

### 3.5 حالة الحذف الأرشيفي (Delete Consultant)
- **عنصر المشغل**: زر الحذف 🗑️ في صف الجدول أو زر `حذف مجمع` في شريط `#bulkBarFloating`.
- **عنصر الواجهة**: نافذة تأكيد الحذف الزجاجية `#deleteModal` (`spatial-modal-card`).
- **محتوى النافذة**:
  - عنوان باللون الأحمر: `حذف سجل الاستشاري`.
  - نص تحذيري: `هل أنت متأكد من حذف سجل الاستشاري [اسم الاستشاري]؟ سيتم نقل السجل إلى الأرشيف ولن تتمكن من تكليفه بمهام جديدة.`.
  - الأزرار: زر `إلغاء` وزر `حذف نهائي` باللون الأحمر (`bg-destructive`).

---

## 4. Table Grid, Bulk Actions & Micro-Interactions

### 4.1 Data Grid Table (`SpatialTable`)
- يضم أعمدة: `تحديد (Check)`, `الرقم الوظيفي`, `الاسم والرمز`, `الموقع`, `نسبة الإنجاز (Progress Bar)`, `الحالة (Pill)`, و `أزرار الإجراءات (👁️ ✏️ 🗑️)`.

### 4.2 Floating Bulk Action Bar (`#bulkBarFloating`)
- يظهر عند تحديد استشاري أو أكثر عبر `row-checkbox`.
- يوفر أزرار: `طباعة تقارير الاستشاريين`, `تصدير البيانات`, و `تعطيل مجمع`.

---

## 5. JavaScript Logic Functions
```javascript
// فتح اللوحة الجانبية بحالة إضافة جديد
function openCreateConsultantDrawer() {
    resetConsultantForm();
    document.getElementById('drawerTitle').innerText = 'إضافة استشاري ميداني جديد';
    openDrawer('consultantDrawer');
}

// فتح اللوحة الجانبية بحالة التعديل وتعبئة البيانات
function openEditConsultantDrawer(consultantData) {
    fillConsultantForm(consultantData);
    document.getElementById('drawerTitle').innerText = `تعديل بيانات الاستشاري: ${consultantData.full_name}`;
    openDrawer('consultantDrawer');
}

// فتح نافذة تفاصيل الملف
function viewConsultantProfile(consultantId) {
    fetchConsultantDetails(consultantId);
    openModal('consultantDetailModal');
}

// فتح نافذة تأكيد الحذف
function confirmDeleteConsultant(consultantId, consultantName) {
    document.getElementById('deleteTargetName').innerText = consultantName;
    document.getElementById('confirmDeleteBtn').onclick = function() { executeDeleteConsultant(consultantId); };
    openModal('deleteModal');
}

// تنفيذ الحذف وإظهار إشعار Toast
function executeDeleteConsultant(consultantId) {
    // إرسال طلب AJAX للحذف
    closeModal('deleteModal');
    showToast('error', 'تم نقل سجل الاستشاري إلى الأرشيف بنجاح');
}
```
