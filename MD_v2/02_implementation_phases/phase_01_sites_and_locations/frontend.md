# 🎨 Frontend Implementation: Phase 01 — إدارة المواقع الميدانية (Sites & Locations)

---

## 1. Overview & UI Objectives
- **اسم المرحلة**: `phase_01_sites_and_locations`
- **الهدف البصري**: توفير واجهة إدارة مواقع فائقة الدقة بأسلوب **Spatial UI v3.0** تتيح تصفية المواقع حسب المدن، البحث الحي، إضافة موقع جديد عبر Drawer، تعديل موقع قائم، تغيير حالة التشغيل، وعرض التفاصيل وحذف السجلات آمنًا مع نافذة التأكيد.
- **الالتزام بالقواعد البصرية**:
  - مطابقة الأبعاد والـ Glassmorphic Gradients المعتمدة في `01_design_system_and_components/design_rules.md`.
  - الاعتماد على المكونات التفاعلية الموثقة في `01_design_system_and_components/components_catalog.html`.

---

## 2. Page Layout & Structure
- **مسار الملف في المشروع**: `resources/views/pages/sites/index.blade.php`
- **مكونات الهيكل الأساسي**:
  - **Header**: عنوان الصفحة `إدارة المواقع الميدانية` + زر الإضافة `+ إضافة موقع جديد` (يفتح `siteFormDrawer` بحالة الإضافة).
  - **Filter & Search Bar**: حقل البحث الحي `spatial-input` + قائمة خيارات المدن (`spatial-dropdown-trigger` بارتفاع 56px / h-14) + أزرار محاكاة الـ Skeleton والـ Empty State.

---

## 3. Detailed UI States & Component Specifications

### 3.1 حالة الإنشـاء الجديد (Create New Site)
- **عنصر المشغل**: زر `+ إضافة موقع جديد` في الـ Header.
- **عنصر الواجهة**: اللوحة الجانبية `siteFormDrawer` (`w-[480px]`).
- **عنوان اللوحة**: `إضافة موقع ميداني جديد`.
- **الحقول والنماذج**:
  - `رمز الموقع (Code)`: حقل `spatial-input h-14` مع فحص الفرادة الفوري وتوليد شارة `validation-msg success/error`.
  - `اسم المنشأة / الموقع`: حقل `spatial-input h-14`.
  - `المدينة / المنطقة`: قائمة اختيار مخصصة (`spatial-dropdown-menu` بارتفاع 56px).
  - `العنوان التفصيلي`: حقل نصي `spatial-input`.
  - `الحالة التشغيلية الابتدائية`: مفتاح التبديل `spatial-switch` (`active` بوضع افتراضي).
  - `ملاحظات الموقع`: مربع نصي.
  - `زر الإجراء`: `spatial-button w-full h-14` بنص `حفظ الموقع الجديد`.

### 3.2 حالة التعديل (Edit Existing Site)
- **عنصر المشغل**: زر التعديل ✏️ في صف الجدول.
- **عنصر الواجهة**: نفس اللوحة الجانبية `siteFormDrawer` بحالة التعديل (`isEdit = true`).
- **عنوان اللوحة**: `تعديل بيانات الموقع: [اسم الموقع]`.
- **سلوك البيانات**: تعبئة القيم السابقة تلقائياً مع تجميد تعديل رمز الموقع `code` للحفاظ على سلامة الروابط التراكمية.
- **زر الإجراء**: `spatial-button w-full h-14` بنص `حفظ التغييرات`.

### 3.3 حالة عرض تفاصيل الموقع (View Site Details)
- **عنصر المشغل**: زر الاستكشاف 👁️ في صف الجدول.
- **عنصر الواجهة**: النافذة المنبثقة الزجاجية `#siteDetailModal` (`spatial-modal-card`).
- **محتوى النافذة**:
  - رمز واسم الموقع + شارة الحالة `status-pill completed`.
  - تفاصيل المدينة والعنوان والملاحظات الهندسيّة.
  - إحصائيات الزيارات الميدانية المسجلة للموقع والمهام المربوطة به.
  - زر `إغلاق`.

### 3.4 حالة تغيير الحالة التشغيلية (Toggle Status)
- **عنصر المشغل**: المفتاح التفاعلي `spatial-switch` أو خيار القائمة.
- **الحالات التشغيلية**:
  - `نشط (Active)`: شارة `status-pill completed` خضراء.
  - `غير نشط (Inactive)`: شارة `status-pill overdue` حمراء.
- **السلوك البصري**: يتغير لون الشارة فورا وتصدر إشارة Toast توضح تحويل الحالة.

### 3.5 حالة الحذف الأرشيفي (Delete Site)
- **عنصر المشغل**: زر الحذف 🗑️ في الصف أو زر `حذف مجمع` في شريط `#bulkBarFloating`.
- **عنصر الواجهة**: نافذة التأكيد الزجاجية `#deleteModal` (`spatial-modal-card`).
- **محتوى النافذة**:
  - عنوان باللون الأحمر: `حذف سجل الموقع الميداني`.
  - نص تحذيري: `هل أنت متأكد من حذف سجل الموقع [اسم الموقع]؟ سيتم أرشفة الموقع ومنع إسناد أي مهام جديدة له.`.
  - الأزرار: زر `إلغاء` وزر `تأكيد الحذف` باللون الأحمر (`bg-destructive`).

---

## 4. Table Grid, Bulk Actions & Micro-Interactions

### 4.1 Data Grid Table (`SpatialTable`)
- يشمل أعمدة: `تحديد (Check)`, `رمز الموقع (#CODE)`, `اسم المنشأة`, `المدينة`, `العنوان`, `الحالة (Pill)`, و `أزرار الإجراءات (👁️ ✏️ 🗑️)`.

### 4.2 Floating Bulk Action Bar (`#bulkBarFloating`)
- يظهر عند تحديد مواقع عبر `row-checkbox` لتأدية إجراءات مجمعة: طباعة الأكواد، تصدير إلى Excel، أو تعطيل مجمع.

---

## 5. JavaScript Logic Functions
```javascript
function openCreateSiteDrawer() {
    resetSiteForm();
    document.getElementById('siteDrawerTitle').innerText = 'إضافة موقع ميداني جديد';
    openDrawer('siteFormDrawer');
}

function openEditSiteDrawer(siteData) {
    fillSiteForm(siteData);
    document.getElementById('siteDrawerTitle').innerText = `تعديل بيانات الموقع: ${siteData.name}`;
    openDrawer('siteFormDrawer');
}

function viewSiteDetails(siteId) {
    fetchSiteDetails(siteId);
    openModal('siteDetailModal');
}

function confirmDeleteSite(siteId, siteName) {
    document.getElementById('deleteTargetName').innerText = siteName;
    document.getElementById('confirmDeleteBtn').onclick = function() { executeDeleteSite(siteId); };
    openModal('deleteModal');
}
```
