# قاعدة البيانات — المرجع الشامل

---

## نظرة عامة

| الوحدة | الجداول |
|--------|---------|
| Users Module | `users`, `consultants` |
| Work Schedule Module | `work_schedule_templates`, `work_schedule_days`, `official_holidays`, `consultant_leaves` |
| Sites Module | `sites` |
| Task Builder Module | `task_definitions`, `task_components`, `task_component_options`, `task_site_assignments`, `task_consultant_assignments` |
| Daily Activity Module | `daily_records`, `site_visits` |
| Task Execution Module | `task_responses`, `task_response_values`, `task_attachments` |
| System Module | `settings`, `activity_logs` |

---

## تدفق البيانات

```
HR ينشئ:
  Sites → Tasks → Task Components → Task Component Options
  Work Schedule Templates → Work Schedule Days
  Consultants (مرتبط بـ User)

الاستشاري يعمل:
  Login → Daily Record (واحد في اليوم)
         → Site Visit (موقع واحد أو أكثر)
                    → Task Response (مهمة واحدة أو أكثر)
                              → Task Response Values (قيمة لكل عنصر)
                              → Task Attachments (صور وملفات)
```

---

## Users Module

### `users`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| name | varchar | |
| username | varchar UNIQUE | |
| email | varchar UNIQUE | |
| password | varchar | bcrypt |
| status | enum | `active`, `inactive` |
| last_login_at | timestamp NULL | |
| created_at / updated_at | timestamp | |
| deleted_at | timestamp NULL | SoftDelete |

**العلاقات:** `hasOne Consultant`

---

### `consultants`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| user_id | bigint FK → users | UNIQUE |
| employee_number | varchar UNIQUE | |
| full_name | varchar | |
| phone | varchar NULL | |
| hire_date | date NULL | |
| specialization | varchar NULL | |
| work_schedule_template_id | bigint FK NULL | |
| employment_status | enum | `active`, `suspended`, `vacation` |
| notes | text NULL | |
| created_at / updated_at | timestamp | |
| deleted_at | timestamp NULL | SoftDelete |

**العلاقات:** `belongsTo User`, `belongsTo WorkScheduleTemplate`, `hasMany DailyRecord`, `hasMany ConsultantLeave`

**قواعد العمل:** BR-003, BR-005, BR-015

---

## Work Schedule Module

### `work_schedule_templates`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| name | varchar | مثال: "دوام كامل" |
| description | text NULL | |
| is_default | boolean | default: false |
| created_at / updated_at | timestamp | |

**العلاقات:** `hasMany WorkScheduleDay`, `hasMany Consultant`

**قواعد العمل:** BR-006, BR-007, BR-009

---

### `work_schedule_days`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| template_id | bigint FK → work_schedule_templates | |
| day_of_week | tinyint | 0=الأحد ... 6=السبت |
| is_working_day | boolean | |

**قواعد العمل:** BR-008, BR-010

---

### `official_holidays`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| name | varchar | |
| holiday_date | date UNIQUE | |
| notes | text NULL | |

**قواعد العمل:** BR-012, BR-013, BR-014

---

### `consultant_leaves`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| consultant_id | bigint FK → consultants | |
| start_date | date | |
| end_date | date | |
| reason | varchar NULL | |
| notes | text NULL | |
| created_at | timestamp | |

**قواعد العمل:** BR-015, BR-016, BR-017

---

## Sites Module

### `sites`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| code | varchar UNIQUE | |
| name | varchar | |
| address | text NULL | |
| city | varchar NULL | |
| status | enum | `active`, `inactive` |
| notes | text NULL | |
| created_at / updated_at | timestamp | |

**العلاقات:** `hasMany SiteVisit`, `hasMany TaskSiteAssignment`

**قواعد العمل:** BR-020, BR-021, BR-022, BR-023

---

## Task Builder Module

### `task_definitions`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| title | varchar | |
| description | text NULL | |
| task_type | enum | `daily`, `on_demand` |
| is_active | boolean | default: true |
| display_order | int | default: 0 |
| created_by | bigint FK → users NULL | |
| created_at / updated_at | timestamp | |

**العلاقات:** `hasMany TaskComponent`, `hasMany TaskSiteAssignment`, `hasMany TaskConsultantAssignment`, `hasMany TaskResponse`

**قواعد العمل:** BR-027, BR-028, BR-029, BR-030, BR-031

---

### `task_components`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| task_definition_id | bigint FK | |
| component_type | enum | `choice`, `text`, `image` |
| label | varchar | |
| placeholder | varchar NULL | |
| display_order | int | default: 0 |
| is_required | boolean | default: false |
| visibility_component_id | bigint FK NULL | العنصر الأب للـ conditional |
| visibility_option_id | bigint FK NULL | الخيار الذي يُظهر هذا العنصر |
| created_at | timestamp | |

**العلاقات:** `belongsTo TaskDefinition`, `hasMany TaskComponentOption`

**قواعد العمل:** BR-032, BR-033, BR-034, BR-035

---

### `task_component_options`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| task_component_id | bigint FK | |
| option_label | varchar | النص الظاهر |
| option_value | varchar | القيمة المخزنة |
| display_order | int | default: 0 |
| is_default | boolean | default: false |
| created_at | timestamp | |

