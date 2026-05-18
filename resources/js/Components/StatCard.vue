<template>
  <div class="p-6 bg-on-surface rounded-xl text-white relative overflow-hidden shadow-sm">
    <p class="text-[10px] font-label-caps uppercase tracking-widest text-surface-variant/60 mb-2">Queue Depth</p>
    <div class="flex items-baseline gap-2">
      <h4 class="text-3xl font-bold">1,029</h4>
      <span class="text-emerald-400 text-xs font-bold">↑ 2.4%</span>
    </div>
    <div class="absolute -bottom-2 -right-2 w-24 h-24 bg-primary/20 rounded-full blur-2xl"></div>
  </div>
</template>

<script>
export default {
  name: 'StatCard'
};
</script>
