<template>
  <HRLayout :title="isEditing ? 'تعديل المهمة الميدانية' : 'بناء مهمة ميدانية جديدة'">
    <div class="space-y-6 max-w-7xl mx-auto pb-16">
      
      <!-- Top Bar / Action Header -->
      <SpatialCard padding="p-6" class="relative z-40">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-white/60 mb-1">
              <Link :href="route('admin.tasks.index')" class="hover:text-primary transition-colors">
                منشئ المهام والتكليفات
              </Link>
              <span>/</span>
              <span class="text-slate-900 dark:text-white">
                {{ isEditing ? 'تعديل بنية المهمة' : 'بناء مهمة جديدة' }}
              </span>
            </div>

            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
              <span>{{ isEditing ? `تعديل: ${form.title || 'مهمة بدون عنوان'}` : 'منصة تصميم الاستبيانات والمهام الميدانية' }}</span>
              <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-bold border border-primary/20 font-mono">
                BR-027 / BR-031
              </span>
            </h1>
          </div>

          <div class="flex items-center gap-3">
            <Link :href="route('admin.tasks.index')">
              <SpatialButton variant="ghost" size="md">
                ← إلغاء والعودة
              </SpatialButton>
            </Link>

            <SpatialButton
              variant="primary"
              size="md"
              :loading="form.processing"
              @click="handleSubmit"
            >
              {{ isEditing ? 'حفظ التعديلات' : 'حفظ ونشر المهمة الميدانية' }}
            </SpatialButton>
          </div>
        </div>
      </SpatialCard>

      <!-- Main Section Grid (Form Canvas vs Sticky Live Preview Panel) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Main Form Canvas (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

          <!-- Section 1: Basic Information -->
          <SpatialCard padding="p-6" class="relative z-30 space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-black/10 dark:border-white/10">
              <span class="w-9 h-9 rounded-2xl bg-primary/10 text-primary font-black flex items-center justify-center text-sm shadow-sm">
                1
              </span>
              <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white">البيانات الأساسية ونوع التشغيل</h3>
                <p class="text-xs font-bold text-slate-500 dark:text-white/60">عنوان المهمة وتحديد ما إذا كانت دورية أو عند الطلب</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-1.5 md:col-span-2">
                <SpatialInput
                  v-model="form.title"
                  label="عنوان المهمة الميدانية *"
                  placeholder="مثال: تفقد أجهزة السلامة ومطابقة المخزون"
                  :error="errors.title"
                />
              </div>

              <div class="space-y-1.5 md:col-span-2">
                <label class="text-xs font-black text-slate-900 dark:text-white/90 block">الوصف التوجيهي للاستشاري الميداني</label>
                <textarea
                  v-model="form.description"
                  rows="3"
                  placeholder="اكتب تعليمات واضحة تشرح طريقة التنفيذ والإثبات المطلوب..."
                  class="w-full p-3.5 rounded-2xl bg-white dark:bg-[#121a2c] border border-black/10 dark:border-white/10 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-primary outline-none transition-all resize-none shadow-xs"
                ></textarea>
              </div>

              <div>
                <SpatialDropdown
                  v-model="form.task_type"
                  label="نوع وتكرار المهمة (BR-027)"
                  :options="taskTypeOptions"
                />
              </div>

              <div class="flex flex-col justify-end">
                <div
                  @click="form.is_active = !form.is_active"
                  :class="[
                    'h-[52px] px-4 rounded-2xl border flex items-center justify-between cursor-pointer select-none transition-all',
                    form.is_active
                      ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400'
                      : 'bg-white dark:bg-[#121a2c] border-black/10 dark:border-white/10 text-slate-500 dark:text-white/50'
                  ]"
                >
                  <span class="text-xs font-black">حالة التفعيل التشغيلي</span>
                  <SpatialCheckbox v-model="form.is_active" />
                </div>
              </div>
            </div>
          </SpatialCard>

          <!-- Section 2: Dynamic Form Builder -->
          <SpatialCard padding="p-6" class="relative z-20 space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-black/10 dark:border-white/10">
              <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-2xl bg-purple-500/10 text-purple-500 font-black flex items-center justify-center text-sm shadow-sm">
                  2
                </span>
                <div>
                  <h3 class="text-sm font-black text-slate-900 dark:text-white">تصميم عناصر الاستبيان والشروط الذكية (Form Builder)</h3>
                  <p class="text-xs font-bold text-slate-500 dark:text-white/60">إضافة أسئلة الاستبيان ومناطق رفع الإثباتات وقواعد الإظهار الشرطي (BR-028 / BR-030)</p>
                </div>
              </div>
            </div>

            <!-- Components Cards List with Reverse Stack Z-Index to avoid overlap -->
            <div class="space-y-4">
              <div
                v-for="(comp, index) in form.components"
                :key="comp.temp_id || comp.id"
                :style="{ zIndex: form.components.length - index + 10 }"
                class="p-5 rounded-2xl bg-white/80 dark:bg-[#121a2c] border border-black/10 dark:border-white/10 space-y-4 shadow-sm relative transition-all focus-within:z-[100]"
              >
                <!-- Card Header Actions -->
                <div class="flex items-center justify-between pb-3 border-b border-black/5 dark:border-white/10">
                  <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-primary/10 text-primary text-xs font-black flex items-center justify-center font-mono">
                      #{{ index + 1 }}
                    </span>
                    <span class="text-xs font-black text-slate-900 dark:text-white">
                      {{ comp.label || 'حقل غير معنون' }}
                    </span>
                  </div>

                  <div class="flex items-center gap-1.5">
                    <button
                      type="button"
                      @click="moveComponent(index, -1)"
                      :disabled="index === 0"
                      class="p-1.5 rounded-lg hover:bg-black/10 dark:hover:bg-white/10 text-slate-500 dark:text-white/60 disabled:opacity-30 cursor-pointer"
                    >
                      ↑
                    </button>
                    <button
                      type="button"
                      @click="moveComponent(index, 1)"
                      :disabled="index === form.components.length - 1"
                      class="p-1.5 rounded-lg hover:bg-black/10 dark:hover:bg-white/10 text-slate-500 dark:text-white/60 disabled:opacity-30 cursor-pointer"
                    >
                      ↓
                    </button>
                    <button
                      type="button"
                      @click="removeComponent(index)"
                      class="p-1.5 rounded-lg hover:bg-red-500/10 text-red-500 cursor-pointer"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Field Setup Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                  <div class="md:col-span-2">
                    <SpatialInput
                      v-model="comp.label"
                      label="عنوان السؤال / الحقل *"
                      placeholder="مثال: هل طفايات الحريق صالحة؟"
                    />
                  </div>

                  <div class="relative z-[60]">
                    <SpatialDropdown
                      v-model="comp.component_type"
                      label="نوع عنصر الإدخال (BR-028)"
                      :options="componentTypeOptions"
                    />
                  </div>

                  <div class="md:col-span-2">
                    <SpatialInput
                      v-model="comp.placeholder"
                      label="تلميح ممتد / Placeholder"
                      placeholder="أدخل القيمة المسجلة..."
                    />
                  </div>

                  <div>
                    <div
                      @click="comp.is_required = !comp.is_required"
                      :class="[
                        'w-full h-[52px] px-3 rounded-2xl border flex items-center justify-between cursor-pointer select-none transition-all',
                        comp.is_required
                          ? 'bg-amber-500/10 border-amber-500/30 text-amber-600 dark:text-amber-400'
                          : 'bg-white dark:bg-[#0c121e] border-black/10 dark:border-white/10 text-slate-500 dark:text-white/50'
                      ]"
                    >
                      <span class="text-xs font-black">حقل إلزامي *</span>
                      <SpatialCheckbox v-model="comp.is_required" />
                    </div>
                  </div>
                </div>

                <!-- Dynamic Options Manager -->
                <div
                  v-if="['select', 'checkbox', 'choice'].includes(comp.component_type)"
                  class="p-4.5 rounded-2xl bg-slate-100/80 dark:bg-[#0c121e] border border-black/10 dark:border-white/10 space-y-3"
                >
                  <!-- Header labels for columns -->
                  <div v-if="comp.options && comp.options.length > 0" class="grid grid-cols-12 gap-2.5 px-1 text-[11px] font-black text-slate-500 dark:text-white/50">
                    <div class="col-span-6 sm:col-span-7">نص الخيار (الظاهر للميدان) *</div>
                    <div class="col-span-5 sm:col-span-4">القيمة المخزنة (Value)</div>
                    <div class="col-span-1 text-center">حذف</div>
                  </div>

                  <!-- Options List -->
                  <div class="space-y-2.5">
                    <div
                      v-for="(opt, optIdx) in comp.options"
                      :key="optIdx"
                      class="grid grid-cols-12 gap-2.5 items-center"
                    >
                      <div class="col-span-6 sm:col-span-7">
                        <SpatialInput
                          v-model="opt.label"
                          placeholder="مثال: ممتاز / مطابق 🟢"
                        />
                      </div>
                      <div class="col-span-5 sm:col-span-4">
                        <SpatialInput
                          v-model="opt.value"
                          placeholder="مثال: yes / compliant"
                          input-class="font-mono text-xs"
                        />
                      </div>
                      <div class="col-span-1 flex justify-center">
                        <button
                          type="button"
                          @click="removeOption(comp, optIdx)"
                          class="w-10 h-[52px] flex items-center justify-center text-red-500 hover:text-white bg-red-500/10 hover:bg-red-500 rounded-2xl transition-all cursor-pointer text-base font-bold"
                          title="حذف الخيار"
                        >
                          ✕
                        </button>
                      </div>
                    </div>

                    <div v-if="!comp.options || comp.options.length === 0" class="p-4 text-center text-xs font-bold text-slate-400 dark:text-white/40 border border-dashed border-slate-300 dark:border-white/10 rounded-2xl">
                      لا توجد خيارات مضافة بعد.
                    </div>
                  </div>

                  <!-- Enhanced Add Option Button -->
                  <div class="pt-1">
                    <button
                      type="button"
                      @click="addOption(comp)"
                      class="w-full py-3 rounded-2xl bg-primary/10 hover:bg-primary/20 text-primary border-2 border-dashed border-primary/30 hover:border-primary text-xs font-black flex items-center justify-center gap-2 transition-all cursor-pointer shadow-xs active:scale-[0.99]"
                    >
                      <span class="w-5 h-5 rounded-lg bg-primary text-white flex items-center justify-center text-xs font-black">+</span>
                      <span>إضافة خيار جديد</span>
                    </button>
                  </div>
                </div>

                <!-- Smart Conditional Logic Panel (BR-030) -->
                <div class="p-4 rounded-2xl bg-purple-500/5 dark:bg-purple-500/10 border border-purple-500/20 space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-purple-600 dark:text-purple-300 flex items-center gap-1.5">
                      <span>🔗</span>
                      <span>الربط الشرطي الذكي (Smart Adaptive Rules)</span>
                    </span>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-end">
                    <div class="relative z-[50]">
                      <SpatialDropdown
                        v-model="comp.conditional_parent_temp_id"
                        label="يظهر فقط عند إجابة الحقل:"
                        placeholder="بدون شرط (يظهر دائماً)"
                        :options="getParentOptions(index)"
                      />
                    </div>

                    <div v-if="comp.conditional_parent_temp_id" class="relative z-[40]">
                      <SpatialDropdown
                        v-if="getTriggerOptions(comp.conditional_parent_temp_id).length > 0"
                        v-model="comp.conditional_value"
                        label="القيمة التريجر المطلوبة:"
                        placeholder="اختر خيار التفعيل..."
                        :options="getTriggerOptions(comp.conditional_parent_temp_id)"
                      />
                      <SpatialInput
                        v-else
                        v-model="comp.conditional_value"
                        label="القيمة التريجر المطلوبة:"
                        placeholder="مثال: no أو غير مطابق"
                      />
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <!-- Prominent "Add New Field" Button at the BOTTOM of Section 2 -->
            <div class="pt-4 border-t border-black/10 dark:border-white/10">
              <button
                type="button"
                @click="addComponent"
                class="w-full py-4 rounded-2xl border-2 border-dashed border-primary/40 hover:border-primary bg-primary/5 hover:bg-primary/10 text-primary font-black text-xs flex items-center justify-center gap-2 transition-all cursor-pointer shadow-xs active:scale-[0.99]"
              >
                <span class="w-6 h-6 rounded-lg bg-primary text-white flex items-center justify-center text-sm font-black">+</span>
                <span>إضافة حقل جديد للاستبيان الميداني</span>
              </button>
            </div>
          </SpatialCard>

          <!-- Section 3: Assignments Matrix -->
          <SpatialCard padding="p-6" class="relative z-10 space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-black/10 dark:border-white/10">
              <span class="w-9 h-9 rounded-2xl bg-amber-500/10 text-amber-500 font-black flex items-center justify-center text-sm shadow-sm">
                3
              </span>
              <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white">مصفوفة التكليفات الميدانية (BR-029)</h3>
                <p class="text-xs font-bold text-slate-500 dark:text-white/60">تحديد المواقع الميدانية أو الاستشاريين المخولين بهذه المهمة</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Sites Matrix -->
              <div class="p-4 rounded-2xl bg-white/80 dark:bg-[#121a2c] border border-black/10 dark:border-white/10 space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-xs font-black text-slate-900 dark:text-white">المواقع الميدانية</label>
                  <span class="text-[10px] font-bold text-slate-500 dark:text-white/50">
                    ({{ form.site_ids.length > 0 ? `تم تحديد ${form.site_ids.length}` : 'جميع المواقع' }})
                  </span>
                </div>

                <div class="max-h-52 overflow-y-auto space-y-1.5 pr-1 custom-scroll">
                  <div
                    v-for="site in sites"
                    :key="site.id"
                    @click="toggleSiteAssignment(site.id)"
                    :class="[
                      'p-3 rounded-xl border flex items-center justify-between text-xs font-bold cursor-pointer transition-all select-none',
                      form.site_ids.includes(site.id)
                        ? 'bg-primary/15 border-primary/40 text-slate-900 dark:text-white shadow-sm'
                        : 'bg-white dark:bg-[#0c121e] border-black/5 dark:border-white/5 text-slate-600 dark:text-white/70 hover:border-slate-300'
                    ]"
                  >
                    <span>{{ site.name }} ({{ site.code }})</span>
                    <SpatialCheckbox :model-value="form.site_ids.includes(site.id)" />
                  </div>
                </div>
              </div>

              <!-- Consultants Matrix -->
              <div class="p-4 rounded-2xl bg-white/80 dark:bg-[#121a2c] border border-black/10 dark:border-white/10 space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-xs font-black text-slate-900 dark:text-white">الاستشاريين الميدانيين</label>
                  <span class="text-[10px] font-bold text-slate-500 dark:text-white/50">
                    ({{ form.consultant_ids.length > 0 ? `تم تحديد ${form.consultant_ids.length}` : 'جميع الاستشاريين' }})
                  </span>
                </div>

                <div class="max-h-52 overflow-y-auto space-y-1.5 pr-1 custom-scroll">
                  <div
                    v-for="consultant in consultants"
                    :key="consultant.id"
                    @click="toggleConsultantAssignment(consultant.id)"
                    :class="[
                      'p-3 rounded-xl border flex items-center justify-between text-xs font-bold cursor-pointer transition-all select-none',
                      form.consultant_ids.includes(consultant.id)
                        ? 'bg-primary/15 border-primary/40 text-slate-900 dark:text-white shadow-sm'
                        : 'bg-white dark:bg-[#0c121e] border-black/5 dark:border-white/5 text-slate-600 dark:text-white/70 hover:border-slate-300'
                    ]"
                  >
                    <span>{{ consultant.full_name }} ({{ consultant.employee_number }})</span>
                    <SpatialCheckbox :model-value="form.consultant_ids.includes(consultant.id)" />
                  </div>
                </div>
              </div>
            </div>
          </SpatialCard>

        </div>

        <!-- Sticky Side Column: Live Interactive Form Preview (4 cols) -->
        <div class="lg:col-span-4 sticky top-6 self-start z-30">
          <SpatialCard padding="p-5" class="space-y-5">
            <div class="flex items-center justify-between pb-3.5 border-b border-black/10 dark:border-white/10">
              <span class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                <span>👁️</span>
                <span>المعاينة الحية التفاعلية للميدان</span>
              </span>

              <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">
                مباشر Live
              </span>
            </div>

            <!-- Simulated Card Header -->
            <div class="p-4 rounded-2xl bg-white/70 dark:bg-[#121a2c] border border-black/10 dark:border-white/10 space-y-2">
              <span
                :class="[
                  'px-2.5 py-0.5 rounded-full text-[10px] font-black inline-block',
                  form.task_type === 'daily'
                    ? 'bg-blue-500/15 text-blue-600 dark:text-blue-400'
                    : 'bg-amber-500/15 text-amber-600 dark:text-amber-400'
                ]"
              >
                {{ form.task_type === 'daily' ? '📅 مهمة يومية دورية' : '⚡ مهمة عند الطلب' }}
              </span>

              <h4 class="text-sm font-black text-slate-900 dark:text-white">
                {{ form.title || 'عنوان المهمة الميدانية...' }}
              </h4>

              <p class="text-xs text-slate-500 dark:text-white/60">
                {{ form.description || 'لا يوجد وصف توجيهي مضاف...' }}
              </p>
            </div>

            <!-- Simulated Form Inputs (Dynamic Height Growth) -->
            <div class="space-y-3">
              <div
                v-for="comp in visibleComponents"
                :key="comp.temp_id || comp.id"
                class="p-4 rounded-2xl bg-white/70 dark:bg-[#121a2c] border border-black/10 dark:border-white/10 space-y-2.5 text-xs shadow-xs"
              >
                <div class="flex items-center justify-between">
                  <label class="font-black text-slate-900 dark:text-white">
                    {{ comp.label || 'سؤال غير معنون' }}
                    <span v-if="comp.is_required" class="text-red-500">*</span>
                  </label>
                  <span v-if="comp.conditional_parent_temp_id" class="text-[9px] font-bold text-purple-500 bg-purple-500/10 px-1.5 py-0.5 rounded">
                    مشروط
                  </span>
                </div>

                <!-- Simulation Inputs -->
                <div v-if="comp.component_type === 'text'">
                  <SpatialInput v-model="previewValues[getCompKey(comp)]" :placeholder="comp.placeholder || 'أدخل النص...'" />
                </div>

                <div v-else-if="comp.component_type === 'number'">
                  <SpatialInput v-model="previewValues[getCompKey(comp)]" type="number" :placeholder="comp.placeholder || 'أدخل الرقم...'" />
                </div>

                <div v-else-if="comp.component_type === 'select' || comp.component_type === 'choice'">
                  <SpatialDropdown
                    v-model="previewValues[getCompKey(comp)]"
                    placeholder="اختر إجابة..."
                    :options="comp.options ? comp.options.map(o => ({ label: o.label || o.value, value: o.value || o.label })) : []"
                  />
                </div>

                <div v-else-if="comp.component_type === 'checkbox'" class="space-y-2 pt-1">
                  <div
                    v-for="(opt, optIdx) in (comp.options || [])"
                    :key="optIdx"
                    class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5"
                  >
                    <SpatialCheckbox :model-value="false" />
                    <span class="text-xs font-bold text-slate-800 dark:text-white/90">{{ opt.label || opt.value || 'خيار' }}</span>
                  </div>
                </div>

                <div v-else-if="comp.component_type === 'image_upload' || comp.component_type === 'image'">
                  <SpatialImageUpload />
                </div>
              </div>

              <div v-if="visibleComponents.length === 0" class="p-6 text-center text-xs font-bold text-slate-400 dark:text-white/40">
                لم تقم ببدء تصميم أي عناصر حتى الآن.
              </div>
            </div>

            <!-- Bottom Quick Summary -->
            <div class="pt-3.5 border-t border-black/10 dark:border-white/10 flex items-center justify-between text-[11px] font-bold text-slate-500 dark:text-white/60">
              <span>إجمالي المكونات: {{ form.components.length }}</span>
              <span>المواقع: {{ form.site_ids.length || 'الكل' }}</span>
            </div>
          </SpatialCard>
        </div>
      </div>
    </div>
  </HRLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import HRLayout from '@/Layouts/HRLayout.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';
