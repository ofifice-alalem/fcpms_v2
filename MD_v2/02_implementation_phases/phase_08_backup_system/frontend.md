# 🎨 Frontend Implementation: Phase 08 — نظام النسخ الاحتياطية (Backup System)

---

## 1. Overview & UI Objectives
- **اسم المرحلة**: `phase_08_backup_system`
- **الهدف البصري**: توفير صفحة إدارة نسخ احتياطية بأسلوب **Spatial UI v3.0** تتيح إنشاء نسخة جديدة، استعادتها مع تأكيد مزدوج، تحميلها، رفع نسخة خارجية، وحذفها — مع عرض جدول كامل للنسخ المتاحة.
- **الالتزام بالقواعد البصرية**:
  - مطابقة الأبعاد والـ Glassmorphic Gradients المعتمدة في `01_design_system_and_components/design_rules.md`.
  - الاعتماد على المكونات التفاعلية الموثقة في `01_design_system_and_components/components_catalog.html`.

---

## 2. Page Layout & Structure
- **مسار الملف في المشروع**: `resources/js/Pages/Admin/Backups/Index.vue`
- **الـ Layout المستخدم**: `HRLayout`
- **مكونات الهيكل الأساسي**:
  - **Header**: عنوان `النسخ الاحتياطية` + وصف + زران للإجراءات الرئيسية.
  - **Flash Messages**: عرض رسائل `success` و `error` من الـ session.
  - **Backups Table Card**: `SpatialCard` يحتوي على جدول النسخ أو حالة الفراغ.
  - **Modals**: ثلاثة modals مدمجة في نفس الملف (`CreateModal`, `RestoreModal`, `UploadModal`).

---

## 3. Detailed UI States & Component Specifications

### 3.1 Header Actions
- **زر "رفع نسخة"**: يفتح `UploadModal` — تصميم ثانوي (border/ghost).
- **زر "إنشاء نسخة احتياطية"**: يفتح `CreateModal` — تصميم `spatial-button` رئيسي.

### 3.2 جدول النسخ الاحتياطية (Backups Table)

**حالة الفراغ (Empty State)**:
- أيقونة 🗄️ كبيرة + نص `لا توجد نسخ احتياطية بعد`.

**الأعمدة**:
| العمود | المحتوى |
| :--- | :--- |
| اسم الملف | أيقونة قاعدة بيانات + اسم الملف بخط صغير |
| التاريخ | شارة `bg-black/5` تعرض التاريخ والوقت |
| الحجم | حجم الملف (B / KB / MB / GB) |
| إجراءات | 3 أزرار: تحميل (أزرق) + استعادة (أصفر) + حذف (أحمر) |
| ملاحظة | نص الملاحظة أو `—` |

**Tooltips**: كل زر إجراء يحتوي على tooltip يظهر عند hover.

**Mobile Cards**: في الشاشات الصغيرة يُستبدل الجدول بـ cards عمودية لكل نسخة.

### 3.3 CreateModal — إنشاء نسخة احتياطية
- **المشغل**: زر `إنشاء نسخة احتياطية` في الـ Header.
- **المحتوى**:
  - أيقونة قاعدة بيانات + زر إغلاق.
  - عنوان: `إنشاء نسخة احتياطية`.
  - وصف: `سيتم إنشاء نسخة احتياطية كاملة لقاعدة البيانات وحفظها.`
  - حقل `textarea` للملاحظة (اختياري) — placeholder: `مثال: قبل تحديث النظام...`
  - زر `إلغاء` + زر `إنشاء` (يُظهر spinner أثناء التحميل).
- **السلوك**: `router.post('/admin/backups/create', { note })` — يُغلق Modal عند النجاح.

### 3.4 RestoreModal — استعادة نسخة احتياطية
- **المشغل**: زر الاستعادة 🔄 في صف الجدول.
- **خطوات التأكيد المزدوج**:

  **الخطوة 1 (`confirm1`)**:
  - أيقونة تحذير صفراء.
  - عنوان: `استعادة نسخة احتياطية`.
  - تحذير: `سيتم استبدال جميع البيانات الحالية بالكامل. هذا الإجراء لا يمكن التراجع عنه.`
  - عرض اسم الملف المختار.
  - زر `إلغاء` + زر `متابعة` (أصفر).

  **الخطوة 2 (`confirm2`)**:
  - عنوان أحمر: `تأكيد نهائي`.
  - تحذير: `هل أنت متأكد تماماً؟ ستُحذف جميع البيانات الحالية ولا يمكن استرجاعها.`
  - زر `رجوع` + زر `تنفيذ الاستعادة` (أحمر).

  **الخطوة 3 (`loading`)**:
  - أيقونة دوارة.
  - عنوان: `جارٍ الاستعادة...` + تحذير `لا تغلق الصفحة`.
  - شريط تقدم وهمي يصل إلى 90% ثم يقفز إلى 100% عند النجاح.
  - الـ backdrop لا يُغلق Modal أثناء التحميل.

- **السلوك**: `router.post('/admin/backups/restore/{filename}')`.

### 3.5 UploadModal — رفع نسخة خارجية
- **المشغل**: زر `رفع نسخة` في الـ Header.
- **المحتوى**:
  - أيقونة رفع خضراء + زر إغلاق.
  - عنوان: `رفع نسخة خارجية`.
  - وصف: `ارفع ملف .zip نسخة احتياطية سابقة. الحد الأقصى 512MB.`
  - منطقة رفع ملف (drag-and-drop style) — تقبل `.zip` فقط.
  - عرض اسم وحجم الملف المختار.
  - زر `إلغاء` + زر `رفع` (أخضر، معطّل حتى اختيار ملف).
- **السلوك**: `router.post('/admin/backups/upload', formData, { forceFormData: true })`.

---

## 4. Vue Component Structure

```
Pages/Admin/Backups/Index.vue
├── <script setup>
│   ├── props: { backups: Backup[], flash?: { success?, error? } }
│   ├── refs: showCreate, showUpload, restoreTarget
│   └── interface Backup { filename, size, date, note }
├── <template>
│   ├── HRLayout
│   │   ├── Header (title + action buttons)
│   │   ├── Flash Messages
│   │   └── SpatialCard (backups table / empty state)
│   ├── CreateModal (v-if="showCreate")
│   ├── UploadModal (v-if="showUpload")
│   └── RestoreModal (v-if="restoreTarget" :backup="restoreTarget")
```

---

## 5. Props & Data Contracts

```typescript
interface Backup {
    filename: string;
    size: string;       // "2.5 MB"
    date: string | null; // "2026-08-10 14:30:00"
    note: string | null;
}

interface Props {
    backups: Backup[];
    flash?: {
        success?: string;
        error?: string;
    };
}
```

---

## 6. Inertia Router Calls

| الإجراء | الاستدعاء |
| :--- | :--- |
| إنشاء نسخة | `router.post('/admin/backups/create', { note })` |
| استعادة نسخة | `router.post('/admin/backups/restore/' + filename)` |
| رفع نسخة | `router.post('/admin/backups/upload', formData, { forceFormData: true })` |
| حذف نسخة | `router.delete('/admin/backups/' + filename)` |
| تحميل نسخة | `<a :href="'/admin/backups/download/' + filename">` (رابط مباشر) |
