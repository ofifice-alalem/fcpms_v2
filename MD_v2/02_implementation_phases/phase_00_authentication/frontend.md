# 🎨 Frontend Implementation: Phase 00 — تسجيل الدخول والمصادقة (Authentication & Authorization)

---

## 1. Overview & UI Objectives
- **اسم المرحلة**: `phase_00_authentication`
- **الهدف البصري**: تصميم واجهة تسجيل دخول زجاجية فاخرة وفائقة الأناقة بأسلوب **Spatial UI v3.0** تتيح للمستخدمين تسجيل الدخول بالاسم أو البريد مع دعم كامل للـ Dark/Light Mode وتغذية راجعة فورية للأخطاء.
- **الالتزام بالقواعد البصرية**:
  - الاعتماد على خلفية شبكية متدرجة عاكسة ومكونات زجاجية مرئية الموثقة في `01_design_system_and_components/design_rules.md`.
  - المكونات الموثقة في `01_design_system_and_components/components_catalog.html`.

---

## 2. Page Layout & Structure
- **مسار الملف في المشروع**: `resources/views/auth/login.blade.php`
- **هيكل الصفحة الزجاجي**:
  - كرت زجاجي مركزي في منتصف الشاشة (`spatial-card max-w-md w-full p-8 lg:p-10`).
  - شعار النظام الفاخر FCPMS + عنوان مرحب في الأعلى.
  - حقل إدخال اسم المستخدم/البريد (`spatial-input h-14 rounded-[18px]`).
  - حقل كلمة المرور مع زر إظهار/إخفاء كلمة المرور (SVG Eye Icon).
  - خيار "تذكرني" باستخدام الـ `custom-checkbox` المخصص.
  - زر تسجيل الدخول الفاخر `spatial-button w-full h-14`.

---

## 3. UI Components Used (From `components_catalog.html`)

### 3.1 Spatial Login Card (`spatial-card`)
- زوايا بقطر 30px مع تأثير Glassmorphic Backdrop Blur خلفي زجاجي وتحدب متوهج عند التحويم.

### 3.2 Form Inputs (`spatial-input`)
- ارتفاع 56px (`h-14`) مع أنصاف أقطار 18px، مع تفعيل حالة `error` باللون الأحمر عند إدخال بيانات خاطئة.

### 3.3 Custom Checkbox (`custom-checkbox`)
- مربع اختيار مخصص بلون تدرجي وتوهج أزرق ناعم عند التحديد لخيار "تذكرني".

### 3.4 Feedback Toast Notifications (`toast-item`)
- عند حدوث خطأ أو حظر للحساب، يظهر إشعار Toast عائم من الأعلى جهة اليسار (`#toastContainer`) يحدد سبب الخطأ.

---

## 4. Micro-Interactions & Form Validation Rules
1. **Toggle Password Visibility**: زر تبديل رؤية كلمة المرور باستخدام أيقونة SVG متجهة بدقة عالية.
2. **Button Loading State**: عند النقر على الدخول، يتغير نص الزر إلى `جاري التحميل...` ويصبح غير قابل للنقر لمنع التكرار (Simulated Loading).

---

## 5. JavaScript Logic Functions
```javascript
function togglePasswordVisibility() {
    const input = document.getElementById('passwordInput');
    input.type = input.type === 'password' ? 'text' : 'password';
}

function handleLoginSubmit(form) {
    const btn = form.querySelector('.spatial-button');
    btn.disabled = true;
    btn.querySelector('.btn-text').innerText = 'جاري التحميل...';
}
```
