# 🛣️ Route Definitions: Phase 00 — تسجيل الدخول والمصادقة (Authentication & Authorization)

---

## 1. Overview & Group Configuration
- **اسم المرحلة**: `phase_00_authentication`
- **المسار الرئيسي**: `/login` & `/logout`
- **مجموعة الـ Middleware**: `['web', 'guest']` لمسارات الدخول، و `['web', 'auth']` للمغادرة.
- **الـ Controller المسؤول**: `App\Http\Controllers\Auth\LoginController`

---

## 2. Route Table Definition

| Method | URI | Route Name | Action Method | Middleware | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/login` | `login` | `showLoginForm` | `guest` | عرض صفحة تسجيل الدخول الزجاجية |
| `POST` | `/login` | `login.submit` | `login` | `guest`, `throttle:5,1` | معالجة طلب الدخول والتحقق من الحساب |
| `POST` | `/logout` | `logout` | `logout` | `auth` | إنهاء الجلسة وإلغاء الكوكيز والخروج |

---

## 3. API & AJAX Response Contracts

### 3.1 Success Response (`POST /login` - AJAX):
```json
{
  "success": true,
  "message": "تم تسجيل الدخول بنجاح",
  "redirect_url": "/dashboard"
}
```

### 3.2 Error Response (`422 Unprocessable Entity` / Inactive Account):
```json
{
  "success": false,
  "message": "بيانات الدخول غير صحيحة أو الحساب غير نشط",
  "errors": {
    "login": ["الحساب غير نشط أو اسم المستخدم غير مفعّل"]
  }
}
```

---

## 4. Security & Throttling
- **Throttling**: `throttle:5,1` (حظر المحاولات الفاشلة لأكثر من 5 مرات في الدقيقة لمنع الهجمات).
- **CSRF Token**: إجباري على طلبات تسجيل الدخول والخروج.
