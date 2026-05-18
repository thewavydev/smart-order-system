<template>
  <div class="bg-white border border-outline-variant/40 rounded-xl p-6 shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-sm font-bold text-on-surface">Order Progress</h3>
      <span class="text-[10px] font-mono text-primary bg-primary/5 px-2 py-0.5 rounded uppercase">Active</span>
    </div>

    <div class="space-y-6">
      <!-- Step 1 -->
      <div class="flex gap-4">
        <div class="flex flex-col items-center">
          <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center">
            <Check :size="14" />
          </div>
          <div class="w-0.5 flex-1 bg-outline-variant/30 my-1"></div>
        </div>
        <div class="pb-4">
          <p class="text-xs font-semibold text-on-surface">Validation</p>
          <p class="text-[11px] text-outline-variant">Inventory locked</p>
        </div>
      </div>

      <!-- Step 2 -->
      <div class="flex gap-4">
        <div class="flex flex-col items-center">
          <div class="w-6 h-6 rounded-full bg-primary ring-4 ring-primary/10 flex items-center justify-center">
            <div class="w-2 h-2 rounded-full bg-white animate-pulse"></div>
          </div>
          <div class="w-0.5 flex-1 bg-outline-variant/10 my-1"></div>
        </div>
        <div class="pb-4">
          <p class="text-xs font-semibold text-primary">Queueing</p>
          <p class="text-[11px] text-outline-variant">WMS Node Chicago</p>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="flex gap-4 opacity-30">
        <div class="flex flex-col items-center">
          <div class="w-6 h-6 rounded-full bg-outline-variant flex items-center justify-center"></div>
        </div>
        <div>
          <p class="text-xs font-semibold text-on-surface">Shipping</p>
          <p class="text-[11px] text-outline-variant">Pending handoff</p>
        </div>
      </div>
    </div>

    <button class="w-full mt-6 text-primary border border-primary/20 py-2 rounded-lg text-xs hover:bg-primary/5 transition-colors cursor-pointer">
      View Audit Log
    </button>
  </div>
</template>

<script>
import { Check } from '@lucide/vue';

export default {
  name: 'OrderProgress',
  components: {
    Check
  }
};
</script>
