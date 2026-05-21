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
        href="#"
        @click.prevent="$emit('update:activeTab', item.id)"
        class="rounded-lg mx-2 px-3 py-2 flex items-center gap-3 transition-all duration-100 active:scale-[0.98]"
        :class="activeTab === item.id ? 'bg-primary-container text-white' : 'text-white hover:text-white hover:bg-white/10'"
      >
        <component :is="item.icon" :size="18" />
        <span class="font-label-caps">{{ item.name }}</span>
      </a>
    </nav>

      <hr class="border-t border-white/10 my-14 mx-2" />
    <!-- <div class="px-2 mt-auto">
      <button class="w-full bg-[#0c56d0] text-white font-label-caps py-2 rounded-lg mb-4 hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer">
        New Deployment
      </button>
      
      <div class="space-y-1 border-t border-white/10 pt-4">
        <a class="text-white hover:text-white hover:bg-white/10 rounded-lg px-3 py-2 flex items-center gap-3 transition-colors duration-100" href="#">
          <Settings :size="18" />
          <span class="font-label-caps">Settings</span>
        </a>
        <a class="text-white hover:text-white hover:bg-white/10 rounded-lg px-3 py-2 flex items-center gap-3 transition-colors duration-100" href="#">
          <HelpCircle :size="18" />
          <span class="font-label-caps">Support</span>
        </a>
      </div>
    </div> -->
  </aside>
</template>

<script>
import { 
  LayoutDashboard, 
  ShoppingCart, 
  Network, 
  Terminal, 
  Settings, 
  HelpCircle 
} from '@lucide/vue';

export default {
  name: 'Sidebar',
  components: {
    Settings,
    HelpCircle
  },
  props: {
    activeTab: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      navItems: [
        // { id: 'overview', name: 'Overview', icon: LayoutDashboard },
        { id: 'orders', name: 'Orders', icon: ShoppingCart },
        // { id: 'infrastructure', name: 'Infrastructure', icon: Network },
        // { id: 'logs', name: 'Logs', icon: Terminal },
      ]
    };
  }
};
</script>
