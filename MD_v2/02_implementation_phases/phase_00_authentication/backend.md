# ⚙️ Backend Implementation: Phase 00 — تسجيل الدخول والمصادقة (Authentication & Authorization)

---

## 1. Overview & Scope
- **اسم المرحلة**: `phase_00_authentication`
- **الهدف التنفيذي**: بناء وتوفير آليات المصادقة وإدارة الجلسات للمستخدمين (مدراء الموارد البشرية والاستشاريين الميدانيين)، التحقق من الحالة النشطة للمستخدم، وتسجيل آخر وقت دخول.
- **التزام المعمارية**:
  - الالتزام بنمط `Repository-Service Pattern` ومعيار `Strict Mode` من `00_architecture_and_rules/architecture.md`.
  - الامتثال لجدول `users` المحدد في `00_architecture_and_rules/database.md#users-module`.
  - تطبيق قواعد الأعمال: `BR-001` (طريقة تسجيل الدخول والحالة النشطة) و `BR-002` (تحديث `last_login_at` وحظر الحسابات الموقوفة).

---

## 2. Models & Database Entities
- **النموذج الرئيسي**: `App\Models\User`
- **جدول البيانات**: `users`
- **الحقول المعالجة**:
  - `id`: `bigint PK`
  - `name`: `varchar`
  - `username`: `varchar UNIQUE` (تسجيل الدخول باسم المستخدم)
  - `email`: `varchar UNIQUE` (أو بالبريد الإلكتروني)
  - `password`: `varchar` (مشفرة بـ `Bcrypt`)
  - `status`: `enum('active', 'inactive')` (default: `active`)
  - `last_login_at`: `timestamp NULL` (يُحدث عند كل دخول ناجح - BR-002)
  - `created_at`, `updated_at`, `deleted_at`: `timestamps` (مع `SoftDeletes`)
- **العلاقات**: `hasOne(Consultant::class)`, `roles()` (عبر Spatie Permission)

---

## 3. Repository Layer (`App\Repositories`)
- **الواجهة**: `App\Repositories\Contracts\UserRepositoryInterface`
- **التطبيق**: `App\Repositories\Eloquent\UserRepository`
- **الدوال المحددة**:
  ```php
  public function findByUsernameOrEmail(string $loginInput): ?User;
  public function updateLastLogin(int $userId): bool;
  public function isUserActive(User $user): bool;
  ```

---

## 4. Service Layer (`App\Services`)
- **الفئة**: `App\Services\AuthService`
- **منطق الأعمال وقواعد BRs**:
  - **BR-001**: محاولة المصادقة مطابقة `username` أو `email` مع كلمة المرور المشفرة.
  - **BR-002 (Active Check)**: إذا كانت حالة الحساب `inactive` يتم رفض الجلسة فوراً وإرجاع خطأ "الحساب غير نشط".
  - **Audit Logging**: عند النجاح يتم تسجيل آخر دخول وتحديث `last_login_at = now()`.

---

## 5. Controllers & Form Requests
- **الـ Controller**: `App\Http\Controllers\Auth\LoginController`
- **طلبات التحقق (Form Requests)**: `App\Http\Requests\Auth\LoginRequest`
- **قواعد التحقق (Validation Rules Example)**:
  ```php
  public function rules(): array
  {
      return [
          'login'    => ['required', 'string'],
          'password' => ['required', 'string', 'min:6'],
          'remember' => ['nullable', 'boolean'],
      ];
  }
  ```

---

## 6. Security & Session Handling
- **حماية الـ Brute-force (Rate Limiting)**: `ThrottleRequests` (5 محاولات فاشلة لكل دقيقة).
- **إدارة الجلسة**: إعادة توليد الـ Session ID عند النجاح لمنع الـ Session Fixation.

---

## 7. Testing Standards
- **ملف الاختبار**: `tests/Feature/Auth/AuthenticationTest.php`
- **الحالات المغطاة**:
  - `test_user_can_login_with_valid_username_or_email()`
  - `test_inactive_user_cannot_login()`
  - `test_last_login_timestamp_updated_on_successful_login()`
  - `test_login_rate_limiting_after_five_failed_attempts()`
