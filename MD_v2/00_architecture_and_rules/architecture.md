# المرحلة 02.5 — System Architecture
# الهيكل المعماري للنظام

---

## الفلسفة العامة للنظام

تم تصميم النظام ليكون نظاماً لإدارة أداء الاستشاريين الميدانيين، **وليس نظام حضور وانصراف**.

يعتمد النظام على توثيق الأنشطة الفعلية التي يقوم بها الاستشاري داخل مواقع العمل، ثم تحويل هذه البيانات إلى مؤشرات أداء وتقارير إدارية.

لا يتم تقييم الاستشاري بناءً على دخوله للنظام فقط، وإنما بناءً على الأعمال التي قام بتسجيلها داخل مواقع المشاريع.

---

## المبادئ الأساسية للنظام

- جميع العمليات مرتبطة بموقع عمل — لا توجد أي مهمة خارج موقع
- لكل استشاري سجل يومي واحد فقط لكل تاريخ
- يمكن أن يحتوي السجل اليومي على عدة مواقع
- كل موقع يحتوي على مجموعة مستقلة من المهام
- جميع البيانات تحفظ كسجل تاريخي
- لا يتم تعديل السجلات السابقة عند تعديل تعريفات المهام

---

## الكيانات الرئيسية (Core Entities)

يتكون النظام من ستة كيانات رئيسية:

### Consultant
يمثل الشخص الذي يقوم بالزيارات الميدانية. مرتبط بحساب مستخدم (`User`) ويمتلك جدول عمل خاص به.

### Daily Record
يمثل سجل العمل الخاص بالاستشاري في يوم معين. لا يحتوي على المهام مباشرة، وإنما يجمع جميع المواقع التي تمت زيارتها خلال ذلك اليوم. لكل استشاري سجل واحد فقط لكل تاريخ.

### Site
يمثل مشروعاً أو موقع عمل. ترتبط جميع العمليات داخله. لا يمكن تسجيل أي مهمة خارج موقع.

### Site Visit
تمثل زيارة الاستشاري لموقع معين خلال يوم محدد. كل زيارة تعتبر وحدة عمل مستقلة وتحتوي على جميع المهام المنفذة داخل ذلك الموقع.

### Task Definition
تمثل تعريف المهمة (القالب). لا تحتوي على أي بيانات تنفيذ، وإنما تحدد: اسم المهمة، نوعها، مكوناتها، وهل هي يومية أم حسب الحاجة.

### Task Response
تمثل الإجابة الفعلية التي سجلها الاستشاري أثناء تنفيذ المهمة. تحتوي على الاختيارات والنصوص والصور. لا تؤثر على تعريف المهمة.

---

## مفهوم المهام: Template و Response

كل مهمة عبارة عن **قالب (Template)** يتم استخدامه من قبل جميع الاستشاريين.
أما البيانات التي يدخلها الاستشاري فهي **استجابة (Response)** لهذا القالب.
وبالتالي فإن تعديل المهمة مستقبلاً لا يؤثر على البيانات التاريخية.

---

## مفهوم تخصيص المهام

```
المهمة عامة بشكل افتراضي
    ↓
تظهر في جميع المواقع ولكل الاستشاريين
    ↓
يمكن تخصيصها لموقع أو أكثر (task_site_assignments)
    ↓
يمكن تخصيصها لاستشاري أو أكثر (task_consultant_assignments)
    ↓
إذا خُصصت لموقع واستشاري → تظهر عند اجتماع الشرطين معاً
```

---

## دورة حياة البيانات (Data Lifecycle)

```
تعريف المهمة
    ↓
إضافة الموقع
    ↓
إنشاء الاستشاري
    ↓
بدء يوم العمل
    ↓
إنشاء السجل اليومي (تلقائي)
    ↓
اختيار موقع
    ↓
تحميل المهام الخاصة بالموقع
    ↓
إدخال البيانات
    ↓
حفظ الزيارة
    ↓
احتساب الأداء (PerformanceCalculationService)
    ↓
إظهار التقارير
```

---

## تسلسل عمل الاستشاري

```
تسجيل الدخول
    ↓
فتح يوم العمل
    ↓
اختيار موقع
    ↓
تنفيذ المهام اليومية
    ↓
تنفيذ المهام حسب الحاجة (اختياري)
    ↓
حفظ الموقع
    ↓
إضافة موقع آخر (اختياري)
    ↓
إنهاء اليوم
```

---

## تسلسل احتساب الأداء

```
التحقق من العطل الرسمية
    ↓
التحقق من جدول العمل
    ↓
التحقق من الإجازة
    ↓
التحقق من وجود نشاط
    ↓
احتساب الحضور أو الغياب
    ↓
احتساب نسبة الإنجاز
    ↓
تحديث التقارير
```

---

## الهيكل المنطقي للنظام

