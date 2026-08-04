# ⚙️ Backend Implementation: Phase 03 — جداول الدوام والعطلات والإجازات (Work Schedules & Leaves)

---

## 1. Overview & Scope
- **اسم المرحلة**: `phase_03_work_schedules_and_leaves`
- **الهدف التنفيذي**: بناء المنطق البرمجي الكامل لإدارة قوالب ساعات العمل والدوام الرسمية، تخصيص أيام العمل الأسبوعية، تسجيل العطلات الرسمية، وإدارة إجازات الاستشاريين الميدانيين وتغطي كل الإجراءات: (عرض الجداول، إنشاء قالب/عطلة/إجازة جديدة، تعديل القوالب، حذف أو أرشفة القوالب، واستعراض تفاصيل الدوام).
- **التزام المعمارية**:
  - الالتزام بنمط `Repository-Service Pattern` وحالة `Strict Mode` الموثقة في `00_architecture_and_rules/architecture.md`.
  - الامتثال لجداول الموديول في `00_architecture_and_rules/database.md#work-schedule-module`.
  - تطبيق قواعد الأعمال: `BR-006` (القالب الافتراضي والتعيين)، `BR-007` (فرادة القالب الافتراضي)، `BR-008` (أيام العمل)، `BR-012` (العطلات الرسمية)، و `BR-015` (تزامن إجازات الاستشاريين).

---

## 2. Models & Database Entities
- **النماذج الرئيسية**:
  1. `App\Models\WorkScheduleTemplate` (`work_schedule_templates`)
  2. `App\Models\WorkScheduleDay` (`work_schedule_days`)
  3. `App\Models\OfficialHoliday` (`official_holidays`)
  4. `App\Models\ConsultantLeave` (`consultant_leaves`)

- **الحقول والعلاقات**:
  - `WorkScheduleTemplate`: `id`, `name`, `description`, `is_default (boolean)`, `created_at`, `updated_at`.
    - العلاقات: `hasMany(WorkScheduleDay::class)`, `hasMany(Consultant::class)`.
  - `WorkScheduleDay`: `id`, `template_id (FK)`, `day_of_week (tinyint 0-6)`, `is_working_day (boolean)`.
  - `OfficialHoliday`: `id`, `name`, `holiday_date (date UNIQUE)`, `notes`.
  - `ConsultantLeave`: `id`, `consultant_id (FK)`, `start_date`, `end_date`, `reason`, `notes`.

---

## 3. Detailed Actions & Operations Logic

### 3.1 صفحة عرض الكل واسترجاع القوالب والعطلات (Index & Listing Operation)
- **المسار المطلوب**: `GET /admin/work-schedules`
- **منطق المعالجة البرمجية**:
  1. استرجاع قوالب الدوام مع أيام العمل المسندة لكل قالب `with('workScheduleDays')`.
  2. جلب جدول العطلات الرسمية المسجلة مرتبة تاريخياً.
  3. جلب قائمة إجازات الاستشاريين الفعالة والقادمة.

### 3.2 عملية الإنشـاء الجديد (Create Operation)
- **قالب دوام جديد (`POST /admin/work-schedules/templates`)**:
  1. إنشاء سجل `WorkScheduleTemplate`.
  2. إنشاء 7 سجلات في `work_schedule_days` تحدد أيام العمل والراحة (0=الأحد إلى 6=السبت) (BR-008).
  3. إذا تم تفعيل `is_default = true` يتم إلغاء الـ default من القوالب الأخرى (BR-007).
- **إضافة عطلة رسمية (`POST /admin/work-schedules/holidays`)**:
  1. التحقق من عدم تكرار `holiday_date` في جدول `official_holidays` (BR-012).
- **إضافة إجازة استشاري (`POST /admin/work-schedules/leaves`)**:
  1. التحقق من صلاحية التواريخ (`start_date <= end_date`).
  2. تحديث حالة الاستشاري تلقائياً إلى `vacation` خلال فترة الإجازة (BR-015).

### 3.3 عملية التحديث والتعديل (Update Operation)
- **المسار المطلوب**: `PUT /admin/work-schedules/templates/{template}`
- **منطق المعالجة البرمجية**:
  1. تحديث اسم القالب والوصف وحالة القالب الافتراضي.
  2. تحديث أيام العمل المسندة في `work_schedule_days`.

### 3.4 عملية الحذف الأرشيفي (Delete Operation)
- **المسار المطلوب**: `DELETE /admin/work-schedules/templates/{template}`
- **منطق المعالجة البرمجية**:
  1. فحص سلامة البيانات: منع حذف أي قالب دوام مسند حالياً لاستشاريين نشطين قبل إعادة تعيين قالب بديل لهم (BR-006).

---

## 4. Repository Layer (`App\Repositories`)
- **الواجهات والتطبيقات**:
  - `WorkScheduleRepositoryInterface` / `WorkScheduleRepository`
  - `ConsultantLeaveRepositoryInterface` / `ConsultantLeaveRepository`
- **الدوال المحددة**:
  ```php
  public function getAllTemplatesWithDays(): Collection;
  public function createTemplateWithDays(array $templateData, array $daysData): WorkScheduleTemplate;
  public function setAsDefaultTemplate(int $templateId): bool;
  public function addOfficialHoliday(array $data): OfficialHoliday;
  public function recordConsultantLeave(array $leaveData): ConsultantLeave;
  ```

---

## 5. Service Layer (`App\Services`)
- **الفئة**: `App\Services\WorkScheduleService`
- **منطق المعاملات والحماية**:
  ```php
  public function createNewScheduleTemplate(array $data): WorkScheduleTemplate
  {
      return DB::transaction(function () use ($data) {
          if (!empty($data['is_default'])) {
              $this->scheduleRepo->resetDefaultTemplates();
          }
          return $this->scheduleRepo->createTemplateWithDays($data, $data['days']);
      });
  }
  ```

---

## 6. Controllers & Form Requests
- **الـ Controllers**: `App\Http\Controllers\Admin\WorkScheduleController`
- **طلب التحقق**: `StoreScheduleTemplateRequest`, `StoreOfficialHolidayRequest`, `StoreConsultantLeaveRequest`.

---

## 7. Testing Standards
- **ملف الاختبار**: `tests/Feature/Admin/WorkScheduleManagementTest.php`
- **الحالات المغطاة**:
  - `test_can_create_template_with_working_days()`
  - `test_only_one_default_template_can_exist()`
  - `test_cannot_delete_template_assigned_to_active_consultants()`
  - `test_adding_leave_updates_consultant_status_to_vacation()`
