# ⚙️ Backend Implementation: Phase 02 — إدارة الاستشاريين الميدانيين (Consultants Management)

---

## 1. Overview & Scope
- **اسم المرحلة**: `phase_02_consultants`
- **الهدف التنفيذي**: بناء المنطق البرمجي الكامل لإدارة الاستشاريين الميدانيين وتغطية دورة الحياة الكاملة للحسابات: (الإنشاء الموحد مع حساب المستخدم، التعديل الدوري للبيانات والتخصص، تغيير حالة التوظيف التشغيلية، والحذف الآمن عبر SoftDeletes).
- **التزام المعمارية**:
  - الالتزام المعماري بنمط `Repository-Service Pattern` ومعيار `Strict Mode` من `00_architecture_and_rules/architecture.md`.
  - الامتثال لجدول `consultants` و `users` الموثق في `00_architecture_and_rules/database.md#consultants`.
  - الالتزام بقواعد الأعمال: `BR-003` (فرادة الرقم الوظيفي `#EMP-XXXX`)، `BR-005` (إدارة الملف والحالة)، و `BR-015` (التعليق الآمن وتأثيره على المهام الجارية).

---

## 2. Models & Database Entities
- **النموذج الرئيسي**: `App\Models\Consultant`
- **جدول البيانات**: `consultants`
- **الحقول المعالجة**:
  - `id`: `bigint PK`
  - `user_id`: `bigint FK → users UNIQUE` (ربط الاستشاري بحساب المستخدم)
  - `employee_number`: `varchar UNIQUE` (الرقم الوظيفي المرجعي #EMP-XXXX - BR-003)
  - `full_name`: `varchar` (الاسم الكامل)
  - `phone`: `varchar NULL` (رقم الهاتف)
  - `hire_date`: `date NULL` (تاريخ التعيين)
  - `specialization`: `varchar NULL` (التخصص الميداني)
  - `work_schedule_template_id`: `bigint FK → work_schedule_templates NULL`
  - `employment_status`: `enum('active', 'suspended', 'vacation')` (حالة التوظيف - BR-005)
  - `notes`: `text NULL`
  - `created_at`, `updated_at`, `deleted_at`: `timestamps` (مع `SoftDeletes`)
- **العلاقات (Relationships)**:
  - `user()`: `belongsTo(User::class)`
  - `workScheduleTemplate()`: `belongsTo(WorkScheduleTemplate::class)`
  - `dailyRecords()`: `hasMany(DailyRecord::class)`
  - `consultantLeaves()`: `hasMany(ConsultantLeave::class)`

---

## 3. Detailed Actions & Operations Logic

### 3.1 عملية الإنشـاء الجديد (Create Operation)
- **المسار المطلوب**: `POST /admin/consultants`
- **منطق المعالجة البرمجية (Database Transaction)**:
  1. فتح معاملة بيانات DB Transaction.
  2. التوليد التلقائي للرقم الوظيفي `#EMP-XXXX` بناءً على التسلسل وتدقيق فرادته (BR-003).
  3. إنشاء حساب `User` جديد في جدول `users` بالبريد وكلمة مرور ابتدائية عشوائية وتعيين `status = 'active'`.
  4. منح الحساب دور `Consultant` عبر Spatie Permissions.
  5. إنشاء سجل `Consultant` المرتبط بـ `user_id` وتخزين التخصص والهاتف وتاريخ التعيين.
  6. اعتماد المعاملة (DB Commit) وإرجاع السجل المنشأ.

### 3.2 عملية التحديث والتعديل (Update Operation)
- **المسار المطلوب**: `PUT /admin/consultants/{consultant}`
- **منطق المعالجة البرمجية**:
  1. التحقق من صلاحية `edit-consultants`.
  2. تحديث الاسم الكامل ورقم الهاتف وتعديل بيانات `user` المرتبط إذا تم تغيير البريد الإلكتروني.
  3. تجميد وعدم السماح بتعديل `employee_number` للحفاظ على السلامة التاريخية للمستندات.
  4. حفظ التغييرات وتوليد حدث `ConsultantUpdatedEvent`.

### 3.3 عملية تغيير حالة التوظيف (Update Status Operation)
- **المسار المطلوب**: `PATCH /admin/consultants/{consultant}/status`
- **منطق المعالجة البرمجية**:
  1. استقبال الحالة الجديدة (`active`, `suspended`, `vacation`).
  2. تطبيق **BR-015**: إذا أصبحت الحالة `suspended` أو `vacation` يتم:
     - إلغاء تفعيل جلسة المستخدم الحالية (Revoke Tokens / Flush Sessions).
     - تحديث حالة الـ User الموازي `status = 'inactive'` لمنع تسجيل الدخول أثناء فترة التعليق.
  3. إرجاع النتيجة وتحديث الشارة البصرية فورا.

### 3.4 عملية الحذف الأرشيفي (Delete Operation)
- **المسار المطلوب**: `DELETE /admin/consultants/{consultant}`
- **منطق المعالجة البرمجية**:
  1. التحقق من صلاحية `delete-consultants` وإجراء فحص الأمان (BR-022): التأكد من عدم وجود زيارات ميدانية معلقة لم يتم اعتمادها.
  2. تطبيق **SoftDelete** على سجل `Consultant` وعلى حساب `User` المرتبط به.
  3. عدم حذف أي سجلات زيارات تاريخية لضمان سلامة تقارير الأداء والأرشيف المالي.

---

## 4. Repository Layer (`App\Repositories`)
- **الواجهة**: `App\Repositories\Contracts\ConsultantRepositoryInterface`
- **التطبيق**: `App\Repositories\Eloquent\ConsultantRepository`
- **الدوال المحددة**:
  ```php
  public function getAllPaginated(array $filters, int $perPage = 15): LengthAwarePaginator;
  public function findByEmployeeNumber(string $empNum): ?Consultant;
  public function createWithUserAccount(array $consultantData, array $userData): Consultant;
  public function updateConsultant(Consultant $consultant, array $data): bool;
  public function changeStatus(Consultant $consultant, string $status): bool;
  public function softDeleteConsultant(Consultant $consultant): bool;
  ```

---

## 5. Service Layer (`App\Services`)
- **الفئة**: `App\Services\ConsultantService`
- **إدارة المعاملات**:
  ```php
  public function registerNewConsultant(array $data): Consultant
  {
      return DB::transaction(function () use ($data) {
          // 1. Generate Employee Number
          $data['employee_number'] = $this->generateUniqueEmpNumber();
          
          // 2. Create User Account
          $user = $this->userRepo->create([
              'name' => $data['full_name'],
              'email' => $data['email'],
              'password' => bcrypt(Str::random(12)),
              'status' => 'active',
          ]);
          $user->assignRole('Consultant');
          
          // 3. Create Consultant Entity
          $data['user_id'] = $user->id;
          return $this->consultantRepo->create($data);
      });
  }
  ```

---

## 6. Controllers & Form Requests
- **الـ Controller**: `App\Http\Controllers\Admin\ConsultantController`
- **طلبات التحقق (Form Requests)**:
  - `StoreConsultantRequest`
  - `UpdateConsultantRequest`
  - `ChangeConsultantStatusRequest`

---

## 7. Testing Standards
- **ملف الاختبار**: `tests/Feature/Admin/ConsultantManagementTest.php`
- **الحالات المغطاة**:
  - `test_can_create_consultant_with_user_account()`
  - `test_can_update_consultant_profile()`
  - `test_changing_status_to_suspended_revokes_sessions()`
  - `test_soft_deleting_consultant_preserves_historical_records()`
