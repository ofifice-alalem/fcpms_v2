# ⚙️ Backend Implementation: Phase 05 — تنفيذ الزيارات اليومية الميدانية (Consultant Daily Visits)

---

## 1. Overview & Operational Scope
- **اسم المرحلة**: `phase_05_daily_visits`
- **الهدف التنفيذي**: بناء وتوفير المنطق البرمجي الكامل للبوابة الميدانية للاستشاري (Consultant Field Portal) لتسجيل الحضور وسجل اليوم الفعال، فتح زيارة موقع ميداني وتحديثها، وتطبيق الفصل التام بين **المهام اليومية الإجبارية (Daily Tasks)** و**المهام المخصصة عند الحاجة (On-Demand Tasks via Dropdown)** مع رفع الإثباتات الحية وتعديل الإجابات خلال سريان الزيارة.
- **تغطي المرحلة الخصائص الخمس كاملة**: (عرض سجل اليوم الحالي، فتح سجل وزيارة موقع جديد، تعديل إجابات المهام، عرض تفاصيل الإجابات والمرفقات، والحذف/الإلغاء للزيارة المعلقة).
- **التزام المعمارية**:
  - الالتزام بنمط `Repository-Service Pattern` وحالة `Strict Mode` الموثقة في `00_architecture_and_rules/architecture.md`.
  - الامتثال الدقيق لجداول الموديول في `00_architecture_and_rules/database.md#daily-activity-module`.
  - تطبيق قواعد الأعمال: `BR-018` (سجل روزنامي واحد يومياً لكل استشاري)، `BR-019` (فصل المهام اليومية والمهام عند الحاجة)، `BR-023` (تسجيل زيارة الموقع)، و `BR-028/BR-030` (حفظ قيم الاستبيان والحقول الشرطية والمرفقات).

---

## 2. Models & Database Schema

### 2.1 `daily_records` (سجل اليوم العملي)
- `id`: `bigint PK`
- `consultant_id`: `bigint FK → consultants`
- `record_date`: `date` (فريد لكل استشاري في اليوم الواحد - BR-018)
- `check_in_time`: `timestamp NULL` (وقت بدء اليوم العملي)
- `check_out_time`: `timestamp NULL` (وقت إنهاء اليوم العملي)
- `notes`: `text NULL`
- **العلاقات**: `belongsTo(Consultant::class)`, `hasMany(SiteVisit::class)`.

### 2.2 `site_visits` (زيارات المواقع الميدانية)
- `id`: `bigint PK`
- `daily_record_id`: `bigint FK → daily_records`
- `site_id`: `bigint FK → sites` (اختيار الموقع من الـ Dropdown)
- `visit_time`: `timestamp`
- `status`: `enum('in_progress', 'completed')` (default: `in_progress`)
- `notes`: `text NULL`
- **العلاقات**: `belongsTo(DailyRecord::class)`, `belongsTo(Site::class)`, `hasMany(TaskResponse::class)`.

### 2.3 `task_responses` & `task_response_values` & `task_attachments` (الإجابات والإثباتات)
- `task_responses`: `id`, `site_visit_id (FK)`, `task_definition_id (FK)`, `submitted_at`, `status ('draft', 'submitted')`.
- `task_response_values`: `id`, `task_response_id (FK)`, `task_component_id (FK)`, `value (text)`.
- `task_attachments`: `id`, `task_response_id (FK)`, `file_path`, `file_type`.

---

## 3. Detailed Operational Logic & API Actions

### 3.1 🟢 1. فتح سجل اليوم واسترجاع المواقع المتاحة (`GET /consultant/daily-record`)
- **المنطق البرمجي**:
  1. التحقق مما إذا كان لدى الاستشاري `daily_record` قائم لتاريخ اليوم `record_date = today()`.
  2. إذا لم يكن موجوداً، يتم إنشاؤه تلقائياً أو عبر زر `بدء اليوم العملي`.
  3. استرجاع قائمة المواقع الفعالة المتاحة (`active sites`) لعرضها في قائمة اختيار الموقع (`Site Dropdown`).

### 3.2 ➕ 2. اختيار موقع وفتح زيارة جارية (`POST /consultant/site-visits`)
- **المنطق البرمجي**:
  1. يستقبل `site_id` المختار من قائمة الـ Dropdown.
  2. يعالج تكرار الزيارات لتجنب فتح زيارتين لنفس الموقع بنفس الوقت.
  3. إنشاء سجل `site_visit` بحالة `in_progress`.
  4. **الفصل التلقائي بين أنواع المهام (BR-019)**:
     - **المهام اليومية (`daily`)**: يتم جلب واستخراج جميع المهام اليومية المنسدلة لهذا الموقع/الاستشاري فوراً وتجهيز نماذج إجاباتها الابتدائية (`task_responses` بحالة `draft`).
     - **المهام عند الحاجة (`on_demand`)**: لا تظهر تلقائياً؛ يتم إرجاع قائمة بالمهام عند الحاجة المتاحة لهذا الموقع ليختار منها الاستشاري عبر `On-Demand Dropdown`.

### 3.3 ➕ 3. تفعيل مهمة عند الحاجة (`POST /consultant/site-visits/{visit}/trigger-on-demand`)
- **المنطق البرمجي**:
  1. يستقبل `task_definition_id` المختار من قائمة المهام عند الحاجة.
  2. ينشئ نموذج إجابة مخصص للمهمة المحددة مضافاً لسجل الزيارة الحالي.

### 3.4 ✏️ 4. تقديم وتحديث الإجابات والمرفقات (`POST /consultant/site-visits/{visit}/save-responses`)
- **المنطق البرمجي (DB Transaction)**:
  1. السماح بالتعديل المستمر على قيم الحقول والمكونات طالما أن الزيارة بحالة `in_progress`.
  2. حفظ قيم المكونات في `task_response_values`.
  3. المعالجة الفورية للحقول الشرطية الذكية (`BR-030`): حفظ القيم المشروطة فقط إذا تحقق شرط الحقل الأب.
  4. حفظ واستضافة صور الإثبات الحية المرفوعة في `task_attachments` وتخزين المسار بقطر الأمان.

### 3.5 👁️ 5. عرض تفاصيل وإجابات الزيارة الحالية أو السابقة (`GET /consultant/site-visits/{visit}`)
- **المنطق البرمجي**: استرجاع جميع المهام اليومية والمهام عند الحاجة المنجزة مع الصور والمرفقات والمعاينة الحية.

### 3.6 🗑️ 6. إلغاء زيارة معلقة (`DELETE /consultant/site-visits/{visit}`)
- **المنطق البرمجي**: السماح فقط بإلغاء الزيارة المسجلة بحالة `in_progress` والتي لم تُحفظ فيها إجابات نهائية بعد.

---

## 4. Repository & Service Layers
- **الواجهات والتطبيقات**:
  - `DailyRecordRepositoryInterface` / `DailyRecordRepository`
  - `SiteVisitRepositoryInterface` / `SiteVisitRepository`
  - `ConsultantVisitService`
- **طلب التحقق**: `StartDailyRecordRequest`, `OpenSiteVisitRequest`, `SaveTaskResponsesRequest`.

---

## 5. Testing Standards
- **ملف الاختبار**: `tests/Feature/Consultant/DailyVisitsExecutionTest.php`
- **الحالات المغطاة**:
  - `test_consultant_can_start_daily_record_and_select_site_from_dropdown()`
  - `test_daily_tasks_auto_load_while_ondemand_tasks_require_dropdown_trigger()`
  - `test_consultant_can_edit_responses_during_active_visit()`