import SpatialImageUpload from '@/Components/Spatial/SpatialImageUpload.vue';

const props = defineProps({
  task: {
    type: Object,
    default: null,
  },
  sites: {
    type: Array,
    default: () => [],
  },
  consultants: {
    type: Array,
    default: () => [],
  },
});

const isEditing = computed(() => !!props.task);

const taskTypeOptions = [
  { label: '📅 مهمة يومية دورية (Daily)', value: 'daily' },
  { label: '⚡ مهمة عند الطلب (On-Demand)', value: 'on_demand' },
];

const componentTypeOptions = [
  { label: '✏️ نص عادي (Text)', value: 'text' },
  { label: '🔢 رقم (Number)', value: 'number' },
  { label: '📋 قائمة اختيار (Select)', value: 'select' },
  { label: '🔘 اختيار متعدد (Choice)', value: 'choice' },
  { label: '☑️ مربع اختيار (Checkbox)', value: 'checkbox' },
  { label: '📸 منطقة رفع الإثبات الحية (Image Upload)', value: 'image_upload' },
  { label: '📅 تاريخ (Date)', value: 'date' },
];

const form = useForm({
  title: '',
  description: '',
  task_type: 'daily',
  is_active: true,
  display_order: 0,
  components: [],
  site_ids: [],
  consultant_ids: [],
});