```
Daily Record
    ↓
Site Visits
    ↓
Task Responses
    ↓
Task Response Values + Task Attachments
```

السجل اليومي لا يحتوي على المهام مباشرة، وإنما يحتوي على زيارات المواقع، وكل زيارة تحتوي على المهام الخاصة بها.

---

## Stack المعتمد

| الطبقة | التقنية |
|--------|---------|
| Backend | Laravel 11.x |
| Frontend | Vue.js 3 + Inertia.js |
| Styling | Tailwind CSS + Spatial UI v3.0 |
| Auth | Spatie Laravel Permission |
| Routes (Frontend) | Ziggy |

---

## الحزم المثبتة

### Core
| الحزمة | الغرض |
|--------|--------|
| `inertiajs/inertia-laravel` | ربط Backend بـ Vue |
| `tightenco/ziggy` | استخدام `route()` في Vue |

### Authorization & Queries
| الحزمة | الغرض |
|--------|--------|
| `spatie/laravel-permission` | إدارة الأدوار والصلاحيات (BR-001, BR-002) |
| `spatie/laravel-query-builder` | فلاتر التقارير والبحث المتقدم |

### Architecture
| الحزمة | الغرض |
|--------|--------|
| `prettus/l5-repository` | Repository Pattern — **إجباري** |

### Database & Auditing
| الحزمة | الغرض |
|--------|--------|
| `owen-it/laravel-auditing` | تسجيل التعديلات على البيانات الحساسة (BR-065, BR-066) |

### Reporting & Exports
| الحزمة | الغرض |
|--------|--------|
| `barryvdh/laravel-dompdf` | تصدير PDF |
| `dompdf/dompdf` | مكتبة PDF الأساسية |
| `maatwebsite/excel` | تصدير Excel |
| `phpoffice/phpspreadsheet` | مكتبة Excel الأساسية |

### Utilities
| الحزمة | الغرض |
|--------|--------|
| `nesbot/carbon` | التعامل مع التواريخ وجداول العمل |
| `guzzlehttp/guzzle` | API Calls الخارجية |
| `vlucas/phpdotenv` | إدارة المتغيرات البيئية |
| `ramsey/uuid` | المعرفات الفريدة |

### Debugging & Development
| الحزمة | الغرض |
|--------|--------|
| `barryvdh/laravel-debugbar` | تصحيح الأخطاء أثناء التطوير |
| `barryvdh/laravel-ide-helper` | تحسين الإكمال التلقائي في IDE |

### Testing
| الحزمة | الغرض |
|--------|--------|
| `pestphp/pest` | إطار الاختبار الأساسي |
| `pestphp/pest-plugin-laravel` | تكامل Pest مع Laravel |
| `phpunit/phpunit` | قاعدة الاختبارات |
| `mockery/mockery` | محاكاة الكائنات أثناء الاختبار |
| `brianium/paratest` | تسريع الاختبارات (Parallel Testing) |

### Code Quality
| الحزمة | الغرض |
|--------|--------|
| `nunomaduro/larastan` | تحليل ثابت للكود |
| `phpstan/phpstan` | اكتشاف الأخطاء مبكراً |

---

## القواعد المعمارية — STRICT

### 1. Repository Pattern — إجباري
- كل جدول له منطق أعمال يجب أن يمر عبر Repository
- **ممنوع** كتابة Eloquent أو Query Builder مباشرة داخل Controller

```
Controller → Repository → Model
Controller → Service → Repository → Model  (للمنطق المعقد)
```

### 2. طبقات النظام

```
HTTP Request
    ↓
Controller       ← استقبال الطلب + Form Request validation فقط
    ↓
Service          ← منطق الأعمال المعقد (PerformanceCalculation, ConsultantWorkflow)
    ↓
Repository       ← التفاعل مع قاعدة البيانات
    ↓
Model            ← تعريف العلاقات والـ casts فقط
```

### 3. متى تستخدم Service؟
- حساب نسبة الإنجاز (`PerformanceCalculationService`)
- دورة عمل الاستشاري (`ConsultantWorkflowService`)
- أي منطق يمس أكثر من Repository واحد

### 4. متى تستخدم Repository مباشرة من Controller؟
- CRUD بسيط (Sites, Consultants, Tasks)
- لا يوجد منطق أعمال معقد

### 5. الصلاحيات
- استخدام `spatie/laravel-permission` فقط
- **ممنوع** استخدام `if ($user->role === 'hr')` مباشرة في الكود

### 6. التواريخ
- استخدام `Carbon` فقط للتعامل مع `work_date`, `start_date`, `end_date`
- **ممنوع** استخدام `date()` الخام
- **ممنوع** تخزين التواريخ بصيغة نصية عشوائية — استخدم `timestamp` أو `date`

### 7. Auditing
تفعيل `owen-it/laravel-auditing` على:
- `DailyRecord`
- `SiteVisit`
- `TaskResponse`
- `TaskDefinition`

