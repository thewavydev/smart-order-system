<template>
  <div v-if="isOpen">
    <!-- Backdrop -->
    <div
      class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[60]"
      @click="$emit('close')"
    ></div>
    
    <!-- Panel -->
    <div
      class="fixed top-0 right-0 h-full w-[400px] bg-white shadow-2xl z-[70] border-l border-outline-variant/30 flex flex-col transition-transform duration-300"
      :class="isOpen ? 'translate-x-0' : 'translate-x-full'"
    >
      <div class="p-6 border-b border-outline-variant/20 flex items-center justify-between">
        <div>
          <h3 class="text-xl font-bold text-on-surface">New Order</h3>
          <p class="text-xs text-outline-variant">Manual order entry system</p>
        </div>
        <button
          @click="$emit('close')"
          class="w-10 h-10 rounded-full hover:bg-surface-container-low flex items-center justify-center transition-colors cursor-pointer"
        >
          <X :size="20" class="text-on-surface-variant" />
        </button>
      </div>

      <div class="flex-1 overflow-y-auto p-8 space-y-8">
        <div class="space-y-2">
          <label class="font-label-caps text-outline-variant">Customer</label>
          <div class="relative">
            <Building2 :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant" />
            <select class="w-full pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant/30 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none appearance-none cursor-pointer">
              <option>Select Customer...</option>
              <option>Cyberdyne Systems</option>
              <option>Weyland-Yutani Corp</option>
              <option>Tyrell Engineering</option>
              <option>Stark Industries</option>
            </select>
          </div>
        </div>

        <div class="space-y-2">
          <label class="font-label-caps text-outline-variant">Line Items</label>
          <div class="space-y-3">
            <div class="p-3 border border-outline-variant/20 rounded-lg bg-surface-container-lowest flex items-center justify-between group">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-surface-container-low rounded-md flex items-center justify-center">
                  <Package :size="20" class="text-on-surface-variant" />
                </div>
                <div>
                  <p class="text-xs font-semibold">Neural Link v2.0</p>
                  <p class="text-[11px] text-outline-variant">Unit Price: R1,200.00</p>
                </div>
              </div>
              <input
                type="number"
                value="1"
                class="w-16 h-8 text-center border border-outline-variant/30 rounded focus:ring-primary text-xs outline-none"
              />
            </div>
            
            <button class="w-full py-2 border-2 border-dashed border-outline-variant/30 rounded-lg text-xs text-outline-variant hover:border-primary/40 hover:text-primary transition-all flex items-center justify-center gap-2 cursor-pointer">
              <Plus :size="14" />
              Add Item
            </button>
          </div>
        </div>

        <div class="p-6 bg-surface-container-low rounded-xl space-y-4">
          <div class="flex justify-between items-center text-xs">
            <span class="text-on-surface-variant">Subtotal</span>
            <span class="font-data-mono text-on-surface">R1,200.00</span>
          </div>
          <div class="flex justify-between items-center text-xs">
            <span class="text-on-surface-variant">Estimated Tax</span>
            <span class="font-data-mono text-on-surface">R96.00</span>
          </div>
          <div class="h-px bg-outline-variant/20 w-full"></div>
          <div class="flex justify-between items-center">
            <span class="font-bold text-on-surface">Total</span>
            <span class="font-data-mono text-lg font-bold text-primary">R1,296.00</span>
          </div>
        </div>
      </div>

      <div class="p-6 border-t border-outline-variant/20 bg-surface-container-lowest flex gap-3">
        <button class="flex-1 py-3 bg-primary text-white rounded-lg font-label-caps shadow-lg shadow-primary/20 hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer">
          Commit Order
        </button>
        <button
          @click="$emit('close')"
          class="px-6 py-3 border border-outline-variant text-on-surface-variant rounded-lg font-label-caps hover:bg-surface-container-low transition-all cursor-pointer"
        >
          Cancel
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { X, Building2, Package, Plus } from '@lucide/vue';

export default {
  name: 'CreateOrderPanel',
  components: {
    X,
    Building2,
    Package,
    Plus
  },
  props: {
    isOpen: {
      type: Boolean,
      required: true
    }
  }
};
</script>
