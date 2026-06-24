<template>
  <div class="bg-white border border-outline-variant/40 rounded-xl overflow-hidden shadow-sm">

    <table class="w-full text-left border-collapse">

      <thead class="bg-surface-container-lowest border-b border-outline-variant/20">
        <tr>
          <th class="px-6 py-4 text-[10px] text-outline">ORDER</th>
          <th class="px-6 py-4 text-[10px] text-outline">CUSTOMER</th>
          <th class="px-6 py-4 text-[10px] text-outline">PRODUCTS</th>
          <th class="px-6 py-4 text-[10px] text-outline">STATUS</th>
          <th class="px-6 py-4 text-[10px] text-outline text-right">SOURCE</th>
          <th class="px-6 py-4 text-[10px] text-outline text-right">AMOUNT</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-outline-variant/10">

        <tr
          v-for="order in orders"
          :key="order.id"
          class="hover:bg-surface-container-low/50 cursor-pointer transition-colors"
        >

          <!-- ORDER ID -->
          <td class="px-6 py-5 font-data-mono text-[13px] text-on-surface font-semibold">
            #{{ order.id }}
          </td>

          <!-- CUSTOMER -->
          <td class="px-6 py-5 text-sm text-on-surface">
            {{ order.customer.phone_number }}
          </td>

          <!-- PRODUCTS -->
          <td class="px-6 py-5 text-xs text-on-surface max-w-[300px]">
            <span class="line-clamp-2">
              {{ order.products }}
            </span>
          </td>

          <!-- STATUS -->
          <td class="px-6 py-5">
            <span
              class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold border uppercase"
              :class="getStatusStyles(order.status)"
            >
              {{ order.status }}
            </span>
          </td>

          <!-- SOURCE -->
          <td class="px-6 py-5 text-xs text-right text-on-surface">
            {{ order.source }}
          </td>

          <!-- TOTAL -->
          <td class="px-6 py-5 text-xs text-right text-on-surface">
            R{{ order.total }}
          </td>

        </tr>

      </tbody>
    </table>

    <!-- PAGINATION -->
    <div class="px-6 py-4 flex items-center justify-between bg-surface-container-low/20">
      <span class="text-[11px] text-outline uppercase">
        Page {{ meta.current_page }} of {{ meta.last_page }}
      </span>

      <div class="flex items-center gap-4">
        <button @click="$emit('prev')" class="hover:text-primary">
          <ChevronLeft :size="18" />
        </button>

        <button @click="$emit('next')" class="hover:text-primary">
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
  components: { ChevronLeft, ChevronRight },

  props: {
    orders: {
      type: Array,
      required: true
    },
    meta: {
      type: Object,
      default: () => ({
        current_page: 1,
        last_page: 1
      })
    }
  },

  methods: {
    getStatusStyles(status) {
      const styles = {
        pending: 'bg-yellow-50 text-yellow-600 border-yellow-100',
        processing: 'bg-blue-50 text-blue-600 border-blue-100',
        completed: 'bg-emerald-50 text-emerald-600 border-emerald-100',
        failed: 'bg-red-50 text-red-600 border-red-100',
      };

      return styles[status?.toLowerCase()] || '';
    }
  }
};
</script>