const errors = ref({});
const previewValues = ref({});
let nextTempId = 1;

watch(
  () => props.task,
  (t) => {
    if (t) {
      form.title = t.title || '';
      form.description = t.description || '';
      form.task_type = t.task_type || 'daily';
      form.is_active = t.is_active !== undefined ? t.is_active : true;
      form.display_order = t.display_order || 0;

      if (t.components && t.components.length > 0) {
        form.components = t.components.map((c) => ({
          id: c.id,
          temp_id: `comp_${c.id}`,
          label: c.label || '',
          component_type: c.component_type || 'text',
          placeholder: c.placeholder || '',
          is_required: !!c.is_required,
          conditional_parent_temp_id: c.conditional_parent_id ? `comp_${c.conditional_parent_id}` : null,
          conditional_value: c.conditional_value || null,
          options: c.options
            ? c.options.map((opt) => ({
                label: opt.option_label || opt.label || '',
                value: opt.option_value || opt.value || '',
              }))
            : [],
        }));
      } else {
        form.components = [];
      }

      form.site_ids = t.site_assignments ? t.site_assignments.map((sa) => sa.site_id) : [];
      form.consultant_ids = t.consultant_assignments ? t.consultant_assignments.map((ca) => ca.consultant_id) : [];
    } else {
      addComponent();
    }
  },
  { immediate: true }
);

