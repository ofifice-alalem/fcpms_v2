# ⚙️ Backend Implementation: Phase 08 — نظام النسخ الاحتياطية (Backup System)

---

## 1. Overview & Scope
- **اسم المرحلة**: `phase_08_backup_system`
- **الهدف التنفيذي**: بناء Controller مباشر يتولى إنشاء نسخ احتياطية لقاعدة البيانات عبر PDO، واستعادتها، وتحميلها، ورفع نسخ خارجية، وحذفها — بدون أي package خارجي.
- **التزام المعمارية**:
  - لا يوجد Repository أو Service (لا نماذج Eloquent في هذه الوحدة).
  - التعامل مع قاعدة البيانات عبر `DB::connection()->getPdo()` مباشرةً.
  - الالتزام بـ `Strict Mode` الموثق في `00_architecture_and_rules/architecture.md`.

---

## 2. Models & Database Entities
- **لا نماذج Eloquent** — النظام يعتمد على ملفات ZIP في `storage/app/backups/database/`.
- **هيكل ملف النسخة الاحتياطية**:
  ```
  backup_{Y-m-d_H-i-s}_database.zip
  ├── database.sql     → SQL dump كامل لقاعدة البيانات
  └── note.txt         → ملاحظة المستخدم (اختياري)
  ```

---

## 3. Controller: `App\Http\Controllers\Admin\BackupController`

### 3.1 الخصائص
```php
private string $backupDir; // storage_path('app/backups/database')
```

### 3.2 الدوال العامة (Public Methods)

#### `index(): Response`
- يمسح `storage/app/backups/database/` ويجمع كل ملفات `.zip`.
- لكل ملف: يقرأ `note.txt` من داخل ZIP إن وُجد.
- يستخرج التاريخ من اسم الملف بـ regex.
- يرتب النسخ من الأحدث للأقدم.
- يُرجع `Inertia::render('Admin/Backups/Index', ['backups' => $backups])`.

#### `create(Request $request): RedirectResponse`
- **إعدادات الأداء**: `set_time_limit(300)`, `ini_set('memory_limit', '512M')`, `session_write_close()`.
- **منطق التنفيذ**:
  1. إنشاء مجلد `backups/database/` إن لم يكن موجوداً.
  2. توليد اسم الملف: `backup_{Y-m-d_H-i-s}_database.zip`.
  3. إنشاء ملف SQL مؤقت في `storage/app/backups/tmp_{timestamp}.sql`.
  4. كتابة header SQL: تعليق الـ backup + `SET FOREIGN_KEY_CHECKS=0`.
  5. جلب كل الجداول عبر `SHOW TABLES`.
  6. لكل جدول: `DROP TABLE IF EXISTS` + `SHOW CREATE TABLE` + INSERT بالدفعات (chunk 500).
  7. كتابة `SET FOREIGN_KEY_CHECKS=1` في النهاية.
  8. إضافة الملف المؤقت للـ ZIP كـ `database.sql`.
  9. إضافة `note.txt` للـ ZIP إن وُجدت ملاحظة.
  10. حذف الملف المؤقت.
  11. إرجاع `back()->with('success', ...)`.

#### `restore(string $filename): RedirectResponse`
- **إعدادات الأداء**: `set_time_limit(0)`, `ini_set('memory_limit', '1G')`, `session_write_close()`.
- **منطق التنفيذ**:
  1. التحقق من وجود الملف.
  2. استخراج ZIP إلى مجلد مؤقت `storage/app/restore_temp_{time()}`.
  3. تنفيذ `restoreLargeSQL()` على `database.sql`.
  4. حذف المجلد المؤقت.
  5. إرجاع `back()->with('success', ...)`.

#### `download(string $filename)`
- التحقق من وجود الملف (404 إن لم يوجد).
- إرجاع `response()->download($zipPath, $filename, ['Content-Type' => 'application/zip'])`.

#### `delete(string $filename): RedirectResponse`
- حذف الملف بـ `unlink()` إن كان موجوداً.
- إرجاع `back()->with('success', ...)`.

#### `upload(Request $request): RedirectResponse`
- **التحقق**: `required | file | mimes:zip | max:524288` (512MB).
- إعادة تسمية الملف إن لم يطابق الصيغة المعتادة.
- حفظ الملف في `storage/app/backups/database/`.
- إرجاع `back()->with('success', ...)`.

---

## 4. الدوال المساعدة (Private Methods)

#### `formatBytes(int $bytes): string`
- تحويل الحجم بالبايت إلى صيغة مقروءة (B / KB / MB / GB).

#### `restoreLargeSQL(string $sqlFile): void`
- قراءة محتوى ملف SQL بالكامل.
- تقسيمه إلى statements منفصلة مع مراعاة الـ strings (تجنب تقسيم `;` داخل قيم النصوص).
- تنفيذ كل statement عبر `$pdo->exec()`.
- تغليف العملية بـ `SET FOREIGN_KEY_CHECKS=0` / `SET FOREIGN_KEY_CHECKS=1`.

#### `deleteDir(string $dir): void`
- حذف مجلد مؤقت بشكل تكراري (recursive).

---

## 5. قواعد الأمان والأداء

| القاعدة | التفاصيل |
| :--- | :--- |
| تقييد الوصول | `middleware('role:hr\|admin')` |
| حماية مسار الملف | `basename($filename)` لمنع Path Traversal |
| حد رفع الملفات | 512MB (`max:524288` في KB) |
| chunk الإنشاء | 500 صف لكل دفعة لتجنب نفاد الذاكرة |
| FOREIGN_KEY_CHECKS | تُعطَّل أثناء الاستعادة وتُعاد بعدها |
| الملف المؤقت | يُحذف دائماً بعد إنشاء ZIP |

---

## 6. Testing Standards
- **لا اختبارات آلية مطلوبة** لهذه المرحلة (اختبار يدوي كافٍ).
- **الاختبار اليدوي المطلوب**:
  - إنشاء نسخة احتياطية والتحقق من صحة ملف ZIP.
  - استعادة نسخة والتحقق من سلامة البيانات.
  - رفع ملف ZIP خارجي وظهوره في القائمة.
  - حذف نسخة والتحقق من اختفائها.