---

### `task_site_assignments`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| task_definition_id | bigint FK | |
| site_id | bigint FK | |
| created_at | timestamp | |

> إذا لم يوجد سجل → المهمة عامة لجميع المواقع

**قواعد العمل:** BR-036, BR-037, BR-038, BR-039

---

### `task_consultant_assignments`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| task_definition_id | bigint FK | |
| consultant_id | bigint FK | |
| created_at | timestamp | |

> إذا لم يوجد سجل → المهمة متاحة لجميع الاستشاريين

**قواعد العمل:** BR-036-A, BR-036-B

---

## Daily Activity Module

### `daily_records`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| consultant_id | bigint FK | |
| work_date | date | |
| required_daily_tasks | int | مشتق مخزن |
| completed_daily_tasks | int | مشتق مخزن |
| completion_percentage | decimal(5,2) | مشتق مخزن — يُحسب بـ PerformanceCalculationService |
| created_at / updated_at | timestamp | |

> UNIQUE على (consultant_id, work_date)

**العلاقات:** `belongsTo Consultant`, `hasMany SiteVisit`

**قواعد العمل:** BR-024, BR-025, BR-026

---

### `site_visits`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| daily_record_id | bigint FK | |
| site_id | bigint FK | |
| visit_started_at | timestamp NULL | |
| visit_finished_at | timestamp NULL | |
| notes | text NULL | |
| created_at / updated_at | timestamp | |

> UNIQUE على (daily_record_id, site_id) — BR-023

**العلاقات:** `belongsTo DailyRecord`, `belongsTo Site`, `hasMany TaskResponse`

**قواعد العمل:** BR-020, BR-021, BR-022, BR-023, BR-056

---

## Task Execution Module

### `task_responses`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| site_visit_id | bigint FK | |
| task_definition_id | bigint FK | |
| completed_at | timestamp NULL | |
| created_at | timestamp | |

**العلاقات:** `belongsTo SiteVisit`, `belongsTo TaskDefinition`, `hasMany TaskResponseValue`, `hasMany TaskAttachment`

**قواعد العمل:** BR-040, BR-045, BR-046

---

### `task_response_values`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| task_response_id | bigint FK → task_responses | |
| task_component_id | bigint FK → task_components | |
| value | text | القيمة المدخلة |
| created_at | timestamp | |

**العلاقات:** `belongsTo TaskResponse`, `belongsTo TaskComponent`

**قواعد العمل:** BR-032, BR-033, BR-034, BR-035, BR-052, BR-053

---

### `task_attachments`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| task_response_id | bigint FK → task_responses | |
| file_name | varchar | |
| file_path | varchar | مسار التخزين فقط |
| mime_type | varchar NULL | |
| file_size | int NULL | بالبايت |
| uploaded_at | timestamp | |

**العلاقات:** `belongsTo TaskResponse`

**قواعد العمل:** BR-049, BR-050, BR-051

---

## System Module

### `settings`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| setting_key | varchar UNIQUE | |
| setting_value | text NULL | |
| description | text NULL | |
| updated_at | timestamp | |

**العلاقات:** لا توجد علاقات مباشرة

---

### `activity_logs`
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| id | bigint PK | |
| user_id | bigint FK → users NULL | |
| action | varchar | مثال: `created`, `updated`, `deleted` |
| entity_type | varchar | مثال: `Task`, `Site` |
| entity_id | bigint NULL | |
| description | text NULL | |
| ip_address | varchar NULL | |
| created_at | timestamp | |

**العلاقات:** `belongsTo User`

---

## قواعد احتساب الأداء

```
ترتيب التحقق اليومي (BR-018):
1. هل اليوم عطلة رسمية؟       → لا غياب
2. هل اليوم ضمن جدول العمل؟   → إذا لا، لا غياب
3. هل الاستشاري في إجازة؟     → لا غياب
4. هل يوجد نشاط مسجل؟        → حاضر
5. لا شيء من السابق           → غياب

نسبة الإنجاز (BR-043, BR-058):
completion_percentage = (completed_daily_tasks / required_daily_tasks) * 100
تُحسب فقط من المهام اليومية (daily)
المهام on_demand لا تدخل في الحساب
```

---

## ملخص العلاقات

```
User ──────────────── Consultant
                          │
              ┌───────────┼───────────┐
              │           │           │
    WorkSchedule    ConsultantLeave  DailyRecord
                                         │
                                    SiteVisit ──── Site
                                         │
                                    TaskResponse ── TaskDefinition
                                         │               │
                               ┌─────────┴──────┐   TaskComponent
                               │                │        │
                    TaskResponseValues  TaskAttachments  TaskComponentOption
```

---

## إحصاءات قاعدة البيانات

| الوحدة | عدد الجداول |
|--------|------------:|
| Users Module | 2 |
| Work Schedule Module | 4 |
| Sites Module | 1 |
| Task Builder Module | 5 |
| Daily Activity Module | 2 |
| Task Execution Module | 3 |
| System Module | 2 |
| **الإجمالي** | **19 جدولاً** |
