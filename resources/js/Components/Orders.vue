<template>
  <div class="flex min-h-screen bg-surface">
    <main class="ml-[240px] flex-1 flex flex-col min-h-screen relative">
      <div class="p-8 max-w-[1200px] mx-auto w-full space-y-10">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-outline-variant/30 pb-6">
          <div>
            <nav class="flex items-center gap-2 mb-2">
              <span class="text-xs text-outline-variant">Dashboard</span>
              <ChevronRight :size="12" class="text-outline-variant" />
              <span class="text-xs text-on-surface/60">Orders</span>
            </nav>
            <h2 class="text-3xl font-bold text-on-surface">
              Order Management
            </h2>
          </div>
        </div>
        <!-- Table -->
        <div class="grid grid-cols-12 gap-8">
          <div class="col-span-12">
            <order-table :orders="orders" :meta="meta" @next="nextPage" @prev="prevPage" />
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { ChevronRight } from "@lucide/vue";
import axios from "axios";

export default {
  components: { ChevronRight },
  data() {
    return {
      orders: [],
      meta: {
        current_page: 1,
        last_page: 1,
      },
    };
  },

  methods: {
    getOrders(page = 1) {
      axios.get(`${this.$apiUrl}/orders/index?page=${page}`)
        .then((response) => {
          const result = response.data.orders;
          this.orders = result.data;
          this.meta.current_page = result.current_page;
          this.meta.last_page = result.last_page;
        })
        .catch((error) => {
          console.error("Error fetching orders:", error);
        });
    },

    nextPage() {
      if (this.meta.current_page < this.meta.last_page) {
        this.getOrders(this.meta.current_page + 1);
      }
    },

    prevPage() {
      if (this.meta.current_page > 1) {
        this.getOrders(this.meta.current_page - 1);
      }
    },
  },

  mounted() {
    this.getOrders();
  },
};
</script>
