# 🎨 Frontend Implementation: Phase 04 — منشئ المهام الديناميكي والتكليفات (Task Builder & Assignments)

---

## 1. Overview & UI Objectives
- **اسم المرحلة**: `phase_04_task_builder`
- **الهدف البصري**: تصميم واجهة منشئ المهام التفاعلي (Interactive Dynamic Task Builder) بأسلوب **Spatial UI v3.0** يمنح مدراء النظام قدرة على بناء الاستبيانات وتحديد الحقول الشرطية الذكية (Smart Adaptive Forms) ومنطقة رفع الإثباتات الحية، وتغطية كل الخصائص الخمس (عرض الدليل، منشئ المهام للإنشاء، التعديل التفاعلي، المعاينة الحية المباشرة، والحذف مع التأكيد).
- **الالتزام بالقواعد البصرية**:
  - الأبعاد والـ Glassmorphic Gradients المعتمدة في `01_design_system_and_components/design_rules.md`.
  - المكونات الموثقة في `01_design_system_and_components/components_catalog.html` (بما فيها `Interactive Image Upload Dropzone` و `Smart Adaptive Form`).

---

## 2. Page Layout & Structure
- **مسار الملف في المشروع**: `resources/views/pages/task_builder/index.blade.php`
- **هيكل الصفحة**:
  - **Header**: العنوان `منشئ المهام وتكليفات الميدان` + زر `+ بناء مهمة جديدة` (يفتح لوحة البناء التفاعلية `#taskBuilderDrawer`).
  - **Filter Bar**: تبويبات سريعة (`الكل`, `المهام اليومية Daily`, `المهام عند الطلب On-Demand`) مع حقل بحث حي بأسماء المهام والمكونات.

---

## 3. Detailed UI States & Component Specifications

### 3.1 🟢 حالة عرض الكل (Task Grid View)
- **جدول / كروت المهام (`SpatialTable` & Cards)**:
  - `عنوان المهمة والوصف`: مع أيقونة نوع المهمة (📅 يومية أو ⚡ عند الطلب).
  - `عدد المكونات والحقول`: عدد الحقول المصممة داخل المهمة.
  - `المواقع المسندة`: شارات بأكواد المواقع (`TR-S-01`, `BEN-S-02`).
  - `حالة التفعيل`: مفتاح تبديل مباشر `spatial-switch` لتفعيل أو إيقاف المهمة.
  - `أزرار الإجراءات الخمسة`: زر المعاينة الحية 👁️, زر التعديل ✏️, وزر الحذف 🗑️.

### 3.2 ➕ خاصية الإنشاء الجديد (Task Builder Canvas)
- **عنصر المشغل**: زر `+ بناء مهمة جديدة`.
- **عنصر الواجهة**: اللوحة التفاعلية الكبيرة `#taskBuilderDrawer` (`w-[800px] lg:w-[960px]`).
- **أقسام منشئ المهام**:
  1. **البيانات الأساسية**:
     - `عنوان المهمة`: حقل `spatial-input h-14`.
     - `نوع المهمة`: اختيار بين `مهمة يومية (Daily)` أو `عند الطلب (On-Demand)`.
  2. **حاوية المكونات الديناميكية (Dynamic Components Builder)**:
     - زر `+ إضافة حقل جديد`.
     - خيارات نوع الحقل (`component_type`):
       - `نص عادي Text` | `رقم Number` | `قائمة اختيار منسدلة Single Select` | `مربع اختيار Checkbox`
       - **`منطقة رفع الإثباتات الحية (Interactive Image Upload Dropzone)`**: منطقة رفع الصور بقطر 18px زجاجية تفاعلية تتيح المعاينة الحية وإسقاط الملفات.
     - **مربعات الخيارات المنسدلة**: عند اختيار `Select` يظهر زر إضافة خيارات (مثل: مطابق، غير مطابق، صيانة).
     - **الحقل الشرطي الذكي (Smart Adaptive Form Builder)**:
       - مربع اختيار `تفعيل حقل شرطي`.
       - قائمة اختيار الحقل الأب (`conditional_parent_id`) والقيمة المطلوبة لإظهار الحقل.
  3. **مصفوفة التكليفات (Assignments Matrix)**:
     - قائمة اختيار المواقع المسندة (`spatial-dropdown-menu` 56px).
     - قائمة اختيار الاستشاريين الميدانيين المسندين.
  4. **زر الحفظ**: `spatial-button w-full h-14` بنص `حفظ وبناء المهمة`.

### 3.3 ✏️ حالة التعديل التفاعلي (Edit Task Canvas)
- **عنصر المشغل**: زر التعديل ✏️.
- **عنصر الواجهة**: نفس اللوحة التفاعلية `#taskBuilderDrawer` بحالة التعديل وتعبئة المكونات والحقول الشرطية وتكليفات المواقع السابقة.

### 3.4 👁️ خاصية عرض التفاصيل والمعاينة الحية (Live Task Preview Modal)
- **عنصر المشغل**: زر المعاينة 👁️.
- **عنصر الواجهة**: النافذة المنبثقة التفاعلية `#taskPreviewModal` (`spatial-modal-card max-w-2xl`).
- **المحتوى التفاعلي**: محاكاة حية 100% لكيفية ظهور المهمة والحقول الشرطية ومنطقة رفع الصور للاستشاري عند تنفيذ الزيارة في الميدان.

### 3.5 🗑️ حالة الحذف الأرشيفي والتأكيد (Delete Task Action)
- **عنصر المشغل**: زر الحذف 🗑️ في الجدول.
- **عنصر الواجهة**: نافذة التأكيد الزجاجية `#deleteTaskModal`.
- **التحذير**: `هل أنت متأكد من أرشفة المهمة [عنوان المهمة]؟ سيتم الاحتفاظ بالإجابات السابقة واستبعاد المهمة من تكليفات الاستشاريين القادمة.`.

---

## 4. JavaScript Logic Functions
```javascript
// فتح منشئ المهام وإضافة حقل ديناميكي جديد
function openTaskBuilder() { openDrawer('taskBuilderDrawer'); }
function addDynamicComponent(type) { appendComponentCard(type); }
function toggleConditionalBuilder(componentId) { toggleElement(`conditional_box_${componentId}`); }

// فتح نافذة المعاينة الحية للمهمة
function previewTaskInteractive(taskId) {
    renderLiveTaskForm(taskId);
    openModal('taskPreviewModal');
}
```