### 8. الفلاتر والبحث
- استخدام `spatie/laravel-query-builder` فقط لمعالجة طلبات البحث والترتيب في التقارير

### 9. API Calls
- استخدام `Guzzle` فقط للتواصل مع أي خدمات خارجية

---

## خدمة احتساب الأداء (PerformanceCalculationService)

مكوّن رئيسي داخل طبقة الخدمات (Services Layer)، مسؤولياته:

- إعادة احتساب الأداء اليومي فور حفظ الإجابات (BR-061-A)
- تحديث مؤشرات الأداء المخزنة داخل `daily_records`
- تزويد نظام التقارير ولوحات التحكم بالبيانات اليومية المجمعة
- تطبيق مبدأ التجميع المرحلي (Layered Aggregation) لحساب الأداء الشهري والسنوي عند الطلب

---

## هيكل المجلدات

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── HR/
│   │   ├── Consultant/
│   │   └── Reports/
│   └── Requests/
├── Models/
├── Repositories/
│   ├── Contracts/       ← الـ Interfaces
│   └── Eloquent/        ← التطبيق الفعلي
└── Services/
    ├── PerformanceCalculationService.php
    └── ConsultantWorkflowService.php

resources/js/
├── Components/
│   └── Spatial/         ← مكونات Design System
├── Composables/
├── Layouts/
│   ├── HRLayout.vue
│   ├── ConsultantLayout.vue
│   └── GuestLayout.vue
└── Pages/
    ├── Auth/
    ├── HR/
    └── Consultant/
```

---

## الأدوار والصلاحيات

| الدور | الوصول |
|-------|--------|
| `hr` | لوحة HR كاملة — Consultants, Sites, Tasks, Reports |
| `consultant` | لوحة الاستشاري — Daily Work, Performance |

### Middleware المستخدم
```php
Route::middleware(['auth'])->group(...)
Route::middleware(['role:hr'])->group(...)
Route::middleware(['role:consultant'])->group(...)
Route::middleware(['permission:view_reports'])->group(...)
```

---

## قواعد Vue + Inertia

- كل صفحة تستخدم Layout المناسب (`HRLayout` أو `ConsultantLayout`)
- البيانات تُمرر من Controller عبر `Inertia::render('Page', [...data])`
- استخدام `useForm()` من `@inertiajs/vue3` لجميع النماذج
- استخدام `route()` من Ziggy للروابط
- **ممنوع** استخدام `axios` مباشرة — كل شيء عبر Inertia
- **ممنوع** كتابة منطق أعمال داخل Vue Components

---

## استراتيجية الاختبار

### السيناريوهات الإجبارية
| السيناريو | القاعدة |
|-----------|---------|
| حساب نسبة الإنجاز (Completion Percentage) | BR-043, BR-058 |
| احتساب الغياب عند عدم وجود نشاط | BR-018 |
| منع تكرار زيارة نفس الموقع في نفس اليوم | BR-023 |
| التحقق من الحقول الشرطية في Task Builder | BR-032, BR-035 |

### الأدوات
- `Mockery` — محاكاة العلاقات المعقدة وقت الاختبار
- `paratest` — Parallel Testing لتقليل وقت التنفيذ
- `Pest` — كتابة الاختبارات بأسلوب واضح ومختصر

---

## أسلوب الكود

- **Clean Architecture**: فصل طبقات Controller (استقبال الطلب) → Service (منطق الأعمال) → Repository (قاعدة البيانات)
- **Modular Design**: كل وحدة لها مجلدها الخاص (Users, Tasks, Sites, Reports)
- **Naming واضح**: مثل `getDailyCompletionRate`, `assignTaskToSite`, `calculateAbsence`

---

## المبادئ المعمارية

- فصل تعريف البيانات (Definitions) عن البيانات التشغيلية (Operational Data)
- عدم تكرار البيانات (Data Normalization)
- الاحتفاظ بالسجل التاريخي لجميع العمليات
- قابلية التوسع لإضافة أنواع جديدة من المهام وحقول الإدخال
- عدم ربط منطق النظام بالواجهات
- اعتماد قواعد العمل (`business_rules.md`) كمرجع أساسي عند أي تعارض

---

## الممنوعات

- ❌ Eloquent مباشر في Controller
- ❌ `if ($user->role === ...)` بدون Spatie
- ❌ `date()` الخام بدون Carbon
- ❌ تخزين التواريخ بصيغة نصية عشوائية
- ❌ `axios` مباشرة في Vue بدون Inertia
- ❌ SQL خام
- ❌ منطق أعمال داخل Vue Components
- ❌ استخدام مكتبات خارج القائمة المعتمدة دون مراجعة
- ❌ كسر Repository Pattern
- ❌ تجاهل `laravel-auditing` عند تعديل أي سجل له علاقة بالأداء أو المهام
