<template>
  <div class="bg-white border border-outline-variant/40 rounded-xl overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse">
      <thead class="bg-surface-container-lowest border-b border-outline-variant/20">
        <tr>
          <th class="px-6 py-4 font-label-caps text-[10px] text-outline">ID</th>
          <th class="px-6 py-4 font-label-caps text-[10px] text-outline">Customer</th>
          <th class="px-6 py-4 font-label-caps text-[10px] text-outline">Status</th>
          <th class="px-6 py-4 font-label-caps text-[10px] text-outline text-right">Amount</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-outline-variant/10">
        <tr
          v-for="(order, index) in orders"
          :key="order.id"
          class="hover:bg-surface-container-low/50 cursor-pointer transition-colors group"
          :class="index === 0 ? 'bg-primary-container/[0.03]' : ''"
        >
          <td class="px-6 py-5 font-data-mono text-[13px]" :class="index === 0 ? 'text-primary font-semibold' : 'text-on-surface-variant'">
            {{ order.id }}
          </td>
          <td class="px-6 py-5 text-sm text-on-surface">{{ order.customer }}</td>
          <td class="px-6 py-5">
            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold border uppercase" :class="getStatusStyles(order.status)">
              {{ order.status }}
            </span>
          </td>
          <td class="px-6 py-5 font-data-mono text-xs text-right text-on-surface">
            {{ order.amount }}
          </td>
        </tr>
      </tbody>
    </table>
    
    <div class="px-6 py-4 flex items-center justify-between bg-surface-container-low/20">
      <span class="text-[11px] text-outline uppercase font-label-caps">Page 1 of 12</span>
      <div class="flex items-center gap-4">
        <button class="text-on-surface-variant hover:text-primary transition-colors cursor-pointer">
          <ChevronLeft :size="18" />
        </button>
        <button class="text-on-surface-variant hover:text-primary transition-colors cursor-pointer">
          <ChevronRight :size="18" />
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ChevronLeft, ChevronRight } from '@lucide/vue';

export default {
  name: 'OrderTable',
  components: {
    ChevronLeft,
    ChevronRight
  },
  props: {
    orders: {
      type: Array,
      required: true
    }
  },
  methods: {
    getStatusStyles(status) {
      const styles = {
        PROCESSING: 'bg-blue-50 text-blue-600 border-blue-100',
        SUCCESS: 'bg-emerald-50 text-emerald-600 border-emerald-100',
        FAILED: 'bg-red-50 text-red-600 border-red-100',
      };
      return styles[status] || '';
    }
  }
};
</script>
