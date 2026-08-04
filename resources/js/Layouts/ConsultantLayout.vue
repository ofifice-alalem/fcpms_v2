<template>
  <div class="min-h-screen bg-[#13131a] text-[#c9d1d9] flex flex-col">
    <!-- Header -->
    <header class="h-16 border-b border-white/10 px-6 flex items-center justify-between glass-card rounded-none">
      <div class="text-xl font-bold text-indigo-400">FCPMS <span class="text-xs text-gray-400">استشاري ميداني</span></div>
      <div class="text-sm text-gray-300">{{ $page.props.auth?.user?.name || 'الاستشاري' }}</div>
    </header>
    <!-- Main Content -->
    <main class="flex-1 p-6 max-w-4xl w-full mx-auto">
      <slot />
    </main>
  </div>
</template>

<script setup>
</script>
