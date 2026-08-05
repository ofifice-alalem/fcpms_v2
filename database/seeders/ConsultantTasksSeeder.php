<?php

namespace Database\Seeders;

use App\Models\Consultant;
use App\Models\Site;
use App\Models\TaskComponent;
use App\Models\TaskComponentOption;
use App\Models\TaskConsultantAssignment;
use App\Models\TaskDefinition;
use App\Models\TaskSiteAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsultantTasksSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $sites = Site::all();
            $consultants = Consultant::all();

            // 1. CLEAR existing definitions & assignments safely if needed
            // TaskDefinition::query()->delete();

            // 2. DEFINE 5 DAILY TASKS
            $dailyTasks = [
                [
                    'title' => '📋 فحص السلامة والصحة المهنية بموقع العمل',
                    'description' => 'متابعة ومدى التزام العمال والمقاولين باشتراطات السلامة ومعدات الوقاية الشخصية في الموقع.',
                    'components' => [
                        [
                            'label' => 'مدى الالتزام بمعدات الوقاية الشخصية (PPE)',
                            'type' => 'choice',
                            'required' => true,
                            'options' => ['ملتزم بالكامل 🟢', 'التزام متوسط 🟡', 'غير ملتزم 🔴'],
                        ],
                        [
                            'label' => 'ملاحظات وتوصيات السلامة المهنية',
                            'type' => 'text',
                            'required' => false,
                            'placeholder' => 'أدخل أي ملاحظات فنية حول السلامة...',
                        ],
                    ],
                ],
                [
                    'title' => '🏗️ متابعة حضور العمالة والمقاولين بالموقع',
                    'description' => 'حصر إجمالي الكوادر والعمالة المتواجدة بالموقع وتقييم نسبة سير الأعمال.',
                    'components' => [
                        [
                            'label' => 'إجمالي عدد العمالة والمهندسين المتواجدين',
                            'type' => 'number',
                            'required' => true,
                            'placeholder' => 'مثال: 25',
                        ],
                        [
                            'label' => 'نسبة إنجاز الأعمال المخططة لهذا اليوم (%)',
                            'type' => 'number',
                            'required' => true,
                            'placeholder' => 'مثال: 85',
                        ],
                    ],
                ],
                [
                    'title' => '🚚 فحص الجاهزية والآليات الميدانية',
                    'description' => 'متابعة حالة المعدات والآليات الثقيلة الشغالة والمتوقفة بالمشروع.',
                    'components' => [
                        [
                            'label' => 'حالة تشغيل المعدات والآليات بالموقع',
                            'type' => 'select',
                            'required' => true,
                            'options' => ['تعمل بكفاءة عالية', 'توجد معدات بحاجة لصيانة', 'توقف آليات رئيسية'],
                        ],
                        [
                            'label' => 'بيانات وآليات الصيانة المطلوبة',
                            'type' => 'text',
                            'required' => false,
                            'placeholder' => 'اذكر أرقام أو أنواع المعدات المتوقفة...',
                        ],
                    ],
                ],
                [
                    'title' => '📄 سجل توثيق أعمال الخرسانات والإنشاءات',
                    'description' => 'متابعة كميات الصب المنجزة ونتائج فحوصات القوام.',
                    'components' => [
                        [
                            'label' => 'كمية الخرسانة المصبوبة اليوم (متر مكعب m³)',
                            'type' => 'number',
                            'required' => false,
                            'placeholder' => 'مثال: 120',
                        ],
                        [
                            'label' => 'نتيجة اختبار الهبوط Slump Test',
                            'type' => 'text',
                            'required' => false,
                            'placeholder' => 'مثال: 12 cm - مطابقة للمواصفات',
                        ],
                    ],
                ],
                [
                    'title' => '🔍 مراقبة جودة المواد الموردة للموقع',
                    'description' => 'التأكد من التوريدات اليومية من أسمنت وحصى وحديد ومطابقتها للمواصفات.',
                    'components' => [
                        [
                            'label' => 'مطابقة التوريدات اليومية للمواصفات المعتمدة',
                            'type' => 'choice',
                            'required' => true,
                            'options' => ['مطابقة بالكامل ✅', 'مرفوضة جزئياً ⚠️', 'مرفوضة بالكامل ❌'],
                        ],
                        [
                            'label' => 'ملاحظات جودة مواد البناء الموردة',
                            'type' => 'text',
                            'required' => false,
                            'placeholder' => 'اكتب التفاصيل الخاصة بالتوريدات...',
                        ],
                    ],
                ],
            ];

            // 3. DEFINE 9 ON-DEMAND TASKS
            $onDemandTasks = [
                [
                    'title' => '⚠️ تقرير رصد مخالفة فنية في الموقع',
                    'description' => 'توثيق المخالفات والتجاوزات التنفيذية التي تستوجب التصحيح من قبل المقاول.',
                    'components' => [
                        [
                            'label' => 'تصنيف نوع المخالفة الفنية',
                            'type' => 'select',
                            'required' => true,
                            'options' => ['مخالفة مواصفات ومخططات', 'تأخير غير مبرر', 'خطورة على السلامة العامة'],
                        ],
                        [
                            'label' => 'وصف المخالفة والإجراء التصحيحي المطلوب',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'اشرح تفاصيل المخالفة والمكان الدقيق...',
                        ],
                    ],
                ],
                [
                    'title' => '📸 توثيق صور وعينات الأعمال العاجلة',
                    'description' => 'رفع إثباتات صورية وفنية لمرحلة تنفيذية معينة.',
                    'components' => [
                        [
                            'label' => 'عنوان أو موضوع الصورة الإثباتية',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'مثال: فحص حديد تسليح الجسر الأوسط',
                        ],
                        [
                            'label' => 'التقرير المرفق وتوضيحات الاستشاري',
                            'type' => 'text',
                            'required' => false,
                            'placeholder' => 'أدخل الملاحظات والتعليق الفني...',
                        ],
                    ],
                ],
                [
                    'title' => '🛑 طلب إيقاف عمل مؤقت لمرحلة تنفيذية',
                    'description' => 'إيقاف الأعمال في بند معين نتيجة خطأ فني أو خطورة عالية.',
                    'components' => [
                        [
                            'label' => 'سبب طلب الإيقاف الفوري',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'اكتب الأسباب والعيوب الفنية بشكل واضح...',
                        ],
                        [
                            'label' => 'المدة المتوقعة للايقاف (بالساعات)',
                            'type' => 'number',
                            'required' => true,
                            'placeholder' => 'مثال: 24',
                        ],
                    ],
                ],
                [
                    'title' => '🌧️ تقرير الأحوال الجوية والتأثيرات البيئية',
                    'description' => 'توثيق العوامل الجوية كالطقس والأمطار وتأثيرها على سير العمل.',
                    'components' => [
                        [
                            'label' => 'حالة الطقس الميداني',
                            'type' => 'choice',
                            'required' => true,
                            'options' => ['طبيعي / مشمس ☀️', 'أمطار وجريان 🌧️', 'رياح شديدة / غبار 🌪️', 'حرارة مرتفعة جداً 🌡️'],
                        ],
                        [
                            'label' => 'مدى تأثر الأعمال الميدانية بالطقس',
                            'type' => 'select',
                            'required' => true,
                            'options' => ['لم يتأثر سير العمل', 'توقف جزئي لبنود معينة', 'توقف كلي وتام للموقع'],
                        ],
                    ],
                ],
                [
                    'title' => '🔄 طلب تعديل خريطة أو مخطط تنفيذي',
                    'description' => 'رفع اقتراح أو طلب تعديل هندسي على المخطط التفصيلي.',
                    'components' => [
                        [
                            'label' => 'رقم أو اسم المخطط المرد تعديله',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'مثال: DWG-STR-104',
                        ],
                        [
                            'label' => 'تفاصيل التعديل المطلوب ومبرراته',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'اشرح سبب التعديل الهندسي المقترح...',
                        ],
                    ],
                ],
                [
                    'title' => '📦 فحص وتفقد المواد المخزنة بالموقع',
                    'description' => 'التفتيش على مخازن الموقع ومواد البناء والحماية من التلف.',
                    'components' => [
                        [
                            'label' => 'تقييم حالة وسُبل التخزين بالموقع',
                            'type' => 'choice',
                            'required' => true,
                            'options' => ['تخزين آمن وممتاز 🟢', 'تحتاج زيادة عزل وتغطية 🟡', 'تخزين سيء وتلف مواد 🔴'],
                        ],
                        [
                            'label' => 'ملاحظات المواد التالفة أو المعرضة للخطر',
                            'type' => 'text',
                            'required' => false,
                            'placeholder' => 'اذكر المواد وكمياتها المعرضة للتلف...',
                        ],
                    ],
                ],
                [
                    'title' => '🧪 طلب أخذ عينات خرسانة أو حديد للمختبر',
                    'description' => 'تجهيز سحب عينات الفحص المعملي المعتمد.',
                    'components' => [
                        [
                            'label' => 'نوع العينات والموقع الميداني الدقيق',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'مثال: مكعبات خرسانية - أعمدة الدور الأول',
                        ],
                        [
                            'label' => 'اسم المختبر الهندسي المعتمد للفحص',
                            'type' => 'text',
                            'required' => false,
                            'placeholder' => 'مثال: المختبر الوطني للبحوث الهندسية',
                        ],
                    ],
                ],
                [
                    'title' => '🤝 اجتماع تنسيقي طارئ مع المهندس المقيم',
                    'description' => 'تسجيل مخرجات وقرارات الاجتماع الميداني الطارئ.',
                    'components' => [
                        [
                            'label' => 'الموضوع الرئيسي للاجتماع الطارئ',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'مثال: مناقشة مسار خطوط الخدمات الكهربائية',
                        ],
                        [
                            'label' => 'التوصيات والقرارات الحاسمة المتفق عليها',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'اكتب ما تم الاتفاق عليه للتنفيذ...',
                        ],
                    ],
                ],
                [
                    'title' => '📝 ملاحظات واستشارات إضافية للاستشاري',
                    'description' => 'تقديم استشارة فنية أو توجيهات إضافية بناءً على الطلب.',
                    'components' => [
                        [
                            'label' => 'موضوع الاستشارة الفنية الميدانية',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'مثال: معالجة فواصل التمدد الإنشائية',
                        ],
                        [
                            'label' => 'التفاصيل والتوجيهات الفنية الكاملة',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'أدخل الخطوات والتوجيهات الموصى بها...',
                        ],
                    ],
                ],
            ];

            // helper function to seed tasks
            $createTask = function (array $data, string $taskType, int &$order) use ($sites, $consultants) {
                $taskDef = TaskDefinition::create([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'task_type' => $taskType,
                    'is_active' => true,
                    'display_order' => $order++,
                ]);

                // Create Components
                $compOrder = 1;
                foreach ($data['components'] as $compData) {
                    $component = TaskComponent::create([
                        'task_definition_id' => $taskDef->id,
                        'component_type' => $compData['type'],
                        'label' => $compData['label'],
                        'placeholder' => $compData['placeholder'] ?? null,
                        'display_order' => $compOrder++,
                        'is_required' => $compData['required'] ?? false,
                    ]);

                    // Add options if any
                    if (!empty($compData['options'])) {
                        $optOrder = 1;
                        foreach ($compData['options'] as $optLabel) {
                            TaskComponentOption::create([
                                'task_component_id' => $component->id,
                                'option_label' => $optLabel,
                                'option_value' => $optLabel,
                                'display_order' => $optOrder++,
                            ]);
                        }
                    }
                }

                // Link to ALL SITES with no exception
                foreach ($sites as $site) {
                    TaskSiteAssignment::firstOrCreate([
                        'task_definition_id' => $taskDef->id,
                        'site_id' => $site->id,
                    ]);
                }

                // Link to ALL CONSULTANTS with no exception
                foreach ($consultants as $consultant) {
                    TaskConsultantAssignment::firstOrCreate([
                        'task_definition_id' => $taskDef->id,
                        'consultant_id' => $consultant->id,
                    ]);
                }
            };

            $order = 1;

            // Seed Daily Tasks
            foreach ($dailyTasks as $dt) {
                $createTask($dt, 'daily', $order);
            }

            // Seed On-Demand Tasks
            foreach ($onDemandTasks as $odt) {
                $createTask($odt, 'on_demand', $order);
            }
        });
    }
}
