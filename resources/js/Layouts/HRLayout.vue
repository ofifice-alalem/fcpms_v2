<template>
  <div class="min-h-screen bg-[#13131a] text-[#c9d1d9] flex">
    <!-- Sidebar -->
    <aside class="w-64 border-l border-white/10 glass-card rounded-none p-6 flex flex-col justify-between hidden md:flex">
      <div>
        <div class="text-2xl font-bold text-indigo-400 mb-8 flex items-center gap-2">
          <span>FCPMS</span>
          <span class="text-xs bg-indigo-500/20 text-indigo-300 px-2 py-0.5 rounded-full">HR</span>
        </div>
        <nav class="space-y-2">
          <!-- Links will be expanded per module phase -->
          <div class="px-3 py-2 rounded-xl bg-indigo-600/20 text-indigo-300 font-medium">لوحة التحكم</div>
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0">
      <header class="h-16 border-b border-white/10 px-6 flex items-center justify-between glass-card rounded-none">
        <div class="font-medium text-gray-300">لوحة تحكم الموارد البشرية</div>
        <div class="flex items-center gap-3">
          <span class="text-sm text-gray-400">{{ $page.props.auth?.user?.name || 'مستخدم HR' }}</span>
        </div>
      </header>
      <div class="p-6 flex-1">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
</script>
