<template>
  <div class="flex min-h-screen bg-surface">
    <sidebar :active-tab="activeTab" @update:activeTab="activeTab = Revent" />
    
    <main class="ml-[240px] flex-1 flex flex-col min-h-screen relative">
      <top-bar />

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

          <div class="flex items-center gap-4">
            <div class="flex items-center gap-1 bg-surface-container-low p-1 rounded-lg border border-outline-variant/50">
              <button class="px-4 py-1.5 rounded-md bg-white shadow-sm text-primary font-label-caps text-[10px] uppercase tracking-wider cursor-pointer">All</button>
              <button class="px-4 py-1.5 rounded-md hover:bg-surface-container-high text-on-surface-variant font-label-caps text-[10px] uppercase tracking-wider transition-colors cursor-pointer">Issues</button>
            </div>
            <button
              @click="isPanelOpen = true"
              class="bg-primary text-white px-6 py-2 rounded-lg font-label-caps flex items-center gap-2 hover:bg-primary-container transition-all active:scale-[0.98] cursor-pointer"
            >
              <Plus :size="16" />
              Create New Order
            </button>
          </div>
        </div>

        <div class="grid grid-cols-12 gap-8 items-start">
          <!-- Table View -->
          <div class=" w-full col-span-15">
            <order-table :orders="orders" />
          </div>

          <!-- Side Content -->
          <!-- <div class="col-span-4 space-y-6">
            <order-progress />
            <stat-card />
          </div> -->
        </div>
      </div>
      
      <create-order :is-open="isPanelOpen" @close="isPanelOpen = false" />
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
      isPanelOpen: false,
      activeTab: 'orders',
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
