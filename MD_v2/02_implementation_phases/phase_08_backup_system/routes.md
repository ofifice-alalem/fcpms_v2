# 🛣️ Route Definitions: Phase 08 — نظام النسخ الاحتياطية (Backup System)

---

## 1. Overview & Group Configuration
- **اسم المرحلة**: `phase_08_backup_system`
- **المسار الرئيسي**: `/admin/backups`
- **مجموعة الـ Middleware**: `['web', 'auth', 'role:hr|admin']`
- **الـ Controller المسؤول**: `App\Http\Controllers\Admin\BackupController`

---

## 2. Route Table Definition (All Actions Covered)

| Method | URI | Route Name | Action Method | Description |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/admin/backups` | `admin.backups.index` | `index` | عرض قائمة النسخ الاحتياطية |
| `POST` | `/admin/backups/create` | `admin.backups.create` | `create` | إنشاء نسخة احتياطية جديدة |
| `POST` | `/admin/backups/upload` | `admin.backups.upload` | `upload` | رفع نسخة احتياطية خارجية |
| `POST` | `/admin/backups/restore/{filename}` | `admin.backups.restore` | `restore` | استعادة نسخة احتياطية محددة |
| `GET` | `/admin/backups/download/{filename}` | `admin.backups.download` | `download` | تحميل نسخة احتياطية محددة |
| `DELETE` | `/admin/backups/{filename}` | `admin.backups.delete` | `delete` | حذف نسخة احتياطية محددة |

---

## 3. Route Group Definition

```php
Route::middleware(['auth', 'role:hr|admin'])
    ->prefix('admin/backups')
    ->name('admin.backups.')
    ->group(function () {
        Route::get('/',                    [BackupController::class, 'index'])   ->name('index');
        Route::post('/create',             [BackupController::class, 'create'])  ->name('create');
        Route::post('/upload',             [BackupController::class, 'upload'])  ->name('upload');
        Route::post('/restore/{filename}', [BackupController::class, 'restore'])->name('restore');
        Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
        Route::delete('/{filename}',       [BackupController::class, 'delete'])  ->name('delete');
    });
```

---

## 4. Detailed Response Contracts

### 4.1 `GET /admin/backups` — Inertia Page Response
```json
{
  "component": "Admin/Backups/Index",
  "props": {
    "backups": [
      {
        "filename": "backup_2026-08-10_14-30-00_database.zip",
        "size": "2.5 MB",
        "date": "2026-08-10 14:30:00",
        "note": "قبل تحديث النظام"
      }
    ]
  }
}
```

### 4.2 `POST /admin/backups/create` — Redirect with Flash
```
Redirect back → flash('success', 'تم إنشاء النسخة الاحتياطية بنجاح')
```
أو عند الفشل:
```
Redirect back → flash('error', 'تعذّر إنشاء ملف التخزين المؤقت')
```

### 4.3 `POST /admin/backups/restore/{filename}` — Redirect with Flash
```
Redirect back → flash('success', 'تمت استعادة النسخة الاحتياطية بنجاح')
```
أو عند الفشل:
```
Redirect back → flash('error', 'فشل استعادة قاعدة البيانات: {message}')
```

### 4.4 `GET /admin/backups/download/{filename}` — File Download
```
HTTP 200 + Content-Type: application/zip + Content-Disposition: attachment
```
أو عند عدم وجود الملف:
```
HTTP 404
```

### 4.5 `POST /admin/backups/upload` — Redirect with Flash
```
Redirect back → flash('success', 'تم رفع النسخة الاحتياطية بنجاح')
```
أو عند فشل التحقق:
```
HTTP 422 → Validation errors
```

### 4.6 `DELETE /admin/backups/{filename}` — Redirect with Flash
```
Redirect back → flash('success', 'تم حذف النسخة الاحتياطية')
```

---

## 5. Security & Throttling
- **Role Guard**: `role:hr|admin` — أي مستخدم بدور آخر يحصل على `403 Forbidden`.
- **Path Traversal Protection**: جميع `$filename` تمر عبر `basename()` قبل أي عملية على الملف.
- **CSRF Protection**: إجباري لكافة طلبات `POST` و `DELETE`.
- **File Upload Limit**: `max:524288` (512MB) في قاعدة التحقق.
