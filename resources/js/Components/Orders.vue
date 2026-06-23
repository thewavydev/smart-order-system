<template>
  <div class="flex min-h-screen bg-surface">    
    <main class="ml-[240px] flex-1 flex flex-col min-h-screen relative">
      <div class="p-8 max-w-[1200px] mx-auto w-full space-y-10">
        <!-- Page Header & Actions -->
        <div class="flex items-center justify-between border-b border-outline-variant/30 pb-6">
          <div>
            <nav class="flex items-center gap-2 mb-2">
              <span class="text-xs text-outline-variant">Dashboard</span>
              <ChevronRight :size="12" class="text-outline-variant" />
              <span class="text-xs text-on-surface/60">Orders</span>
            </nav>
            <h2 class="text-3xl font-bold text-on-surface tracking-tight">Order Management</h2>
          </div>
        </div>
        <div class="grid grid-cols-12 gap-8 items-start">
          <!-- Table View -->
          <div class=" w-full col-span-15">
            <order-table :orders="orders" />
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { ChevronRight, Plus } from '@lucide/vue';
import axios from 'axios';

export default {
  name: 'App',
  components: {
    ChevronRight,
    Plus,
  },  
  data() {
    return {
      orders: [],
      current_page: 1

    };
  },
  methods: {
    getOrders(){
      axios.get(`${this.$apiUrl}/orders/index`)
      .then(response => {
          this.orders = response.data.orders.data;
          this.current_page = response.data.orders.current_page;
          console.log('Orders fetched successfully:', this.orders);
        })
        .catch(error => {
          console.error('Error fetching orders:', error);
        });
    }
  },
  mounted() {
    this.getOrders();
  }
};
</script>
