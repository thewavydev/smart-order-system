<template>
  <div class="p-6 bg-on-surface rounded-xl text-white relative overflow-hidden shadow-sm">
    <p class="text-[10px] font-label-caps uppercase tracking-widest text-surface-variant/60 mb-2">{{ this.statCard.title }}</p>
    <div class="flex items-baseline gap-2">
      <h4 class="text-3xl font-bold">{{ this.statCard.value }}</h4>
    </div>
    <div class="absolute -bottom-2 -right-2 w-24 h-24 bg-primary/20 rounded-full blur-2xl"></div>
  </div>
</template>

<script>
export default {
  props: {
    statCard: {
      type: Object,
      default: () => ({
        title: 'Queue Depth',
        value: '1,029',
      }),
    },
  },
  name: 'StatCard',
  data() {
    return {
      // You can add any data properties here if needed
    };
  },
};
</script>