function addComponent() {
  const tempId = `temp_${nextTempId++}`;
  form.components.push({
    temp_id: tempId,
    label: '',
    component_type: 'text',
    placeholder: '',
    is_required: true,
    conditional_parent_temp_id: null,
    conditional_value: null,
    options: [
      { label: 'مطابق', value: 'yes' },
      { label: 'غير مطابق', value: 'no' },
    ],
  });
}

function removeComponent(index) {
  form.components.splice(index, 1);
}

function moveComponent(index, direction) {
  const targetIndex = index + direction;
  if (targetIndex < 0 || targetIndex >= form.components.length) return;
  const temp = form.components[index];
  form.components[index] = form.components[targetIndex];
  form.components[targetIndex] = temp;
}

function addOption(comp) {
  if (!comp.options) comp.options = [];
  comp.options.push({ label: '', value: '' });
}

function removeOption(comp, optIdx) {
  comp.options.splice(optIdx, 1);
}

function getParentOptions(currentIndex) {
  const parents = form.components.slice(0, currentIndex);
  const options = [{ label: 'بدون شرط (يظهر دائماً)', value: null }];
  parents.forEach((p) => {
    options.push({
      label: `#${p.label || 'حقل غير معنون'}`,
      value: p.temp_id || `comp_${p.id}`,
    });
  });
  return options;
}

