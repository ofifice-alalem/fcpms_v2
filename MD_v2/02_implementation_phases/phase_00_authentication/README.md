# 📌 Phase 00: المصادقة وتأمين الدخول (Authentication & Access Control)

---

## 1. Phase Information

| Field | Value |
| :--- | :--- |
| **Phase Number** | `phase_00` |
| **Phase Name** | المصادقة وتأمين الدخول (Authentication & Access Control) |
| **Module Name** | `AuthModule` |
| **Status** | Approved / Documented |
| **Priority** | High |
| **Estimated Complexity** | Medium |

---

## 2. Business Purpose

توفير الواجهة الآمنة والمنطق البرمجي لمصادقة المستخدمين والاستشاريين والمشرفين عبر النظام، وتأمين حماية الجلسات وتطبيق التحكم بالحسابات المعلقة وتسجيل الدخول والخروج مع الحماية من الهجمات.

---

## 3. Scope

تغطية كافة عمليات المصادقة والتحقق من الهوية واستعادة كلمة المرور وإدارة الجلسات الفعالة لجميع مستخدمي النظام وتطبيق قواعد الأعمال (`BR-001`, `BR-002`).

---

## 4. In Scope

* **تسجيل الدخول (Login)**: بالبريد وكلمة المرور، والتحقق من حالة الحساب `status = 'active'`.
* **تسجيل الخروج (Logout)**: إلغاء الجلسة الفعالة وسحب الـ Tokens.
* **إدارة الحسابات المعلقة**: تطبيق تجميد الدخول للحسابات غير النشطة أو الموقوفة (`BR-002`).
* **تأمين الجلسات وتطبيق الحماية**: CSRF Protection، وتحديد معدل المحاولات `throttle:5,1` لحماية مسارات الدخول.
* **إعادة تعيين كلمة المرور**: إرسال روابط إعادة التعيين والتحقق.

---

## 5. Out of Scope

* إنشاء ملفات الاستشاريين والأرقام الوظيفية (راجع: `phase_02_consultants`).
* إدارة الأدوار وصلاحيات Spatie الفردية (راجع: `phase_07_system_governance`).

---

## 6. Prerequisites

* **الصلاحيات ومجموعات Middleware**: `web`, `auth`, `guest`.
* **الجدول المرجعي في قاعدة البيانات**: جدول `users`.

---

## 7. Dependencies

### 7.1 Internal Dependencies
* **جدول البيانات**: `users`
* **النماذج المترابطة**: `User` عبر `App\Models\User`.

### 7.2 External Dependencies
* Not yet documented.

### 7.3 Shared Components
* مكونات Spatial UI v3.0 (`spatial-input`, `spatial-button`, `spatial-card`).

---

## 8. Deliverables

### 8.1 Backend Deliverables
* **Model**: `App\Models\User`
* **Controller**: `App\Http\Controllers\Auth\LoginController`, `App\Http\Controllers\Auth\ResetPasswordController`
* **Form Request**: `LoginRequest`
* **Service / Action**: `AuthenticateUserService`
* **Feature Test**: `tests/Feature/Auth/AuthenticationTest.php`

### 8.2 Frontend Deliverables
* **Blade View**: `resources/views/auth/login.blade.php`
* **UI Components**: بطاقة تسجيل الدخول الزجاجية المصممة بـ Spatial UI v3.0 مع رسائل الأخطاء `validation-msg`.

### 8.3 Routes Deliverables
* **Routes**: `/login` (`GET`, `POST`), `/logout` (`POST`), `/password/reset` (`GET`, `POST`).

### 8.4 Database Impact
* **Table**: `users` (`id`, `name`, `email`, `password`, `status`, `remember_token`, `created_at`, `updated_at`).

---

## 9. Success Criteria & Definition of Done (DoD)

> [!IMPORTANT]
> **لا تعتبر المرحلة منتهية حتى يتحقق الآتي:**
> * [ ] ✓ جميع الـ Routes والـ Endpoints تعمل بدون أخطاء.
> * [ ] ✓ جميع سياسات الحماية والصلاحيات (Policies & Permissions) تفرض وتعمل بدقة.
> * [ ] ✓ جميع اختبارات الميزات (Feature Tests) ناجحة بنسبة 100%.
> * [ ] ✓ جميع صفحات وعناصر الواجهة (Spatial UI) مكتملة ومطابقة للمواصفات البصرية.
> * [ ] ✓ جميع قواعد الأعمال المحددة (Business Rules) مطبقة ومختبرة بالكامل.

---

* [ ] نجاح تسجيل الدخول للحسابات الفعالة وصد الحسابات غير النشطة (BR-002).
* [ ] حظر المحاولات المكررة بعد 5 محاولات خاطئة بواسطة Throttling.
* [ ] نجاح سحب الجلسة والـ Tokens فور تسجيل الخروج.
* [ ] نجاح كافة اختبارات المصادقة في `AuthenticationTest`.

---

## 10. Cross References

* **Architecture Standards**:
  → See: `00_architecture_and_rules/architecture.md`
* **Business Rules (BR-001, BR-002)**:
  → See: `00_architecture_and_rules/business_rules.md`
* **Database Schema (Users Entity)**:
  → See: `00_architecture_and_rules/database.md#users-table`
* **Design Rules & Tokens**:
  → See: `01_design_system_and_components/design_rules.md`
* **UI Components Catalog**:
  → See: `01_design_system_and_components/components_catalog.html`

---

## 11. Navigation

* ⚙️ **[backend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_00_authentication/backend.md)**: يحتوي على تفاصيل خدمات المصادقة، طلب التحقق، واختبارات الأتمتة.
* 🎨 **[frontend.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_00_authentication/frontend.md)**: يحتوي على عناصر واجهة تسجيل الدخول والتصميم الزجاجي.
* 🛣️ **[routes.md](file:///home/hammam/Desktop/work/fcpms/MD_v2/02_implementation_phases/phase_00_authentication/routes.md)**: يحتوي على مسارات الدخول والـ Throttling والـ Middleware.

---

## 12. Developer Notes

* **التحقق من حالة الحساب (BR-002)**: يجب التحقق التلقائي من `status = 'active'` قبل السماح بإنشاء الجلسة.
* **الحماية من الهجمات**: تطبييق `throttle:5,1` إجباري على مسار `POST /login`.
