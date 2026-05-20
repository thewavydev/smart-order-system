<template>
  <div v-if="isOpen">
    <!-- Backdrop -->
    <div
      class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[60]"
      @click="$emit('close')"
    ></div>
    
    <!-- Panel -->
    <div
      class="fixed top-0 right-0 h-full w-1/3 bg-white shadow-2xl z-[70] border-l border-outline-variant/30 flex flex-col transition-transform duration-300"
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
          <!-- <label class="font-label-caps text-outline-variant">Customer Name</label> -->
          <input v-model="orderDetails.customer_name" type="text" placeholder="Enter customer name..." class="w-full px-4 py-2 border border-outline-variant/30 rounded-lg focus:ring-primary outline-none transition-colors" />
          <input v-model="orderDetails.customer_email" type="email" placeholder="Enter customer email..." class="w-full px-4 py-2 border border-outline-variant/30 rounded-lg focus:ring-primary outline-none transition-colors" />
          <input v-model="orderDetails.customer_phone" type="text" placeholder="Enter customer phone..." class="w-full px-4 py-2 border border-outline-variant/30 rounded-lg focus:ring-primary outline-none transition-colors" />
          <input v-model="orderDetails.customer_address" type="text" placeholder="Enter customer address..." class="w-full px-4 py-2 border border-outline-variant/30 rounded-lg focus:ring-primary outline-none transition-colors" />
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
              <input type="number" v-model="orderDetails.quantity" class="w-16 h-8 text-center border border-outline-variant/30 rounded focus:ring-primary text-xs outline-none"/>
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
        <button @click="submit()" class="flex-1 py-3 bg-primary text-white rounded-lg font-label-caps shadow-lg shadow-primary/20 hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer">
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
import axios from 'axios';
export default {
  name: 'CreateOrderPanel',
  components: { X, Building2, Package, Plus},
  props: {
    isOpen: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return {
      orderDetails: {
        customer_name: '',
        customer_email: '',
        customer_phone: '',
        customer_address: '',
        product_name: '',
        quantity: '',
        total_price: '',
      },
    };
  },
  methods: {
    submit() {
      const request = {
        //test data, replace with actual form data
        customer_name: 'Lehlohonolo', 
        customer_email: 'lehlohonolomona23@gmail.com',
        customer_phone: '0785268696',
        customer_address: '13206 Maphnga Street, Zone 6, Bloemfontein',
        product_name: 'Neural Link v2.0', // This should ideally come from the line items added
        quantity: 4,
        total_price: 1200.00, // This should be calculated based on the line items and their quantities
      } 

      // axios.post(`${this.$apiUrl}/orders/store`, request)
      axios.post(`${this.$apiUrl}/orders/store`, request)
        .then(response => {
          alert(response.data.message);
          console.log('Order created successfully:', response.data);
          this.$emit('close'); // Close the panel after successful submission
        })
    }
  }
};
</script>
