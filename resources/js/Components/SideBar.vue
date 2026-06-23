<template>
  <aside class="fixed left-0 top-0 h-full w-[240px] bg-on-surface flex flex-col py-4 border-r border-outline-variant z-50">
    
    <div class="px-6 mb-8">
      <h1 class="text-xl font-bold text-white">SmartOrder-Sys</h1>
      <hr class="border-t border-white/10 my-4">
    </div>

    <nav class="flex-1 space-y-1">
      <a
        v-for="item in navItems"
        :key="item.id"
        :href="item.link"
        class="rounded-lg mx-2 px-3 py-2 flex items-center gap-3 transition-all duration-100 active:scale-[0.98]"
        :class="currentPath === item.link
          ? 'bg-primary-container text-white'
          : 'text-white hover:bg-white/10'"
      >
        <component :is="item.icon" :size="18" />
        <span class="font-label-caps">{{ item.name }}</span>
      </a>
    </nav>

  </aside>
</template>

<script>
import { LayoutDashboard, ShoppingCart } from '@lucide/vue';

export default {
  name: 'Sidebar',
  // computed: {
  //   currentPath() {
  //     return this.$route.path;
  //   }
  // },
  data() {
    return {
      currentPath: window.location.pathname,
      navItems: [
        { id: 'overview', name: 'Overview', icon: LayoutDashboard, link: '/' },
        { id: 'orders', name: 'Orders', icon: ShoppingCart, link: '/orders' },
      ]
    };
  }
};
</script> 