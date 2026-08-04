# ⚙️ Backend Implementation: Phase 01 — إدارة المواقع الميدانية (Sites & Locations)

---

## 1. Overview & Scope
- **اسم المرحلة**: `phase_01_sites_and_locations`
- **الهدف التنفيذي**: بناء المنطق البرمجي الكامل لإدارة دليل المواقع والمنشآت الميدانية وتغطية كافة العمليات والإجراءات: (الإنشاء المباشر برمز فريد، التعديل والتحديث الدوري، تغيير حالة التشغيل، أرشفة وحذف الموقع، واسترجاع التفاصيل).
- **التزام المعمارية**:
  - الالتزام بنمط `Repository-Service Pattern` وحالة `Strict Mode` الموثقة في `00_architecture_and_rules/architecture.md`.
  - الامتثال لجدول `sites` الموثق في `00_architecture_and_rules/database.md#sites-module`.
  - تطبيق قواعد الأعمال الخاصة بالمواقع: `BR-020`, `BR-021`, `BR-022`, `BR-023` من `00_architecture_and_rules/business_rules.md`.

---

## 2. Models & Database Entities
- **النموذج الرئيسي**: `App\Models\Site`
- **جدول البيانات**: `sites`
- **الحقول المعالجة**:
  - `id`: `bigint PK`
  - `code`: `varchar UNIQUE` (رمز فريد للموقع الميداني - BR-020)
  - `name`: `varchar` (اسم الموقع/المنشأة)
  - `address`: `text NULL` (العنوان التفصيلي)
  - `city`: `varchar NULL` (المدينة/المنطقة)
  - `status`: `enum('active', 'inactive')` (الحالة التشغيلية للموقع - BR-021)
  - `notes`: `text NULL` (ملاحظات هندسية/ميدانية)
  - `created_at`, `updated_at`, `deleted_at`: `timestamps` (مع `SoftDeletes`)
- **العلاقات (Relationships)**:
  - `siteVisits()`: `hasMany(SiteVisit::class)`
  - `taskSiteAssignments()`: `hasMany(TaskSiteAssignment::class)`

---

## 3. Detailed Actions & Operations Logic

### 3.1 عملية الإنشـاء الجديد (Create Site Operation)
- **المسار المطلوب**: `POST /admin/sites`
- **منطق المعالجة البرمجية**:
  1. التحقق من صلاحية `create-sites`.
  2. تطبيق **BR-020**: التحقق الفوري والفريد لرمز الموقع `code` والتأكد من عدم تكراره.
  3. تعيين الحالة التشغيلية الابتدائية `status = 'active'` بشكل افتراضي.
  4. حفظ السجل وإرجاع النتيجة وتوليد حدث `SiteCreatedEvent`.

### 3.2 عملية التحديث والتعديل (Update Site Operation)
- **المسار المطلوب**: `PUT /admin/sites/{site}`
- **منطق المعالجة البرمجية**:
  1. التحقق من صلاحية `edit-sites`.
  2. التثبيت وعدم تعديل `code` الموقع إذا كان لديه زيارات سابقة للحفاظ على السلامة التاريخية للمستندات.
  3. تحديث الاسم والمدينة والعنوان والملاحظات الهندسية وحفظ التغييرات.

### 3.3 عملية تغيير الحالة التشغيلية (Toggle Status Operation)
- **المسار المطلوب**: `PATCH /admin/sites/{site}/toggle-status`
- **منطق المعالجة البرمجية**:
  1. استقبال الحالة الجديدة (`active` أو `inactive`).
  2. تطبيق **BR-021**: عند تحويل حالة الموقع إلى `inactive` يمنع النظام فورا إسناد أي مهام ميدانية جديدة لهذا الموقع.
  3. إرجاع النتيجة وتحديث الشارة البصرية.

### 3.4 عملية الحذف الأرشيفي (Delete Site Operation)
- **المسار المطلوب**: `DELETE /admin/sites/{site}`
- **منطق المعالجة البرمجية**:
  1. التحقق من صلاحية `delete-sites`.
  2. تطبيق **BR-022**: فحص السلامة قبل الحذف، بحيث يمنع النظام حذف أو تعطيل أي موقع مركب إذا كان لديه زيارات ميدانية جارية أو معلقة لم تُعتمد بعد.
  3. تطبيق **SoftDelete** آمن على سجل الموقع بدون مسح أرشيف الزيارات التاريخية المرتبطة به.

---

## 4. Repository Layer (`App\Repositories`)
- **الواجهة**: `App\Repositories\Contracts\SiteRepositoryInterface`
- **التطبيق**: `App\Repositories\Eloquent\SiteRepository`
- **الدوال المحددة**:
  ```php
  public function getAllPaginated(array $filters, int $perPage = 15): LengthAwarePaginator;
  public function findByCode(string $code): ?Site;
  public function createSite(array $data): Site;
  public function updateSite(Site $site, array $data): bool;
  public function toggleStatus(Site $site, string $status): bool;
  public function softDeleteSite(Site $site): bool;
  ```

---

## 5. Service Layer (`App\Services`)
- **الفئة**: `App\Services\SiteService`
- **تطبيق قواعد BRs**:
  ```php
  public function deactiveOrDeleteSite(Site $site): bool
  {
      // BR-022: Check active visits
      if ($site->siteVisits()->where('status', 'pending')->exists()) {
          throw new BusinessRuleValidationException("لا يمكن حذف أو تعطيل موقع لديه زيارات معلقة.");
      }
      return $this->siteRepo->softDeleteSite($site);
  }
  ```

---

## 6. Controllers & Form Requests
- **الـ Controller**: `App\Http\Controllers\Admin\SiteController`
- **طلبات التحقق (Form Requests)**:
  - `StoreSiteRequest`
  - `UpdateSiteRequest`

---

## 7. Testing Standards
- **ملف الاختبار**: `tests/Feature/Admin/SiteManagementTest.php`
- **الحالات المغطاة**:
  - `test_can_create_site_with_unique_code()`
  - `test_cannot_delete_site_with_pending_visits()`
  - `test_deactivating_site_prevents_new_task_assignments()`