function getTriggerOptions(parentTempId) {
  if (!parentTempId) return [];
  const parentComp = form.components.find(
    (c) => (c.temp_id || `comp_${c.id}`) === parentTempId
  );
  if (!parentComp || !parentComp.options || parentComp.options.length === 0) return [];

  return parentComp.options
    .filter((opt) => opt.value || opt.label)
    .map((opt) => ({
      label: opt.label || opt.value,
      value: opt.value || opt.label,
    }));
}

function toggleSiteAssignment(siteId) {
  const index = form.site_ids.indexOf(siteId);
  if (index > -1) {
    form.site_ids.splice(index, 1);
  } else {
    form.site_ids.push(siteId);
  }
}

function toggleConsultantAssignment(consultantId) {
  const index = form.consultant_ids.indexOf(consultantId);
  if (index > -1) {
    form.consultant_ids.splice(index, 1);
  } else {
    form.consultant_ids.push(consultantId);
  }
}

const getCompKey = (comp) => comp.temp_id || `comp_${comp.id}`;

const visibleComponents = computed(() => {
  return form.components.filter((comp) => {
    const parentTempId = comp.conditional_parent_temp_id;
    if (!parentTempId) return true;

    const parentComp = form.components.find((c) => (c.temp_id || `comp_${c.id}`) === parentTempId);
    if (!parentComp) return true;

    const parentKey = getCompKey(parentComp);
    const parentVal = previewValues.value[parentKey];
    return String(parentVal) === String(comp.conditional_value);
  });
});

function handleSubmit() {
  if (isEditing.value) {
    form.put(route('admin.tasks.update', props.task.id));
  } else {
    form.post(route('admin.tasks.store'));
  }
}
</script>
