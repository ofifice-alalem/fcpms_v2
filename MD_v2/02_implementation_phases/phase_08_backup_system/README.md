# 📌 Phase 08: نظام النسخ الاحتياطية (Backup System)

---

## 1. Phase Information

| Field | Value |
| :--- | :--- |
| **Phase Number** | `phase_08` |
| **Phase Name** | نظام النسخ الاحتياطية (Backup System) |
| **Module Name** | `BackupModule` |
| **Status** | Approved / Documented |
| **Priority** | High |
| **Estimated Complexity** | Medium |

---

## 2. Business Purpose

بناء نظام متكامل لإنشاء نسخ احتياطية لقاعدة البيانات واستعادتها عند الحاجة، مع إمكانية رفع نسخ خارجية وتحميلها. النظام لا يعتمد على أي package خارجي بل يُنفَّذ بالكامل عبر PHP/PDO مباشرةً لضمان الأداء والتحكم الكامل.

---

## 3. Scope

تغطية جميع عمليات دورة حياة النسخة الاحتياطية: الإنشاء، الاستعادة، التحميل، الرفع، والحذف — مع تقييد الوصول لدور `hr` و `admin` فقط.

---

## 4. In Scope

* **إنشاء نسخة احتياطية**: توليد SQL dump كامل لقاعدة البيانات وضغطه في ملف ZIP مع ملاحظة اختيارية.
* **استعادة نسخة احتياطية**: استبدال كامل لبيانات قاعدة البيانات الحالية ببيانات النسخة المختارة مع تأكيد مزدوج.
* **تحميل نسخة احتياطية**: تنزيل ملف ZIP مباشرةً من المتصفح.
* **رفع نسخة خارجية**: قبول ملف `.zip` من المستخدم وحفظه في مجلد النسخ الاحتياطية.
* **حذف نسخة احتياطية**: حذف ملف ZIP من التخزين مع تأكيد.
* **عرض قائمة النسخ**: جدول يعرض جميع النسخ مرتبةً من الأحدث للأقدم مع الاسم والتاريخ والحجم والملاحظة.

---

## 5. Out of Scope

* النسخ الاحتياطية للملفات المرفوعة (المشروع لا يحتوي على ملفات مرفوعة من المستخدم).
* الجدولة التلقائية للنسخ الاحتياطية (Scheduled Backups).
* رفع النسخ إلى تخزين سحابي خارجي (S3, GCS, إلخ).

---

## 6. Prerequisites

* **الأدوار المصرح لها**: `hr`, `admin`.
* **المكونات والأنماط المعمارية**: Controller مباشر بدون Repository/Service (لا توجد نماذج Eloquent).
* **امتداد PHP**: `ZipArchive` مفعّل.
* **مجلد التخزين**: `storage/app/backups/database/` (يُنشأ تلقائياً عند أول نسخة).

---

## 7. Dependencies

### 7.1 Internal Dependencies
* لا توجد نماذج Eloquent — التعامل مع قاعدة البيانات يتم عبر `DB::connection()->getPdo()` مباشرةً.

### 7.2 External Dependencies
* لا توجد packages خارجية — يعتمد على `ZipArchive` المدمج في PHP.

### 7.3 Shared Components
* مكونات Spatial UI v3.0 (`SpatialModal`, `SpatialButton`, `SpatialCard`, `SpatialToast`).
* `HRLayout` كـ Layout رئيسي للصفحة.

---

## 8. Deliverables

### 8.1 Backend Deliverables
* **Controller**: `App\Http\Controllers\Admin\BackupController`
* **لا Form Requests** (التحقق داخل Controller مباشرةً لبساطة المنطق)
* **لا Repository / Service** (لا توجد نماذج Eloquent)

### 8.2 Frontend Deliverables
* **Vue Page**: `resources/js/Pages/Admin/Backups/Index.vue`
* **Modals**: `CreateModal`, `RestoreModal`, `UploadModal` (مدمجة داخل نفس الملف)

### 8.3 Routes Deliverables
* **Route Group**: `/admin/backups` بـ middleware `role:hr|admin`

### 8.4 Database Impact
* **لا جداول جديدة** — كل شيء يُحفظ كملفات ZIP في `storage/app/backups/database/`

---

## 9. Success Criteria & Definition of Done (DoD)

> [!IMPORTANT]
> **لا تعتبر المرحلة منتهية حتى يتحقق الآتي:**
> * [ ] ✓ جميع الـ Routes والـ Endpoints تعمل بدون أخطاء.
> * [ ] ✓ الصلاحيات مقيّدة بدور `hr` و `admin` فقط.
> * [ ] ✓ إنشاء نسخة احتياطية ينتج ملف ZIP صالح يحتوي على `database.sql`.
> * [ ] ✓ استعادة نسخة احتياطية تستبدل البيانات بالكامل بنجاح.
> * [ ] ✓ جميع صفحات وعناصر الواجهة (Spatial UI) مكتملة ومطابقة للمواصفات البصرية.
> * [ ] ✓ الملاحظة تُحفظ وتُقرأ بشكل صحيح من داخل ملف ZIP.

---

* [ ] إنشاء نسخة احتياطية وتحميلها بنجاح.
* [ ] استعادة نسخة احتياطية مع التأكيد المزدوج.
* [ ] رفع ملف ZIP خارجي وظهوره في القائمة.
* [ ] حذف نسخة احتياطية مع تأكيد.
* [ ] عرض الملاحظة المحفوظة داخل ZIP في الجدول.

---

## 10. Cross References

* **Architecture Standards**:
  → See: `00_architecture_and_rules/architecture.md`
* **Design Rules & Tokens**:
  → See: `01_design_system_and_components/design_rules.md`
* **UI Components Catalog**:
  → See: `01_design_system_and_components/components_catalog.html`

---

## 11. Navigation

* ⚙️ **[backend.md](./backend.md)**: يحتوي على تفاصيل Controller والدوال المساعدة ومنطق إنشاء SQL dump والاستعادة.
* 🎨 **[frontend.md](./frontend.md)**: يحتوي على مكونات Vue، تفاصيل الـ Modals الثلاثة، وهيكل الجدول.
* 🛣️ **[routes.md](./routes.md)**: يحتوي على جدول المسارات الكامل والـ Middleware وعقود الاستجابة.

---

## 12. Developer Notes

* **لا package خارجي**: النظام يعتمد على `ZipArchive` و PDO مباشرةً لتجنب التبعيات الخارجية.
* **الأداء مع البيانات الكبيرة**: استخدام `chunk(500)` عند قراءة الجداول + `set_time_limit(300)` للإنشاء و `set_time_limit(0)` للاستعادة.
* **سلامة الاستعادة**: تنفيذ `SET FOREIGN_KEY_CHECKS=0` قبل تنفيذ SQL وإعادتها بعده لتجنب أخطاء الـ constraints.
* **تسمية الملفات**: `backup_{Y-m-d_H-i-s}_database.zip` — أي ملف مرفوع بصيغة مختلفة يُعاد تسميته تلقائياً.
* **الملاحظة**: تُحفظ كـ `note.txt` داخل ملف ZIP نفسه ولا تحتاج جدول في قاعدة البيانات.
